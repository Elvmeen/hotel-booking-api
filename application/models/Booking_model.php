<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model
{
    protected string $table = 'bookings';

    // -----------------------------------------------------------------------
    // Query methods
    // -----------------------------------------------------------------------

    public function all(int $page = 1, int $per_page = 15, array $filters = []): array
    {
        $offset = ($page - 1) * $per_page;

        $this->db->select('b.*, u.name AS guest_name, u.email AS guest_email, r.room_number, rt.name AS room_type, rt.base_price AS price_per_night');
        $this->db->from($this->table . ' b');
        $this->db->join('users u',      'u.id = b.user_id',    'left');
        $this->db->join('rooms r',      'r.id = b.room_id',    'left');
        $this->db->join('room_types rt','rt.id = r.room_type_id', 'left');

        $this->_apply_filters($filters);

        $this->db->order_by('b.created_at', 'DESC');
        $this->db->limit($per_page, $offset);
        return $this->db->get()->result_array();
    }

    public function count(array $filters = []): int
    {
        $this->db->from($this->table . ' b');
        $this->db->join('users u',      'u.id = b.user_id',    'left');
        $this->db->join('rooms r',      'r.id = b.room_id',    'left');
        $this->db->join('room_types rt','rt.id = r.room_type_id', 'left');
        $this->_apply_filters($filters);
        return $this->db->count_all_results();
    }

    public function find(int $id): ?array
    {
        $row = $this->db
            ->select('b.*, u.name AS guest_name, u.email AS guest_email, r.room_number, rt.name AS room_type, rt.base_price AS price_per_night')
            ->from($this->table . ' b')
            ->join('users u',      'u.id = b.user_id',       'left')
            ->join('rooms r',      'r.id = b.room_id',       'left')
            ->join('room_types rt','rt.id = r.room_type_id', 'left')
            ->where('b.id', $id)
            ->get()
            ->row_array();
        return $row ?: null;
    }

    public function find_by_user(int $user_id, int $page = 1, int $per_page = 15): array
    {
        $offset = ($page - 1) * $per_page;
        return $this->db
            ->select('b.*, r.room_number, rt.name AS room_type, rt.base_price AS price_per_night')
            ->from($this->table . ' b')
            ->join('rooms r',      'r.id = b.room_id',       'left')
            ->join('room_types rt','rt.id = r.room_type_id', 'left')
            ->where('b.user_id', $user_id)
            ->order_by('b.created_at', 'DESC')
            ->limit($per_page, $offset)
            ->get()
            ->result_array();
    }

    public function is_room_available(int $room_id, string $check_in, string $check_out, int $exclude_id = 0): bool
    {
        $this->db->where('room_id', $room_id);
        $this->db->where_in('status', ['confirmed', 'pending']);
        $this->db->where('check_in <',  $check_out);
        $this->db->where('check_out >', $check_in);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) === 0;
    }

    // -----------------------------------------------------------------------
    // Stats (admin dashboard) — PostgreSQL-compatible date functions
    // -----------------------------------------------------------------------

    public function stats(): array
    {
        $today = date('Y-m-d');
        return [
            'total'     => $this->db->count_all($this->table),
            'confirmed' => $this->db->where('status', 'confirmed')->count_all_results($this->table),
            'pending'   => $this->db->where('status', 'pending')->count_all_results($this->table),
            'cancelled' => $this->db->where('status', 'cancelled')->count_all_results($this->table),
            'today'     => $this->db
                ->where("created_at::date = '{$today}'", NULL, FALSE)
                ->count_all_results($this->table),
        ];
    }

    public function revenue(string $period = 'month'): float
    {
        $where = match($period) {
            'today' => "check_in::date = CURRENT_DATE",
            'week'  => "check_in >= CURRENT_DATE - INTERVAL '7 days'",
            'year'  => "EXTRACT(YEAR FROM check_in) = EXTRACT(YEAR FROM CURRENT_DATE)",
            default => "DATE_TRUNC('month', check_in) = DATE_TRUNC('month', CURRENT_DATE)",
        };

        $row = $this->db
            ->select_sum('total_price', 'revenue')
            ->where('status', 'confirmed')
            ->where($where, NULL, FALSE)
            ->get($this->table)
            ->row_array();
        return (float) ($row['revenue'] ?? 0);
    }

    // -----------------------------------------------------------------------
    // Write methods
    // -----------------------------------------------------------------------

    public function create(array $data): int
    {
        $data['booking_reference'] = $this->_generate_reference();
        $data['created_at']        = date('Y-m-d H:i:s');
        $data['updated_at']        = date('Y-m-d H:i:s');
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
        if (!empty($filters['status'])) {
            $this->db->where('b.status', $filters['status']);
        }
        if (!empty($filters['user_id'])) {
            $this->db->where('b.user_id', (int) $filters['user_id']);
        }
        if (!empty($filters['room_id'])) {
            $this->db->where('b.room_id', (int) $filters['room_id']);
        }
        if (!empty($filters['from'])) {
            $this->db->where('b.check_in >=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $this->db->where('b.check_out <=', $filters['to']);
        }
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('b.booking_reference', $filters['search']);
            $this->db->or_like('u.name', $filters['search']);
            $this->db->or_like('u.email', $filters['search']);
            $this->db->group_end();
        }
    }

    private function _generate_reference(): string
    {
        do {
            $ref = 'HB-' . strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
        } while ($this->db->where('booking_reference', $ref)->count_all_results($this->table) > 0);
        return $ref;
    }
}
