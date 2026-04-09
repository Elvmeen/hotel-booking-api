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
        $this->_apply_filters($filters);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($per_page, $offset);
        return $this->db->get($this->table)->result_array();
    }

    public function count(array $filters = []): int
    {
        $this->_apply_filters($filters);
        return $this->db->count_all_results($this->table);
    }

    public function find(int $id): ?array
    {
        $row = $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row_array();
        return $row ?: null;
    }

    /**
     * Return rooms not booked between check_in and check_out.
     */
    public function available(string $check_in, string $check_out, string $type = ''): array
    {
        $this->db->select('r.*');
        $this->db->from($this->table . ' r');
        $this->db->where('r.status', 'active');

        // Exclude rooms with overlapping confirmed/pending bookings
        $sub = $this->db->subquery(); // CI3 doesn't support native subqueries well, use string
        $this->db->where("r.id NOT IN (
            SELECT b.room_id FROM bookings b
            WHERE b.status IN ('confirmed','pending')
              AND b.check_in  < '{$check_out}'
              AND b.check_out > '{$check_in}'
        )", NULL, FALSE);

        if ($type !== '') {
            $this->db->where('r.type', $type);
        }

        $this->db->order_by('r.price_per_night', 'ASC');
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
            $this->db->where('type', $filters['type']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        if (!empty($filters['min_price'])) {
            $this->db->where('price_per_night >=', (float) $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $this->db->where('price_per_night <=', (float) $filters['max_price']);
        }
        if (!empty($filters['capacity'])) {
            $this->db->where('capacity >=', (int) $filters['capacity']);
        }
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('room_number', $filters['search']);
            $this->db->or_like('description', $filters['search']);
            $this->db->group_end();
        }
    }
}
