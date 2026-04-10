<div class="page-header">
    <h1>Rooms</h1>
    <button class="btn btn-primary" onclick="openModal('room-modal')">+ Add Room</button>
</div>

<?php if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<!-- Search -->
<form method="GET" action="<?= site_url('admin/rooms') ?>" class="search-bar">
    <input type="text" name="search" placeholder="Search rooms…" value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn btn-outline">Search</button>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Room No.</th>
                        <th>Type</th>
                        <th>Floor</th>
                        <th>Capacity</th>
                        <th>Price / Night</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($rooms)): ?>
                    <tr><td colspan="8" class="text-center text-muted">No rooms found</td></tr>
                <?php else: ?>
                    <?php foreach ($rooms as $i => $r): ?>
                    <tr>
                        <td><?= ((int)$page - 1) * 20 + $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($r['room_number']) ?></strong></td>
                        <td><?= ucfirst(htmlspecialchars($r['type'])) ?></td>
                        <td><?= $r['floor'] ?></td>
                        <td><?= $r['capacity'] ?></td>
                        <td>$<?= number_format($r['price_per_night'], 2) ?></td>
                        <td><span class="badge badge-<?= $r['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($r['status']) ?></span></td>
                        <td class="actions">
                            <button class="btn btn-sm btn-outline"
                                onclick='editRoom(<?= json_encode($r) ?>)'>Edit</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pages > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
        <a href="<?= site_url('admin/rooms?page=' . $p . '&search=' . urlencode($search)) ?>"
           class="page-btn <?= $p == $page ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Room Modal -->
<div id="room-modal" class="modal" style="display:none;">
    <div class="modal-overlay" onclick="closeModal('room-modal')"></div>
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="room-modal-title">Add Room</h3>
            <button class="modal-close" onclick="closeModal('room-modal')">&times;</button>
        </div>
        <form id="room-form" method="POST" action="">
            <div class="modal-body">
                <input type="hidden" id="room-id" name="id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Room Number *</label>
                        <input type="text" name="room_number" id="f-room_number" required>
                    </div>
                    <div class="form-group">
                        <label>Type *</label>
                        <select name="type" id="f-type" required>
                            <option value="">Select type</option>
                            <option value="single">Single</option>
                            <option value="double">Double</option>
                            <option value="suite">Suite</option>
                            <option value="deluxe">Deluxe</option>
                            <option value="presidential">Presidential</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Floor</label>
                        <input type="number" name="floor" id="f-floor" min="1" value="1">
                    </div>
                    <div class="form-group">
                        <label>Capacity</label>
                        <input type="number" name="capacity" id="f-capacity" min="1" value="2">
                    </div>
                </div>
                <div class="form-group">
                    <label>Price per Night (USD) *</label>
                    <input type="number" name="price_per_night" id="f-price_per_night" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="f-description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Amenities (comma-separated)</label>
                    <input type="text" name="amenities" id="f-amenities" placeholder="WiFi, TV, Mini-bar">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="f-status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('room-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="room-submit-btn">Save Room</button>
            </div>
        </form>
    </div>
</div>

<script>
function editRoom(room) {
    document.getElementById('room-modal-title').textContent = 'Edit Room #' + room.room_number;
    document.getElementById('room-id').value           = room.id;
    document.getElementById('f-room_number').value     = room.room_number;
    document.getElementById('f-type').value            = room.type;
    document.getElementById('f-floor').value           = room.floor;
    document.getElementById('f-capacity').value        = room.capacity;
    document.getElementById('f-price_per_night').value = room.price_per_night;
    document.getElementById('f-description').value     = room.description || '';
    document.getElementById('f-amenities').value       = room.amenities || '';
    document.getElementById('f-status').value          = room.status;
    openModal('room-modal');
}

document.getElementById('room-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id     = document.getElementById('room-id').value;
    const method = id ? 'PUT' : 'POST';
    const url    = id ? 'https://hotel-booking-api-1-zmcs.onrender.com/api/rooms/' + id : 'https://hotel-booking-api-1-zmcs.onrender.com/api/rooms';
    const token  = localStorage.getItem('admin_token');

    const body = {
        room_number:    document.getElementById('f-room_number').value,
        type:           document.getElementById('f-type').value,
        floor:          parseInt(document.getElementById('f-floor').value),
        capacity:       parseInt(document.getElementById('f-capacity').value),
        price_per_night: parseFloat(document.getElementById('f-price_per_night').value),
        description:    document.getElementById('f-description').value,
        amenities:      document.getElementById('f-amenities').value,
        status:         document.getElementById('f-status').value,
    };

    const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
        body: JSON.stringify(body),
    });

    if (res.ok) { location.reload(); }
    else {
        const data = await res.json();
        alert(data.message || 'An error occurred');
    }
});
</script>
