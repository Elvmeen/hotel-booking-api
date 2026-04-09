<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bookings Controller
 *
 * GET    /api/bookings        - List bookings (admin: all, guest: own)
 * GET    /api/bookings/{id}   - Get booking
 * POST   /api/bookings        - Create booking (authenticated)
 * PUT    /api/bookings/{id}   - Update booking status (admin)
 * DELETE /api/bookings/{id}   - Cancel/delete booking
 */
class Bookings extends Base_api
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Booking_model');
        $this->load->model('Room_model');
    }

    // GET /api/bookings
    public function index(): void
    {
        $this->require_auth();

        $page     = max(1, (int) ($this->input->get('page') ?? 1));
        $per_page = min(100, max(1, (int) ($this->input->get('per_page') ?? 15)));

        $filters = [];

        // Guests only see their own bookings
        if ($this->auth_user['role'] !== 'admin') {
            $filters['user_id'] = $this->auth_user['id'];
        } else {
            if ($this->input->get('user_id'))  $filters['user_id']  = (int) $this->input->get('user_id');
            if ($this->input->get('room_id'))  $filters['room_id']  = (int) $this->input->get('room_id');
            if ($this->input->get('from'))     $filters['from']     = $this->input->get('from');
            if ($this->input->get('to'))       $filters['to']       = $this->input->get('to');
            if ($this->input->get('search'))   $filters['search']   = $this->input->get('search');
        }

        if ($this->input->get('status')) $filters['status'] = $this->input->get('status');

        $bookings = $this->Booking_model->all($page, $per_page, $filters);
        $total    = $this->Booking_model->count($filters);

        $this->respond_success([
            'bookings'   => $bookings,
            'pagination' => [
                'page'      => $page,
                'per_page'  => $per_page,
                'total'     => $total,
                'last_page' => (int) ceil($total / $per_page),
            ],
        ]);
    }

    // GET /api/bookings/{id}
    public function show(int $id): void
    {
        $this->require_auth();

        $booking = $this->Booking_model->find($id);
        if (!$booking) {
            $this->respond_not_found('Booking not found');
        }

        // Guests can only see their own booking
        if ($this->auth_user['role'] !== 'admin' && (int) $booking['user_id'] !== (int) $this->auth_user['id']) {
            $this->respond_forbidden();
        }

        $this->respond_success($booking);
    }

    // POST /api/bookings
    public function create(): void
    {
        $this->require_auth();

        $data   = $this->get_json_body();
        $errors = $this->_validate_create($data);
        if ($errors) {
            $this->respond_validation_error($errors);
        }

        $room = $this->Room_model->find((int) $data['room_id']);
        if (!$room || $room['status'] !== 'active') {
            $this->respond_error('Room is not available');
        }

        if (!$this->Booking_model->is_room_available($data['room_id'], $data['check_in'], $data['check_out'])) {
            $this->respond_error('Room is not available for the selected dates', 409);
        }

        $nights     = (int) round((strtotime($data['check_out']) - strtotime($data['check_in'])) / 86400);
        $total      = $nights * (float) $room['price_per_night'];

        $id      = $this->Booking_model->create([
            'user_id'      => $this->auth_user['id'],
            'room_id'      => (int) $data['room_id'],
            'check_in'     => $data['check_in'],
            'check_out'    => $data['check_out'],
            'nights'       => $nights,
            'guests'       => (int) ($data['guests'] ?? 1),
            'total_price'  => $total,
            'status'       => 'pending',
            'special_requests' => $data['special_requests'] ?? '',
        ]);

        $booking = $this->Booking_model->find($id);
        $this->respond_created($booking, 'Booking created successfully');
    }

    // PUT /api/bookings/{id}
    public function update(int $id): void
    {
        $this->require_auth();

        $booking = $this->Booking_model->find($id);
        if (!$booking) {
            $this->respond_not_found('Booking not found');
        }

        // Guests can only cancel their own pending booking
        if ($this->auth_user['role'] !== 'admin') {
            if ((int) $booking['user_id'] !== (int) $this->auth_user['id']) {
                $this->respond_forbidden();
            }
        }

        $data = $this->get_json_body();

        $allowed_statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        if ($this->auth_user['role'] !== 'admin') {
            $allowed_statuses = ['cancelled'];
        }

        $update = [];

        if (isset($data['status'])) {
            if (!in_array($data['status'], $allowed_statuses, true)) {
                $this->respond_error('Invalid or unauthorized status change');
            }
            $update['status'] = $data['status'];
        }

        if ($this->auth_user['role'] === 'admin') {
            if (isset($data['special_requests'])) $update['special_requests'] = $data['special_requests'];
            if (isset($data['notes']))            $update['notes'] = $data['notes'];
        }

        if (empty($update)) {
            $this->respond_error('No valid fields provided');
        }

        $this->Booking_model->update($id, $update);
        $this->respond_success($this->Booking_model->find($id), 'Booking updated successfully');
    }

    // DELETE /api/bookings/{id}
    public function delete(int $id): void
    {
        $this->require_admin();

        $booking = $this->Booking_model->find($id);
        if (!$booking) {
            $this->respond_not_found('Booking not found');
        }

        $this->Booking_model->delete($id);
        $this->respond_success(null, 'Booking deleted successfully');
    }

    // -----------------------------------------------------------------------
    // Internals
    // -----------------------------------------------------------------------

    private function _validate_create(array $data): array
    {
        $errors = [];

        if (empty($data['room_id'])) {
            $errors['room_id'] = 'Room ID is required';
        }

        if (empty($data['check_in'])) {
            $errors['check_in'] = 'Check-in date is required';
        } elseif (strtotime($data['check_in']) < strtotime('today')) {
            $errors['check_in'] = 'Check-in date cannot be in the past';
        }

        if (empty($data['check_out'])) {
            $errors['check_out'] = 'Check-out date is required';
        }

        if (!empty($data['check_in']) && !empty($data['check_out'])) {
            if (strtotime($data['check_in']) >= strtotime($data['check_out'])) {
                $errors['check_out'] = 'Check-out must be after check-in';
            }
        }

        if (isset($data['guests']) && $data['guests'] < 1) {
            $errors['guests'] = 'At least one guest is required';
        }

        return $errors;
    }
}
