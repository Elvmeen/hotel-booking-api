<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base_api Library
 *
 * Base class for all REST API controllers. Handles JSON responses,
 * JWT authentication, role-based authorization, and CORS.
 */
class Base_api extends CI_Controller
{
    protected ?array $auth_user = null;

    public function __construct()
    {
        parent::__construct();
        $this->_handle_cors();
        $this->_set_json_headers();
    }

    // -----------------------------------------------------------------------
    // Response helpers
    // -----------------------------------------------------------------------

    protected function respond(array $data, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function respond_success($data = null, string $message = 'Success', int $status = 200): void
    {
        $body = ['success' => true, 'message' => $message];
        if ($data !== null) {
            $body['data'] = $data;
        }
        $this->respond($body, $status);
    }

    protected function respond_created($data = null, string $message = 'Created successfully'): void
    {
        $this->respond_success($data, $message, 201);
    }

    protected function respond_error(string $message, int $status = 400, array $errors = []): void
    {
        $body = ['success' => false, 'message' => $message];
        if (!empty($errors)) {
            $body['errors'] = $errors;
        }
        $this->respond($body, $status);
    }

    protected function respond_unauthorized(string $message = 'Unauthorized'): void
    {
        $this->respond_error($message, 401);
    }

    protected function respond_forbidden(string $message = 'Forbidden'): void
    {
        $this->respond_error($message, 403);
    }

    protected function respond_not_found(string $message = 'Resource not found'): void
    {
        $this->respond_error($message, 404);
    }

    protected function respond_validation_error(array $errors): void
    {
        $this->respond_error('Validation failed', 422, $errors);
    }

    // -----------------------------------------------------------------------
    // Request helpers
    // -----------------------------------------------------------------------

    protected function get_json_body(): array
    {
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return [];
        }
        $data = json_decode($raw, true);
        return (json_last_error() === JSON_ERROR_NONE && is_array($data)) ? $data : [];
    }

    protected function get_request_method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    // -----------------------------------------------------------------------
    // Authentication & Authorization
    // -----------------------------------------------------------------------

    /**
     * Require a valid JWT token. Sets $this->auth_user on success.
     * Exits with 401 on failure.
     */
    protected function require_auth(): void
    {
        $token = jwt_get_bearer_token();
        if (!$token) {
            $this->respond_unauthorized('Authorization token required');
        }

        $secret  = $this->config->item('jwt_secret_key');
        $payload = jwt_decode($token, $secret);

        if (!$payload || empty($payload['sub'])) {
            $this->respond_unauthorized('Invalid or expired token');
        }

        $this->load->model('User_model');
        $user = $this->User_model->find($payload['sub']);
        if (!$user) {
            $this->respond_unauthorized('User not found');
        }

        $this->auth_user = $user;
    }

    /**
     * Require admin role. Calls require_auth() first.
     */
    protected function require_admin(): void
    {
        $this->require_auth();
        if (($this->auth_user['role'] ?? '') !== 'admin') {
            $this->respond_forbidden('Admin access required');
        }
    }

    // -----------------------------------------------------------------------
    // Internals
    // -----------------------------------------------------------------------

    private function _handle_cors(): void
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    private function _set_json_headers(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
    }
}
