<?php

namespace App\Controllers;

use App\Models\ConferenceModel;
use App\Models\UserModel;
use App\Models\ConferenceAdminModel;

class SuperAdmin extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        if (!session()->get('logged_in') || session()->get('role') !== 'superadmin') {
            throw new \CodeIgniter\Router\Exceptions\RedirectException('auth/login');
        }
    }

    public function dashboard()
    {
        $confModel = new ConferenceModel();
        $userModel = new UserModel();
        $confAdminModel = new ConferenceAdminModel();

        // Get all conferences
        $conferences = $confModel->orderBy('year', 'DESC')->findAll();

        // Count users by role
        $stats = [
            'total_users' => $userModel->countAllResults(),
            'admins'      => $userModel->where('role', 'admin')->countAllResults(),
            'reviewers'   => $userModel->where('role', 'reviewer')->countAllResults(),
            'committees'  => $userModel->where('role', 'committee')->countAllResults(),
            'authors'     => $userModel->where('role', 'author')->countAllResults(),
        ];

        // Get admins list for assignment
        $adminsList = $userModel->where('role', 'admin')->findAll();

        // Get current admin assignments
        $db = \Config\Database::connect();
        $assignments = $db->table('conference_admins')
                          ->select('conference_admins.*, users.first_name, users.last_name, users.email, conferences.year, conferences.title')
                          ->join('users', 'users.id = conference_admins.user_id')
                          ->join('conferences', 'conferences.id = conference_admins.conference_id')
                          ->get()
                          ->getResultArray();

        return view('superadmin/dashboard', [
            'conferences' => $conferences,
            'stats'       => $stats,
            'adminsList'  => $adminsList,
            'assignments' => $assignments
        ]);
    }

    public function createConference()
    {
        if ($this->request->is('post')) {
            $confModel = new ConferenceModel();
            
            $data = [
                'year'                  => $this->request->getPost('year'),
                'title'                 => $this->request->getPost('title'),
                'host_name'             => $this->request->getPost('host_name'),
                'description'           => $this->request->getPost('description'),
                'is_active'             => $this->request->getPost('is_active') ? 1 : 0,
                'accept_submissions'    => 1,
                'accept_evaluations'    => 1,
                'accept_grading'        => 1,
                'default_revision_days' => $this->request->getPost('default_revision_days') ?: 30
            ];

            // If new conference is set active, deactivate all others
            if ($data['is_active'] == 1) {
                $confModel->where('is_active', 1)->set(['is_active' => 0])->update();
            }

            $confModel->insert($data);
            return redirect()->to(base_url('superadmin/dashboard'))->with('success', 'สร้างปีการจัดงานประชุมเรียบร้อยแล้ว');
        }
    }

    public function editConference($id)
    {
        $confModel = new ConferenceModel();
        $conference = $confModel->find($id);

        if (!$conference) {
            return redirect()->to(base_url('superadmin/dashboard'))->with('error', 'ไม่พบการประชุมที่ระบุ');
        }

        if ($this->request->is('post')) {
            $data = [
                'year'                  => $this->request->getPost('year'),
                'title'                 => $this->request->getPost('title'),
                'host_name'             => $this->request->getPost('host_name'),
                'description'           => $this->request->getPost('description'),
                'accept_submissions'    => $this->request->getPost('accept_submissions') ? 1 : 0,
                'accept_evaluations'    => $this->request->getPost('accept_evaluations') ? 1 : 0,
                'accept_grading'        => $this->request->getPost('accept_grading') ? 1 : 0,
                'default_revision_days' => $this->request->getPost('default_revision_days') ?: 30
            ];

            $confModel->update($id, $data);
            return redirect()->to(base_url('superadmin/dashboard'))->with('success', 'แก้ไขรอบปีการประชุมเรียบร้อยแล้ว');
        }

        return view('superadmin/edit_conference', [
            'conference' => $conference
        ]);
    }

    public function toggleConference($id)
    {
        $confModel = new ConferenceModel();
        $conference = $confModel->find($id);

        if ($conference) {
            // Deactivate all first
            $confModel->where('is_active', 1)->set(['is_active' => 0])->update();
            // Activate selected
            $confModel->update($id, ['is_active' => 1]);
            return redirect()->to(base_url('superadmin/dashboard'))->with('success', 'เปลี่ยนการประชุมปัจจุบันเป็นปี ' . $conference['year'] . ' เรียบร้อยแล้ว');
        }

        return redirect()->to(base_url('superadmin/dashboard'))->with('error', 'ไม่พบการประชุมที่ระบุ');
    }

    public function assignAdmin()
    {
        if ($this->request->is('post')) {
            $confAdminModel = new ConferenceAdminModel();
            
            $data = [
                'conference_id' => $this->request->getPost('conference_id'),
                'user_id'       => $this->request->getPost('user_id')
            ];

            // Check if already assigned
            $existing = $confAdminModel->where($data)->first();
            if ($existing) {
                return redirect()->to(base_url('superadmin/dashboard'))->with('error', 'แอดมินผู้นี้ได้รับการแต่งตั้งให้ดูแลการประชุมปีนี้อยู่แล้ว');
            }

            $confAdminModel->insert($data);
            return redirect()->to(base_url('superadmin/dashboard'))->with('success', 'แต่งตั้งผู้ดูแลการประชุมประจำปีสำเร็จ');
        }
    }

    public function removeAdmin($id)
    {
        $confAdminModel = new ConferenceAdminModel();
        $confAdminModel->delete($id);
        return redirect()->to(base_url('superadmin/dashboard'))->with('success', 'ถอนสิทธิ์ผู้ดูแลการประชุมประจำปีเรียบร้อยแล้ว');
    }

    public function deleteConference($id)
    {
        return redirect()->to(base_url('superadmin/dashboard'))->with('error', 'ระบบระงับการลบรอบปีการประชุมเพื่อป้องกันข้อมูลสูญหาย (ไม่อนุญาตให้ลบ)');
    }
}
