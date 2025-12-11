<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;
    protected $session;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('admin');
        }
        
        return view('auth/login');
    }

    public function prosesLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        // Validasi
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Cek apakah ada user di database
        $userCount = $this->userModel->countUsers();
        
        // Jika belum ada user, buat user admin pertama
        if ($userCount === 0) {
            $this->createFirstAdmin($email, $password);
        }
        
        // Autentikasi user
        $user = $this->userModel->authenticate($email, $password);
        
        if ($user) {
            // Set session
            $sessionData = [
                'userId' => $user['id'],
                'nama' => $user['nama'],
                'email' => $user['email'],
                'role' => $user['role'],
                'isLoggedIn' => true
            ];
            
            $this->session->set($sessionData);
            
            // Redirect ke dashboard admin
            return redirect()->to('admin');
        } else {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah!');
        }
    }
    
    private function createFirstAdmin($email, $password)
    {
        $data = [
            'nama' => 'Administrator',
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'is_active' => 1
        ];
        
        $this->userModel->save($data);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('login');
    }
    
    public function register()
    {
        // Hanya untuk pembuatan user pertama
        $userCount = $this->userModel->countUsers();
        
        if ($userCount > 0) {
            return redirect()->to('login')->with('error', 'Registrasi sudah ditutup. Silakan login.');
        }
        
        return view('auth/register');
    }
    
    public function prosesRegister()
    {
        // Hanya untuk pembuatan user pertama
        $userCount = $this->userModel->countUsers();
        
        if ($userCount > 0) {
            return redirect()->to('login')->with('error', 'Registrasi sudah ditutup.');
        }
        
        // Validasi
        $rules = [
            'nama' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => 'admin',
            'is_active' => 1
        ];
        
        $this->userModel->save($data);
        
        return redirect()->to('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}