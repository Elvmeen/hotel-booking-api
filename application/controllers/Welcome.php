<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Welcome Controller
 * Displays the API documentation landing page.
 */
class Welcome extends CI_Controller
{
    public function index(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'name'    => 'Hotel Booking API',
            'version' => '1.0.0',
            'status'  => 'running',
            'endpoints' => [
                'auth'     => '/api/auth/{login,register,logout,me}',
                'rooms'    => '/api/rooms',
                'bookings' => '/api/bookings',
                'users'    => '/api/users',
                'admin'    => '/admin/dashboard',
            ],
            'docs' => 'See README.md for full API documentation.',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
