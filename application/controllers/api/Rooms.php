<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rooms Controller
 *
 * GET    /api/rooms           - List rooms (public)
 * GET    /api/rooms/{id}      - Get room (public)
 * GET    /api/rooms/available - Available rooms for dates (public)
 * POST   /api/rooms           - Create room (admin)
 * PUT    /api/rooms/{id}      - Update room (admin)
 * DELETE /api/rooms/{id}      - Delete room (admin)
 */
class Rooms extends Base_api
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Room_model');
    }

    // GET /api/rooms
    public function index(): void
    {
        $page     = max(1, (int) ($this->input->get('page') ?? 1));
        $per_page = min(100, max(1, (int) ($this->input->get('per_page') ?? 15)));

        $filters = [
            'type'      => $this->input->get('type')      ?? '',
            'status'    => $this->input->get('status')    ?? '',
            'min_price' => $this->input->get('min_price') ?? '',
            'max_price' => $this->input->get('max_price') ?? '',
            'capacity'  => $this->input->get('capacity')  ?? '',
            'search'    => $this->input->get('search')    ?? '',
        ];

        $rooms = $this->Room_model->all($page, $per_page, $filters);
        $total = $this->Room_model->count($filters);

        $this->respond_success([
            'rooms'      => $rooms,
            'pagination' => [
                'page'       => $page,
                'per_page'   => $per_page,
                'total'      => $total,
                'last_page'  => (int) ceil($total / $per_page),
            ],
        ]);
    }

    // GET /api/rooms/{id}
    public function show(int $id): void
    {
        $room = $this->Room_model->find($id);
        if (!$room) {
            $this->respond_not_found('Room not found');
        }
        $this->respond_success($room);
    }

    // GET /api/rooms/available?check_in=&check_out=&type=
    public function available(): void
    {
        $check_in  = $this->input->get('check_in')  ?? '';
        $check_out = $this->input->get('check_out') ?? '';
        $type      = $this->input->get('type')      ?? '';

        if (empty($check_in) || empty($check_out)) {
            $this->respond_validation_error([
                'check_in'  => empty($check_in)  ? 'check_in date is required'  : null,
                'check_out' => empty($check_out) ? 'check_out date is required' : null,
            ]);
        }

        if (strtotime($check_in) >= strtotime($check_out)) {
            $this->respond_error('check_out must be after check_in');
        }

        $rooms = $this->Room_model->available($check_in, $check_out, $type);
        $this->respond_success(['rooms' => $rooms, 'check_in' => $check_in, 'check_out' => $check_out]);
    }

    // POST /api/rooms
    public function create(): void
    {
        $this->require_admin();
        $data   = $this->get_json_body();
        $errors = $this->_validate($data);

        if ($errors) {
            $this->respond_validation_error($errors);
        }

        if ($this->Room_model->room_number_exists($data['room_number'])) {
            $this->respond_error('Room number already exists', 409);
        }

        $id   = $this->Room_model->create($this->_sanitize($data));
        $room = $this->Room_model->find($id);
        $this->respond_created($room, 'Room created successfully');
    }

    // PUT /api/rooms/{id}
    public function update(int $id): void
    {
        $this->require_admin();

        $room = $this->Room_model->find($id);
        if (!$room) {
            $this->respond_not_found('Room not found');
        }

        $data = $this->get_json_body();

        if (isset($data['room_number']) && $data['room_number'] !== $room['room_number']) {
            if ($this->Room_model->room_number_exists($data['room_number'], $id)) {
                $this->respond_error('Room number already exists', 409);
            }
        }

        $allowed = ['room_number', 'type', 'floor', 'capacity', 'price_per_night', 'description', 'amenities', 'status', 'image_url'];
        $update  = array_intersect_key($data, array_flip($allowed));

        if (empty($update)) {
            $this->respond_error('No valid fields provided for update');
        }

        $this->Room_model->update($id, $update);
        $this->respond_success($this->Room_model->find($id), 'Room updated successfully');
    }

    // DELETE /api/rooms/{id}
    public function delete(int $id): void
    {
        $this->require_admin();

        $room = $this->Room_model->find($id);
        if (!$room) {
            $this->respond_not_found('Room not found');
        }

        $this->Room_model->delete($id);
        $this->respond_success(null, 'Room deleted successfully');
    }

    // -----------------------------------------------------------------------
    // Internals
    // -----------------------------------------------------------------------

    private function _validate(array $data, bool $require_all = true): array
    {
        $errors = [];

        if ($require_all && empty($data['room_number'])) {
            $errors['room_number'] = 'Room number is required';
        }

        if ($require_all && empty($data['type'])) {
            $errors['type'] = 'Room type is required';
        } elseif (!empty($data['type']) && !in_array($data['type'], ['single', 'double', 'suite', 'deluxe', 'presidential'], true)) {
            $errors['type'] = 'Invalid room type';
        }

        if ($require_all && empty($data['price_per_night'])) {
            $errors['price_per_night'] = 'Price per night is required';
        } elseif (isset($data['price_per_night']) && (!is_numeric($data['price_per_night']) || $data['price_per_night'] <= 0)) {
            $errors['price_per_night'] = 'Price must be a positive number';
        }

        if (isset($data['capacity']) && (!is_numeric($data['capacity']) || $data['capacity'] < 1)) {
            $errors['capacity'] = 'Capacity must be at least 1';
        }

        return $errors;
    }

    private function _sanitize(array $data): array
    {
        return [
            'room_number'    => trim($data['room_number']),
            'type'           => $data['type'],
            'floor'          => (int) ($data['floor'] ?? 1),
            'capacity'       => (int) ($data['capacity'] ?? 2),
            'price_per_night' => (float) $data['price_per_night'],
            'description'    => $data['description'] ?? '',
            'amenities'      => $data['amenities']   ?? '',
            'status'         => $data['status']      ?? 'active',
            'image_url'      => $data['image_url']   ?? '',
        ];
    }
}
