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
        color: #6c757d; /* muted gray */
        font-weight: 500;
        margin-bottom: 0.5rem;
        font-style: italic;
    }
    .blog-title {
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #343a40; /* dark gray */
    }
    .blog-content {
        font-size: 1.125rem;
        color: #495057; /* medium gray */
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
</style>

<div class="container m-3 d-flex align-items-end flex-column">
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
@endsection
