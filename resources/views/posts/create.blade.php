@extends('layout')

@section('content')
<div class="container mt-5">
    <h2>Create New Post</h2>
    <form id="createPostForm">
        @csrf
        <input type="text" name="title" class="form-control mb-2" placeholder="Title">
        <textarea name="content" class="form-control mb-2" placeholder="Content"></textarea>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <div id="createMsg" class="mt-3 text-success"></div>
</div>

<script>
    $('#createPostForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: '/posts',
            method: 'POST',
            data: $(this).serialize(),
            success: function(res){
                $('#createMsg').text('Post submitted for approval.');
                $('#createPostForm')[0].reset();
            },
            error: function(xhr){
                $('#createMsg').removeClass('text-success').addClass('text-danger').text('Error: ' + xhr.responseText);
            }
        });
    });
</script>
@endsection
