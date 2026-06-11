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
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            throw new \CodeIgniter\Router\Exceptions\RedirectException('auth/login');
        }

        // Fetch conferences this admin has access to
        $db = \Config\Database::connect();
        $this->allowedConfs = $db->table('conference_admins')
                                 ->select('conferences.*')
                                 ->join('conferences', 'conferences.id = conference_admins.conference_id')
                                 ->where('conference_admins.user_id', session()->get('user_id'))
                                 ->get()
                                 ->getResultArray();

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
        }

        return redirect()->to('/admin/dashboard');
    }

    public function dashboard()
    {
        $paperModel = new PaperModel();
        $userModel = new UserModel();

        // 1. Statistics
        $stats = [
            'total_papers' => $paperModel->where('conference_id', $this->currentConfId)->countAllResults(),
            'under_review' => $paperModel->where(['conference_id' => $this->currentConfId, 'status' => 'under_review'])->countAllResults(),
            'passed_round1' => $paperModel->where(['conference_id' => $this->currentConfId, 'status' => 'passed_round1'])->countAllResults(),
            'passed_round2' => $paperModel->where(['conference_id' => $this->currentConfId, 'status' => 'passed_round2'])->countAllResults(),
            'paid'          => $paperModel->where(['conference_id' => $this->currentConfId, 'payment_status' => 'paid'])->countAllResults(),
            'unpaid'        => $paperModel->where(['conference_id' => $this->currentConfId, 'payment_status' => 'unpaid'])->countAllResults(),
        ];

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
                                             ->select('paper_reviews.*, users.first_name, users.last_name, users.email')
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
            return redirect()->to('/admin/disciplines')->with('success', 'เพิ่มศาสตร์ย่อยเรียบร้อยแล้ว');
        }
    }

    public function deleteDiscipline($id)
    {
        $db = \Config\Database::connect();
        try {
            $db->table('disciplines')->delete(['id' => $id]);
            return redirect()->to('/admin/disciplines')->with('success', 'ลบศาสตร์ย่อยเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->to('/admin/disciplines')->with('error', 'ไม่สามารถลบศาสตร์ย่อยได้เนื่องจากมีบทความวิชาการผูกอยู่');
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
            $critModel->insert([
                'conference_id' => $this->currentConfId,
                'round'         => $this->request->getPost('round'),
                'criteria_name' => $this->request->getPost('criteria_name'),
                'max_score'     => $this->request->getPost('max_score'),
                'description'   => $this->request->getPost('description')
            ]);
            return redirect()->to('/admin/criteria')->with('success', 'เพิ่มเกณฑ์ประเมินเรียบร้อยแล้ว');
        }
    }

    public function deleteCriteria($id)
    {
        $critModel = new CriteriaModel();
        try {
            $critModel->delete($id);
            return redirect()->to('/admin/criteria')->with('success', 'ลบเกณฑ์ประเมินเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->to('/admin/criteria')->with('error', 'ไม่สามารถลบเกณฑ์ประเมินได้เนื่องจากเริ่มมีการประเมินผลงานและลงคะแนนไปแล้ว');
        }
    }

    public function saveAsTemplate()
    {
        if ($this->request->is('post')) {
            $round = $this->request->getPost('round');
            $templateName = $this->request->getPost('template_name');

            if (empty(trim($templateName))) {
                return redirect()->to('/admin/criteria')->with('error', 'กรุณาระบุชื่อแบบฟอร์มคะแนน');
            }

            $critModel = new CriteriaModel();
            $currentCriteria = $critModel->where([
                'conference_id' => $this->currentConfId,
                'round'         => $round
            ])->findAll();

            if (empty($currentCriteria)) {
                return redirect()->to('/admin/criteria')->with('error', 'ไม่สามารถบันทึกได้ เนื่องจากไม่มีเกณฑ์ประเมินสำหรับขั้นตอนนี้');
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
                return redirect()->to('/admin/criteria')->with('error', 'เกิดข้อผิดพลาดในการบันทึกแบบฟอร์มสำเร็จรูป');
            }

            return redirect()->to('/admin/criteria')->with('success', 'บันทึกแบบฟอร์มคะแนนสำเร็จรูปเรียบร้อยแล้ว');
        }
    }

    public function importTemplate()
    {
        if ($this->request->is('post')) {
            $templateId = $this->request->getPost('template_id');
            $db = \Config\Database::connect();

            $template = $db->table('evaluation_templates')->where('id', $templateId)->get()->getRowArray();
            if (!$template) {
                return redirect()->to('/admin/criteria')->with('error', 'ไม่พบแบบฟอร์มคะแนนที่ระบุ');
            }

            $templateCriteria = $db->table('evaluation_template_criteria')->where('template_id', $templateId)->get()->getResultArray();
            if (empty($templateCriteria)) {
                return redirect()->to('/admin/criteria')->with('error', 'แบบฟอร์มนี้ไม่มีรายการเกณฑ์ประเมินย่อย');
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
                    return redirect()->to('/admin/criteria')->with('error', 'เกิดข้อผิดพลาดในการนำเข้าเกณฑ์ประเมิน');
                }

                return redirect()->to('/admin/criteria')->with('success', 'นำเข้าเกณฑ์ประเมินเรียบร้อยแล้ว');

            } catch (\Exception $e) {
                $db->transRollback();
                return redirect()->to('/admin/criteria')->with('error', 'ไม่สามารถนำเข้าเกณฑ์ได้ เนื่องจากมีบทความที่เริ่มได้รับการลงคะแนนในรอบนี้ไปแล้ว');
            }
        }
    }

    public function deleteTemplate($id)
    {
        $db = \Config\Database::connect();
        $db->table('evaluation_templates')->delete(['id' => $id]);
        return redirect()->to('/admin/criteria')->with('success', 'ลบแบบฟอร์มสำเร็จรูปเรียบร้อยแล้ว');
    }

    // ==========================================
    // 3. REVIEWER ASSIGNMENT
    // ==========================================
    public function assignReviewers()
    {
        if ($this->request->is('post')) {
            $paperId = $this->request->getPost('paper_id');
            $reviewerIds = $this->request->getPost('reviewer_ids'); // Array of 3 reviewers

            if (empty($reviewerIds) || count($reviewerIds) < 3) {
                return redirect()->to('/admin/dashboard')->with('error', 'ต้องเลือกผู้ทรงคุณวุฒิอย่างน้อย 3 คน');
            }

            $db = \Config\Database::connect();
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
                return redirect()->to('/admin/dashboard')->with('error', 'เกิดข้อผิดพลาดในการมอบหมายผู้ทรงคุณวุฒิ');
            }

            return redirect()->to('/admin/dashboard')->with('success', 'มอบหมายผู้ทรงคุณวุฒิประเมินเรียบร้อยแล้ว');
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
            return redirect()->to('/admin/rooms')->with('success', 'สร้างห้องนำเสนอเรียบร้อยแล้ว');
        }
    }

    public function deleteRoom($id)
    {
        $roomModel = new RoomModel();
        $roomModel->delete($id);
        return redirect()->to('/admin/rooms')->with('success', 'ลบห้องนำเสนอเรียบร้อยแล้ว');
    }

    public function assignCommittee()
    {
        if ($this->request->is('post')) {
            $db = \Config\Database::connect();
            $roomId = $this->request->getPost('room_id');
            $commId = $this->request->getPost('committee_id');

            // Check if already assigned
            $existing = $db->table('room_committees')->where(['room_id' => $roomId, 'committee_id' => $commId])->get()->getRow();
            if ($existing) {
                return redirect()->to('/admin/rooms')->with('error', 'กรรมการท่านนี้ถูกมอบหมายในห้องนี้อยู่แล้ว');
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

            return redirect()->to('/admin/rooms')->with('success', 'มอบหมายกรรมการเข้าห้องพรีเซนต์เรียบร้อยแล้ว');
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
        
        return redirect()->to('/admin/rooms')->with('success', 'ถอดถอนกรรมการสำเร็จ');
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
                return redirect()->to('/admin/rooms')->with('error', 'บทความวิชาการนี้ได้รับการจัดห้องนำเสนอเรียบร้อยแล้ว');
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

            return redirect()->to('/admin/rooms')->with('success', 'จัดสรรบทความเข้าห้องนำเสนอเรียบร้อยแล้ว');
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

        return redirect()->to('/admin/rooms')->with('success', 'ถอนบทความออกจากห้องนำเสนอเรียบร้อยแล้ว');
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

        return redirect()->to('/admin/payments')->with('success', 'อนุมัติการชำระเงินเรียบร้อยแล้ว');
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

        return redirect()->to('/admin/payments')->with('success', 'ปฏิเสธการชำระเงินเรียบร้อยแล้ว');
    }
}
