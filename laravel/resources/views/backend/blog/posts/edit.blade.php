@extends('backend.layouts.html')
@section('content')
    <livewire:backend.posts.post-edit :postId="$postId" />
@endsection
