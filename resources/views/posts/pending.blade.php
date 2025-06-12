@extends('layout')

@section('content')
<div class="container mt-4">
    <h2>Pending Posts for Approval</h2>

    @if($pendingPosts->isEmpty())
        <p>No pending posts to approve or decline.</p>
    @else
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Author</th>
                    <th>Submitted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingPosts as $post)
                <tr id="post-{{ $post->id }}">
                    <td>{{ $post->title }}</td>
                    <td>{{ Str::limit($post->content, 50) }}</td>
                    <td>{{ optional($post->user)->name ?? 'Unknown' }}</td>
                    <td>{{ $post->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        @can('approve-post', $post)
                            <button class="btn btn-success btn-sm approveBtn" data-id="{{ $post->id }}">Approve</button>
                            <button class="btn btn-danger btn-sm declineBtn" data-id="{{ $post->id }}">Decline</button>
                        @else
                            <span class="text-muted">Not allowed</span>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
    $('.approveBtn').click(function(){
        let id = $(this).data('id');
        if(confirm('Approve this post?')) {
            $.post(`/posts/${id}/approve`, {
                _token: '{{ csrf_token() }}'
            }, function(res){
                if(res.success){
                    alert(res.message);
                    $(`#post-${id}`).remove();
                } else {
                    alert(res.message || 'Failed to approve.');
                }
            });
        }
    });

    $('.declineBtn').click(function(){
        let id = $(this).data('id');
        if(confirm('Decline this post?')) {
            $.post(`/posts/${id}/decline`, {
                _token: '{{ csrf_token() }}'
            }, function(res){
                if(res.success){
                    alert(res.message);
                    $(`#post-${id}`).remove();
                } else {
                    alert(res.message || 'Failed to decline.');
                }
            });
        }
    });
});
</script>
@endsection
