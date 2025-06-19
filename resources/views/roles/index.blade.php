@extends('layout')

@section('head')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
@endsection

@section('content')
<div class="container mt-4">
    <h2>Manage Roles</h2>

    {{-- Create Role --}}
    <form method="POST" action="{{ route('roles.store') }}" class="mb-4">
        @csrf
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="Role Name" required>
            <button class="btn btn-primary">Create Role</button>
        </div>
    </form>

    {{-- Roles --}}
    <table class="table table-bordered" id="rolesTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Role Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="roleTable">
            @foreach($roles as $index => $role)
            <tr id="role-row-{{ $role->id }}">
                <td>{{ $index + 1 }}</td>
                <td class="role-name">{{ $role->name }}</td>
                <td>
                    <button class="btn btn-sm btn-warning editRoleBtn" data-id="{{ $role->id }}" data-name="{{ $role->name }}">Edit</button>

                    <form class="d-inline deleteRoleForm" data-id="{{ $role->id }}">
                        {{-- @csrf
                        @method('DELETE') --}}
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach

            @if($roles->isEmpty())
            <tr>
                <td colspan="3" class="text-center">No roles available.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editRoleForm">
        {{-- @csrf
        @method('PUT') --}}
        <input type="hidden" id="editRoleId">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="name" id="editRoleName" class="form-control" placeholder="Role Name" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update Role</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    

<script>
$(document).ready(function() {

    // DataTables 
        $('#rolesTable').DataTable({
            "pageLength": 10,
            "columnDefs": [
                { "orderable": false, "targets": 2 } 
            ]
        });


    // Open modal and set values
    $('.editRoleBtn').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#editRoleId').val(id);
        $('#editRoleName').val(name);
        $('#editRoleModal').modal('show');
    });

    // Submit edit form via AJAX
    $('#editRoleForm').submit(function(e) {
        e.preventDefault();
        const id = $('#editRoleId').val();
        const name = $('#editRoleName').val();
        $.ajax({
            url: `/roles/${id}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                name: name
            },
            success: function(response) {
                if (response.success) {
                    const row = $(`#role-row-${id}`);
                    row.find('.role-name').text(name);
                    row.find('.editRoleBtn').data('name', name);
                    $('#editRoleModal').modal('hide');
                    alert('Role updated successfully');
                }
            },
            error: function(xhr) {
                alert('Update failed: ' + xhr.responseJSON.message);
            }
        });
    });

    // Delete via AJAX
    $('.deleteRoleForm').submit(function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this role?')) {
            $.ajax({
                url: `/roles/${id}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(response) {
                    if (response.success) {
                        $(`#role-row-${id}`).remove();
                        alert('Role deleted successfully');
                    }
                },
                error: function(xhr) {
                    alert('Delete failed: ' + xhr.responseJSON.message);
                }
            });
        }
    });
});
</script>
@endsection
