<div class="blog-posts-page">
    <div class="blog-posts-header">
        <div>
            <span class="blog-eyebrow">MyBlog</span>
            <h1>Posts</h1>
            <p>Manage articles and their publication from one place.</p>
        </div>
        <a href="{{ route('admin.blog.posts.create') }}" class="blog-create-button">
            <i class="fa-solid fa-plus"></i><span>Create a post</span>
        </a>
    </div>

    <div class="blog-posts-toolbar">
        <label class="blog-search" for="blog-post-search"><i class="fa-solid fa-magnifying-glass"></i><input
                id="blog-post-search" type="search" placeholder="Search for a title, category or tag..."
                wire:model.live.debounce.300ms="search"></label>
        <span class="blog-result-hint"><i class="fa-solid fa-circle"></i> Instant filtering</span>
    </div>

    <div class="blog-posts-table-card">
        <div class="blog-posts-card-heading">
            <div>
                <h2>Liste des posts</h2><span>{{ $posts->total() }} article(s) au total</span>
            </div><i class="fa-solid fa-newspaper"></i>
        </div>
        <div class="table-responsive">
            <table class="table blog-posts-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('title')" class="is-sortable">
                            <div><span>Titre</span>
                                @if ($sortField == 'title')
                                    <i class="fa-solid fa-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('status')" class="is-sortable">
                            <div><span>Statut</span>
                                @if ($sortField == 'status')
                                    <i class="fa-solid fa-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </div>
                        </th>
                        <th>Categories</th>
                        <th>Tags</th>
                        <th wire:click="sortBy('created_at')" class="is-sortable">
                            <div><span>Created on</span>
                                @if ($sortField == 'created_at')
                                    <i class="fa-solid fa-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </div>
                        </th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td class="blog-post-title">
                                <strong>{{ $post->title }}</strong><small>#{{ $post->id }}</small>
                            </td>
                            <td><span class="blog-status blog-status--{{ $post->status }}"><i
                                        class="fa-solid fa-circle"></i>{{ ucfirst($post->status) }}</span></td>
                            <td>
                                @foreach ($post->categories as $category)
                                    <span class="blog-taxonomy blog-taxonomy--category">{{ $category->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($post->tags as $tag)
                                    <span class="blog-taxonomy">#{{ $tag->name }}</span>
                                @endforeach
                            </td>
                            <td class="blog-post-date">{{ $post->created_at->format('d/m/Y') }}</td>
                            <td class="blog-post-actions">
                                <a href="{{ route('admin.blog.posts.edit', $post->id) }}" title="Edit post"><i
                                        class="fa-solid fa-pen-to-square"></i></a>
                                <button type="button" title="Delete post"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                    wire:click.stop="delete({{ $post->id }})"><i
                                        class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="blog-empty-state"><i
                                    class="fa-regular fa-file-lines"></i><strong>No posts found</strong><span>Try
                                    another search.</span></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="blog-posts-pagination admin-pagination">
            <div class="admin-pagination-controls">
                {{ $posts->links('pagination::bootstrap-5') }}
                <select class="admin-pagination-select" wire:model.live="perPage" aria-label="Nombre de posts par page">
                    @foreach ([5, 10, 20, 50, 100] as $pageSize)
                        <option value="{{ $pageSize }}">{{ $pageSize }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
