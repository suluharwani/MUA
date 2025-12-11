<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }
        
        // Optional: Check user role if needed
        // if ($session->get('role') !== 'admin') {
        //     return redirect()->to('/');
        // }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}