@extends('layout')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createPostModal">
            + New Post
        </button>

        @can('can-approve-posts')
            <a href="{{ route('posts.pending') }}" class="btn btn-warning">View Pending Posts</a>
        @endcan
    </div>

    <h3 class="mt-5">All Posts</h3>
    <table class="table table-bordered mt-3" id="postsTable">
        <thead class="table-light">
            <tr>
                <th style="width: 20%;">Title</th>
                <th style="width: 50%;">Content</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
            <tr id="post-{{ $post->id }}">
                <td class="align-middle">{{ $post->title }}</td>
                <td class="align-middle">
                    @php
                        $maxLength = 300;
                        $isLong = strlen($post->content) > $maxLength;
                        $preview = $isLong ? substr($post->content, 0, $maxLength) . '...' : $post->content;
                    @endphp
                    <div class="post-content-preview" style="white-space: pre-line; max-height: 7.5em; overflow: hidden;">
                        {!! e($preview) !!}
                    </div>
                    @if($isLong)
                        <a href="#" class="read-more-toggle text-primary" data-bs-toggle="modal" data-bs-target="#readMoreModal" data-content="{{ e($post->content) }}" data-title="{{ e($post->title) }}">Read More</a>
                    @endif
                </td>
                <td class="align-middle">{{ ucfirst($post->status) }}</td>
                <td class="align-middle">
                    @if($post->user_id == auth()->id() && $post->status !== 'approved')
                        <button class="btn btn-sm btn-warning editBtn" data-id="{{ $post->id }}">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $post->id }}">Delete</button>
                    @endif

                    @can('approve-post', $post)
                      @if($post->status === 'pending')
                          <button class="btn btn-sm btn-success approveBtn" data-id="{{ $post->id }}">Approve</button>
                          <button class="btn btn-sm btn-secondary declineBtn" data-id="{{ $post->id }}">Decline</button>
                      @endif
                    @endcan

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Create Post Modal -->
<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createPostForm" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="createPostModalLabel">Create New Post</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" placeholder="Enter post title" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Content</label>
          <textarea name="content" class="form-control" placeholder="Enter post content" rows="4" required></textarea>
        </div>
        <div id="postMsg" class="text-danger mt-2"></div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Submit Post</button>
      </div>
    </form>
  </div>
</div>

<!-- Read More Modal -->
<div class="modal fade" id="readMoreModal" tabindex="-1" aria-labelledby="readMoreModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="readMoreModalLabel">Post Content</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="white-space: pre-line;"></div>
    </div>
  </div>
</div>

<!-- Edit Post Modal -->
<div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="editPostModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editPostForm" class="modal-content">
      {{-- @csrf
      @method('PUT') --}}
      <input type="hidden" name="post_id" id="editPostId">
      <div class="modal-header">
        <h5 class="modal-title" id="editPostModalLabel">Edit Post</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" name="title" id="editPostTitle" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Content</label>
          <textarea name="content" id="editPostContent" class="form-control" rows="4" required></textarea>
        </div>
        <div class="text-danger" id="editPostError"></div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update Post</button>
      </div>
    </form>
  </div>
</div>



<script>
$(document).ready(function() {
    // Create post
    $('#createPostForm').submit(function(e){
        e.preventDefault();

        let title = $('input[name="title"]').val().trim();
        let content = $('textarea[name="content"]').val().trim();

        if(title === '' || content === '') {
            $('#postMsg').text('Title and Content cannot be empty.');
            return;
        }

        $('#postMsg').text(''); 

        $.ajax({
            url: '/posts',
            method: 'POST',
            data: $(this).serialize(),
            success: function(res){
                if(res.success){
                    $('#createPostModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr){
                let errors = xhr.responseJSON.errors;
                $('#postMsg').text(Object.values(errors).join(', '));
            }
        });
    });


    // Delete post
    $('.deleteBtn').click(function(){
        let postId = $(this).data('id');
        if(confirm("Delete this post?")){
            $.ajax({
                url: '/posts/' + postId,
                method: 'DELETE',
                data: {'_token': '{{ csrf_token() }}'},
                success: function(res){
                    if(res.success){
                        $('#post-' + postId).remove();
                    }
                }
            });
        }
    });

    // Open edit modal
    $('.editBtn').click(function(){
        let postId = $(this).data('id');
        let row = $('#post-' + postId);
        let title = row.find('td:eq(0)').text().trim();
        let content = row.find('.post-content-preview').text().trim();

        $('#editPostId').val(postId);
        $('#editPostTitle').val(title);
        $('#editPostContent').val(content);
        $('#editPostModal').modal('show');
    });

    // Submit edit form
$('#editPostForm').submit(function(e){
    e.preventDefault();

    let postId = $('#editPostId').val();
    let title = $('#editPostTitle').val().trim();
    let content = $('#editPostContent').val().trim();

    if(title === '' || content === ''){
        $('#editPostError').text('Title and content cannot be empty.');
        return;
    }

    let formData = {
        '_token': '{{ csrf_token() }}',
        '_method': 'PUT',
        'title': title,
        'content': content
    };

    // Show loader before AJAX
    $('#loaderOverlay').fadeIn();

    $.ajax({
        url: '/posts/' + postId,
        method: 'POST',
        data: formData,
        success: function(res){
            if(res.success){
                setTimeout(function(){
                    $('#editPostModal').modal('hide');
                    $('#editPostError').text('');
                    location.reload(); 
                }, 2000); // 3 seconds delay
            } else {
                $('#editPostError').text('Something went wrong.');
                $('#loaderOverlay').fadeOut();
            }
        },
        error: function(xhr){
            let errors = xhr.responseJSON?.errors || {'error': ['Unknown error']};
            $('#editPostError').text(Object.values(errors).join(', '));
            $('#loaderOverlay').fadeOut();
        }
    });
});



    // Approve / Decline
    $('.approveBtn, .declineBtn').click(function(){
        let postId = $(this).data('id');
        let status = $(this).hasClass('approveBtn') ? 'approved' : 'declined';
        let confirmMsg = status === 'approved' ? 'Approve this post?' : 'Decline this post?';

        if(confirm(confirmMsg)){
            $.post('/posts/' + postId + '/approve', {
                '_token': '{{ csrf_token() }}',
                'status': status
            }, function(res){
                if(res.success){
                    location.reload();
                }
            });
        }
    });

    // Read more modal
    $('#readMoreModal').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget);
        let content = button.data('content');
        let title = button.data('title');
        $(this).find('.modal-title').text(title);
        $(this).find('.modal-body').text(content);
    });
});




</script>
@endsection
