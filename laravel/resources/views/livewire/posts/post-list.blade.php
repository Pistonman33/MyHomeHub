<div class="container mt-4 p-4 bg-white rounded shadow">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4">Posts List</h2>
        <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create a New Post
        </a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Categories</th>
                <th>Tags</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
                <tr>
                    <td>{{ $post->title }}</td>
                    <td>{{ ucfirst($post->status) }}</td>
                    <td>
                        @foreach($post->categories as $category)
                            <span class="badge bg-info text-dark">{{ $category->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        @foreach($post->tags as $tag)
                            <span class="badge bg-secondary">{{ $tag->name }}</span>
                        @endforeach
                    </td>
                    <td>{{ $post->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.blog.posts.edit', $post->id) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button wire:click="delete({{ $post->id }})" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination Bootstrap --}}
    <div class="mt-3">
        {{ $posts->links('pagination::bootstrap-5') }}
    </div>
</div>
