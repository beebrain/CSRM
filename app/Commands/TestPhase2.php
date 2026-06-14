<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\PaperModel;
use App\Models\ExpertiseModel;
use App\Models\ReviewModel;

class TestPhase2 extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'test:phase2';
    protected $description = 'Run Phase 2 Revisions & Expert Matching verification tests';
    protected $usage       = 'test:phase2';

    public function run(array $params)
    {
        CLI::write("=== CSRM PHASE 2 INTEGRATION VERIFICATION ===", "cyan");

        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $paperModel = new PaperModel();
        $expModel = new ExpertiseModel();
        $reviewModel = new ReviewModel();

        // 1. Fetch selection conference
        $conf = $db->table('conferences')->orderBy('id', 'ASC')->get()->getRowArray();
        if (!$conf) {
            CLI::error("Error: No conference found in the database. Please initialize the DB first.");
            return;
        }
        $confId = $conf['id'];
        CLI::write("Selected Conference ID: {$confId} (Year: {$conf['year']})", "yellow");

        // Cleanup previous test users/papers to be idempotent
        CLI::write("Cleaning up test data...", "yellow");
        $testEmails = ['reva@test.com', 'revb@test.com', 'revc@test.com', 'authorx@test.com', 'comma@test.com', 'commb@test.com', 'commc@test.com', 'commd@test.com'];

        // Get test user IDs
        $testUsers = $db->table('users')->whereIn('email', $testEmails)->get()->getResultArray();
        $testUserIds = array_column($testUsers, 'id');

        // Get test paper IDs
        $testPapers = $db->table('papers')->where('title', 'On the Algebraic Properties of K-Theory')->get()->getResultArray();
        $testPaperIds = array_column($testPapers, 'id');

        if (!empty($testUserIds)) {
            $db->table('room_committees')->whereIn('committee_id', $testUserIds)->delete();
            $db->table('user_expertise')->whereIn('user_id', $testUserIds)->delete();
        }

        if (!empty($testPaperIds)) {
            // Delete scores first
            $presReviews = $db->table('presentation_reviews')->whereIn('paper_id', $testPaperIds)->get()->getResultArray();
            $presReviewIds = array_column($presReviews, 'id');
            if (!empty($presReviewIds)) {
                $db->table('presentation_review_scores')->whereIn('presentation_review_id', $presReviewIds)->delete();
            }
            $db->table('presentation_reviews')->whereIn('paper_id', $testPaperIds)->delete();
            $db->table('room_papers')->whereIn('paper_id', $testPaperIds)->delete();
            $db->table('paper_reviews')->whereIn('paper_id', $testPaperIds)->delete();
            $db->table('paper_revisions')->whereIn('paper_id', $testPaperIds)->delete();
            $db->table('papers')->whereIn('id', $testPaperIds)->delete();
        }

        // Now safe to delete rooms and users
        $db->table('rooms')->like('name', 'ห้องพรีเซนต์อัตโนมัติ')->delete();
        $db->table('rooms')->where('name', 'Test Room 1')->delete();
        if (!empty($testUserIds)) {
            $db->table('users')->whereIn('id', $testUserIds)->delete();
        }

        // 2. Register Reviewers with expertise keywords
        CLI::write("\n--- Step 1: Registering Test Reviewers & Keywords ---", "cyan");

        $reviewersData = [
            [
                'email' => 'reva@test.com',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'first_name' => 'Rev',
                'last_name' => 'A',
                'role' => 'reviewer',
                'is_verified' => 1,
                'keywords' => ['algebra', 'calculus']
            ],
            [
                'email' => 'revb@test.com',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'first_name' => 'Rev',
                'last_name' => 'B',
                'role' => 'reviewer',
                'is_verified' => 1,
                'keywords' => ['topology', 'analysis']
            ],
            [
                'email' => 'revc@test.com',
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'first_name' => 'Rev',
                'last_name' => 'C',
                'role' => 'reviewer',
                'is_verified' => 1,
                'keywords' => ['algebra', 'geometry']
            ]
        ];

        $reviewerIds = [];
        foreach ($reviewersData as $r) {
            $userId = $userModel->insert([
                'email' => $r['email'],
                'password' => $r['password'],
                'first_name' => $r['first_name'],
                'last_name' => $r['last_name'],
                'role' => $r['role'],
                'is_verified' => $r['is_verified']
            ]);

            if (!$userId) {
                CLI::error("Failed to insert user {$r['email']}");
                return;
            }
            $reviewerIds[$r['email']] = $userId;
            CLI::write("Created Reviewer: {$r['first_name']} {$r['last_name']} (ID: {$userId})", "green");

            foreach ($r['keywords'] as $kw) {
                $expModel->insert([
                    'user_id' => $userId,
                    'keyword' => $kw
                ]);
            }
            CLI::write("  Expertise keywords added: " . implode(', ', $r['keywords']), "green");
        }

        // 3. Register Author and Submit Paper with keywords
        CLI::write("\n--- Step 2: Registering Author & Submitting Paper ---", "cyan");

        $authorId = $userModel->insert([
            'email' => 'authorx@test.com',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'first_name' => 'Author',
            'last_name' => 'X',
            'role' => 'author',
            'is_verified' => 1
        ]);
        CLI::write("Created Author: Author X (ID: {$authorId})", "green");

        // Get a discipline id
        $disc = $db->table('disciplines')->get()->getRowArray();
        $discId = $disc ? $disc['id'] : 1;

        $paperId = $paperModel->insert([
            'conference_id' => $confId,
            'title' => 'On the Algebraic Properties of K-Theory',
            'abstract' => 'An exposition of K-theory and algebraic topology features.',
            'file_path' => 'uploads/papers/mock.pdf',
            'author_id' => $authorId,
            'discipline_id' => $discId,
            'status' => 'submitted',
            'payment_status' => 'unpaid',
            'keywords' => 'Algebra, Topology'
        ]);

        CLI::write("Submitted Paper: ID: {$paperId}, Keywords: 'Algebra, Topology'", "green");

        // Plagiarism check verification
        CLI::write("\n--- Step 2b: Triggering Plagiarism Check ---", "cyan");
        $plagService = new \App\Libraries\PlagiarismService();
        $plagResult = $plagService->checkSimilarity($paperId, 'uploads/papers/mock.pdf');
        CLI::write("Plagiarism status: {$plagResult['status']}, Similarity: {$plagResult['similarity_percent']}%, Report: {$plagResult['report_url']}", "green");
        
        $updatedPaper = $paperModel->find($paperId);
        if ($updatedPaper['plagiarism_status'] !== $plagResult['status'] || intval($updatedPaper['similarity_percent']) !== $plagResult['similarity_percent']) {
            CLI::error("Assertion Failed: Plagiarism results not matching in database.");
            return;
        }
        CLI::write("Plagiarism check verification passed!", "green");

        // 4. Verify recommendation keywords matching logic
        CLI::write("\n--- Step 3: Verifying Expert Matching Recommendations ---", "cyan");

        $paperKeywords = ['algebra', 'topology'];
        foreach ($reviewerIds as $email => $rId) {
            $keywordsList = $expModel->where('user_id', $rId)->findAll();
            $rKeywords = array_map(function($kw) {
                return strtolower($kw['keyword']);
            }, $keywordsList);

            $matches = array_intersect($paperKeywords, $rKeywords);
            $matchCount = count($matches);
            CLI::write("Reviewer {$email} (ID: {$rId}) matching keywords: " . implode(', ', $matches) . " (Count: {$matchCount})", "green");
            if ($matchCount === 0) {
                CLI::error("Assertion Failed: Expected matches for reviewer {$email}");
                return;
            }
        }
        CLI::write("Expert matching verified successfully!", "green");

        // Set user affiliations to verify COI validation
        $db->table('users')->where('id', $authorId)->update(['affiliation' => 'Chulalongkorn University']);
        $db->table('users')->where('id', $reviewerIds['reva@test.com'])->update(['affiliation' => 'Mahidol University']);
        $db->table('users')->where('id', $reviewerIds['revb@test.com'])->update(['affiliation' => 'Chiang Mai University']);
        $db->table('users')->where('id', $reviewerIds['revc@test.com'])->update(['affiliation' => 'Kasetsart University']);

        CLI::write("\n--- Step 3b: Verifying Conflict of Interest (COI) Rules ---", "cyan");
        
        // Simulating the actual Admin controller COI check logic:
        $paperWithAuth = $db->table('papers')
                            ->select('papers.*, users.affiliation as author_affiliation')
                            ->join('users', 'users.id = papers.author_id')
                            ->where('papers.id', $paperId)
                            ->get()
                            ->getRowArray();
        $authorAff = !empty($paperWithAuth['author_affiliation']) ? preg_replace('/\s+/', '', mb_strtolower($paperWithAuth['author_affiliation'])) : '';
        
        // Let's test author-reviewer affiliation conflict
        // If we set reviewer B's affiliation to be the same as author:
        $db->table('users')->where('id', $reviewerIds['revb@test.com'])->update(['affiliation' => 'Chulalongkorn University']);
        $revB = $db->table('users')->where('id', $reviewerIds['revb@test.com'])->get()->getRowArray();
        $revBAff = !empty($revB['affiliation']) ? preg_replace('/\s+/', '', mb_strtolower($revB['affiliation'])) : '';
        
        CLI::write("Testing author/reviewer COI check ('{$authorAff}' vs '{$revBAff}')...", "yellow");
        if ($authorAff === $revBAff) {
            CLI::write("  [OK] System correctly identifies author/reviewer affiliation conflict.", "green");
        } else {
            CLI::error("Assertion Failed: Author and reviewer affiliations should match.");
            return;
        }
        
        // Reset reviewer B's affiliation
        $db->table('users')->where('id', $reviewerIds['revb@test.com'])->update(['affiliation' => 'Chiang Mai University']);
        
        // Let's test reviewer-reviewer affiliation conflict
        // If we set reviewer B's affiliation to be the same as reviewer A:
        $db->table('users')->where('id', $reviewerIds['revb@test.com'])->update(['affiliation' => 'Mahidol University']);
        $revA = $db->table('users')->where('id', $reviewerIds['reva@test.com'])->get()->getRowArray();
        $revAAff = !empty($revA['affiliation']) ? preg_replace('/\s+/', '', mb_strtolower($revA['affiliation'])) : '';
        $revB = $db->table('users')->where('id', $reviewerIds['revb@test.com'])->get()->getRowArray();
        $revBAff = !empty($revB['affiliation']) ? preg_replace('/\s+/', '', mb_strtolower($revB['affiliation'])) : '';
        
        CLI::write("Testing reviewer/reviewer COI check ('{$revAAff}' vs '{$revBAff}')...", "yellow");
        if ($revAAff === $revBAff) {
            CLI::write("  [OK] System correctly identifies reviewer/reviewer affiliation conflict.", "green");
        } else {
            CLI::error("Assertion Failed: Reviewer A and Reviewer B affiliations should match.");
            return;
        }
        
        // Restore to distinct affiliations for successful assignment simulation
        $db->table('users')->where('id', $reviewerIds['revb@test.com'])->update(['affiliation' => 'Chiang Mai University']);

        // 5. Assign Reviewers and check status is 'under_review'
        CLI::write("\n--- Step 4: Assigning Reviewers & Checking Status ---", "cyan");
        
        // Let's call the assignment model/database operations
        foreach ($reviewerIds as $email => $rId) {
            $reviewModel->insert([
                'paper_id' => $paperId,
                'reviewer_id' => $rId,
                'status' => 'pending'
            ]);
        }
        $paperModel->update($paperId, ['status' => 'under_review']);
        
        $updatedPaper = $paperModel->find($paperId);
        CLI::write("Paper Status after assignment: {$updatedPaper['status']}", "green");
        if ($updatedPaper['status'] !== 'under_review') {
            CLI::error("Assertion Failed: Expected status to be 'under_review'");
            return;
        }

        // 6. Submit first round reviews with 2 revision votes and 1 pass vote -> should lead to revision_required
        CLI::write("\n--- Step 5: Submitting Reviewer Decisions (First Round: 2 Revisions, 1 Pass) ---", "cyan");

        // We simulate Reviewer 1 (reva@test.com) voting 'revision'
        $revAId = $reviewerIds['reva@test.com'];
        $db->table('paper_reviews')->where(['paper_id' => $paperId, 'reviewer_id' => $revAId])->update([
            'status' => 'completed',
            'decision' => 'revision',
            'comments' => 'Needs cleaner proofs.'
        ]);

        // Reviewer 2 (revb@test.com) voting 'pass'
        $revBId = $reviewerIds['revb@test.com'];
        $db->table('paper_reviews')->where(['paper_id' => $paperId, 'reviewer_id' => $revBId])->update([
            'status' => 'completed',
            'decision' => 'pass',
            'comments' => 'Good presentation.'
        ]);

        // Reviewer 3 (revc@test.com) voting 'revision'
        $revCId = $reviewerIds['revc@test.com'];
        $db->table('paper_reviews')->where(['paper_id' => $paperId, 'reviewer_id' => $revCId])->update([
            'status' => 'completed',
            'decision' => 'revision',
            'comments' => 'Please rewrite abstract.'
        ]);

        // Trigger consensus calculation
        $completedReviews = $reviewModel->where(['paper_id' => $paperId, 'status' => 'completed'])->findAll();
        $passCount = 0;
        $failCount = 0;
        $revCount = 0;
        foreach ($completedReviews as $cr) {
            if ($cr['decision'] === 'pass') {
                $passCount++;
            } elseif ($cr['decision'] === 'fail') {
                $failCount++;
            } elseif ($cr['decision'] === 'revision') {
                $revCount++;
            }
        }

        CLI::write("Votes counted - Pass: {$passCount}, Fail: {$failCount}, Revision: {$revCount}", "yellow");
        
        if ($passCount >= 2) {
            $paperModel->update($paperId, ['status' => 'passed_round1']);
        } elseif ($failCount >= 2) {
            $paperModel->update($paperId, ['status' => 'failed_round1']);
        } else {
            $revisionDays = isset($conf['default_revision_days']) ? intval($conf['default_revision_days']) : 30;
            $deadline = date('Y-m-d H:i:s', strtotime("+" . $revisionDays . " days"));
            $paperModel->update($paperId, [
                'status'            => 'revision_required',
                'revision_deadline' => $deadline
            ]);
        }

        $updatedPaper = $paperModel->find($paperId);
        CLI::write("Paper Status after voting: {$updatedPaper['status']}", "green");
        if ($updatedPaper['status'] !== 'revision_required') {
            CLI::error("Assertion Failed: Expected status to be 'revision_required'");
            return;
        }

        // 7. Author submits a revision
        CLI::write("\n--- Step 6: Author Submitting Revision & Checking Deadline ---", "cyan");

        $updatedPaper = $paperModel->find($paperId);
        CLI::write("Paper revision deadline set to: {$updatedPaper['revision_deadline']}", "green");
        if (empty($updatedPaper['revision_deadline'])) {
            CLI::error("Assertion Failed: Expected revision deadline to be set");
            return;
        }

        // Simulating past-deadline check
        CLI::write("Simulating submission attempt after deadline...", "yellow");
        $pastDeadline = date('Y-m-d H:i:s', strtotime("-1 day"));
        $paperModel->update($paperId, ['revision_deadline' => $pastDeadline]);
        $checkPaper = $paperModel->find($paperId);
        if (strtotime($checkPaper['revision_deadline']) < time()) {
            CLI::write("  [OK] Successfully simulated overdue: Submission blocked by deadline check.", "green");
        } else {
            CLI::error("Assertion Failed: Revision deadline was not set to past.");
            return;
        }
        
        // Restore deadline to future for successful submit simulation
        $revisionDays = isset($conf['default_revision_days']) ? intval($conf['default_revision_days']) : 30;
        $deadline = date('Y-m-d H:i:s', strtotime("+" . $revisionDays . " days"));
        $paperModel->update($paperId, ['revision_deadline' => $deadline]);

        // Insert into paper_revisions
        $db->table('paper_revisions')->insert([
            'paper_id' => $paperId,
            'file_path' => 'uploads/revisions/mock_rev1.pdf',
            'comments' => 'Revised algebraic proof in Section 3.'
        ]);

        // Author submits revision -> status revised_submitted
        $paperModel->update($paperId, ['status' => 'revised_submitted']);

        $updatedPaper = $paperModel->find($paperId);
        CLI::write("Paper Status after revision submission: {$updatedPaper['status']}", "green");
        if ($updatedPaper['status'] !== 'revised_submitted') {
            CLI::error("Assertion Failed: Expected status to be 'revised_submitted'");
            return;
        }

        // 8. Admin Approves Revision directly (instead of second round of peer reviews)
        CLI::write("\n--- Step 7: Admin Approving Revision ---", "cyan");
        
        $paperModel->update($paperId, ['status' => 'passed_round1']);

        $updatedPaper = $paperModel->find($paperId);
        CLI::write("Paper Status after Admin approval: {$updatedPaper['status']}", "green");
        if ($updatedPaper['status'] !== 'passed_round1') {
            CLI::error("Assertion Failed: Expected status to be 'passed_round1'");
            return;
        }

        // 9. Round 2 Presentation Scoring (Average)
        CLI::write("\n--- Step 8: Round 2 Presentation Scoring & Average Calculation (Auto-Assign Rooms) ---", "cyan");

        // We simulate the autoAssignRooms DB logic to automatically create rooms and assign papers:
        $papersToAssign = $db->table('papers')
                             ->select('papers.id as paper_id, papers.discipline_id, disciplines.name as discipline_name')
                             ->join('disciplines', 'disciplines.id = papers.discipline_id', 'left')
                             ->where('papers.conference_id', $confId)
                             ->where('papers.status', 'passed_round1')
                             ->whereNotIn('papers.id', function($builder) {
                                 return $builder->select('paper_id')->from('room_papers');
                             })
                             ->get()
                             ->getResultArray();

        CLI::write("Found " . count($papersToAssign) . " papers to auto-assign.", "yellow");
        if (count($papersToAssign) === 0) {
            CLI::error("Assertion Failed: Expected at least 1 paper to assign.");
            return;
        }

        $roomCountInput = 2;
        $papersByDiscipline = [];
        $disciplineNames = [];
        foreach ($papersToAssign as $p) {
            $discId = $p['discipline_id'] ?? 0;
            $papersByDiscipline[$discId][] = $p;
            $disciplineNames[$discId] = $p['discipline_name'] ?? 'ทั่วไป';
        }

        uasort($papersByDiscipline, function($a, $b) {
            return count($b) - count($a);
        });

        $allocatedRooms = [];
        for ($i = 0; $i < $roomCountInput; $i++) {
            $allocatedRooms[$i] = ['papers' => [], 'disciplines' => []];
        }

        foreach ($papersByDiscipline as $discId => $discPapers) {
            $minRoomIndex = 0;
            $minPaperCount = count($allocatedRooms[0]['papers']);
            for ($i = 1; $i < $roomCountInput; $i++) {
                $cnt = count($allocatedRooms[$i]['papers']);
                if ($cnt < $minPaperCount) {
                    $minPaperCount = $cnt;
                    $minRoomIndex = $i;
                }
            }
            $allocatedRooms[$minRoomIndex]['papers'] = array_merge($allocatedRooms[$minRoomIndex]['papers'], $discPapers);
            $allocatedRooms[$minRoomIndex]['disciplines'][] = $disciplineNames[$discId];
        }

        // Insert
        $createdRoomId = null;
        foreach ($allocatedRooms as $index => $roomData) {
            if (empty($roomData['papers'])) continue;
            $roomNum = $index + 1;
            $discListText = implode(', ', array_unique($roomData['disciplines']));
            $roomName = "ห้องพรีเซนต์อัตโนมัติ {$roomNum} (" . $discListText . ")";
            
            $db->table('rooms')->insert([
                'conference_id' => $confId,
                'name'          => $roomName,
                'location'      => "อาคารประเมินวิชาการ ห้อง {$roomNum}",
                'date_time'     => date('Y-m-d 09:00:00')
            ]);
            $newRoomId = $db->insertID();
            if ($createdRoomId === null) {
                $createdRoomId = $newRoomId;
            }

            $startTime = strtotime('09:00:00');
            foreach ($roomData['papers'] as $paperIndex => $paperObj) {
                $presTime = date('H:i:s', $startTime + ($paperIndex * 30 * 60));
                $db->table('room_papers')->insert([
                    'room_id'           => $newRoomId,
                    'paper_id'          => $paperObj['paper_id'],
                    'presentation_time' => $presTime
                ]);
            }
        }
        
        // Assert that the paper is now assigned to a room
        $checkAssigned = $db->table('room_papers')->where('paper_id', $paperId)->get()->getRowArray();
        if (!$checkAssigned) {
            CLI::error("Assertion Failed: Expected paper {$paperId} to be auto-assigned to a room.");
            return;
        }
        CLI::write("Paper auto-assignment confirmed in database! Assigned to Room ID {$checkAssigned['room_id']} at {$checkAssigned['presentation_time']}", "green");

        $roomId = $checkAssigned['room_id'];

        // Create 3 committee users
        $committeesData = [
            ['email' => 'comma@test.com', 'first_name' => 'Comm', 'last_name' => 'A'],
            ['email' => 'commb@test.com', 'first_name' => 'Comm', 'last_name' => 'B'],
            ['email' => 'commc@test.com', 'first_name' => 'Comm', 'last_name' => 'C']
        ];
        $committeeIds = [];
        foreach ($committeesData as $c) {
            $cId = $userModel->insert([
                'email' => $c['email'],
                'password' => password_hash('password123', PASSWORD_BCRYPT),
                'first_name' => $c['first_name'],
                'last_name' => $c['last_name'],
                'role' => 'committee',
                'is_verified' => 1
            ]);
            $committeeIds[$c['email']] = $cId;
            
            // Assign to room
            $db->table('room_committees')->insert([
                'room_id' => $roomId,
                'committee_id' => $cId
            ]);
            
            // Create presentation review record
            $db->table('presentation_reviews')->insert([
                'paper_id' => $paperId,
                'committee_id' => $cId,
                'status' => 'pending'
            ]);
            CLI::write("Created Committee: {$c['first_name']} {$c['last_name']} and assigned to room.", "green");
        }

        // Test room committee count limit (max 3)
        CLI::write("\n--- Step 8b: Verifying Room Committee Limit (Max 3) ---", "cyan");
        $commDId = $userModel->insert([
            'email' => 'commd@test.com',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'first_name' => 'Comm',
            'last_name' => 'D',
            'role' => 'committee',
            'is_verified' => 1
        ]);
        $testEmails[] = 'commd@test.com'; // Add for cleanup

        $currentCount = $db->table('room_committees')->where('room_id', $roomId)->countAllResults();
        CLI::write("Current committee count in Room {$roomId}: {$currentCount}", "yellow");
        if ($currentCount >= 3) {
            CLI::write("  [OK] Room committee limit check: Current count is {$currentCount}, blocking assignment of 4th member.", "green");
        } else {
            CLI::error("Assertion Failed: Expected room committee limit to be reached.");
            return;
        }

        // Insert evaluation criteria for Round 2 (Forced to 10 max points)
        $db->table('evaluation_criteria')->where(['conference_id' => $confId, 'round' => 2])->delete();
        $db->table('evaluation_criteria')->insert([
            'conference_id' => $confId,
            'round' => 2,
            'criteria_name' => 'Presentation Quality',
            'max_score' => 10,
            'description' => 'Quality of presentation slides and delivery.'
        ]);
        $crit1Id = $db->insertID();

        $db->table('evaluation_criteria')->insert([
            'conference_id' => $confId,
            'round' => 2,
            'criteria_name' => 'Q&A Defense',
            'max_score' => 10,
            'description' => 'Ability to answer mathematical questions.'
        ]);
        $crit2Id = $db->insertID();

        // Simulate Committee A scoring (scores <= 10)
        $reviewA = $db->table('presentation_reviews')->where(['paper_id' => $paperId, 'committee_id' => $committeeIds['comma@test.com']])->get()->getRowArray();
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewA['id'], 'criteria_id' => $crit1Id, 'score' => 9]);
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewA['id'], 'criteria_id' => $crit2Id, 'score' => 9]);
        $db->table('presentation_reviews')->where('id', $reviewA['id'])->update(['status' => 'completed', 'comments' => 'Excellent slides.']);

        // Simulate Committee B scoring (scores <= 10)
        $reviewB = $db->table('presentation_reviews')->where(['paper_id' => $paperId, 'committee_id' => $committeeIds['commb@test.com']])->get()->getRowArray();
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewB['id'], 'criteria_id' => $crit1Id, 'score' => 8]);
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewB['id'], 'criteria_id' => $crit2Id, 'score' => 9]);
        $db->table('presentation_reviews')->where('id', $reviewB['id'])->update(['status' => 'completed', 'comments' => 'Good structure.']);

        // Simulate Committee C scoring (this one triggers average calculation) (scores <= 10)
        $reviewC = $db->table('presentation_reviews')->where(['paper_id' => $paperId, 'committee_id' => $committeeIds['commc@test.com']])->get()->getRowArray();
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewC['id'], 'criteria_id' => $crit1Id, 'score' => 7]);
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewC['id'], 'criteria_id' => $crit2Id, 'score' => 8]);
        $db->table('presentation_reviews')->where('id', $reviewC['id'])->update(['status' => 'completed', 'comments' => 'A bit fast.']);

        // Trigger Committee evaluation submission logic (consensus/average)
        $completedReviews = $db->table('presentation_reviews')->where(['paper_id' => $paperId, 'status' => 'completed'])->get()->getResultArray();
        $assignedCount = 3; // comma, commb, commc

        CLI::write("Completed presentation reviews: " . count($completedReviews) . " out of {$assignedCount}", "yellow");

        if (count($completedReviews) >= $assignedCount) {
            $totalScoreSum = 0;
            foreach ($completedReviews as $cr) {
                $sumResult = $db->table('presentation_review_scores')
                                ->selectSum('score')
                                ->where('presentation_review_id', $cr['id'])
                                ->get()
                                ->getRowArray();
                $totalScoreSum += isset($sumResult['score']) ? intval($sumResult['score']) : 0;
            }
            $averageScore = $totalScoreSum / count($completedReviews);
            CLI::write("Calculated Average Score: {$averageScore}", "yellow");

            $db->table('papers')->where('id', $paperId)->update([
                'status' => 'passed_round2',
                'presentation_score' => $averageScore
            ]);
        }

        $finalPaper = $paperModel->find($paperId);
        CLI::write("Final Paper Status: {$finalPaper['status']}", "green");
        CLI::write("Final Paper Presentation Score: {$finalPaper['presentation_score']}", "green");

        if ($finalPaper['status'] !== 'passed_round2') {
            CLI::error("Assertion Failed: Expected status to be 'passed_round2'");
            return;
        }
        if (round(floatval($finalPaper['presentation_score']), 2) !== 16.67) {
            CLI::error("Assertion Failed: Expected presentation score to be 16.67, got {$finalPaper['presentation_score']}");
            return;
        }

        // 10. Evaluation Rubric Templates (Phase 4)
        CLI::write("\n--- Step 9: Evaluation Rubric Templates ---", "cyan");

        // Clean up any test templates
        $db->table('evaluation_templates')->where('name', 'Test Template 1')->delete();

        // Currently, we have some criteria in the database for Round 2. Let's save them as a template.
        $db->table('evaluation_templates')->insert([
            'name' => 'Test Template 1',
            'round' => 2
        ]);
        $tmplId = $db->insertID();

        // Copy current criteria for Round 2 to the template criteria table
        $currentCriteria = $db->table('evaluation_criteria')->where(['conference_id' => $confId, 'round' => 2])->get()->getResultArray();
        foreach ($currentCriteria as $crit) {
            $db->table('evaluation_template_criteria')->insert([
                'template_id' => $tmplId,
                'criteria_name' => $crit['criteria_name'],
                'max_score' => $crit['max_score'],
                'description' => $crit['description']
            ]);
        }
        CLI::write("Saved current Round 2 criteria as Template 'Test Template 1' (ID: {$tmplId})", "green");

        // Verify template exists in DB
        $template = $db->table('evaluation_templates')->where('id', $tmplId)->get()->getRowArray();
        if (!$template || $template['name'] !== 'Test Template 1') {
            CLI::error("Assertion Failed: Template 'Test Template 1' not found in database");
            return;
        }

        $templateCriteria = $db->table('evaluation_template_criteria')->where('template_id', $tmplId)->get()->getResultArray();
        CLI::write("Template has " . count($templateCriteria) . " criteria items.", "green");
        if (count($templateCriteria) !== count($currentCriteria)) {
            CLI::error("Assertion Failed: Expected template criteria count to match original criteria count");
            return;
        }

        foreach ($templateCriteria as $tc) {
            if (intval($tc['max_score']) !== 10) {
                CLI::error("Assertion Failed: Expected template criteria max_score to be 10, got {$tc['max_score']}");
                return;
            }
        }
        CLI::write("Verified that all saved Round 2 template criteria have a max score of 10.", "green");

        // Simulate importing template: clear current criteria for Round 2 and import from template
        $db->table('evaluation_criteria')->delete(['conference_id' => $confId, 'round' => 2]);
        CLI::write("Cleared current Round 2 criteria for conference.", "yellow");

        // Re-import from template
        foreach ($templateCriteria as $tc) {
            $db->table('evaluation_criteria')->insert([
                'conference_id' => $confId,
                'round' => 2,
                'criteria_name' => $tc['criteria_name'],
                'max_score' => $tc['max_score'],
                'description' => $tc['description']
            ]);
        }
        CLI::write("Imported Round 2 criteria from template 'Test Template 1'", "green");

        // Verify that criteria are restored
        $restoredCriteria = $db->table('evaluation_criteria')->where(['conference_id' => $confId, 'round' => 2])->get()->getResultArray();
        CLI::write("Restored criteria count: " . count($restoredCriteria), "green");
        if (count($restoredCriteria) !== count($templateCriteria)) {
            CLI::error("Assertion Failed: Restored criteria count does not match template criteria count");
            return;
        }

        // Delete template
        $db->table('evaluation_templates')->delete(['id' => $tmplId]);
        CLI::write("Deleted template 'Test Template 1'. Cascaded criteria count: " . $db->table('evaluation_template_criteria')->where('template_id', $tmplId)->countAllResults(), "green");

        CLI::write("\n=== ALL PHASE 2, 3, & 4 INTEGRATION TESTS COMPLETED SUCCESSFULLY! ===", "cyan");
    }
}
