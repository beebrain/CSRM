<?php

namespace App\Controllers;

use App\Models\PaperModel;
use App\Models\ReviewModel;
use App\Models\CriteriaModel;

class Reviewer extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        if (!session()->get('logged_in') || session()->get('role') !== 'reviewer') {
            throw new \CodeIgniter\Router\Exceptions\RedirectException('auth/login');
        }
    }

    public function dashboard()
    {
        $db = \Config\Database::connect();
        
        // Get reviewer's assigned papers
        $reviews = $db->table('paper_reviews')
                      ->select('paper_reviews.*, papers.title, papers.abstract, papers.file_path, papers.conference_id, disciplines.name as discipline_name, tracks.name as track_name, conferences.year as conference_year')
                      ->join('papers', 'papers.id = paper_reviews.paper_id')
                      ->join('disciplines', 'disciplines.id = papers.discipline_id')
                      ->join('tracks', 'tracks.id = disciplines.track_id')
                      ->join('conferences', 'conferences.id = papers.conference_id')
                      ->where('reviewer_id', session()->get('user_id'))
                      ->get()
                      ->getResultArray();

        return view('reviewer/dashboard', [
            'reviews' => $reviews
        ]);
    }

    public function evaluate($reviewId)
    {
        $db = \Config\Database::connect();
        
        // Fetch review details
        $review = $db->table('paper_reviews')
                     ->select('paper_reviews.*, papers.title, papers.abstract, papers.file_path, papers.conference_id')
                     ->join('papers', 'papers.id = paper_reviews.paper_id')
                     ->where(['paper_reviews.id' => $reviewId, 'reviewer_id' => session()->get('user_id')])
                     ->get()
                     ->getRowArray();

        if (!$review) {
            return redirect()->to(base_url('reviewer/dashboard'))->with('error', 'ไม่พบรายการประเมินที่ระบุ');
        }

        // Fetch dynamic criteria for Round 1
        $critModel = new CriteriaModel();
        $criteria = $critModel->where(['conference_id' => $review['conference_id'], 'round' => 1])->findAll();

        if (empty($criteria)) {
            return redirect()->to(base_url('reviewer/dashboard'))->with('error', 'แอดมินยังไม่ได้ระบุเกณฑ์ประเมินรอบที่ 1 กรุณาแจ้งผู้ดูแลระบบ');
        }

        // If completed, fetch previous scores
        $scores = [];
        if ($review['status'] === 'completed') {
            $scoresList = $db->table('paper_review_scores')
                             ->where('review_id', $reviewId)
                             ->get()
                             ->getResultArray();
            foreach ($scoresList as $s) {
                $scores[$s['criteria_id']] = $s['score'];
            }
        }

        // Fetch revisions if any
        $revisions = $db->table('paper_revisions')
                        ->where('paper_id', $review['paper_id'])
                        ->orderBy('created_at', 'ASC')
                        ->get()
                        ->getResultArray();

        return view('reviewer/evaluate', [
            'review'    => $review,
            'criteria'  => $criteria,
            'scores'    => $scores,
            'revisions' => $revisions
        ]);
    }

    public function submitEvaluation($reviewId)
    {
        if ($this->request->is('post')) {
            $db = \Config\Database::connect();
            
            // Check review access
            $review = $db->table('paper_reviews')->where(['id' => $reviewId, 'reviewer_id' => session()->get('user_id')])->get()->getRowArray();
            if (!$review) {
                return redirect()->to(base_url('reviewer/dashboard'))->with('error', 'ไม่มีสิทธิ์เข้าถึงการประเมินนี้');
            }

            $criteriaScores = $this->request->getPost('scores'); // array [criteria_id => score]
            $decision = $this->request->getPost('decision');
            $comments = $this->request->getPost('comments');

            $db->transStart();

            // Save individual scores
            $db->table('paper_review_scores')->delete(['review_id' => $reviewId]);
            foreach ($criteriaScores as $criteriaId => $score) {
                $db->table('paper_review_scores')->insert([
                    'review_id'   => $reviewId,
                    'criteria_id' => $criteriaId,
                    'score'       => intval($score)
                ]);
            }

            // Update main review record
            $db->table('paper_reviews')->where('id', $reviewId)->update([
                'status'   => 'completed',
                'decision' => $decision,
                'comments' => $comments
            ]);

            // Consensus checking:
            // Check if there are at least 3 completed reviews for this paper
            $completedReviews = $db->table('paper_reviews')
                                   ->where(['paper_id' => $review['paper_id'], 'status' => 'completed'])
                                   ->get()
                                   ->getResultArray();

            if (count($completedReviews) >= 3) {
                // Count votes
                $passCount = 0;
                $failCount = 0;
                $revCount  = 0;
                foreach ($completedReviews as $cr) {
                    if ($cr['decision'] === 'pass') {
                        $passCount++;
                    } elseif ($cr['decision'] === 'fail') {
                        $failCount++;
                    } elseif ($cr['decision'] === 'revision') {
                        $revCount++;
                    }
                }

                // If at least 2 out of 3 pass -> Paper passes Round 1
                if ($passCount >= 2) {
                    $db->table('papers')->where('id', $review['paper_id'])->update(['status' => 'passed_round1']);
                } elseif ($failCount >= 2) {
                    $db->table('papers')->where('id', $review['paper_id'])->update(['status' => 'failed_round1']);
                } else {
                    // At least one revision vote, or no consensus majority -> Trigger revision
                    $db->table('papers')->where('id', $review['paper_id'])->update(['status' => 'revision_required']);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to(base_url('reviewer/dashboard'))->with('error', 'เกิดข้อผิดพลาดในการบันทึกผลการประเมิน');
            }

            return redirect()->to(base_url('reviewer/dashboard'))->with('success', 'บันทึกผลการประเมินเรียบร้อยแล้ว');
        }
    }
}
