<div class="d-flex justify-content-end">
    <button wire:click="openModal" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i>
        Create a New Friend
    </button>

    <!-- Modal Bootstrap -->
    <div wire:ignore.self class="modal fade" id="friendModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="save">
                    <div class="modal-header">
                        <h5 class="modal-title"> {{ $form->id ? 'Edit Friend' : 'Create a new friend' }}</h5>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>

                    </div>
                    <div class="modal-body">
                        <input class="form-control mb-2"
                               wire:model.defer="form.firstname"
                               placeholder="First name">
                        @error('form.firstname') <small class="text-danger">{{ $message }}</small> @enderror
                        <input class="form-control mb-2"
                               wire:model.defer="form.lastname"
                               placeholder="Last name">
                        @error('form.lastname') <small class="text-danger">{{ $message }}</small> @enderror
                        <input type="date"
                               class="form-control mb-2"
                               wire:model.defer="form.birthdate">
                        @error('form.birthdate') <small class="text-danger">{{ $message }}</small> @enderror
                        <div class="mb-2">
                            <label for="group">Group</label>
                            <select class="form-control" wire:model.defer="form.fk_id_friend_group" id="group">
                                <option value="">-- Sélectionnez un groupe --</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                            @error('form.fk_id_friend_group') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
