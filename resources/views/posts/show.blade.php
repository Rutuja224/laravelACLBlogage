@extends('layout')

@section('content')
<style>
    .blog-container {
        max-width: 700px;
        margin: 3rem auto;
        padding: 2rem;
        background: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: center;
    }
    .blog-author {
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 0.5rem;
        font-style: italic;
    }
    .blog-title {
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #343a40;
    }
    .blog-content {
        font-size: 1.125rem;
        color: #495057;
        white-space: pre-line;
        margin-bottom: 2.5rem;
        line-height: 1.6;
    }
    .btn-back {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
        font-weight: 600;
        padding: 0.6rem 1.4rem;
        border-radius: 5px;
        transition: background-color 0.3s ease;
        text-decoration: none;
    }
    .btn-back:hover {
        background-color: #0b5ed7;
        color: white;
        text-decoration: none;
    }
    .comment-box {
        max-width: 700px;
        margin: 1rem auto;
        padding: 1rem;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        text-align: left;
    }
    .reply-box {
        margin-left: 2rem;
        margin-top: 0.5rem;
        border-left: 2px solid #e0e0e0;
        padding-left: 1rem;
    }
    .comment-user {
        font-weight: bold;
        color: #0d6efd;
    }
    .comment-text {
        margin: 0.2rem 0 0.8rem;
        color: #343a40;
    }
    .comment-form textarea {
        width: 100%;
        resize: none;
    }
</style>

<div class="container d-flex align-items-end flex-column">
    <a href="{{ url()->previous() }}" class="btn btn-back">&larr; Back</a>
</div>

<div class="blog-container">
    <h5 class="blog-author">By {{ $blog->user->name ?? 'Unknown' }}</h5>
    <hr>
    <h2 class="blog-title">{{ $blog->title }}</h2>
    <div class="blog-content">
        {{ $blog->content }}
    </div>
</div>

{{-- Comments Section --}}
<div class="comment-box">
    <h4>Comments</h4>

    @auth
        <form action="{{ route('comments.store') }}" method="POST" class="comment-form my-2">
            @csrf
            <input type="hidden" name="post_id" value="{{ $blog->id }}">
            <textarea name="content" rows="2" class="form-control mb-2" placeholder="Write a comment..."></textarea>
            <button type="submit" class="btn btn-primary btn-sm">Post Comment</button>
        </form>
    @endauth

    @guest
        <p>
            <textarea rows="2" class="form-control mb-2"  placeholder="Write a comment..."></textarea>
            <button onclick="showLoginModal()" class="btn btn-primary btn-sm">Post Comment</button>
        </p>
    @endguest

    {{-- Display Comments --}}
    @foreach($blog->comments()->whereNull('parent_id')->with('user', 'replies.user')->orderBy('id', 'desc')->get() as $comment)
        <div class="mt-3">
            <span class="comment-user">{{ $comment->user->name }}</span>
            <p class="comment-text">{!! nl2br(e($comment->content)) !!}</p>

            {{-- Reply Form --}}
            @auth
            <form action="{{ route('comments.store') }}" method="POST" class="reply-form mb-2">
                @csrf
                <input type="hidden" name="post_id" value="{{ $blog->id }}">
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <textarea name="content" rows="1" class="form-control mb-1" placeholder="Reply..."></textarea>
                <button type="submit" class="btn btn-outline-secondary btn-sm">Reply</button>
            </form>
            @endauth

            {{-- Replies --}}
            @foreach($comment->replies as $reply)
                <div class="reply-box">
                    <span class="comment-user">{{ $reply->user->name }}</span>
                    <p class="comment-text">{!! nl2br(e($reply->content)) !!}</p>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
@endsection

<!-- Login Prompt Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title d-flex align-items-center gap-2" id="loginModalLabel">
          <i class="bi bi-person-lock"></i> Login Required
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center py-4">
        <p class="fs-5 text-secondary">To post a comment or reply, please log in to your account.</p>
        <img src="https://cdn-icons-png.flaticon.com/512/3176/3176365.png" alt="Login required" width="80" class="my-2">
      </div>
      <div class="modal-footer d-flex justify-content-between px-4 py-3">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href= "{{ route('showLogin') }}" class="btn btn-success px-4">
          <i class="bi bi-box-arrow-in-right me-1"></i> Login
        </a>
      </div>
    </div>
  </div>
</div>




@section('scripts')
<script>
    function showLoginModal() {
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
    }
</script>
@endsection



