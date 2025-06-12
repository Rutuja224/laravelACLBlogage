{{-- welcome.blade.php --}}
@extends('layout')

@section('content')
    <!-- Header -->
    <header class="masthead" style="background-image: url('assets/img/home-bg.jpg'); height: 700px;">
        <div class="container px-4 px-lg-5">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-7 text-center">
                    <div class="site-heading">
                        <h1>Clean Blog</h1>
                        <span class="subheading">A Blog Theme by Rutuja Kadav</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Approved Posts List -->
    <div class="container px-4 px-lg-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7">
                @forelse ($posts as $blog)
                    <div class="post-preview">
                        <a href="{{ route('posts.show', $blog->id) }}">
                            <h2 class="post-title">{{ $blog->title }}</h2>
                            <h3 class="post-subtitle">{{ Str::limit($blog->content, 100) }}</h3>
                        </a>
                        <p class="post-meta">Posted by {{ $blog->user->name ?? 'Unknown' }} on {{ $blog->created_at->format('F d, Y') }}</p>
                    </div>
                    <hr class="my-4" />
                @empty
                    <p>No approved blog posts yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
