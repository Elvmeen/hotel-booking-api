<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['User_model', 'Room_model', 'Booking_model']);
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation']);
    }

    // -----------------------------------------------------------------------
    // Auth
    // -----------------------------------------------------------------------

    public function login(): void
    {
        if ($this->_is_logged_in()) {
            //redirect('admin/dashboard');
            var_dump($this->session->userdata('admin_jwt')); exit;
        }
        $this->load->view('admin/layout_auth', ['page' => 'admin/login', 'data' => []]);
    }

    public function login_action(): void
    {
        $email    = $this->input->post('email');
        $password = $this->input->post('password');

        if (empty($email) || empty($password)) {
            $this->session->set_flashdata('error', 'Email and password are required.');
            redirect('admin/login');
        }

        $user = $this->User_model->find_by_email(strtolower(trim($email)));

        // find_by_email returns an object from the DB, cast to array for consistent access
        if (is_object($user)) {
            $user = (array) $user;
        }

        if (!$user || $user['role'] !== 'admin' || !$this->User_model->verify_password($password, $user['password'])) {
            $this->session->set_flashdata('error', 'Invalid credentials or insufficient permissions.');
            redirect('admin/login');
        }

        // FIXED: column is is_active (boolean), not status (string)
        if (empty($user['is_active'])) {
            $this->session->set_flashdata('error', 'Your account has been suspended.');
            redirect('admin/login');
        }

        $this->session->set_userdata([
            'admin_id'    => $user['id'],
            'admin_name'  => $user['name'],
            'admin_email' => $user['email'],
            'admin_role'  => $user['role'],
        ]);

        // Fetch JWT token and store in session for API calls
        $api_url = getenv('APP_URL') ?: 'https://hotel-booking-api-1-zmcs.onrender.com/';
        $ch = curl_init(rtrim($api_url, '/') . '/api/v1/auth/login');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POST           => TRUE,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode(['email' => $email, 'password' => $password]),
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_TIMEOUT        => 10,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $parsed = json_decode($response, TRUE);
        if (!empty($parsed['data']['token'])) {
            $this->session->set_userdata('admin_jwt', $parsed['data']['token']);
        }

        redirect('admin/dashboard');
    }

    public function logout_action(): void
    {
        $this->session->unset_userdata(['admin_id', 'admin_name', 'admin_email', 'admin_role']);
        $this->session->sess_destroy();
        redirect('admin/login');
    }

    // -----------------------------------------------------------------------
    // Dashboard
    // -----------------------------------------------------------------------

    public function dashboard(): void
    {
        $this->_require_admin();

        $room_stats = [
            'total'    => $this->Room_model->count(),
            'active'   => $this->Room_model->count(['status' => 'available']),
            'inactive' => $this->Room_model->count(['status' => 'inactive']),
        ];
        $booking_stats   = $this->Booking_model->stats();
        $user_count      = $this->User_model->count();
        $revenue         = [
            'today' => $this->Booking_model->revenue('today'),
            'month' => $this->Booking_model->revenue('month'),
            'year'  => $this->Booking_model->revenue('year'),
        ];
        $recent_bookings = $this->Booking_model->all(1, 10);

        $this->_view('dashboard', compact('room_stats', 'booking_stats', 'user_count', 'revenue', 'recent_bookings'));
    }

    // -----------------------------------------------------------------------
    // Rooms
    // -----------------------------------------------------------------------

    public function rooms(): void
    {
        $this->_require_admin();
        $page    = max(1, (int) ($this->input->get('page') ?? 1));
        $search  = $this->input->get('search') ?? '';
        $filters = ['search' => $search];

        $rooms = $this->Room_model->all($page, 20, $filters);
        $total = $this->Room_model->count($filters);
        $pages = (int) ceil($total / 20);

        $flash = $this->session->flashdata('success');
        $error = $this->session->flashdata('error');

        $this->_view('rooms', compact('rooms', 'total', 'page', 'pages', 'search', 'flash', 'error'));
    }

    // -----------------------------------------------------------------------
    // Bookings
    // -----------------------------------------------------------------------

    public function bookings(): void
    {
        $this->_require_admin();
        $page    = max(1, (int) ($this->input->get('page') ?? 1));
        $status  = $this->input->get('status') ?? '';
        $search  = $this->input->get('search') ?? '';
        $filters = ['status' => $status, 'search' => $search];

        $bookings = $this->Booking_model->all($page, 20, $filters);
        $total    = $this->Booking_model->count($filters);
        $pages    = (int) ceil($total / 20);

        $flash = $this->session->flashdata('success');
        $error = $this->session->flashdata('error');

        $this->_view('bookings', compact('bookings', 'total', 'page', 'pages', 'status', 'search', 'flash', 'error'));
    }

    // -----------------------------------------------------------------------
    // Users
    // -----------------------------------------------------------------------

    public function users(): void
    {
        $this->_require_admin();
        $page   = max(1, (int) ($this->input->get('page') ?? 1));
        $search = $this->input->get('search') ?? '';

        $users = $this->User_model->all($page, 20, $search);
        $total = $this->User_model->count($search);
        $pages = (int) ceil($total / 20);

        $flash = $this->session->flashdata('success');
        $error = $this->session->flashdata('error');

        $this->_view('users', compact('users', 'total', 'page', 'pages', 'search', 'flash', 'error'));
    }

    // -----------------------------------------------------------------------
    // Internals
    // -----------------------------------------------------------------------

    private function _is_logged_in(): bool
    {
        return !empty($this->session->userdata('admin_id'));
    }

    private function _require_admin(): void
    {
        if (!$this->_is_logged_in()) {
            redirect('admin/login');
        }
    }

    private function _view(string $template, array $data = []): void
    {
        $data['admin_name'] = $this->session->userdata('admin_name');
        $data['page']       = 'admin/' . $template;
        $this->load->view('admin/layout', $data);
    }
}
