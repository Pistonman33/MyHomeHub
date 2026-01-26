<div class="container-fluid mt-4">
    <div class="row g-3">
        {{-- COLONNE GAUCHE : Contenu (≈ 80%) --}}
        <div class="col-lg-9">
            <div class="p-4 bg-white rounded shadow">

                {{-- Titre --}}
                <div class="mb-3">
                    <input type="text" wire:model.defer="form.title" class="form-control form-control-lg"
                        placeholder="Add title">
                    @error('form.title')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Contenu --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Contenu (HTML)</label>
                    <textarea wire:model.defer="form.content" id="editor" class="form-control" rows="20"></textarea>
                    @error('form.content')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE : Meta (≈ 20%) --}}
        <div class="col-lg-3">

            {{-- Status --}}
            <div class="bg-white rounded shadow p-3 mb-3">
                <h6 class="fw-semibold mb-2">Publication</h6>

                <select wire:model.defer="form.status" class="form-select mb-2">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>

                <button wire:click="save" class="btn btn-primary w-100">
                    <i class="fa-solid fa-save"></i> Publish
                </button>
                <a href="{{ route('admin.blog.posts') }}" class="btn btn-secondary w-100 mt-2">
                    Cancel
                </a>
            </div>

            {{-- Categories --}}
            <div class="bg-white rounded shadow p-3 mb-3">
                <h6 class="fw-semibold mb-2">Catégories</h6>

                @foreach ($categories as $category)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="selectedCategories"
                            value="{{ $category->id }}" id="checkbox_{{ $category->id }}">
                        <label class="form-check-label" for="checkbox_{{ $category->id }}">
                            {{ $category->name }}
                        </label>
                    </div>
                @endforeach

                {{-- Ajouter catégorie --}}
                <div class="input-group mt-2">
                    <input type="text" class="form-control" placeholder="Nouvelle catégorie"
                        wire:model.defer="newCategoryName">
                    <button class="btn btn-outline-primary" wire:click="addCategory">
                        +
                    </button>
                    @error('categorySearch')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>
            </div>

            {{-- Tags (WordPress-like) --}}
            <div class="bg-white rounded shadow p-3">
                <h6 class="fw-semibold mb-2">Tags</h6>

                {{-- Input tags --}}
                <input type="text" class="form-control mb-2" placeholder="Add new tag" wire:model.live="tagSearch">

                {{-- Suggestions --}}
                @if (!empty($tagSuggestions))
                    <div class="list-group mb-2">
                        @foreach ($tagSuggestions as $tag)
                            <button type="button" class="list-group-item list-group-item-action"
                                wire:click="addExistingTag({{ $tag['id'] }})">
                                {{ $tag['name'] }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <button class="btn btn-outline-primary btn-sm w-100 mb-2" wire:click="addTagFromInput">
                    Add
                </button>
                @error('tagSearch')
                    <div class="text-danger small mt-1">
                        {{ $message }}
                    </div>
                @enderror

                {{-- Tags sélectionnés --}}
                <div class="d-flex flex-wrap gap-1">
                    @foreach ($selectedTags as $tagId)
                        @php
                            $tag = $tags->firstWhere('id', $tagId);
                        @endphp
                        @if ($tag)
                            <span class="badge bg-secondary" style="margin-right:3px;">
                                {{ $tag->name }}
                                <span wire:click="removeTag({{ $tagId }})" style="cursor:pointer;">×</span>
                            </span>
                        @endif
                    @endforeach
                </div>

            </div>

        </div>
    </div>
</div>
