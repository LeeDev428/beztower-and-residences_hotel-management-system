@extends('layouts.admin')

@section('title', 'Edit Room')
@section('page-title', 'Edit Room #' . $room->room_number)

@section('content')
<div style="margin-bottom: 1.5rem;">
    <x-admin.button type="outline" href="{{ route('admin.rooms.index') }}">← Back to Rooms</x-admin.button>
</div>

<x-admin.card title="Room Information">
    <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Room Number *</label>
                <input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                @error('room_number')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Room Type *</label>
                <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
                    <select name="room_type_id" required style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                        <option value="">Select Type</option>
                        @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ old('room_type_id', $room->room_type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} - ₱{{ number_format($type->base_price, 2) }}
                            @if($type->discount_percentage > 0)
                                ({{ $type->discount_percentage }}% OFF)
                            @endif
                        </option>
                        @endforeach
                    </select>
                    <button type="button" onclick="openRoomTypesModal()" class="btn-view-details">View Details</button>
                </div>
                @error('room_type_id')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Floor *</label>
                <input type="number" name="floor" value="{{ old('floor', $room->floor) }}" required min="1" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                @error('floor')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Discount Percentage</label>
                <input type="number" name="discount_percentage" value="{{ old('discount_percentage', $room->discount_percentage ?? 0) }}" min="0" max="100" step="5" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                <small style="color: #666; font-size: 0.875rem;">Must be divisible by 5 (e.g., 0%, 5%, 10%, 15%)</small>
                @error('discount_percentage')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Status *</label>
                <select name="status" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                    <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                    <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
                @error('status')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <label style="font-weight: 600;">Amenities</label>
                <button type="button" onclick="openAmenitiesModal()" class="btn-view-details">View Details</button>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; padding: 1rem; background: var(--light-gray); border-radius: 8px;">
                @foreach($amenities as $amenity)
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" 
                        {{ in_array($amenity->id, old('amenities', $room->amenities->pluck('id')->toArray())) ? 'checked' : '' }} 
                        style="cursor: pointer;">
                    <span>{{ $amenity->name }}</span>
                </label>
                @endforeach
            </div>
            @error('amenities')
            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Description</label>
            <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">{{ old('description', $room->description) }}</textarea>
            @error('description')
            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Existing Photos -->
        @if($room->photos->count() > 0)
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Current Photos</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
                @foreach($room->photos as $photo)
                <div style="position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Room Photo" 
                         style="width: 100%; height: 150px; object-fit: cover;">
                    <button type="button" onclick="deletePhoto({{ $photo->id }})" 
                            style="position: absolute; top: 8px; right: 8px; background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; font-weight: bold; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                        ×
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Add New Photos -->
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Add New Photos</label>
            <input type="file" name="photos[]" id="photos" accept="image/*" multiple 
                   onchange="previewImages(event)"
                   style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            <div style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem;">
                Upload multiple images (Max 5MB per image)
            </div>
            @error('photos.*')
            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
            
            <div id="imagePreview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem; margin-top: 1rem;"></div>
        </div>

        <div style="display: flex; gap: 1rem;">
            <x-admin.button type="primary">Update Room</x-admin.button>
            <x-admin.button type="outline" href="{{ route('admin.rooms.index') }}">Cancel</x-admin.button>
        </div>
    </form>
</x-admin.card>

<!-- Room Types Modal -->
<div id="roomTypesModal" class="modal-overlay" style="display: none;">
    <div class="modal-content-large">
        <div class="modal-header">
            <h3>Room Types Management</h3>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" onclick="openAddRoomTypeForm()" class="btn-add">+ Add Room Type</button>
                <button type="button" onclick="closeRoomTypesModal()" class="btn-close">&times;</button>
            </div>
        </div>
        <div style="padding: 0 1.5rem;">
            <div style="display: flex; gap: 1rem; border-bottom: 2px solid #e5e5e5;">
                <button onclick="loadRoomTypes(false)" id="activeRoomTypesTab" style="padding: 0.75rem 1.5rem; background: none; border: none; border-bottom: 3px solid #d4af37; font-weight: 600; cursor: pointer; color: #2c2c2c;">Active</button>
                <button onclick="loadRoomTypes(true)" id="archivedRoomTypesTab" style="padding: 0.75rem 1.5rem; background: none; border: none; border-bottom: 3px solid transparent; font-weight: 600; cursor: pointer; color: #666;">Archived</button>
            </div>
        </div>
        <div class="modal-body">
            <div id="roomTypesTableContainer">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Base Price</th>
                            <th>Max Guests</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="roomTypesTableBody">
                        <!-- Will be populated via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Amenities Modal -->
<div id="amenitiesModal" class="modal-overlay" style="display: none;">
    <div class="modal-content-large">
        <div class="modal-header">
            <h3>Amenities Management</h3>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" onclick="openAddAmenityForm()" class="btn-add">+ Add Amenity</button>
                <button type="button" onclick="closeAmenitiesModal()" class="btn-close">&times;</button>
            </div>
        </div>
        <div style="padding: 0 1.5rem;">
            <div style="display: flex; gap: 1rem; border-bottom: 2px solid #e5e5e5;">
                <button onclick="loadAmenities(false)" id="activeAmenitiesTab" style="padding: 0.75rem 1.5rem; background: none; border: none; border-bottom: 3px solid #d4af37; font-weight: 600; cursor: pointer; color: #2c2c2c;">Active</button>
                <button onclick="loadAmenities(true)" id="archivedAmenitiesTab" style="padding: 0.75rem 1.5rem; background: none; border: none; border-bottom: 3px solid transparent; font-weight: 600; cursor: pointer; color: #666;">Archived</button>
            </div>
        </div>
        <div class="modal-body">
            <div id="amenitiesTableContainer">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Icon</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="amenitiesTableBody">
                        <!-- Will be populated via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Room Type Form Modal -->
<div id="roomTypeFormModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="roomTypeFormTitle">Add Room Type</h3>
            <button type="button" onclick="closeRoomTypeFormModal()" class="btn-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="roomTypeForm" onsubmit="saveRoomType(event)">
                <input type="hidden" id="roomTypeId" name="id">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" id="roomTypeName" name="name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="roomTypeDescription" name="description" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Base Price *</label>
                    <input type="number" id="roomTypeBasePrice" name="base_price" step="0.01" min="0" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Max Guests *</label>
                    <input type="number" id="roomTypeMaxGuests" name="max_guests" min="1" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Bed Type *</label>
                    <input type="text" id="roomTypeBedType" name="bed_type" required class="form-control" placeholder="e.g., King, Queen, Twin">
                </div>
                <div class="form-group">
                    <label>Size (sqm) *</label>
                    <input type="number" id="roomTypeSize" name="size_sqm" step="0.01" min="0" required class="form-control" placeholder="e.g., 25.5">
                </div>
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                    <button type="submit" class="btn-primary">Save</button>
                    <button type="button" onclick="closeRoomTypeFormModal()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add/Edit Amenity Form Modal -->
<div id="amenityFormModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="amenityFormTitle">Add Amenity</h3>
            <button type="button" onclick="closeAmenityFormModal()" class="btn-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="amenityForm" onsubmit="saveAmenity(event)">
                <input type="hidden" id="amenityId" name="id">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" id="amenityName" name="name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Icon (Font Awesome class)</label>
                    <input type="text" id="amenityIcon" name="icon" placeholder="fa-wifi" class="form-control">
                    <small style="color: #666;">Example: fa-wifi, fa-tv, fa-parking</small>
                </div>
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                    <button type="submit" class="btn-primary">Save</button>
                    <button type="button" onclick="closeAmenityFormModal()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-view-details {
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #d4af37, #f4e4c1);
        color: #2c2c2c;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-view-details:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(212, 175, 55, 0.3);
    }
    
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        overflow-y: auto;
        padding: 2rem;
    }
    
    .modal-content-large {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 1000px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }
    
    .modal-content {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }
    
    .modal-header {
        padding: 1.5rem;
        border-bottom: 2px solid #e5e5e5;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-header h3 {
        margin: 0;
        color: #2c2c2c;
        font-size: 1.5rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .btn-add {
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-add:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }
    
    .btn-close {
        font-size: 2rem;
        background: none;
        border: none;
        cursor: pointer;
        color: #666;
        line-height: 1;
        padding: 0;
        width: 32px;
        height: 32px;
    }
    
    .btn-close:hover {
        color: #d4af37;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    
    .data-table th {
        background: #f8f8f8;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #e5e5e5;
    }
    
    .data-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e5e5;
    }
    
    .data-table tr:hover {
        background: #f8f8f8;
    }
    
    .btn-edit, .btn-archive, .btn-restore {
        padding: 0.25rem 0.75rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        margin-right: 0.25rem;
    }
    
    .btn-edit {
        background: #007bff;
        color: white;
    }
    
    .btn-edit:hover {
        background: #0056b3;
    }
    
    .btn-archive {
        background: #ffc107;
        color: #2c2c2c;
    }
    
    .btn-archive:hover {
        background: #e0a800;
    }
    
    .btn-restore {
        background: #28a745;
        color: white;
    }
    
    .btn-restore:hover {
        background: #218838;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #2c2c2c;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        font-size: 1rem;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #d4af37;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
    }
    
    .btn-primary {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #d4af37, #f4e4c1);
        color: #2c2c2c;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(212, 175, 55, 0.3);
    }
    
    .btn-secondary {
        padding: 0.75rem 1.5rem;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    .discount-badge {
        background: #dc3545;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }
</style>

<script>
    // Image Preview Function
    function previewImages(event) {
        const previewContainer = document.getElementById('imagePreview');
        previewContainer.innerHTML = '';
        
        const files = event.target.files;
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.cssText = 'position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width: 100%; height: 120px; object-fit: cover;';
                
                div.appendChild(img);
                previewContainer.appendChild(div);
            };
            
            reader.readAsDataURL(file);
        }
    }

    // Delete Photo Function
    async function deletePhoto(photoId) {
        if (!confirm('Are you sure you want to delete this photo?')) return;
        
        try {
            const response = await fetch(`/admin/rooms/{{ $room->id }}/photos/${photoId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '{{ csrf_token() }}'
                }
            });
            
            if (response.ok) {
                location.reload();
            } else {
                alert('Failed to delete photo');
            }
        } catch (error) {
            console.error('Error deleting photo:', error);
            alert('Failed to delete photo');
        }
    }

    // Room Types Management
    function openRoomTypesModal() {
        document.getElementById('roomTypesModal').style.display = 'flex';
        loadRoomTypes();
    }
    
    function closeRoomTypesModal() {
        document.getElementById('roomTypesModal').style.display = 'none';
    }
    
    function openAmenitiesModal() {
        document.getElementById('amenitiesModal').style.display = 'flex';
        loadAmenities();
    }
    
    function closeAmenitiesModal() {
        document.getElementById('amenitiesModal').style.display = 'none';
    }
    
    function openAddRoomTypeForm() {
        document.getElementById('roomTypeFormTitle').textContent = 'Add Room Type';
        document.getElementById('roomTypeForm').reset();
        document.getElementById('roomTypeId').value = '';
        document.getElementById('roomTypeFormModal').style.display = 'flex';
    }
    
    function closeRoomTypeFormModal() {
        document.getElementById('roomTypeFormModal').style.display = 'none';
    }
    
    function openAddAmenityForm() {
        document.getElementById('amenityFormTitle').textContent = 'Add Amenity';
        document.getElementById('amenityForm').reset();
        document.getElementById('amenityId').value = '';
        document.getElementById('amenityFormModal').style.display = 'flex';
    }
    
    function closeAmenityFormModal() {
        document.getElementById('amenityFormModal').style.display = 'none';
    }
    
    async function loadRoomTypes(showArchived = false) {
        try {
            const url = showArchived ? '/admin/room-types?archived=yes' : '/admin/room-types';
            const response = await fetch(url);
            const data = await response.json();
            
            // Update tab styles
            document.getElementById('activeRoomTypesTab').style.borderBottomColor = showArchived ? 'transparent' : '#d4af37';
            document.getElementById('activeRoomTypesTab').style.color = showArchived ? '#666' : '#2c2c2c';
            document.getElementById('archivedRoomTypesTab').style.borderBottomColor = showArchived ? '#d4af37' : 'transparent';
            document.getElementById('archivedRoomTypesTab').style.color = showArchived ? '#2c2c2c' : '#666';
            
            const tbody = document.getElementById('roomTypesTableBody');
            tbody.innerHTML = '';
            
            data.roomTypes.forEach(type => {
                    
                const row = `
                    <tr>
                        <td>${type.name}</td>
                        <td>₱${parseFloat(type.base_price).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td>${type.max_guests}</td>
                        <td>
                            <button class="btn-edit" onclick="editRoomType(${type.id})">Edit</button>
                            ${type.archived_at 
                                ? `<button class="btn-restore" onclick="restoreRoomType(${type.id})">Restore</button>`
                                : `<button class="btn-archive" onclick="archiveRoomType(${type.id})">Archive</button>`
                            }
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } catch (error) {
            console.error('Error loading room types:', error);
            alert('Failed to load room types');
        }
    }
    
    async function loadAmenities(showArchived = false) {
        try {
            const url = showArchived ? '/admin/amenities?archived=yes' : '/admin/amenities';
            const response = await fetch(url);
            const data = await response.json();
            
            // Update tab styles
            document.getElementById('activeAmenitiesTab').style.borderBottomColor = showArchived ? 'transparent' : '#d4af37';
            document.getElementById('activeAmenitiesTab').style.color = showArchived ? '#666' : '#2c2c2c';
            document.getElementById('archivedAmenitiesTab').style.borderBottomColor = showArchived ? '#d4af37' : 'transparent';
            document.getElementById('archivedAmenitiesTab').style.color = showArchived ? '#2c2c2c' : '#666';
            
            const tbody = document.getElementById('amenitiesTableBody');
            tbody.innerHTML = '';
            
            data.amenities.forEach(amenity => {
                const row = `
                    <tr>
                        <td>${amenity.name}</td>
                        <td>${amenity.icon || '-'}</td>
                        <td>
                            <button class="btn-edit" onclick="editAmenity(${amenity.id})">Edit</button>
                            ${amenity.archived_at 
                                ? `<button class="btn-restore" onclick="restoreAmenity(${amenity.id})">Restore</button>`
                                : `<button class="btn-archive" onclick="archiveAmenity(${amenity.id})">Archive</button>`
                            }
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } catch (error) {
            console.error('Error loading amenities:', error);
            alert('Failed to load amenities');
        }
    }
    
    async function saveRoomType(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const id = formData.get('id');
        const data = Object.fromEntries(formData.entries());
        delete data.id;
        
        try {
            const url = id ? `/admin/room-types/${id}` : '/admin/room-types';
            const method = id ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (response.ok) {
                alert(result.message);
                closeRoomTypeFormModal();
                loadRoomTypes();
                // Reload page to update dropdown
                location.reload();
            } else {
                alert(result.error || 'Failed to save room type');
            }
        } catch (error) {
            console.error('Error saving room type:', error);
            alert('Failed to save room type');
        }
    }
    
    async function saveAmenity(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const id = formData.get('id');
        const data = Object.fromEntries(formData.entries());
        delete data.id;
        
        try {
            const url = id ? `/admin/amenities/${id}` : '/admin/amenities';
            const method = id ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (response.ok) {
                alert(result.message);
                closeAmenityFormModal();
                loadAmenities();
                // Reload page to update checkboxes
                location.reload();
            } else {
                alert(result.error || 'Failed to save amenity');
            }
        } catch (error) {
            console.error('Error saving amenity:', error);
            alert('Failed to save amenity');
        }
    }
    
    async function editRoomType(id) {
        try {
            const response = await fetch('/admin/room-types');
            const data = await response.json();
            const roomType = data.roomTypes.find(rt => rt.id === id);
            
            if (roomType) {
                document.getElementById('roomTypeFormTitle').textContent = 'Edit Room Type';
                document.getElementById('roomTypeId').value = roomType.id;
                document.getElementById('roomTypeName').value = roomType.name;
                document.getElementById('roomTypeDescription').value = roomType.description || '';
                document.getElementById('roomTypeBasePrice').value = roomType.base_price;
                document.getElementById('roomTypeMaxGuests').value = roomType.max_guests;
                document.getElementById('roomTypeBedType').value = roomType.bed_type || '';
                document.getElementById('roomTypeSize').value = roomType.size_sqm || '';
                document.getElementById('roomTypeFormModal').style.display = 'flex';
            }
        } catch (error) {
            console.error('Error loading room type:', error);
            alert('Failed to load room type');
        }
    }
    
    async function editAmenity(id) {
        try {
            const response = await fetch('/admin/amenities');
            const data = await response.json();
            const amenity = data.amenities.find(a => a.id === id);
            
            if (amenity) {
                document.getElementById('amenityFormTitle').textContent = 'Edit Amenity';
                document.getElementById('amenityId').value = amenity.id;
                document.getElementById('amenityName').value = amenity.name;
                document.getElementById('amenityIcon').value = amenity.icon || '';
                document.getElementById('amenityFormModal').style.display = 'flex';
            }
        } catch (error) {
            console.error('Error loading amenity:', error);
            alert('Failed to load amenity');
        }
    }
    
    async function archiveRoomType(id) {
        if (!confirm('Are you sure you want to archive this room type?')) return;
        
        try {
            const response = await fetch(`/admin/room-types/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            alert(result.message);
            loadRoomTypes();
        } catch (error) {
            console.error('Error archiving room type:', error);
            alert('Failed to archive room type');
        }
    }
    
    async function restoreRoomType(id) {
        try {
            const response = await fetch(`/admin/room-types/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            alert(result.message);
            loadRoomTypes();
            location.reload();
        } catch (error) {
            console.error('Error restoring room type:', error);
            alert('Failed to restore room type');
        }
    }
    
    async function archiveAmenity(id) {
        if (!confirm('Are you sure you want to archive this amenity?')) return;
        
        try {
            const response = await fetch(`/admin/amenities/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            alert(result.message);
            loadAmenities();
        } catch (error) {
            console.error('Error archiving amenity:', error);
            alert('Failed to archive amenity');
        }
    }
    
    async function restoreAmenity(id) {
        try {
            const response = await fetch(`/admin/amenities/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            alert(result.message);
            loadAmenities();
            location.reload();
        } catch (error) {
            console.error('Error restoring amenity:', error);
            alert('Failed to restore amenity');
        }
    }
</script>

@endsection
