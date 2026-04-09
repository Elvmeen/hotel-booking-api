<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller
 *
 * Handles registration, login, logout, and token validation.
 */
class Auth extends Base_api
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    // POST /api/auth/register
    public function register(): void
    {
        $data = $this->get_json_body();

        $errors = $this->_validate_register($data);
        if ($errors) {
            $this->respond_validation_error($errors);
        }

        if ($this->User_model->email_exists($data['email'])) {
            $this->respond_error('Email address is already registered', 409);
        }

        $id = $this->User_model->create([
            'name'     => trim($data['name']),
            'email'    => strtolower(trim($data['email'])),
            'password' => $data['password'],
            'phone'    => $data['phone'] ?? null,
            'role'     => 'guest',
            'status'   => 'active',
        ]);

        $user  = $this->User_model->find($id);
        $token = $this->_issue_token($user);

        $this->respond_created([
            'token' => $token,
            'user'  => $user,
        ], 'Account created successfully');
    }

    // POST /api/auth/login
    public function login(): void
    {
        $data = $this->get_json_body();

        if (empty($data['email']) || empty($data['password'])) {
            $this->respond_validation_error([
                'email'    => empty($data['email'])    ? 'Email is required'    : null,
                'password' => empty($data['password']) ? 'Password is required' : null,
            ]);
        }

        $user = $this->User_model->find_by_email(strtolower(trim($data['email'])));

        if (!$user || !$this->User_model->verify_password($data['password'], $user['password'])) {
            $this->respond_error('Invalid email or password', 401);
        }

        if ($user['status'] !== 'active') {
            $this->respond_error('Your account has been suspended', 403);
        }

        // Remove password from response
        unset($user['password']);

        $this->respond_success([
            'token' => $this->_issue_token($user),
            'user'  => $user,
        ], 'Login successful');
    }

    // POST /api/auth/logout
    public function logout(): void
    {
        // JWT is stateless; the client discards the token.
        $this->respond_success(null, 'Logged out successfully');
    }

    // GET /api/auth/me
    public function me(): void
    {
        $this->require_auth();
        $this->respond_success($this->auth_user);
    }

    // -----------------------------------------------------------------------
    // Internals
    // -----------------------------------------------------------------------

    private function _issue_token(array $user): string
    {
        $secret = $this->config->item('jwt_secret_key');
        $expire = $this->config->item('jwt_expire');

        return jwt_encode([
            'sub'  => $user['id'],
            'role' => $user['role'],
            'name' => $user['name'],
        ], $secret, $expire);
    }

    private function _validate_register(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Name must not exceed 100 characters';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email must be a valid email address';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }

        return $errors;
    }
}
