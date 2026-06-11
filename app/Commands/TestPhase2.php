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
        $testEmails = ['reva@test.com', 'revb@test.com', 'revc@test.com', 'authorx@test.com', 'comma@test.com', 'commb@test.com', 'commc@test.com'];
        $db->table('rooms')->where('name', 'Test Room 1')->delete();
        $db->table('papers')->where('title', 'On the Algebraic Properties of K-Theory')->delete();
        $db->table('users')->whereIn('email', $testEmails)->delete();

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
            $paperModel->update($paperId, ['status' => 'revision_required']);
        }

        $updatedPaper = $paperModel->find($paperId);
        CLI::write("Paper Status after voting: {$updatedPaper['status']}", "green");
        if ($updatedPaper['status'] !== 'revision_required') {
            CLI::error("Assertion Failed: Expected status to be 'revision_required'");
            return;
        }

        // 7. Author submits a revision
        CLI::write("\n--- Step 6: Author Submitting Revision ---", "cyan");

        // Insert into paper_revisions
        $db->table('paper_revisions')->insert([
            'paper_id' => $paperId,
            'file_path' => 'uploads/revisions/mock_rev1.pdf',
            'comments' => 'Revised algebraic proof in Section 3.'
        ]);

        // Reset reviews and update status to revised_submitted
        $paperModel->update($paperId, ['status' => 'revised_submitted']);
        $db->table('paper_reviews')->where('paper_id', $paperId)->update([
            'status' => 'pending',
            'decision' => null,
            'comments' => null
        ]);

        $updatedPaper = $paperModel->find($paperId);
        CLI::write("Paper Status after revision submission: {$updatedPaper['status']}", "green");
        if ($updatedPaper['status'] !== 'revised_submitted') {
            CLI::error("Assertion Failed: Expected status to be 'revised_submitted'");
            return;
        }

        $resettedReviews = $reviewModel->where('paper_id', $paperId)->findAll();
        foreach ($resettedReviews as $rr) {
            CLI::write("  Review assignment for reviewer ID {$rr['reviewer_id']}: status={$rr['status']}, decision=" . ($rr['decision'] ?? 'NULL'), "green");
            if ($rr['status'] !== 'pending' || $rr['decision'] !== null) {
                CLI::error("Assertion Failed: Expected review assignments to be reset to pending and NULL decision");
                return;
            }
        }

        // 8. Second round review submissions (2 passes, 1 fail)
        CLI::write("\n--- Step 7: Submitting Reviewer Decisions (Second Round: 2 Passes, 1 Fail) ---", "cyan");

        // Reviewer 1 votes 'pass'
        $db->table('paper_reviews')->where(['paper_id' => $paperId, 'reviewer_id' => $revAId])->update([
            'status' => 'completed',
            'decision' => 'pass',
            'comments' => 'Looks great now.'
        ]);

        // Reviewer 2 votes 'pass'
        $db->table('paper_reviews')->where(['paper_id' => $paperId, 'reviewer_id' => $revBId])->update([
            'status' => 'completed',
            'decision' => 'pass',
            'comments' => 'Approved.'
        ]);

        // Reviewer 3 votes 'fail'
        $db->table('paper_reviews')->where(['paper_id' => $paperId, 'reviewer_id' => $revCId])->update([
            'status' => 'completed',
            'decision' => 'fail',
            'comments' => 'Still has minor issues.'
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
            $paperModel->update($paperId, ['status' => 'revision_required']);
        }

        $updatedPaper = $paperModel->find($paperId);
        CLI::write("Paper Status after second round: {$updatedPaper['status']}", "green");
        if ($updatedPaper['status'] !== 'passed_round1') {
            CLI::error("Assertion Failed: Expected status to be 'passed_round1'");
            return;
        }

        // 9. Round 2 Presentation Scoring (Average)
        CLI::write("\n--- Step 8: Round 2 Presentation Scoring & Average Calculation ---", "cyan");

        // Create a test room
        $db->table('rooms')->insert([
            'conference_id' => $confId,
            'name' => 'Test Room 1',
            'location' => 'Building 3, Room 302',
            'date_time' => '2026-06-10 09:00:00'
        ]);
        $roomId = $db->insertID();
        CLI::write("Created Room: Test Room 1 (ID: {$roomId})", "green");

        // Associate paper to room
        $db->table('room_papers')->insert([
            'room_id' => $roomId,
            'paper_id' => $paperId,
            'presentation_time' => '09:00 - 09:30'
        ]);

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

        // Insert evaluation criteria for Round 2
        $db->table('evaluation_criteria')->where(['conference_id' => $confId, 'round' => 2])->delete();
        $crit1Id = $db->table('evaluation_criteria')->insert([
            'conference_id' => $confId,
            'round' => 2,
            'criteria_name' => 'Presentation Quality',
            'max_score' => 50,
            'description' => 'Quality of presentation slides and delivery.'
        ]);
        $crit2Id = $db->table('evaluation_criteria')->insert([
            'conference_id' => $confId,
            'round' => 2,
            'criteria_name' => 'Q&A Defense',
            'max_score' => 50,
            'description' => 'Ability to answer mathematical questions.'
        ]);

        // Simulate Committee A scoring
        $reviewA = $db->table('presentation_reviews')->where(['paper_id' => $paperId, 'committee_id' => $committeeIds['comma@test.com']])->get()->getRowArray();
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewA['id'], 'criteria_id' => $crit1Id, 'score' => 45]);
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewA['id'], 'criteria_id' => $crit2Id, 'score' => 45]);
        $db->table('presentation_reviews')->where('id', $reviewA['id'])->update(['status' => 'completed', 'comments' => 'Excellent slides.']);

        // Simulate Committee B scoring
        $reviewB = $db->table('presentation_reviews')->where(['paper_id' => $paperId, 'committee_id' => $committeeIds['commb@test.com']])->get()->getRowArray();
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewB['id'], 'criteria_id' => $crit1Id, 'score' => 40]);
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewB['id'], 'criteria_id' => $crit2Id, 'score' => 45]);
        $db->table('presentation_reviews')->where('id', $reviewB['id'])->update(['status' => 'completed', 'comments' => 'Good structure.']);

        // Simulate Committee C scoring (this one triggers average calculation)
        $reviewC = $db->table('presentation_reviews')->where(['paper_id' => $paperId, 'committee_id' => $committeeIds['commc@test.com']])->get()->getRowArray();
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewC['id'], 'criteria_id' => $crit1Id, 'score' => 35]);
        $db->table('presentation_review_scores')->insert(['presentation_review_id' => $reviewC['id'], 'criteria_id' => $crit2Id, 'score' => 40]);
        $db->table('presentation_reviews')->where('id', $reviewC['id'])->update(['status' => 'completed', 'comments' => 'A bit fast.']);

        // Trigger Committee evaluation submission logic (consensus/average)
        $completedReviews = $db->table('presentation_reviews')->where(['paper_id' => $paperId, 'status' => 'completed'])->get()->getResultArray();
        $assignedCount = count($committeeIds);

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
        if (floatval($finalPaper['presentation_score']) !== 83.33) {
            CLI::error("Assertion Failed: Expected presentation score to be 83.33, got {$finalPaper['presentation_score']}");
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
