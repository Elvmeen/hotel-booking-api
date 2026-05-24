<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Room_model extends CI_Model
{
    protected string $table = 'rooms';

    // -----------------------------------------------------------------------
    // Query methods
    // -----------------------------------------------------------------------

    public function all(int $page = 1, int $per_page = 15, array $filters = []): array
    {
        $offset = ($page - 1) * $per_page;
        $this->db->select('r.*, rt.name AS type, rt.base_price AS price_per_night, rt.capacity, rt.description AS type_description');
        $this->db->from($this->table . ' r');
        $this->db->join('room_types rt', 'rt.id = r.room_type_id', 'left');
        $this->_apply_filters($filters);
        $this->db->order_by('r.created_at', 'DESC');
        $this->db->limit($per_page, $offset);
        return $this->db->get()->result_array();
    }

    public function count(array $filters = []): int
    {
        $this->db->from($this->table . ' r');
        $this->db->join('room_types rt', 'rt.id = r.room_type_id', 'left');
        $this->_apply_filters($filters);
        return $this->db->count_all_results();
    }

    public function find(int $id): ?array
    {
        $row = $this->db
            ->select('r.*, rt.name AS type, rt.base_price AS price_per_night, rt.capacity, rt.description AS type_description')
            ->from($this->table . ' r')
            ->join('room_types rt', 'rt.id = r.room_type_id', 'left')
            ->where('r.id', $id)
            ->get()
            ->row_array();
        return $row ?: null;
    }

    /**
     * Return rooms not booked between check_in and check_out.
     * Uses a raw NOT IN subquery (safe — dates are validated by the controller).
     */
    public function available(string $check_in, string $check_out, string $type = ''): array
    {
        $this->db->select('r.*, rt.name AS type, rt.base_price AS price_per_night, rt.capacity');
        $this->db->from($this->table . ' r');
        $this->db->join('room_types rt', 'rt.id = r.room_type_id', 'left');
        $this->db->where('r.status', 'available');

        // Exclude rooms with overlapping confirmed/pending bookings
        $check_in_esc  = $this->db->escape($check_in);
        $check_out_esc = $this->db->escape($check_out);
        $this->db->where("r.id NOT IN (
            SELECT b.room_id FROM bookings b
            WHERE b.status IN ('confirmed','pending')
              AND b.check_in  < {$check_out_esc}
              AND b.check_out > {$check_in_esc}
        )", NULL, FALSE);

        if ($type !== '') {
            $this->db->where('rt.name', $type);
        }

        $this->db->order_by('rt.base_price', 'ASC');
        return $this->db->get()->result_array();
    }

    public function room_number_exists(string $room_number, int $exclude_id = 0): bool
    {
        $this->db->where('room_number', $room_number);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    // -----------------------------------------------------------------------
    // Write methods
    // -----------------------------------------------------------------------

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return (int) $this->db->insert_id();
    }

    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete(int $id): bool
    {
        return $this->db->where('id', $id)->delete($this->table);
    }

    // -----------------------------------------------------------------------
    // Internals
    // -----------------------------------------------------------------------

    private function _apply_filters(array $filters): void
    {
        if (!empty($filters['type'])) {
            $this->db->where('rt.name', $filters['type']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('r.status', $filters['status']);
        }
        if (!empty($filters['min_price'])) {
            $this->db->where('rt.base_price >=', (float) $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $this->db->where('rt.base_price <=', (float) $filters['max_price']);
        }
        if (!empty($filters['capacity'])) {
            $this->db->where('rt.capacity >=', (int) $filters['capacity']);
        }
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('r.room_number', $filters['search']);
            $this->db->or_like('rt.description', $filters['search']);
            $this->db->group_end();
        }
    }
}
