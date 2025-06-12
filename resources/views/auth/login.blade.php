@extends('layout')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="height: 80vh;">
    <div class="card p-4 shadow-lg" style="min-width: 350px;">
        <h2 class="text-center mb-3">Login</h2>
        <form id="loginForm">
            @csrf
            <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
            <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
            <button type="submit" class="btn btn-success w-100">Login</button>
            <div class="text-center mt-3">
                Don't Have Account? <a href="{{ route('showRegister') }}">Register here</a>
            </div>
        </form>
        <div id="loginMsg" class="mt-3 text-danger text-center"></div>
    </div>
</div>

<script>
    $('#loginForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: '/login',
            method: 'POST',
            data: $(this).serialize(),
            success: function(res){
                if(res.success){
                    window.location.href = '/posts';
                }
            },
            error: function(xhr){
                $('#loginMsg').text(xhr.responseJSON.error || 'Login failed');
            }
        });
    });
</script>
@endsection
