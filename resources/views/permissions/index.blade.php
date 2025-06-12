@extends('layout')

@section('content')
<div class="container mt-4">
    <h2>Manage Permissions</h2>

    {{-- Create Permission --}}
    <form method="POST" action="{{ route('permissions.store') }}" class="mb-4">
        @csrf
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="Permission Name" required>
            <button class="btn btn-primary">Create Permission</button>
        </div>
    </form>

    {{-- Display Permissions --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Permission Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $index => $permission)
            <tr id="permission-{{ $permission->id }}">
                <td>{{ $index + 1 }}</td>
                <td class="perm-name">{{ $permission->name }}</td>
                <td>
                    <button class="btn btn-sm btn-warning editBtn" data-id="{{ $permission->id }}">Edit</button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $permission->id }}">Delete</button>
                </td>
            </tr>
            @endforeach

            @if($permissions->isEmpty())
            <tr>
                <td colspan="3" class="text-center">No permissions available.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editPermissionForm">
        @csrf
        @method('PUT')
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editPermissionId">
                <div class="mb-3">
                    <label for="editPermissionName" class="form-label">Permission Name</label>
                    <input type="text" class="form-control" id="editPermissionName" name="name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    // Delete permission
    $('.deleteBtn').click(function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to delete this permission?')) {
            $.ajax({
                url: '/permissions/' + id,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(res) {
                    if (res.success) {
                        $('#permission-' + id).remove();
                    }
                }
            });
        }
    });

    // Open Edit Modal
    $('.editBtn').click(function() {
        let id = $(this).data('id');
        $.get('/permissions/' + id, function(data) {
            $('#editPermissionId').val(data.id);
            $('#editPermissionName').val(data.name);
            $('#editPermissionModal').modal('show');
        });
    });

    // Update permission
    $('#editPermissionForm').submit(function(e) {
        e.preventDefault();
        let id = $('#editPermissionId').val();
        let name = $('#editPermissionName').val();
        $.ajax({
            url: '/permissions/' + id,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                name: name
            },
            success: function(res) {
                if (res.success) {
                    $('#permission-' + id + ' .perm-name').text(name);
                    $('#editPermissionModal').modal('hide');
                }
            }
        });
    });
});
</script>
@endsection
