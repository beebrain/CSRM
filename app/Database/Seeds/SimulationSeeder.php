<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SimulationSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $passwordHash = password_hash('password123', PASSWORD_DEFAULT);

        echo "Cleaning up existing simulation data...\n";
        // 1. Delete papers created by simulation authors
        $db->query("DELETE FROM papers WHERE author_id IN (SELECT id FROM users WHERE email LIKE '%@csrm-simulation.org')");
        // 2. Delete paper reviews associated with simulation reviewers
        $db->query("DELETE FROM paper_reviews WHERE reviewer_id IN (SELECT id FROM users WHERE email LIKE '%@csrm-simulation.org')");
        // 3. Delete simulation users
        $db->query("DELETE FROM users WHERE email LIKE '%@csrm-simulation.org'");

        // Predefined 15 universities
        $universities = [
            'มหาวิทยาลัยนเรศวร',
            'มหาวิทยาลัยเชียงใหม่',
            'มหาวิทยาลัยราชภัฏพิบูลสงคราม',
            'จุฬาลงกรณ์มหาวิทยาลัย',
            'มหาวิทยาลัยเกษตรศาสตร์',
            'มหาวิทยาลัยธรรมศาสตร์',
            'มหาวิทยาลัยมหิดล',
            'มหาวิทยาลัยขอนแก่น',
            'มหาวิทยาลัยสงขลานครินทร์',
            'มหาวิทยาลัยเทคโนโลยีพระจอมเกล้าธนบุรี',
            'มหาวิทยาลัยเทคโนโลยีสุรนารี',
            'มหาวิทยาลัยบูรพา',
            'มหาวิทยาลัยศิลปากร',
            'มหาวิทยาลัยแม่โจ้',
            'มหาวิทยาลัยราชภัฏเชียงใหม่'
        ];

        // Predefined Thai first/last names for generating realistic combinations
        $firstNames = ['สมชาย', 'สมหญิง', 'วิทยา', 'อนันต์', 'กนกวรรณ', 'จิราพร', 'ธนพล', 'ปิยะ', 'พัชรา', 'เมธา', 'สุรชัย', 'อารีย์', 'เกียรติ', 'นงนุช', 'ประเสริฐ', 'วรรณา', 'ศักดิ์ชัย', 'อภิชาต', 'กิตติ', 'ชลดา', 'ณัฐพล', 'ทศพล', 'นภัสสร', 'บุญส่ง', 'พรเพ็ญ', 'รุ่งโรจน์', 'วิเชียร', 'ศิริพร', 'สมบัติ', 'อัญชลี'];
        $lastNames = ['รักดี', 'ใจดี', 'ศิริพัฒนา', 'รุ่งเรือง', 'งามเลิศ', 'เจริญสุข', 'ทองดี', 'พงษ์พานิช', 'มีชัย', 'สมบูรณ์', 'สุวรรณ', 'ประเสริฐสุข', 'ยิ่งยืน', 'ทรัพย์เพิ่ม', 'บุญรอด', 'คงมั่น', 'สว่างวงศ์', 'แก้วมณี', 'จิตรดี', 'ดวงดี', 'พรมมา', 'ศรีสุข', 'มั่งคั่ง', 'รวยรื่น', 'ร่มเย็น', 'เกษมสุข', 'พาณิชย์', 'เลิศล้ำ', 'วิไลรัตน์', 'แสนสุข'];

        // Predefined realistic paper titles (exactly 30)
        $paperTitles = [
            'ผลการจัดการเรียนรู้วิชาแคลคูลัสโดยใช้แบบจำลองทางคณิตศาสตร์',
            'การประยุกต์ใช้พีชคณิตเชิงเส้นสำหรับการจัดสรรทรัพยากรการผลิต',
            'การศึกษาพฤติกรรมการตัดสินใจโดยใช้อสมการเชิงอนุพันธ์',
            'การเปรียบเทียบวิธีการสอนคณิตศาสตร์ระดับมัธยมศึกษาตอนปลาย',
            'แบบจำลองการแพร่ระบาด of โรคติดต่อโดยใช้สมการอนุพันธ์ย่อย',
            'การใช้เทคโนโลยีดิจิทัลในการส่งเสริมการคิดขั้นสูงทางคณิตศาสตร์',
            'ความสัมพันธ์ระหว่างความเข้าใจมโนทัศน์ทางเรขาคณิตและความคิดสร้างสรรค์',
            'การประยุกต์ทฤษฎีกราฟในการออกแบบระบบเครือข่ายโลจิสติกส์',
            'ทัศนคติของนักศึกษาวิชาชีพครูต่อการวิจัยปฏิบัติการทางคณิตศาสตร์',
            'การวิเคราะห์อนุกรมเวลาสำหรับทำนายราคาพืชผลทางการเกษตรในเขตภาคเหนือ',
            'การศึกษาความสัมพันธ์เชิงพื้นที่ของข้อมูลด้วยวิธีการทางสถิติมิติสูง',
            'ความเข้าใจผิดทางคณิตศาสตร์เรื่องจำนวนจริงของนักเรียนชั้นมัธยมศึกษาปีที่ 4',
            'การเปรียบเทียบผลสัมฤทธิ์ทางการเรียนวิชาสถิติโดยการเรียนรู้แบบร่วมมือ',
            'การพัฒนาบทเรียนคอมพิวเตอร์ช่วยสอนวิชาเรขาคณิตวิเคราะห์',
            'การใช้เกมเป็นสื่อในการเรียนรู้ทางคณิตศาสตร์เรื่องเศษส่วน',
            'การศึกษาทฤษฎีกลุ่มและโครงสร้างทางพีชคณิตนามธรรม',
            'การคาดการณ์ปริมาณน้ำฝนโดยใช้ฟังก์ชันการกระจายแบบกัมเบล',
            'การแก้ปัญหาขอบเขตสำหรับสมการเชิงอนุพันธ์เชิงตัวเลข',
            'ความเชื่อมั่นของการประเมินผลสัมฤทธิ์ทางการเรียนคณิตศาสตร์แนวใหม่',
            'ความสามารถในการคิดวิเคราะห์โจทย์ปัญหาคณิตศาสตร์ของนักเรียนชั้นประถมศึกษาปีที่ 6',
            'การศึกษาตัวสร้างสำหรับพีชคณิตลีแบบขยาย',
            'ทฤษฎีจุดตรึงและแอปพลิเคชันในสมการอินทิกรัลไม่เชิงเส้น',
            'ความคาดหวังและระดับความพึงพอใจของครูผู้สอนต่อหลักสูตรคณิตศาสตร์ปรับปรุงใหม่',
            'การพัฒนารูปแบบการสอนคณิตศาสตร์โดยเน้นกระบวนการแก้ปัญหาของโพลยา',
            'การสร้างและหาคุณภาพชุดกิจกรรมคณิตศาสตร์เรื่องเมทริกซ์',
            'การแก้ปัญหากำหนดการเชิงเส้นสำหรับการขนส่งทางเลือก',
            'การศึกษาพฤติกรรมความเครียดและการเรียนคณิตศาสตร์ออนไลน์ของวัยรุ่น',
            'ผลของการใช้โปรแกรม Geogebra ต่อผลสัมฤทธิ์ทางการเรียนวิชาเรขาคณิต',
            'การประยุกต์ใช้เทคโนโลยีในการคัดกรองพฤติกรรมการเรียนรู้คณิตศาสตร์',
            'แบบจำลองคณิตศาสตร์สำหรับประเมินการลงทุนระบบพลังงานทดแทนในชุมชน'
        ];

        // 1. Add 30 Authors & 30 Papers
        echo "Generating 30 authors and 30 papers...\n";
        for ($i = 0; $i < 30; $i++) {
            $firstName = $firstNames[$i % count($firstNames)];
            $lastName = $lastNames[($i + 5) % count($lastNames)];
            $univ = $universities[$i % count($universities)];

            // Insert Author
            $authorData = [
                'email' => "author_sim_" . ($i + 1) . "@csrm-simulation.org",
                'password' => $passwordHash,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'affiliation' => $univ,
                'role' => 'author',
                'is_verified' => 1
            ];

            $db->table('users')->insert($authorData);
            $authorId = $db->insertID();

            // Insert Paper
            $title = $paperTitles[$i];
            $abstract = "งานวิจัยนี้มีวัตถุประสงค์เพื่อศึกษาเรื่อง " . $title . " โดยการเก็บข้อมูลจากกลุ่มตัวอย่างและวิเคราะห์ผลเชิงสถิติ ผลการศึกษาพบว่าวิธีการดังกล่าวช่วยพัฒนาทักษะความรู้และความเข้าใจทางคณิตศาสตร์ได้อย่างมีนัยสำคัญทางสถิติที่ระดับ .05";
            
            // Randomly distribute paper status & payment status
            $statusOptions = ['submitted', 'under_review', 'passed_round1'];
            $status = $statusOptions[$i % count($statusOptions)];
            
            $paymentOptions = ['unpaid', 'pending_verification', 'paid'];
            $paymentStatus = $paymentOptions[$i % count($paymentOptions)];

            // Get active conference ID dynamically, fallback to first conference, then to 1
            $activeConf = $db->query("SELECT id FROM conferences WHERE is_active = 1 LIMIT 1")->getRow();
            if (!$activeConf) {
                $activeConf = $db->query("SELECT id FROM conferences LIMIT 1")->getRow();
            }
            $conferenceId = $activeConf ? $activeConf->id : 1;

            $paperData = [
                'conference_id' => $conferenceId,
                'title' => $title,
                'abstract' => $abstract,
                'file_path' => "uploads/papers/mock_paper_" . ($i + 1) . ".pdf",
                'author_id' => $authorId,
                'discipline_id' => ($i % 7) + 1, // Cycles through disciplines 1-7
                'status' => $status,
                'payment_status' => $paymentStatus
            ];

            $db->table('papers')->insert($paperData);
        }

        // 2. Add 40 Reviewers
        echo "Generating 40 reviewers...\n";
        $titles = ['ดร.', 'ผศ.ดร.', 'รศ.ดร.'];
        for ($i = 0; $i < 40; $i++) {
            $title = $titles[$i % count($titles)];
            $firstName = $firstNames[($i + 10) % count($firstNames)];
            $lastName = $lastNames[($i + 12) % count($lastNames)];
            $univ = $universities[($i + 3) % count($universities)];

            $reviewerData = [
                'email' => "reviewer_sim_" . ($i + 1) . "@csrm-simulation.org",
                'password' => $passwordHash,
                'first_name' => $title . $firstName,
                'last_name' => $lastName,
                'affiliation' => $univ,
                'role' => 'reviewer',
                'is_verified' => 1
            ];

            $db->table('users')->insert($reviewerData);
        }

        echo "Simulation data generation completed successfully!\n";
    }
}
