<?php

namespace App\Controllers;

use App\Models\ConferenceModel;
use App\Models\DisciplineModel;
use App\Models\PaperModel;
use App\Models\PaymentModel;

class Author extends BaseController
{
    protected $activeConf = null;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Access check
        if (!session()->get('logged_in') || session()->get('role') !== 'author') {
            throw new \CodeIgniter\Router\Exceptions\RedirectException('auth/login');
        }

        // Get active conference
        $confModel = new ConferenceModel();
        $this->activeConf = $confModel->where('is_active', 1)->first();
    }

    public function dashboard()
    {
        $paperModel = new PaperModel();
        $db = \Config\Database::connect();

        // Get author's papers for all years (with details)
        $papers = $paperModel->getDetails(['papers.author_id' => session()->get('user_id')]);

        // Get presentation room schedule details for each paper if scheduled
        $schedules = [];
        $comments = [];
        foreach ($papers as $p) {
            $schedules[$p['id']] = $db->table('room_papers')
                                      ->select('rooms.name as room_name, rooms.location, rooms.date_time, room_papers.presentation_time')
                                      ->join('rooms', 'rooms.id = room_papers.room_id')
                                      ->where('paper_id', $p['id'])
                                      ->get()
                                      ->getRowArray();

            // Fetch reviewer comments if revision required
            if ($p['status'] === 'revision_required') {
                $comments[$p['id']] = $db->table('paper_reviews')
                                         ->select('comments')
                                         ->where(['paper_id' => $p['id'], 'status' => 'completed'])
                                         ->where('comments IS NOT NULL')
                                         ->get()
                                         ->getResultArray();
            }

            // Fetch presentation comments (Round 2)
            $presentationComments[$p['id']] = $db->table('presentation_reviews')
                                                 ->select('presentation_reviews.comments, users.first_name, users.last_name')
                                                 ->join('users', 'users.id = presentation_reviews.committee_id')
                                                 ->where(['paper_id' => $p['id'], 'presentation_reviews.status' => 'completed'])
                                                 ->where('presentation_reviews.comments IS NOT NULL')
                                                 ->get()
                                                 ->getResultArray();
        }

        // Get disciplines for the submission form
        $discModel = new DisciplineModel();
        $disciplines = $discModel->getWithTrack();

        return view('author/dashboard', [
            'activeConf'           => $this->activeConf,
            'papers'               => $papers,
            'schedules'            => $schedules,
            'comments'             => $comments,
            'presentationComments' => $presentationComments,
            'disciplines'          => $disciplines
        ]);
    }

    public function submitPaper()
    {
        if (!$this->activeConf) {
            return redirect()->to(base_url('author/dashboard'))->with('error', 'ขออภัย ขณะนี้ไม่มีการเปิดรับสมัครงานประชุมวิชาการประจำปี');
        }

        if ($this->request->is('post')) {
            $rules = [
                'title'         => 'required',
                'abstract'      => 'required',
                'discipline_id' => 'required|is_not_unique[disciplines.id]',
                'pdf_file'      => 'uploaded[pdf_file]|max_size[pdf_file,10240]|ext_in[pdf_file,pdf]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->to(base_url('author/dashboard'))->withInput()->with('errors', $this->validator->getErrors());
            }

            $pdf = $this->request->getFile('pdf_file');
            $newName = $pdf->getRandomName();
            
            // Move file to public/uploads/papers
            $pdf->move('uploads/papers', $newName);
            $filePath = 'uploads/papers/' . $newName;

            $paperModel = new PaperModel();
            $paperModel->insert([
                'conference_id'  => $this->activeConf['id'],
                'title'          => $this->request->getPost('title'),
                'abstract'       => $this->request->getPost('abstract'),
                'keywords'       => $this->request->getPost('keywords'),
                'file_path'      => $filePath,
                'author_id'      => session()->get('user_id'),
                'discipline_id'  => $this->request->getPost('discipline_id'),
                'status'         => 'submitted',
                'payment_status' => 'unpaid'
            ]);

            return redirect()->to(base_url('author/dashboard'))->with('success', 'ส่งบทความวิชาการเรียบร้อยแล้ว');
        }
    }

    public function payment()
    {
        $paperModel = new PaperModel();
        
        // Find unpaid papers for the current active conference year
        if (!$this->activeConf) {
            return redirect()->to(base_url('author/dashboard'))->with('error', 'ขออภัย ไม่มีงานประชุมที่เปิดลงทะเบียนชำระเงินขณะนี้');
        }

        $unpaidPapers = $paperModel->getDetails([
            'papers.author_id' => session()->get('user_id'),
            'papers.conference_id' => $this->activeConf['id'],
            'papers.payment_status' => 'unpaid'
        ]);

        return view('author/payment', [
            'activeConf'   => $this->activeConf,
            'unpaidPapers' => $unpaidPapers
        ]);
    }

    public function payBank()
    {
        if ($this->request->is('post')) {
            $paperIds = $this->request->getPost('paper_ids');
            $rules = [
                'paper_ids' => 'required',
                'slip_file' => 'uploaded[slip_file]|max_size[slip_file,5120]|is_image[slip_file]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->to(base_url('author/payment'))->with('errors', $this->validator->getErrors());
            }

            $slip = $this->request->getFile('slip_file');
            $newName = $slip->getRandomName();
            $slip->move('uploads/slips', $newName);
            $slipPath = 'uploads/slips/' . $newName;

            $db = \Config\Database::connect();
            $db->transStart();

            // Insert Payment
            $amount = count($paperIds) * 1000.00; // 1000 Baht per paper
            
            $db->table('payments')->insert([
                'conference_id'  => $this->activeConf['id'],
                'user_id'        => session()->get('user_id'),
                'payment_method' => 'bank_transfer',
                'amount'         => $amount,
                'slip_path'      => $slipPath,
                'status'         => 'pending'
            ]);
            $paymentId = $db->insertID();

            // Insert Items & Update Paper statuses to pending verification
            foreach ($paperIds as $pId) {
                $db->table('payment_items')->insert([
                    'payment_id' => $paymentId,
                    'paper_id'   => $pId
                ]);
                $db->table('papers')->where('id', $pId)->update(['payment_status' => 'pending_verification']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to(base_url('author/payment'))->with('error', 'เกิดข้อผิดพลาดในการแนบหลักฐานการโอนเงิน');
            }

            return redirect()->to(base_url('author/dashboard'))->with('success', 'แนบหลักฐานการชำระเงินเรียบร้อยแล้ว รอแอดมินดำเนินการตรวจสอบ');
        }
    }

    public function payGateway()
    {
        if ($this->request->is('post')) {
            $paperIds = $this->request->getPost('paper_ids');
            
            if (empty($paperIds)) {
                return redirect()->to(base_url('author/payment'))->with('error', 'กรุณาเลือกบทความอย่างน้อย 1 รายการเพื่อชำระเงิน');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            $amount = count($paperIds) * 1000.00; // 1000 Baht per paper
            $ref = 'TXN-' . strtoupper(bin2hex(random_bytes(6)));

            // Insert Approved Payment (since it is online gateway mockup)
            $db->table('payments')->insert([
                'conference_id'         => $this->activeConf['id'],
                'user_id'               => session()->get('user_id'),
                'payment_method'        => 'online_gateway',
                'amount'                => $amount,
                'transaction_reference' => $ref,
                'status'                => 'approved'
            ]);
            $paymentId = $db->insertID();

            // Insert Items & Update Paper status immediately to paid
            foreach ($paperIds as $pId) {
                $db->table('payment_items')->insert([
                    'payment_id' => $paymentId,
                    'paper_id'   => $pId
                ]);
                $db->table('papers')->where('id', $pId)->update(['payment_status' => 'paid']);
            }

            $db->transComplete();

            return redirect()->to(base_url('author/dashboard'))->with('success', 'ชำระเงินสำเร็จ (เลขอ้างอิง: ' . $ref . ') บทความของคุณเปลี่ยนสถานะเป็นชำระเงินแล้ว!');
        }
    }

    public function submitRevision()
    {
        if (!$this->activeConf) {
            return redirect()->to(base_url('author/dashboard'))->with('error', 'ขออภัย ขณะนี้ไม่มีการเปิดรับสมัครงานประชุมวิชาการประจำปี');
        }

        if ($this->request->is('post')) {
            $paperId = $this->request->getPost('paper_id');
            $paperModel = new PaperModel();
            
            // Check if paper belongs to this author and needs revision
            $paper = $paperModel->where(['id' => $paperId, 'author_id' => session()->get('user_id')])->first();
            if (!$paper || $paper['status'] !== 'revision_required') {
                return redirect()->to(base_url('author/dashboard'))->with('error', 'ไม่พบความต้องการแก้ไขบทความนี้ หรือคุณไม่มีสิทธิ์');
            }

            $rules = [
                'comments' => 'required',
                'pdf_file' => 'uploaded[pdf_file]|max_size[pdf_file,10240]|ext_in[pdf_file,pdf]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->to(base_url('author/dashboard'))->with('errors', $this->validator->getErrors());
            }

            $pdf = $this->request->getFile('pdf_file');
            $newName = $pdf->getRandomName();
            $pdf->move('uploads/revisions', $newName);
            $filePath = 'uploads/revisions/' . $newName;

            $db = \Config\Database::connect();
            $db->transStart();

            // Insert into paper_revisions
            $db->table('paper_revisions')->insert([
                'paper_id'  => $paperId,
                'file_path' => $filePath,
                'comments'  => $this->request->getPost('comments')
            ]);

            // Update paper status to revised_submitted
            // Reset reviews status to pending, and clear decisions/comments for a fresh round
            $db->table('papers')->where('id', $paperId)->update(['status' => 'revised_submitted']);
            $db->table('paper_reviews')->where('paper_id', $paperId)->update([
                'status'   => 'pending',
                'decision' => null,
                'comments' => null
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to(base_url('author/dashboard'))->with('error', 'เกิดข้อผิดพลาดในการส่งบทความแก้ไข');
            }

            return redirect()->to(base_url('author/dashboard'))->with('success', 'ส่งบทความวิชาการฉบับแก้ไขเรียบร้อยแล้ว รอผู้ทรงคุณวุฒิประเมินผลอีกครั้ง');
        }
    }
}
