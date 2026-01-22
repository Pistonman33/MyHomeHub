@extends('backend.layouts.html')
@section('content')
    <livewire:posts.post-edit :postId="$postId" />
@endsection
