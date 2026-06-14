<?php

namespace App\Controllers;

use App\Models\ConferenceModel;
use App\Models\DisciplineModel;
use App\Models\CriteriaModel;
use App\Models\PaperModel;
use App\Models\UserModel;
use App\Models\ReviewModel;
use App\Models\RoomModel;
use App\Models\PaymentModel;

class Admin extends BaseController
{
    protected $currentConfId = null;
    protected $allowedConfs = [];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Access check
        $role = session()->get('role');
        if (!session()->get('logged_in') || ($role !== 'admin' && $role !== 'superadmin')) {
            throw new \CodeIgniter\Router\Exceptions\RedirectException('auth/login');
        }

        // Fetch conferences this admin has access to
        $db = \Config\Database::connect();
        if ($role === 'superadmin') {
            // Superadmin has access to ALL conferences
            $confModel = new ConferenceModel();
            $this->allowedConfs = $confModel->orderBy('year', 'DESC')->findAll();
        } else {
            // Normal admin has access to only assigned conferences
            $this->allowedConfs = $db->table('conference_admins')
                                     ->select('conferences.*')
                                     ->join('conferences', 'conferences.id = conference_admins.conference_id')
                                     ->where('conference_admins.user_id', session()->get('user_id'))
                                     ->get()
                                     ->getResultArray();
        }

        if (empty($this->allowedConfs)) {
            // No permission to any conference
            session()->destroy();
            throw new \CodeIgniter\Router\Exceptions\RedirectException('auth/login');
        }

        // Set current conference ID
        if (session()->get('admin_conference_id')) {
            $this->currentConfId = session()->get('admin_conference_id');
        } else {
            // Default to active one if allowed, else first allowed
            $default = null;
            foreach ($this->allowedConfs as $conf) {
                if ($conf['is_active'] == 1) {
                    $default = $conf['id'];
                    break;
                }
            }
            if (!$default && !empty($this->allowedConfs)) {
                $default = $this->allowedConfs[0]['id'];
            }
            session()->set('admin_conference_id', $default);
            $this->currentConfId = $default;
        }
    }

    public function selectConference($id)
    {
        // Check if admin is allowed to access this conference
        $allowed = false;
        foreach ($this->allowedConfs as $conf) {
            if ($conf['id'] == $id) {
                $allowed = true;
                break;
            }
        }

        if ($allowed) {
            session()->set('admin_conference_id', $id);
            $redirect = $this->request->getGet('redirect');
            if ($redirect && in_array($redirect, ['admin/dashboard', 'admin/disciplines', 'admin/criteria', 'admin/rooms', 'admin/payments'])) {
                return redirect()->to(base_url($redirect));
            }
        }

        return redirect()->to(base_url('admin/dashboard'));
    }

    public function dashboard()
    {
        $paperModel = new PaperModel();
        $userModel = new UserModel();

        // 1. Statistics
        $db = \Config\Database::connect();
        $stats = [
            'total_papers' => $paperModel->where('conference_id', $this->currentConfId)->countAllResults(),
            'under_review' => $paperModel->where(['conference_id' => $this->currentConfId, 'status' => 'under_review'])->countAllResults(),
            'passed_round1' => $paperModel->where(['conference_id' => $this->currentConfId, 'status' => 'passed_round1'])->countAllResults(),
            'passed_round2' => $paperModel->where(['conference_id' => $this->currentConfId, 'status' => 'passed_round2'])->countAllResults(),
            'paid'          => $paperModel->where(['conference_id' => $this->currentConfId, 'payment_status' => 'paid'])->countAllResults(),
            'unpaid'        => $paperModel->where(['conference_id' => $this->currentConfId, 'payment_status' => 'unpaid'])->countAllResults(),
            'pending_verification_payment' => $paperModel->where(['conference_id' => $this->currentConfId, 'payment_status' => 'pending_verification'])->countAllResults(),
        ];

        // Scoring Progress (Peer Review & Presentation)
        $totalPeerReviews = $db->table('paper_reviews')
                               ->join('papers', 'papers.id = paper_reviews.paper_id')
                               ->where('papers.conference_id', $this->currentConfId)
                               ->countAllResults();
        $completedPeerReviews = $db->table('paper_reviews')
                                  ->join('papers', 'papers.id = paper_reviews.paper_id')
                                  ->where(['papers.conference_id' => $this->currentConfId, 'paper_reviews.status' => 'completed'])
                                  ->countAllResults();

        $totalPresReviews = $db->table('presentation_reviews')
                              ->join('papers', 'papers.id = presentation_reviews.paper_id')
                              ->where('papers.conference_id', $this->currentConfId)
                              ->countAllResults();
        $completedPresReviews = $db->table('presentation_reviews')
                                 ->join('papers', 'papers.id = presentation_reviews.paper_id')
                                 ->where(['papers.conference_id' => $this->currentConfId, 'presentation_reviews.status' => 'completed'])
                                 ->countAllResults();

        $stats['peer_review_progress'] = $totalPeerReviews > 0 ? round(($completedPeerReviews / $totalPeerReviews) * 100, 1) : 0;
        $stats['peer_review_details'] = "{$completedPeerReviews} / {$totalPeerReviews} รายการ";
        
        $stats['presentation_progress'] = $totalPresReviews > 0 ? round(($completedPresReviews / $totalPresReviews) * 100, 1) : 0;
        $stats['presentation_details'] = "{$completedPresReviews} / {$totalPresReviews} รายการ";

        // 2. Papers List with authors
        $papers = $paperModel->getDetails(['papers.conference_id' => $this->currentConfId]);

        // Get reviewers list for assignments
        $reviewers = $userModel->where('role', 'reviewer')->findAll();

        // Fetch current assignments and revisions for each paper
        $db = \Config\Database::connect();
        $paperAssignments = [];
        $paperRevisions = [];
        foreach ($papers as $p) {
            $paperAssignments[$p['id']] = $db->table('paper_reviews')
                                             ->select('paper_reviews.*, users.first_name, users.last_name, users.email, users.affiliation')
                                             ->join('users', 'users.id = paper_reviews.reviewer_id')
                                             ->where('paper_id', $p['id'])
                                             ->get()
                                             ->getResultArray();

            $paperRevisions[$p['id']] = $db->table('paper_revisions')
                                           ->where('paper_id', $p['id'])
                                           ->orderBy('created_at', 'ASC')
                                           ->get()
                                           ->getResultArray();
        }

        // Fetch reviewers expertise keywords
        $reviewerExpertise = [];
        $expertiseList = $db->table('user_expertise')->get()->getResultArray();
        foreach ($expertiseList as $exp) {
            $reviewerExpertise[$exp['user_id']][] = strtolower(trim($exp['keyword']));
        }

        // Get selected conference details
        $confModel = new ConferenceModel();
        $selectedConference = $confModel->find($this->currentConfId);

        return view('admin/dashboard', [
            'allowedConfs'       => $this->allowedConfs,
            'currentConfId'      => $this->currentConfId,
            'selectedConference' => $selectedConference,
            'stats'              => $stats,
            'papers'             => $papers,
            'reviewers'          => $reviewers,
            'reviewerExpertise'  => $reviewerExpertise,
            'paperAssignments'   => $paperAssignments,
            'paperRevisions'     => $paperRevisions
        ]);
    }

    // ==========================================
    // 1. DISCIPLINE MANAGEMENT (CRUD)
    // ==========================================
    public function disciplines()
    {
        $db = \Config\Database::connect();
        $disciplines = $db->table('disciplines')
                          ->select('disciplines.*, tracks.name as track_name')
                          ->join('tracks', 'tracks.id = disciplines.track_id')
                          ->orderBy('track_id', 'ASC')
                          ->get()
                          ->getResultArray();

        $tracks = $db->table('tracks')->get()->getResultArray();

        return view('admin/disciplines', [
            'allowedConfs'  => $this->allowedConfs,
            'currentConfId' => $this->currentConfId,
            'disciplines'   => $disciplines,
            'tracks'        => $tracks
        ]);
    }

    public function addDiscipline()
    {
        if ($this->request->is('post')) {
            $db = \Config\Database::connect();
            $db->table('disciplines')->insert([
                'track_id' => $this->request->getPost('track_id'),
                'name'     => $this->request->getPost('name')
            ]);
            return redirect()->to(base_url('admin/disciplines'))->with('success', 'เพิ่มศาสตร์ย่อยเรียบร้อยแล้ว');
        }
    }

    public function deleteDiscipline($id)
    {
        $db = \Config\Database::connect();
        try {
            $db->table('disciplines')->delete(['id' => $id]);
            return redirect()->to(base_url('admin/disciplines'))->with('success', 'ลบศาสตร์ย่อยเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->to(base_url('admin/disciplines'))->with('error', 'ไม่สามารถลบศาสตร์ย่อยได้เนื่องจากมีบทความวิชาการผูกอยู่');
        }
    }

    // ==========================================
    // 2. CRITERIA MANAGEMENT (CRUD)
    // ==========================================
    public function criteria()
    {
        $db = \Config\Database::connect();
        $critModel = new CriteriaModel();
        $criteria = $critModel->where('conference_id', $this->currentConfId)
                              ->orderBy('round', 'ASC')
                              ->findAll();

        // Fetch all templates
        $templates = $db->table('evaluation_templates')
                        ->orderBy('created_at', 'DESC')
                        ->get()
                        ->getResultArray();

        // Fetch criteria for each template
        foreach ($templates as &$tmpl) {
            $tmpl['criteria'] = $db->table('evaluation_template_criteria')
                                   ->where('template_id', $tmpl['id'])
                                   ->get()
                                   ->getResultArray();
        }

        return view('admin/criteria', [
            'allowedConfs'  => $this->allowedConfs,
            'currentConfId' => $this->currentConfId,
            'criteria'      => $criteria,
            'templates'     => $templates
        ]);
    }

    public function addCriteria()
    {
        if ($this->request->is('post')) {
            $critModel = new CriteriaModel();
            $round = $this->request->getPost('round');
            $maxScore = intval($this->request->getPost('max_score'));

            // Force max score of 10 points for Round 2 (Presentation)
            if ($round == 2) {
                $maxScore = 10;
            }

            $critModel->insert([
                'conference_id' => $this->currentConfId,
                'round'         => $round,
                'criteria_name' => $this->request->getPost('criteria_name'),
                'max_score'     => $maxScore,
                'description'   => $this->request->getPost('description')
            ]);
            return redirect()->to(base_url('admin/criteria'))->with('success', 'เพิ่มเกณฑ์ประเมินเรียบร้อยแล้ว');
        }
    }

    public function deleteCriteria($id)
    {
        $critModel = new CriteriaModel();
        try {
            $critModel->delete($id);
            return redirect()->to(base_url('admin/criteria'))->with('success', 'ลบเกณฑ์ประเมินเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->to(base_url('admin/criteria'))->with('error', 'ไม่สามารถลบเกณฑ์ประเมินได้เนื่องจากเริ่มมีการประเมินผลงานและลงคะแนนไปแล้ว');
        }
    }

    public function saveAsTemplate()
    {
        if ($this->request->is('post')) {
            $round = $this->request->getPost('round');
            $templateName = $this->request->getPost('template_name');

            if (empty(trim($templateName))) {
                return redirect()->to(base_url('admin/criteria'))->with('error', 'กรุณาระบุชื่อแบบฟอร์มคะแนน');
            }

            $critModel = new CriteriaModel();
            $currentCriteria = $critModel->where([
                'conference_id' => $this->currentConfId,
                'round'         => $round
            ])->findAll();

            if (empty($currentCriteria)) {
                return redirect()->to(base_url('admin/criteria'))->with('error', 'ไม่สามารถบันทึกได้ เนื่องจากไม่มีเกณฑ์ประเมินสำหรับขั้นตอนนี้');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // Create template
            $db->table('evaluation_templates')->insert([
                'name'  => $templateName,
                'round' => $round
            ]);
            $templateId = $db->insertID();

            // Copy criteria
            foreach ($currentCriteria as $crit) {
                $db->table('evaluation_template_criteria')->insert([
                    'template_id'   => $templateId,
                    'criteria_name' => $crit['criteria_name'],
                    'max_score'     => $crit['max_score'],
                    'description'   => $crit['description']
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to(base_url('admin/criteria'))->with('error', 'เกิดข้อผิดพลาดในการบันทึกแบบฟอร์มสำเร็จรูป');
            }

            return redirect()->to(base_url('admin/criteria'))->with('success', 'บันทึกแบบฟอร์มคะแนนสำเร็จรูปเรียบร้อยแล้ว');
        }
    }

    public function importTemplate()
    {
        if ($this->request->is('post')) {
            $templateId = $this->request->getPost('template_id');
            $db = \Config\Database::connect();

            $template = $db->table('evaluation_templates')->where('id', $templateId)->get()->getRowArray();
            if (!$template) {
                return redirect()->to(base_url('admin/criteria'))->with('error', 'ไม่พบแบบฟอร์มคะแนนที่ระบุ');
            }

            $templateCriteria = $db->table('evaluation_template_criteria')->where('template_id', $templateId)->get()->getResultArray();
            if (empty($templateCriteria)) {
                return redirect()->to(base_url('admin/criteria'))->with('error', 'แบบฟอร์มนี้ไม่มีรายการเกณฑ์ประเมินย่อย');
            }

            $db->transStart();

            try {
                // Clear existing criteria for current conference and round
                $db->table('evaluation_criteria')->delete([
                    'conference_id' => $this->currentConfId,
                    'round'         => $template['round']
                ]);

                // Copy template criteria
                foreach ($templateCriteria as $tc) {
                    $db->table('evaluation_criteria')->insert([
                        'conference_id' => $this->currentConfId,
                        'round'         => $template['round'],
                        'criteria_name' => $tc['criteria_name'],
                        'max_score'     => $tc['max_score'],
                        'description'   => $tc['description']
                    ]);
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    return redirect()->to(base_url('admin/criteria'))->with('error', 'เกิดข้อผิดพลาดในการนำเข้าเกณฑ์ประเมิน');
                }

                return redirect()->to(base_url('admin/criteria'))->with('success', 'นำเข้าเกณฑ์ประเมินเรียบร้อยแล้ว');

            } catch (\Exception $e) {
                $db->transRollback();
                return redirect()->to(base_url('admin/criteria'))->with('error', 'ไม่สามารถนำเข้าเกณฑ์ได้ เนื่องจากมีบทความที่เริ่มได้รับการลงคะแนนในรอบนี้ไปแล้ว');
            }
        }
    }

    public function deleteTemplate($id)
    {
        $db = \Config\Database::connect();
        $db->table('evaluation_templates')->delete(['id' => $id]);
        return redirect()->to(base_url('admin/criteria'))->with('success', 'ลบแบบฟอร์มสำเร็จรูปเรียบร้อยแล้ว');
    }

    // ==========================================
    // 3. REVIEWER ASSIGNMENT
    // ==========================================
    public function assignReviewers()
    {
        if ($this->request->is('post')) {
            $paperId = $this->request->getPost('paper_id');
            $reviewerIds = $this->request->getPost('reviewer_ids'); // Array of 3 reviewers

            if (empty($reviewerIds) || count($reviewerIds) !== 3) {
                return redirect()->to(base_url('admin/dashboard'))->with('error', 'ต้องมอบหมายผู้ทรงคุณวุฒิเป็นจำนวน 3 ท่าน');
            }

            // Check duplicate reviewers
            if (count($reviewerIds) !== count(array_unique($reviewerIds))) {
                return redirect()->to(base_url('admin/dashboard'))->with('error', 'ห้ามเลือกผู้ทรงคุณวุฒิซ้ำกันในบทความเดียวกัน');
            }

            $db = \Config\Database::connect();

            // Server-side Conflict of Interest (COI) check
            $paper = $db->table('papers')
                        ->select('papers.*, users.affiliation as author_affiliation')
                        ->join('users', 'users.id = papers.author_id')
                        ->where('papers.id', $paperId)
                        ->get()
                        ->getRowArray();

            if (!$paper) {
                return redirect()->to(base_url('admin/dashboard'))->with('error', 'ไม่พบข้อมูลบทความ');
            }

            $authorAff = !empty($paper['author_affiliation']) ? preg_replace('/\s+/', '', mb_strtolower($paper['author_affiliation'])) : '';
            
            // Gather affiliations of reviewers and check conflicts
            $reviewerAffiliations = [];
            foreach ($reviewerIds as $rId) {
                $reviewer = $db->table('users')->where('id', $rId)->get()->getRowArray();
                if ($reviewer) {
                    $revAff = !empty($reviewer['affiliation']) ? preg_replace('/\s+/', '', mb_strtolower($reviewer['affiliation'])) : '';
                    
                    // COI check with author
                    if (!empty($authorAff) && !empty($revAff) && $revAff === $authorAff) {
                        return redirect()->to(base_url('admin/dashboard'))->with('error', 'ไม่สามารถเลือกผู้ทรงคุณวุฒิจากสถาบันเดียวกันกับผู้แต่งบทความได้: ' . esc($reviewer['first_name']) . ' ' . esc($reviewer['last_name']) . ' (สถาบัน: ' . esc($reviewer['affiliation']) . ')');
                    }
                    
                    // COI check among reviewers
                    if (!empty($revAff)) {
                        if (in_array($revAff, $reviewerAffiliations)) {
                            return redirect()->to(base_url('admin/dashboard'))->with('error', 'ไม่สามารถเลือกผู้ทรงคุณวุฒิจากสถาบันเดียวกันซ้ำกันได้: สถาบัน ' . esc($reviewer['affiliation']));
                        }
                        $reviewerAffiliations[] = $revAff;
                    }
                }
            }

            $db->transStart();

            // Clear previous assignments
            $db->table('paper_reviews')->delete(['paper_id' => $paperId]);

            // Add new assignments
            foreach ($reviewerIds as $rId) {
                $db->table('paper_reviews')->insert([
                    'paper_id'    => $paperId,
                    'reviewer_id' => $rId,
                    'status'      => 'pending'
                ]);
            }

            // Update paper status to under_review
            $db->table('papers')->where('id', $paperId)->update(['status' => 'under_review']);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to(base_url('admin/dashboard'))->with('error', 'เกิดข้อผิดพลาดในการมอบหมายผู้ทรงคุณวุฒิ');
            }

            return redirect()->to(base_url('admin/dashboard'))->with('success', 'มอบหมายผู้ทรงคุณวุฒิประเมินเรียบร้อยแล้ว');
        }
    }

    // ==========================================
    // 4. ROOMS & PRESENTATION MANAGEMENT
    // ==========================================
    public function rooms()
    {
        $roomModel = new RoomModel();
        $userModel = new UserModel();
        $paperModel = new PaperModel();
        $db = \Config\Database::connect();

        $rooms = $roomModel->where('conference_id', $this->currentConfId)->findAll();
        
        $committees = $userModel->where('role', 'committee')->findAll();
        
        // Papers that passed Round 1
        $papers = $paperModel->getDetails([
            'papers.conference_id' => $this->currentConfId, 
            'papers.status' => 'passed_round1'
        ]);

        // Fetch details for each room (assigned committees, assigned papers)
        $roomDetails = [];
        foreach ($rooms as $r) {
            $roomDetails[$r['id']] = [
                'committees' => $db->table('room_committees')
                                   ->select('room_committees.id as assignment_id, users.first_name, users.last_name, users.email')
                                   ->join('users', 'users.id = room_committees.committee_id')
                                   ->where('room_id', $r['id'])
                                   ->get()
                                   ->getResultArray(),
                'papers' => $db->table('room_papers')
                               ->select('room_papers.id as assignment_id, papers.title, papers.id as paper_id, users.first_name as author_first, users.last_name as author_last, room_papers.presentation_time')
                               ->join('papers', 'papers.id = room_papers.paper_id')
                               ->join('users', 'users.id = papers.author_id')
                               ->where('room_id', $r['id'])
                               ->get()
                               ->getResultArray()
            ];
        }

        return view('admin/rooms', [
            'allowedConfs'  => $this->allowedConfs,
            'currentConfId' => $this->currentConfId,
            'rooms'         => $rooms,
            'committees'    => $committees,
            'papers'        => $papers,
            'roomDetails'   => $roomDetails
        ]);
    }

    public function createRoom()
    {
        if ($this->request->is('post')) {
            $roomModel = new RoomModel();
            $roomModel->insert([
                'conference_id' => $this->currentConfId,
                'name'          => $this->request->getPost('name'),
                'location'      => $this->request->getPost('location'),
                'date_time'     => $this->request->getPost('date_time')
            ]);
            return redirect()->to(base_url('admin/rooms'))->with('success', 'สร้างห้องนำเสนอเรียบร้อยแล้ว');
        }
    }

    public function deleteRoom($id)
    {
        $roomModel = new RoomModel();
        $roomModel->delete($id);
        return redirect()->to(base_url('admin/rooms'))->with('success', 'ลบห้องนำเสนอเรียบร้อยแล้ว');
    }

    public function autoAssignRooms()
    {
        if ($this->request->is('post')) {
            $db = \Config\Database::connect();
            $roomCount = intval($this->request->getPost('room_count'));
            
            if ($roomCount < 1) {
                return redirect()->to(base_url('admin/rooms'))->with('error', 'กรุณาระบุจำนวนห้องอย่างน้อย 1 ห้อง');
            }

            // Find all papers for the current conference with status 'passed_round1' which are NOT assigned to a room
            $papers = $db->table('papers')
                         ->select('papers.id as paper_id, papers.discipline_id, disciplines.name as discipline_name')
                         ->join('disciplines', 'disciplines.id = papers.discipline_id', 'left')
                         ->where('papers.conference_id', $this->currentConfId)
                         ->where('papers.status', 'passed_round1')
                         ->whereNotIn('papers.id', function($builder) {
                             return $builder->select('paper_id')->from('room_papers');
                         })
                         ->get()
                         ->getResultArray();

            if (empty($papers)) {
                return redirect()->to(base_url('admin/rooms'))->with('error', 'ไม่มีบทความวิชาการที่ผ่านการประเมินรอบแรกและยังไม่ได้รับการจัดห้องนำเสนอ');
            }

            // Group papers by discipline_id
            $papersByDiscipline = [];
            $disciplineNames = [];
            foreach ($papers as $p) {
                $discId = $p['discipline_id'] ?? 0;
                $papersByDiscipline[$discId][] = $p;
                $disciplineNames[$discId] = $p['discipline_name'] ?? 'ทั่วไป';
            }

            // Sort disciplines by count of papers descending
            uasort($papersByDiscipline, function($a, $b) {
                return count($b) - count($a);
            });

            // Initialize rooms buckets
            $allocatedRooms = [];
            for ($i = 0; $i < $roomCount; $i++) {
                $allocatedRooms[$i] = [
                    'papers'      => [],
                    'disciplines' => []
                ];
            }

            // Distribute disciplines to rooms using a simple greedy load balancing heuristic
            foreach ($papersByDiscipline as $discId => $discPapers) {
                // Find room with the smallest current paper count
                $minRoomIndex = 0;
                $minPaperCount = count($allocatedRooms[0]['papers']);
                for ($i = 1; $i < $roomCount; $i++) {
                    $cnt = count($allocatedRooms[$i]['papers']);
                    if ($cnt < $minPaperCount) {
                        $minPaperCount = $cnt;
                        $minRoomIndex = $i;
                    }
                }
                // Assign all papers of this discipline to the room
                $allocatedRooms[$minRoomIndex]['papers'] = array_merge($allocatedRooms[$minRoomIndex]['papers'], $discPapers);
                $allocatedRooms[$minRoomIndex]['disciplines'][] = $disciplineNames[$discId];
            }

            // Insert allocated rooms and room papers into database
            $db->transStart();
            
            $assignedCount = 0;
            $createdRooms = 0;
            foreach ($allocatedRooms as $index => $roomData) {
                if (empty($roomData['papers'])) {
                    continue; // Skip creating empty rooms
                }

                $roomNum = $index + 1;
                $discListText = implode(', ', array_unique($roomData['disciplines']));
                
                // Formulate a nice name
                $roomName = "ห้องพรีเซนต์อัตโนมัติ {$roomNum} (" . (mb_strlen($discListText) > 40 ? mb_substr($discListText, 0, 40) . '...' : $discListText) . ")";
                
                // Create Room
                $db->table('rooms')->insert([
                    'conference_id' => $this->currentConfId,
                    'name'          => $roomName,
                    'location'      => "อาคารสัมมนา ห้องประเมิน {$roomNum}",
                    'date_time'     => date('Y-m-d 09:00:00') // Default to 9:00 AM of today or conference default
                ]);
                $roomId = $db->insertID();
                $createdRooms++;

                // Assign papers sequentially with 30-minute intervals
                $startTime = strtotime('09:00:00');
                foreach ($roomData['papers'] as $paperIndex => $paper) {
                    $presTime = date('H:i:s', $startTime + ($paperIndex * 30 * 60));
                    
                    $db->table('room_papers')->insert([
                        'room_id'           => $roomId,
                        'paper_id'          => $paper['paper_id'],
                        'presentation_time' => $presTime
                    ]);
                    $assignedCount++;
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to(base_url('admin/rooms'))->with('error', 'เกิดข้อผิดพลาดในการจัดห้องนำเสนออัตโนมัติ');
            }

            return redirect()->to(base_url('admin/rooms'))->with('success', "จัดห้องนำเสนออัตโนมัติสำเร็จ! สร้างห้องใหม่ทั้งหมด {$createdRooms} ห้อง และจัดบทความเข้าไป {$assignedCount} เรื่อง");
        }
    }

    public function assignCommittee()
    {
        if ($this->request->is('post')) {
            $db = \Config\Database::connect();
            $roomId = $this->request->getPost('room_id');
            $commId = $this->request->getPost('committee_id');

            // Count existing committees in this room
            $currentCount = $db->table('room_committees')->where('room_id', $roomId)->countAllResults();
            if ($currentCount >= 3) {
                return redirect()->to(base_url('admin/rooms'))->with('error', 'ห้องนี้มีกรรมการประเมินนำเสนอครบ 3 ท่านแล้ว');
            }

            // Check if already assigned
            $existing = $db->table('room_committees')->where(['room_id' => $roomId, 'committee_id' => $commId])->get()->getRow();
            if ($existing) {
                return redirect()->to(base_url('admin/rooms'))->with('error', 'กรรมการท่านนี้ถูกมอบหมายในห้องนี้อยู่แล้ว');
            }

            $db->table('room_committees')->insert([
                'room_id'      => $roomId,
                'committee_id' => $commId
            ]);

            // Add presentation review template for papers in this room for this committee
            $roomPapers = $db->table('room_papers')->where('room_id', $roomId)->get()->getResultArray();
            foreach ($roomPapers as $rp) {
                $check = $db->table('presentation_reviews')
                            ->where(['paper_id' => $rp['paper_id'], 'committee_id' => $commId])
                            ->get()
                            ->getRow();
                if (!$check) {
                    $db->table('presentation_reviews')->insert([
                        'paper_id'     => $rp['paper_id'],
                        'committee_id' => $commId,
                        'status'       => 'pending'
                    ]);
                }
            }

            return redirect()->to(base_url('admin/rooms'))->with('success', 'มอบหมายกรรมการเข้าห้องพรีเซนต์เรียบร้อยแล้ว');
        }
    }

    public function removeCommittee($id)
    {
        $db = \Config\Database::connect();
        
        // Find assignment details to clean up presentation reviews templates
        $assign = $db->table('room_committees')->where('id', $id)->get()->getRowArray();
        if ($assign) {
            $db->transStart();
            
            // Delete room committee
            $db->table('room_committees')->delete(['id' => $id]);
            
            // Delete pending presentation reviews for this committee in this room
            $roomPapers = $db->table('room_papers')->where('room_id', $assign['room_id'])->get()->getResultArray();
            foreach ($roomPapers as $rp) {
                $db->table('presentation_reviews')
                   ->where(['paper_id' => $rp['paper_id'], 'committee_id' => $assign['committee_id'], 'status' => 'pending'])
                   ->delete();
            }
            
            $db->transComplete();
        }
        
        return redirect()->to(base_url('admin/rooms'))->with('success', 'ถอดถอนกรรมการสำเร็จ');
    }

    public function assignPaper()
    {
        if ($this->request->is('post')) {
            $db = \Config\Database::connect();
            $roomId = $this->request->getPost('room_id');
            $paperId = $this->request->getPost('paper_id');
            $presTime = $this->request->getPost('presentation_time');

            // Check if paper already assigned somewhere else
            $existing = $db->table('room_papers')->where('paper_id', $paperId)->get()->getRow();
            if ($existing) {
                return redirect()->to(base_url('admin/rooms'))->with('error', 'บทความวิชาการนี้ได้รับการจัดห้องนำเสนอเรียบร้อยแล้ว');
            }

            $db->transStart();
            
            // Insert
            $db->table('room_papers')->insert([
                'room_id'           => $roomId,
                'paper_id'          => $paperId,
                'presentation_time' => $presTime
            ]);

            // Add presentation review template for all committees in this room
            $committees = $db->table('room_committees')->where('room_id', $roomId)->get()->getResultArray();
            foreach ($committees as $c) {
                $db->table('presentation_reviews')->insert([
                    'paper_id'     => $paperId,
                    'committee_id' => $c['committee_id'],
                    'status'       => 'pending'
                ]);
            }

            $db->transComplete();

            return redirect()->to(base_url('admin/rooms'))->with('success', 'จัดสรรบทความเข้าห้องนำเสนอเรียบร้อยแล้ว');
        }
    }

    public function removePaper($id)
    {
        $db = \Config\Database::connect();
        $assign = $db->table('room_papers')->where('id', $id)->get()->getRowArray();
        
        if ($assign) {
            $db->transStart();
            // Delete room paper
            $db->table('room_papers')->delete(['id' => $id]);
            // Delete pending reviews
            $db->table('presentation_reviews')->where(['paper_id' => $assign['paper_id'], 'status' => 'pending'])->delete();
            $db->transComplete();
        }

        return redirect()->to(base_url('admin/rooms'))->with('success', 'ถอนบทความออกจากห้องนำเสนอเรียบร้อยแล้ว');
    }

    // ==========================================
    // 5. PAYMENT VERIFICATION
    // ==========================================
    public function payments()
    {
        $payModel = new PaymentModel();
        $db = \Config\Database::connect();

        // Get all payments for this conference
        $payments = $payModel->getDetails(['payments.conference_id' => $this->currentConfId]);

        // Get papers covered by each payment
        $paymentPapers = [];
        foreach ($payments as $p) {
            $paymentPapers[$p['id']] = $db->table('payment_items')
                                         ->select('papers.id, papers.title, disciplines.name as discipline_name')
                                         ->join('papers', 'papers.id = payment_items.paper_id')
                                         ->join('disciplines', 'disciplines.id = papers.discipline_id')
                                         ->where('payment_id', $p['id'])
                                         ->get()
                                         ->getResultArray();
        }

        return view('admin/payments', [
            'allowedConfs'  => $this->allowedConfs,
            'currentConfId' => $this->currentConfId,
            'payments'      => $payments,
            'paymentPapers' => $paymentPapers
        ]);
    }

    public function approvePayment($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Update payment status
        $db->table('payments')->where('id', $id)->update(['status' => 'approved']);

        // Update all related papers status to paid
        $items = $db->table('payment_items')->where('payment_id', $id)->get()->getResultArray();
        foreach ($items as $item) {
            $db->table('papers')->where('id', $item['paper_id'])->update(['payment_status' => 'paid']);
        }

        $db->transComplete();

        return redirect()->to(base_url('admin/payments'))->with('success', 'อนุมัติการชำระเงินเรียบร้อยแล้ว');
    }

    public function rejectPayment($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Update payment status
        $db->table('payments')->where('id', $id)->update(['status' => 'rejected']);

        // Update all related papers status back to unpaid
        $items = $db->table('payment_items')->where('payment_id', $id)->get()->getResultArray();
        foreach ($items as $item) {
            $db->table('papers')->where('id', $item['paper_id'])->update(['payment_status' => 'unpaid']);
        }

        $db->transComplete();

        return redirect()->to(base_url('admin/payments'))->with('success', 'ปฏิเสธการชำระเงินเรียบร้อยแล้ว');
    }

    public function setRevisionDeadline()
    {
        if ($this->request->is('post')) {
            $paperId = $this->request->getPost('paper_id');
            $deadline = $this->request->getPost('revision_deadline');
            
            $db = \Config\Database::connect();
            $db->table('papers')->where('id', $paperId)->update([
                'revision_deadline' => $deadline
            ]);
            
            return redirect()->to(base_url('admin/dashboard'))->with('success', 'ปรับปรุงกำหนดส่งแก้ไขบทความเรียบร้อยแล้ว');
        }
    }

    public function approveRevision($paperId)
    {
        $db = \Config\Database::connect();
        
        $db->table('papers')->where('id', $paperId)->update([
            'status' => 'passed_round1'
        ]);
        
        return redirect()->to(base_url('admin/dashboard'))->with('success', 'อนุมัติการแก้ไขบทความเรียบร้อยแล้ว บทความนี้ผ่านรอบแรกและพร้อมนำเสนอผลงาน');
    }

    public function reports()
    {
        $paperModel = new PaperModel();
        $db = \Config\Database::connect();

        // 1. Fetch papers for current conference
        $papers = $paperModel->getDetails(['papers.conference_id' => $this->currentConfId]);

        // 2. Fetch detailed Round 1 reviews, Revisions, Room details, and Round 2 reviews for each paper
        $paperAssignments = [];
        $paperRevisions = [];
        $presentationSchedules = [];
        $presentationReviews = [];
        $criteriaRound1 = $db->table('evaluation_criteria')->where(['conference_id' => $this->currentConfId, 'round' => 1])->get()->getResultArray();
        $criteriaRound2 = $db->table('evaluation_criteria')->where(['conference_id' => $this->currentConfId, 'round' => 2])->get()->getResultArray();

        foreach ($papers as $p) {
            $paperId = $p['id'];

            // Round 1 assignments & scores
            $assigns = $db->table('paper_reviews')
                          ->select('paper_reviews.*, users.first_name, users.last_name, users.email, users.affiliation')
                          ->join('users', 'users.id = paper_reviews.reviewer_id')
                          ->where('paper_id', $paperId)
                          ->get()
                          ->getResultArray();

            foreach ($assigns as &$a) {
                $a['scores'] = $db->table('paper_review_scores')
                                  ->where('review_id', $a['id'])
                                  ->get()
                                  ->getResultArray();
            }
            $paperAssignments[$paperId] = $assigns;

            // Revisions
            $paperRevisions[$paperId] = $db->table('paper_revisions')
                                           ->where('paper_id', $paperId)
                                           ->orderBy('created_at', 'ASC')
                                           ->get()
                                           ->getResultArray();

            // Presentation Room Schedule
            $presentationSchedules[$paperId] = $db->table('room_papers')
                                                  ->select('rooms.name as room_name, rooms.location, rooms.date_time, room_papers.presentation_time')
                                                  ->join('rooms', 'rooms.id = room_papers.room_id')
                                                  ->where('paper_id', $paperId)
                                                  ->get()
                                                  ->getRowArray();

            // Round 2 presentation reviews & scores
            $presReviews = $db->table('presentation_reviews')
                              ->select('presentation_reviews.*, users.first_name, users.last_name, users.email')
                              ->join('users', 'users.id = presentation_reviews.committee_id')
                              ->where('paper_id', $paperId)
                              ->get()
                              ->getResultArray();

            foreach ($presReviews as &$pr) {
                $pr['scores'] = $db->table('presentation_review_scores')
                                   ->where('presentation_review_id', $pr['id'])
                                   ->get()
                                   ->getResultArray();
            }
            $presentationReviews[$paperId] = $presReviews;
        }

        // Get conference details
        $confModel = new ConferenceModel();
        $selectedConference = $confModel->find($this->currentConfId);

        return view('admin/reports', [
            'allowedConfs'       => $this->allowedConfs,
            'currentConfId'      => $this->currentConfId,
            'selectedConference' => $selectedConference,
            'papers'             => $papers,
            'paperAssignments'   => $paperAssignments,
            'paperRevisions'     => $paperRevisions,
            'presentationSchedules' => $presentationSchedules,
            'presentationReviews' => $presentationReviews,
            'criteriaRound1'     => $criteriaRound1,
            'criteriaRound2'     => $criteriaRound2
        ]);
    }

    public function pendingReviews()
    {
        $db = \Config\Database::connect();

        // 1. Fetch pending peer reviews (Round 1)
        $pendingPeerReviews = $db->table('paper_reviews')
                                 ->select('paper_reviews.id as review_id, paper_reviews.created_at as assigned_at, papers.id as paper_id, papers.title as paper_title, papers.status as paper_status, u_rev.first_name as reviewer_first, u_rev.last_name as reviewer_last, u_rev.email as reviewer_email, u_rev.affiliation as reviewer_affiliation, u_auth.first_name as author_first, u_auth.last_name as author_last')
                                 ->join('papers', 'papers.id = paper_reviews.paper_id')
                                 ->join('users u_rev', 'u_rev.id = paper_reviews.reviewer_id')
                                 ->join('users u_auth', 'u_auth.id = papers.author_id')
                                 ->where('papers.conference_id', $this->currentConfId)
                                 ->where('paper_reviews.status', 'pending')
                                 ->orderBy('paper_reviews.created_at', 'ASC')
                                 ->get()
                                 ->getResultArray();

        // 2. Fetch pending presentation reviews (Round 2)
        $pendingPresReviews = $db->table('presentation_reviews')
                                 ->select('presentation_reviews.id as review_id, presentation_reviews.created_at as assigned_at, papers.id as paper_id, papers.title as paper_title, papers.status as paper_status, u_comm.first_name as committee_first, u_comm.last_name as committee_last, u_comm.email as committee_email, u_comm.affiliation as committee_affiliation, u_auth.first_name as author_first, u_auth.last_name as author_last, rooms.name as room_name')
                                 ->join('papers', 'papers.id = presentation_reviews.paper_id')
                                 ->join('users u_comm', 'u_comm.id = presentation_reviews.committee_id')
                                 ->join('users u_auth', 'u_auth.id = papers.author_id')
                                 ->leftJoin('room_papers', 'room_papers.paper_id = papers.id')
                                 ->leftJoin('rooms', 'rooms.id = room_papers.room_id')
                                 ->where('papers.conference_id', $this->currentConfId)
                                 ->where('presentation_reviews.status', 'pending')
                                 ->orderBy('presentation_reviews.created_at', 'ASC')
                                 ->get()
                                 ->getResultArray();

        // Get conference details
        $confModel = new ConferenceModel();
        $selectedConference = $confModel->find($this->currentConfId);

        return view('admin/pending_reviews', [
            'allowedConfs'       => $this->allowedConfs,
            'currentConfId'      => $this->currentConfId,
            'selectedConference' => $selectedConference,
            'pendingPeerReviews' => $pendingPeerReviews,
            'pendingPresReviews' => $pendingPresReviews
        ]);
    }
}

