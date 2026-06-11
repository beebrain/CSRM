<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return $this->redirectUser(session()->get('role'));
        }

        if ($this->request->is('post')) {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $userModel = new UserModel();
            $user = $userModel->where('email', $email)->first();

            if ($user && password_verify($password, $user['password'])) {
                if ($user['is_verified'] == 0) {
                    return redirect()->to(base_url('auth/login'))->with('error', 'กรุณายืนยันอีเมลของคุณก่อนเข้าสู่ระบบ');
                }

                // Set session data
                session()->set([
                    'user_id'    => $user['id'],
                    'email'      => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name'  => $user['last_name'],
                    'role'       => $user['role'],
                    'logged_in'  => true
                ]);

                return $this->redirectUser($user['role']);
            }

            return redirect()->to(base_url('auth/login'))->with('error', 'อีเมลหรือรหัสผ่านไม่ถูกต้อง');
        }

        return view('auth/login');
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return $this->redirectUser(session()->get('role'));
        }

        if ($this->request->is('post')) {
            $rules = [
                'email'      => 'required|valid_email|is_unique[users.email]',
                'password'   => 'required|min_length[6]',
                'first_name' => 'required',
                'last_name'  => 'required',
                'role'       => 'required|in_list[author,reviewer,committee,admin]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->to(base_url('auth/register'))->withInput()->with('errors', $this->validator->getErrors());
            }

            $userModel = new UserModel();
            $token = bin2hex(random_bytes(16));

            $userData = [
                'email'              => $this->request->getPost('email'),
                'password'           => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
                'first_name'         => $this->request->getPost('first_name'),
                'last_name'          => $this->request->getPost('last_name'),
                'role'               => $this->request->getPost('role'),
                'is_verified'        => 0, // Must verify
                'verification_token' => $token
            ];

            $userId = $userModel->insert($userData);

            // Save keywords if reviewer or committee
            $role = $this->request->getPost('role');
            if (in_array($role, ['reviewer', 'committee'])) {
                $keywordsStr = $this->request->getPost('keywords');
                if (!empty($keywordsStr)) {
                    $keywords = array_filter(array_map('trim', explode(',', $keywordsStr)));
                    $expModel = new \App\Models\ExpertiseModel();
                    foreach ($keywords as $kw) {
                        if (!empty($kw)) {
                            try {
                                $expModel->insert([
                                    'user_id' => $userId,
                                    'keyword' => $kw
                                ]);
                            } catch (\Exception $e) {
                                // Ignore duplicates
                            }
                        }
                    }
                }
            }

            // Mock email verification URL
            $verifyUrl = base_url('auth/verify/' . $token);
            session()->setFlashdata('temp_verify_url', $verifyUrl);

            return redirect()->to(base_url('auth/login'))->with('success', 'สมัครสมาชิกสำเร็จ! กรุณายืนยันการใช้งานผ่านอีเมลของคุณ');
        }

        return view('auth/register');
    }

    public function verify($token)
    {
        $userModel = new UserModel();
        $user = $userModel->where('verification_token', $token)->first();

        if ($user) {
            $userModel->update($user['id'], [
                'is_verified'        => 1,
                'verification_token' => null
            ]);

            return redirect()->to(base_url('auth/login'))->with('success', 'ยืนยันอีเมลสำเร็จแล้ว! คุณสามารถเข้าสู่ระบบได้ทันที');
        }

        return redirect()->to(base_url('auth/login'))->with('error', 'โทเค็นสำหรับยืนยันตัวตนไม่ถูกต้องหรือหมดอายุ');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('auth/login'));
    }

    private function redirectUser($role)
    {
        switch ($role) {
            case 'superadmin':
                return redirect()->to(base_url('superadmin/dashboard'));
            case 'admin':
                return redirect()->to(base_url('admin/dashboard'));
            case 'reviewer':
                return redirect()->to(base_url('reviewer/dashboard'));
            case 'committee':
                return redirect()->to(base_url('committee/dashboard'));
            case 'author':
            default:
                return redirect()->to(base_url('author/dashboard'));
        }
    }
}
