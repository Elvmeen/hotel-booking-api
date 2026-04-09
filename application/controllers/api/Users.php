<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Users Controller (Admin)
 *
 * GET    /api/users        - List users (admin)
 * GET    /api/users/{id}   - Get user (admin or self)
 * PUT    /api/users/{id}   - Update user (admin or self)
 * DELETE /api/users/{id}   - Delete user (admin)
 */
class Users extends Base_api
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    // GET /api/users
    public function index(): void
    {
        $this->require_admin();

        $page     = max(1, (int) ($this->input->get('page') ?? 1));
        $per_page = min(100, max(1, (int) ($this->input->get('per_page') ?? 15)));
        $search   = $this->input->get('search') ?? '';

        $users = $this->User_model->all($page, $per_page, $search);
        $total = $this->User_model->count($search);

        $this->respond_success([
            'users'      => $users,
            'pagination' => [
                'page'      => $page,
                'per_page'  => $per_page,
                'total'     => $total,
                'last_page' => (int) ceil($total / $per_page),
            ],
        ]);
    }

    // GET /api/users/{id}
    public function show(int $id): void
    {
        $this->require_auth();

        // Guests can only view their own profile
        if ($this->auth_user['role'] !== 'admin' && (int) $this->auth_user['id'] !== $id) {
            $this->respond_forbidden();
        }

        $user = $this->User_model->find($id);
        if (!$user) {
            $this->respond_not_found('User not found');
        }

        $this->respond_success($user);
    }

    // PUT /api/users/{id}
    public function update(int $id): void
    {
        $this->require_auth();

        // Guests can only update their own profile
        if ($this->auth_user['role'] !== 'admin' && (int) $this->auth_user['id'] !== $id) {
            $this->respond_forbidden();
        }

        $user = $this->User_model->find($id);
        if (!$user) {
            $this->respond_not_found('User not found');
        }

        $data   = $this->get_json_body();
        $errors = [];
        $update = [];

        if (isset($data['name'])) {
            if (empty($data['name'])) {
                $errors['name'] = 'Name cannot be empty';
            } else {
                $update['name'] = trim($data['name']);
            }
        }

        if (isset($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email address';
            } elseif ($this->User_model->email_exists($data['email'], $id)) {
                $errors['email'] = 'Email is already taken';
            } else {
                $update['email'] = strtolower(trim($data['email']));
            }
        }

        if (isset($data['phone'])) {
            $update['phone'] = trim($data['phone']);
        }

        if (isset($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            } else {
                $update['password'] = $data['password'];
            }
        }

        // Admin-only fields
        if ($this->auth_user['role'] === 'admin') {
            if (isset($data['role'])   && in_array($data['role'],   ['admin', 'guest'], true))   $update['role']   = $data['role'];
            if (isset($data['status']) && in_array($data['status'], ['active', 'suspended'], true)) $update['status'] = $data['status'];
        }

        if ($errors) {
            $this->respond_validation_error($errors);
        }

        if (empty($update)) {
            $this->respond_error('No valid fields provided');
        }

        $this->User_model->update($id, $update);
        $this->respond_success($this->User_model->find($id), 'User updated successfully');
    }

    // DELETE /api/users/{id}
    public function delete(int $id): void
    {
        $this->require_admin();

        if ($id === (int) $this->auth_user['id']) {
            $this->respond_error('You cannot delete your own account');
        }

        $user = $this->User_model->find($id);
        if (!$user) {
            $this->respond_not_found('User not found');
        }

        $this->User_model->delete($id);
        $this->respond_success(null, 'User deleted successfully');
    }
}
