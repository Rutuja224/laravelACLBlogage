@extends('layout')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="height: 85vh;">
    <div class="card p-4 shadow-lg" style="min-width: 400px;">
        <h2 class="text-center mb-3">Register</h2>
        <form id="registerForm">
            @csrf
            <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
            <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
            <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
            <input type="password" name="password_confirmation" class="form-control mb-2" placeholder="Confirm Password" required>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <div class="text-center mt-3">
                Already have an account? <a href="{{ route('showLogin') }}">Login here</a>
            </div>
        </form>
        <div id="regMsg" class="mt-3 text-center"></div>
    </div>
</div>

<script>
    $('#registerForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: '/register',
            method: 'POST',
            data: $(this).serialize(),
            success: function(res){
                if(res.success){
                    $('#regMsg')
                        .removeClass('text-danger')
                        .addClass('text-success')
                        .text(res.message);

                    $('#registerForm')[0].reset();

                    // Redirect to login after short delay
                    setTimeout(function() {
                        window.location.href = "{{ route('showLogin') }}";
                    }, 1500);
                }
            },
            error: function(xhr){
                let errors = xhr.responseJSON.errors;
                $('#regMsg')
                    .removeClass('text-success')
                    .addClass('text-danger')
                    .text(Object.values(errors).join(', '));
            }
        });
    });
</script>
@endsection
