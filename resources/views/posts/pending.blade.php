@extends('layout')

@section('head')
    <!-- DataTables CSS --> 
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
@endsection

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between">

        <h2>Pending Posts for Approval</h2>

        <a href="{{ url()->previous() }}" class="btn btn-back btn-primary">Back</a>

    </div>

    @if($pendingPosts->isEmpty())
        <p>No pending posts to approve or decline.</p>
    @else
        <table class="table mt-3" id="pendingPostsTable">
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
@endsection

@section('scripts')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

<script>
$(function(){
    // DataTables 
        $('#pendingPostsTable').DataTable({
            "pageLength": 10,
            "columnDefs": [
                { "orderable": false, "targets": 3 } 
            ]
        });

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
