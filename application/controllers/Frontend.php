<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend extends CI_Controller
{
    public function index(): void
    {
        $this->load->view('frontend/home');
    }

    public function rooms(): void
    {
        $this->load->view('frontend/rooms');
    }

    public function room(int $id): void
    {
        $this->load->view('frontend/room', ['room_id' => $id]);
    }

    public function login(): void
    {
        $this->load->view('frontend/login');
    }

    public function register(): void
    {
        $this->load->view('frontend/register');
    }

    public function dashboard(): void
    {
        $this->load->view('frontend/dashboard');
    }
}
