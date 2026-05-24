<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected string $table = 'users';

    // -----------------------------------------------------------------------
    // Query methods
    // -----------------------------------------------------------------------

    public function all(int $page = 1, int $per_page = 15, string $search = ''): array
    {
        $offset = ($page - 1) * $per_page;
        $this->db->select('id, name, email, phone, role, status, created_at, updated_at');
        $this->db->from($this->table);
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('email', $search);
            $this->db->group_end();
        }
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($per_page, $offset);
        return $this->db->get()->result_array();
    }

    public function count(string $search = ''): int
    {
        $this->db->from($this->table);
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('email', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results();
    }

    public function find(int $id): ?array
    {
        $row = $this->db
            ->select('id, name, email, phone, role, status, created_at, updated_at')
            ->where('id', $id)
            ->get($this->table)
            ->row_array();
        return $row ?: null;
    }

    public function find_by_email(string $email): ?array
    {
        $row = $this->db
            ->where('email', $email)
            ->get($this->table)
            ->row_array();
        return $row ?: null;
    }

    // -----------------------------------------------------------------------
    // Write methods
    // -----------------------------------------------------------------------

    public function create(array $data): int
    {
        $data['password']   = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return (int) $this->db->insert_id();
    }

    public function update(int $id, array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete(int $id): bool
    {
        return $this->db->where('id', $id)->delete($this->table);
    }

    // -----------------------------------------------------------------------
    // Auth
    // -----------------------------------------------------------------------

    public function verify_password(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }
    public function generate_token(int $user_id): string
    {
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 86400);
        $this->db->where('id', $user_id);
        $this->db->update($this->table, [
            'api_token'        => $token,
            'token_expires_at' => $expires,
        ]);
        return $token;
    }

    public function email_exists(string $email, int $exclude_id = 0): bool
    {
        $this->db->where('email', $email);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }
}
