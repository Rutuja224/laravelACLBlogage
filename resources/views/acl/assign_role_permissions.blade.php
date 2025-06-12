@extends('layout')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Assign Permissions to Roles</h3>
    <a href="{{ route('admin.users.index') }}" class="btn btn-warning mb-3">Manage Users</a>

    <div class="mb-3">
        <label for="roleSelect" class="form-label">Select Role</label>
        <select class="form-select" id="roleSelect">
            <option value="">-- Choose Role --</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
            @endforeach
        </select>
    </div>

    <form method="POST" action="{{ route('role_permissions.update') }}">
        @csrf
        <input type="hidden" name="role_id" id="selectedRoleId">

        <div id="permissionsSection" class="d-none">
            <div class="row" id="permissionCheckboxes">
                {{-- Filled by JS --}}
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update Permissions</button>
        </div>
    </form>
</div>

<script>
    const roles = @json($roles);
    const permissions = @json($permissions);
    const rolePermissions = @json($rolePermissions);

    function filterPermissions(perms) {
        const seen = new Set();
        const filtered = [];

        for (let p of perms) {
            if (seen.has(p.name)) continue;

            // Hide decline_post if approve_post is present
            if (p.name === 'decline_post' && perms.some(pp => pp.name === 'approve_post')) continue;

            seen.add(p.name);
            filtered.push(p);
        }

        return filtered;
    }

    document.getElementById('roleSelect').addEventListener('change', function () {
        const roleId = this.value;
        document.getElementById('selectedRoleId').value = roleId;

        const checkboxesContainer = document.getElementById('permissionCheckboxes');
        checkboxesContainer.innerHTML = '';

        if (!roleId) {
            document.getElementById('permissionsSection').classList.add('d-none');
            return;
        }

        const currentPermissions = rolePermissions[roleId] || [];

        const filteredPerms = filterPermissions(permissions);

        for (let perm of filteredPerms) {
            const isChecked = currentPermissions.includes(perm.name);

            checkboxesContainer.innerHTML += `
                <div class="col-md-3">
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            name="permissions[]" 
                            value="${perm.name}" 
                            id="perm_${perm.id}"
                            ${isChecked ? 'checked' : ''}>

                        <label class="form-check-label" for="perm_${perm.id}">
                            ${perm.name.replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
                        </label>
                    </div>
                </div>
            `;
        }

        document.getElementById('permissionsSection').classList.remove('d-none');
    });
</script>
@endsection
