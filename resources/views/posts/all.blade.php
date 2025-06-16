{{-- @extends('layout')

@section('content')
    <h2 class="mb-4">All Posts</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($posts->count())
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Author</th>
                    <th>Status</th>
                    @if($canApprove)
                        <th>Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $post)
                    <tr>
                        <td>{{ $post->title }}</td>
                        <td>{{ Str::limit($post->content, 100) }}</td>
                        <td>{{ $post->user->name }}</td>
                        <td>
                            @if($post->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($post->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-danger">Declined</span>
                            @endif
                        </td>
                        @if($canApprove)
                            <td>
                                @if($post->status === 'pending' && $post->user_id != $user->id)
                                    <button class="btn btn-success btn-sm approve-btn" data-id="{{ $post->id }}">Approve</button>
                                    <button class="btn btn-danger btn-sm decline-btn" data-id="{{ $post->id }}">Decline</button>
                                @else
                                    <small class="text-muted">No actions</small>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No posts found.</p>
    @endif
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.approve-btn').click(function() {
            const postId = $(this).data('id');
            $.post(`/posts/${postId}/approve`, {_token: '{{ csrf_token() }}'}, function(response) {
                if (response.success) location.reload();
            });
        });

        $('.decline-btn').click(function() {
            const postId = $(this).data('id');
            $.post(`/posts/${postId}/decline`, {_token: '{{ csrf_token() }}'}, function(response) {
                if (response.success) location.reload();
            });
        });
    });
</script>
@endsection --}}
<p>Post Page</p>
<p>New p tag</p>
<p>testing only push</p>
<p>code from github
</p>
<h1>yuno</h1>
<h2>testing</h2>