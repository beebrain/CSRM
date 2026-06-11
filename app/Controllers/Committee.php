<?php

namespace App\Controllers;

use App\Models\PaperModel;
use App\Models\CriteriaModel;

class Committee extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        if (!session()->get('logged_in') || session()->get('role') !== 'committee') {
            throw new \CodeIgniter\Router\Exceptions\RedirectException('auth/login');
        }
    }

    public function dashboard()
    {
        $db = \Config\Database::connect();
        $committeeId = session()->get('user_id');

        // Fetch rooms where this committee is assigned
        $rooms = $db->table('room_committees')
                    ->select('rooms.*, conferences.year as conference_year, conferences.host_name')
                    ->join('rooms', 'rooms.id = room_committees.room_id')
                    ->join('conferences', 'conferences.id = rooms.conference_id')
                    ->where('room_committees.committee_id', $committeeId)
                    ->get()
                    ->getResultArray();

        // Fetch papers scheduled in those rooms, along with this committee's review status
        $roomPapers = [];
        foreach ($rooms as $r) {
            $papersList = $db->table('room_papers')
                                       ->select('room_papers.presentation_time, papers.id as paper_id, papers.title, papers.file_path, disciplines.name as discipline_name, tracks.name as track_name, presentation_reviews.id as review_id, presentation_reviews.status as review_status, presentation_reviews.decision')
                                       ->join('papers', 'papers.id = room_papers.paper_id')
                                       ->join('disciplines', 'disciplines.id = papers.discipline_id')
                                       ->join('tracks', 'tracks.id = disciplines.track_id')
                                       // Left join to show if review is generated
                                       ->join('presentation_reviews', 'presentation_reviews.paper_id = papers.id AND presentation_reviews.committee_id = ' . $committeeId, 'left')
                                       ->where('room_papers.room_id', $r['id'])
                                       ->orderBy('room_papers.presentation_time', 'ASC')
                                       ->get()
                                       ->getResultArray();

            foreach ($papersList as &$pl) {
                $pl['my_score'] = null;
                if ($pl['review_status'] === 'completed' && $pl['review_id']) {
                    $scoreRow = $db->table('presentation_review_scores')
                                   ->selectSum('score')
                                   ->where('presentation_review_id', $pl['review_id'])
                                   ->get()
                                   ->getRowArray();
                    $pl['my_score'] = isset($scoreRow['score']) ? intval($scoreRow['score']) : 0;
                }
            }
            $roomPapers[$r['id']] = $papersList;
        }

        return view('committee/dashboard', [
            'rooms'      => $rooms,
            'roomPapers' => $roomPapers
        ]);
    }

    public function evaluate($reviewId)
    {
        $db = \Config\Database::connect();
        
        // Fetch presentation review details
        $review = $db->table('presentation_reviews')
                     ->select('presentation_reviews.*, papers.title, papers.abstract, papers.file_path, papers.conference_id')
                     ->join('papers', 'papers.id = presentation_reviews.paper_id')
                     ->where(['presentation_reviews.id' => $reviewId, 'committee_id' => session()->get('user_id')])
                     ->get()
                     ->getRowArray();

        if (!$review) {
            return redirect()->to(base_url('committee/dashboard'))->with('error', 'ไม่พบรายการประเมินที่ระบุ');
        }

        // Fetch dynamic criteria for Round 2
        $critModel = new CriteriaModel();
        $criteria = $critModel->where(['conference_id' => $review['conference_id'], 'round' => 2])->findAll();

        if (empty($criteria)) {
            return redirect()->to(base_url('committee/dashboard'))->with('error', 'แอดมินยังไม่ได้ระบุเกณฑ์ประเมินรอบที่ 2 (การนำเสนอ) กรุณาแจ้งผู้ดูแลระบบ');
        }

        // If completed, fetch previous scores
        $scores = [];
        if ($review['status'] === 'completed') {
            $scoresList = $db->table('presentation_review_scores')
                             ->where('presentation_review_id', $reviewId)
                             ->get()
                             ->getResultArray();
            foreach ($scoresList as $s) {
                $scores[$s['criteria_id']] = $s['score'];
            }
        }

        return view('committee/evaluate', [
            'review'   => $review,
            'criteria' => $criteria,
            'scores'   => $scores
        ]);
    }

    public function submitEvaluation($reviewId)
    {
        if ($this->request->is('post')) {
            $db = \Config\Database::connect();
            
            // Check review access
            $review = $db->table('presentation_reviews')->where(['id' => $reviewId, 'committee_id' => session()->get('user_id')])->get()->getRowArray();
            if (!$review) {
                return redirect()->to(base_url('committee/dashboard'))->with('error', 'ไม่มีสิทธิ์เข้าถึงการประเมินนี้');
            }

            $criteriaScores = $this->request->getPost('scores'); // array [criteria_id => score]
            $comments = $this->request->getPost('comments');

            $db->transStart();

            // Save individual scores
            $db->table('presentation_review_scores')->delete(['presentation_review_id' => $reviewId]);
            foreach ($criteriaScores as $criteriaId => $score) {
                $db->table('presentation_review_scores')->insert([
                    'presentation_review_id' => $reviewId,
                    'criteria_id'            => $criteriaId,
                    'score'                  => intval($score)
                ]);
            }

            // Update main review record
            $db->table('presentation_reviews')->where('id', $reviewId)->update([
                'status'   => 'completed',
                'decision' => null,
                'comments' => $comments
            ]);

            // Final consensus checking for Round 2:
            // Check if all committee members assigned to this room have completed their reviews
            // Let's get the room this paper belongs to
            $roomPaper = $db->table('room_papers')->where('paper_id', $review['paper_id'])->get()->getRowArray();
            if ($roomPaper) {
                $assignedCommittees = $db->table('room_committees')->where('room_id', $roomPaper['room_id'])->get()->getResultArray();
                $committeeCount = count($assignedCommittees);

                // Check completed reviews for this paper
                $completedReviews = $db->table('presentation_reviews')
                                       ->where(['paper_id' => $review['paper_id'], 'status' => 'completed'])
                                       ->get()
                                       ->getResultArray();

                // If all assigned committees have submitted (or at least 3)
                if (count($completedReviews) >= $committeeCount && $committeeCount > 0) {
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

                    // Update paper status to passed_round2 and save average score
                    $db->table('papers')->where('id', $review['paper_id'])->update([
                        'status'             => 'passed_round2',
                        'presentation_score' => $averageScore
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to(base_url('committee/dashboard'))->with('error', 'เกิดข้อผิดพลาดในการบันทึกผลการประเมิน');
            }

            return redirect()->to(base_url('committee/dashboard'))->with('success', 'บันทึกผลการประเมินนำเสนอเรียบร้อยแล้ว');
        }
    }
}
