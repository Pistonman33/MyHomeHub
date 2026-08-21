<div class="blog-posts-page">
    <div class="blog-posts-header">
        <div>
            <span class="blog-eyebrow">MyBlog</span>
            <h1>Posts</h1>
            <p>Gérez les articles et leur publication depuis un seul endroit.</p>
        </div>
        <a href="{{ route('admin.blog.posts.create') }}" class="blog-create-button">
            <i class="fa-solid fa-plus"></i><span>Créer un post</span>
        </a>
    </div>

    <div class="blog-posts-toolbar">
        <label class="blog-search" for="blog-post-search"><i class="fa-solid fa-magnifying-glass"></i><input
                id="blog-post-search" type="search" placeholder="Rechercher un titre, une catégorie ou un tag..."
                wire:model.live.debounce.300ms="search"></label>
        <span class="blog-result-hint"><i class="fa-solid fa-circle"></i> Filtrage instantané</span>
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
                        <th>Catégories</th>
                        <th>Tags</th>
                        <th wire:click="sortBy('created_at')" class="is-sortable">
                            <div><span>Créé le</span>
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
                                <strong>{{ $post->title }}</strong><small>#{{ $post->id }}</small></td>
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
                                <a href="{{ route('admin.blog.posts.edit', $post->id) }}" title="Modifier le post"><i
                                        class="fa-solid fa-pen-to-square"></i></a>
                                <button type="button" title="Supprimer le post"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                    wire:click.stop="delete({{ $post->id }})"><i
                                        class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="blog-empty-state"><i
                                    class="fa-regular fa-file-lines"></i><strong>Aucun post trouvé</strong><span>Essayez
                                    une autre recherche.</span></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="blog-posts-pagination">
            {{ $posts->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
