<div class="container mt-4 p-4 bg-white rounded shadow" style="max-width: 900px;">

    {{-- Message de succès --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Message d'erreur --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Titre --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">Titre</label>
        <input type="text" wire:model.defer="posts.title" class="form-control">
        @error('posts.title') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- Contenu WYSIWYG --}}
    <div class="mb-3" wire:ignore>
        <label class="form-label fw-semibold">Contenu</label>
        <input id="content" type="hidden" wire:model.defer="posts.content">
        <trix-editor input="content" class="form-control"></trix-editor>
        @error('posts.content') <small class="text-danger">{{ $message }}</small> @enderror
    </div>


    {{-- Statut --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">Statut</label>
        <select wire:model.defer="posts.status" class="form-select">
            <option value="draft">Draft</option>
            <option value="published">Published</option>
        </select>
        @error('posts.status') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

{{-- Catégories --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Catégories</label>
    <div class="input-group mb-2">
        <select wire:model="selectedCategories" multiple class="form-control">
            @if ($categories->isEmpty())
                <option disabled>Aucune catégorie disponible</option>
            @else
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            @endif
        </select>
    </div>

    {{-- Nouveau champ + bouton pour ajouter une catégorie --}}
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Nouvelle catégorie" wire:model.defer="newCategoryName">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-primary" wire:click="addCategory">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>

    @error('selectedCategories') <small class="text-danger">{{ $message }}</small> @enderror
    @error('newCategoryName') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Tags --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Tags</label>
    <div class="input-group mb-2">
        <select wire:model="selectedTags" multiple class="form-control">
            @if ($tags->isEmpty())
                <option disabled>Aucun tag disponible</option>
            @else
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            @endif
        </select>
    </div>

    {{-- Nouveau champ + bouton pour ajouter un tag --}}
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Nouveau tag" wire:model.defer="newTagName">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-primary" wire:click="addTag">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>

    @error('selectedTags') <small class="text-danger">{{ $message }}</small> @enderror
    @error('newTagName') <small class="text-danger">{{ $message }}</small> @enderror
</div>


    {{-- Boutons --}}
    <div class="mt-4 d-flex">
        <button wire:click="save" class="btn btn-primary mr-2">
            <i class="fa-solid fa-save"></i> Save
        </button>
        <a href="{{ route('admin.blog.posts') }}" class="btn btn-secondary">
            Cancel
        </a>
    </div>
</div>
