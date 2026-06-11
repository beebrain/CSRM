<?php
$db = mysqli_connect("db", "csrm_user", "csrm_password", "csrm_db");
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h3>Database User Count by Role:</h3>";
$resUsers = mysqli_query($db, "SELECT role, COUNT(*) as count FROM users GROUP BY role");
while ($row = mysqli_fetch_assoc($resUsers)) {
    echo "Role: " . htmlspecialchars($row['role']) . " -> " . htmlspecialchars($row['count']) . "<br>";
}

echo "<h3>Simulation Users (@csrm-simulation.org):</h3>";
$resSimUsers = mysqli_query($db, "SELECT role, COUNT(*) as count FROM users WHERE email LIKE '%@csrm-simulation.org' GROUP BY role");
while ($row = mysqli_fetch_assoc($resSimUsers)) {
    echo "Role: " . htmlspecialchars($row['role']) . " -> " . htmlspecialchars($row['count']) . "<br>";
}

echo "<h3>Papers Count:</h3>";
$resPapers = mysqli_query($db, "SELECT COUNT(*) as count FROM papers");
$row = mysqli_fetch_assoc($resPapers);
echo "Total Papers -> " . htmlspecialchars($row['count']) . "<br>";

$resSimPapers = mysqli_query($db, "SELECT COUNT(*) as count FROM papers WHERE author_id IN (SELECT id FROM users WHERE email LIKE '%@csrm-simulation.org')");
$row = mysqli_fetch_assoc($resSimPapers);
echo "Simulation Papers -> " . htmlspecialchars($row['count']) . "<br>";
