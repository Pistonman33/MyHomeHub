<div class="p-4 bg-white rounded shadow">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4">Posts List</h2>
        <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Create a New Post
        </a>
    </div>
    <input type="text" class="form-control mb-3" placeholder="Search posts by title, tags or categories" wire:model.live.debounce.300ms="search">

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th wire:click="sortBy('title')" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Title</span>
                        @if ($sortField == 'title')
                            @if ($sortDirection == 'asc')
                                <i class="fa-solid fa-sort-up"></i>
                            @else
                                <i class="fa-solid fa-sort-down"></i>
                            @endif
                        @endif
                    </div>
                </th>
                <th wire:click="sortBy('status')" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Status</span>
                        @if ($sortField == 'status')
                            @if ($sortDirection == 'asc')
                                <i class="fa-solid fa-sort-up"></i>
                            @else
                                <i class="fa-solid fa-sort-down"></i>
                            @endif
                        @endif
                    </div>
                </th>
                <th>Categories</th>
                <th>Tags</th>
                <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Created At</span>
                        @if ($sortField == 'created_at')
                            @if ($sortDirection == 'asc')
                                <i class="fa-solid fa-sort-up"></i>
                            @else
                                <i class="fa-solid fa-sort-down"></i>
                            @endif
                        @endif
                    </div>
                </th>

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
                         {{-- Edit --}}
                        <a href="{{ route('admin.blog.posts.edit', $post->id) }}">
                        <i class="fa-solid fa-pen-to-square fa-lg mt-3 text-primary"
                               style="cursor:pointer;" title="Edit post"></i>
                        </a>
                        {{-- Delete --}}
                        <i class="fa-solid fa-trash fa-lg mt-3 text-danger"
                                style="cursor:pointer;"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                wire:click.stop="delete({{ $post->id }})">
                        </i>
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
