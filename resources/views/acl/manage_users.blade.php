@extends('layout')

@section('head')

 <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">

@endsection
@section('content')


<style>
.table-container {
    max-height: 400px;
    overflow-y: auto;
}
/* .fixed-table thead,
.fixed-table tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
}
.fixed-table thead {
    background-color: #f8f9fa;
    position: sticky;
    top: 0;
    z-index: 2;
}
.fixed-table tbody {
    display: block;
    height: 500px; /* fixed height 
    overflow-y: auto;
} */

</style>
<div class="container mt-4">
    <h3 class="mb-4">Manage Users</h3>

    {{-- Search box with clear button --}}
    {{-- <div class="mb-3 position-relative" style="max-width: 400px;">
        <input type="text" id="searchInput" class="form-control pe-5" placeholder="Search by name, email or role...">
        <button id="clearSearch" type="button" class="btn btn-sm btn-outline-secondary position-absolute end-0 top-0 mt-1 me-2 d-none" style="z-index: 10;">×</button>
    </div> --}}


    <div class="table-responsive">
        <table class="table table-bordered" id="aclTable">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr data-id="{{ $user->id }}">
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ optional($user->role)->name ?? 'No Role' }}</td>
                    <td>
                        <button class="btn btn-info btn-sm viewBtn" data-id="{{ $user->id }}">View</button>
                        <button class="btn btn-warning btn-sm editBtn" data-id="{{ $user->id }}">Edit</button>
                        <button class="btn btn-danger btn-sm deleteBtn" data-id="{{ $user->id }}">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
        </table>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">User Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="viewBody"></div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editUserForm">
            @csrf
            @method('POST')
            <input type="hidden" name="id" id="editId">

            <input type="text" name="name" id="editName" class="form-control mb-2" placeholder="Name" required>
            <input type="email" name="email" id="editEmail" class="form-control mb-2" placeholder="Email" required>
            {{-- <input type="password" name="password" class="form-control mb-2" placeholder="Password (leave blank to keep same)"> --}}
            <input type="password" name="password" id="editPassword" class="form-control mb-2" placeholder="Password (leave blank to keep same)" autocomplete="new-password">

            <select name="role_id" id="editRole" class="form-select mb-2" required>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>

            <button class="btn btn-primary w-100">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')

    <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

<!-- jQuery AJAX -->
<script>
$(document).ready(function() {
    // DataTables 
        $('#aclTable').DataTable({
            scrollY: '400px',
            scrollCollapse: true,
            paging: true,
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: 3 } 
            ]
        });


    // Setup global CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    // View User Details in modal
    $('.viewBtn').click(function() {
        let id = $(this).data('id');
        $.get('/admin/users/' + id, function(user) {
            let role = user.role?.name || 'No Role';
            let html = `
                <p><strong>ID:</strong> ${user.id}</p>
                <p><strong>Name:</strong> ${user.name}</p>
                <p><strong>Email:</strong> ${user.email}</p>
                <p><strong>Role:</strong> ${role}</p>
            `;
            $('#viewBody').html(html);
            $('#viewModal').modal('show');
        });
    });

    // Load user data into Edit Modal
    $('.editBtn').click(function() {
        let id = $(this).data('id');
        $.get('/admin/users/' + id, function(user) {
            $('#editId').val(user.id);
            $('#editName').val(user.name);
            $('#editEmail').val(user.email);
            $('#editRole').val(user.role_id);
            $('#editModal').modal('show');
        });
    });

    // Submit Edit form via AJAX with PATCH method
    $('#editUserForm').submit(function(e) {
    e.preventDefault();
    let id = $('#editId').val();
    let formData = $(this).serializeArray();
    let password = $('#editPassword').val();

    // If password is blank, remove it from formData
    if (password.trim() === '') {
        formData = formData.filter(field => field.name !== 'password');
    }

    $.ajax({
        url: '/admin/users/' + id,
        method: 'POST',
        data: $.param(formData),
        success: function(res) {
            alert(res.message);
            location.reload();
        },
        error: function(xhr) {
            alert('Error updating user.');
        }
    });
    });


    // Delete user via AJAX with confirmation
    $('.deleteBtn').click(function() {
        if (!confirm('Soft delete this user?')) return;
        let id = $(this).data('id');
        $.ajax({
            url: '/admin/users/' + id,
            method: 'DELETE',
            success: function(res) {
                alert(res.message);
                location.reload();
            },
            error: function() {
                alert('Error deleting user.');
            }
        });
    });


   $('#searchInput').on('keyup', function () {
    let search = $(this).val();

    // Show or hide clear button
    $('#clearSearch').toggleClass('d-none', search.length === 0);

    if (search.length === 0) {
        fetchUsers('');
    }
    else if (search.length >= 3){
        fetchUsers(search);
    }

    });

    // clear search button
    $('#clearSearch').on('click', function () {
        $('#searchInput').val('');
        $(this).addClass('d-none');
        fetchUsers('');
    });

    // Load users 
    function fetchUsers(search = '') {
        $.ajax({
            url: "{{ route('admin.users.index') }}",
            method: 'GET',
            data: { search },
            success: function(res) {
                let users = res.users;
                let rows = '';

                if (users.length === 0) {
                    rows = `<tr><td colspan="4" class="text-center">No users found</td></tr>`;
                } else {
                    $.each(users, function(index, user) {
                        rows += `
                            <tr data-id="${user.id}">
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>${user.role ? user.role.name : 'No Role'}</td>
                                <td>
                                    <button class="btn btn-info btn-sm viewBtn" data-id="${user.id}">View</button>
                                    <button class="btn btn-warning btn-sm editBtn" data-id="${user.id}">Edit</button>
                                    <button class="btn btn-danger btn-sm deleteBtn" data-id="${user.id}">Delete</button>
                                </td>
                            </tr>`;
                    });
                }

                $('tbody').html(rows);
            }
        });
}

});



</script>
@endsection
