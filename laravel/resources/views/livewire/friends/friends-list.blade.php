<div class="p-4 bg-white rounded shadow">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4">Friends List</h2>
        <livewire:friends-new />
    </div>

    <input type="text"
           class="form-control mb-3"
           placeholder="Search friends by firstname, lastname"
           wire:model.live.debounce.300ms="search">

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th wire:click="sortBy('name')" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Name</span>
                        @if ($sortField === 'name')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </div>
                </th>

                <th wire:click="sortBy('group')" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Group</span>
                        @if ($sortField === 'group')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </div>
                </th>

                <th wire:click="sortBy('birthdate')" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Birthdate</span>
                        @if ($sortField === 'birthdate')
                            <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </div>
                </th>

                {{-- NEW --}}
                <th class="text-center">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($friends as $friend)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            {{-- Avatar --}}
                            <div class="avatar"
                                 style="background-color: {{ $this->avatarColor($friend->firstname, $friend->lastname) }}">
                                {{ strtoupper(substr($friend->firstname,0,1)) }}
                                {{ strtoupper(substr($friend->lastname,0,1)) }}
                            </div>

                            {{-- Name --}}
                            <div class="fw-semibold">
                                {{ $friend->firstname }} {{ $friend->lastname }}
                            </div>
                        </div>
                    </td>

                    <td>{{ $friend->group->name }}</td>
                    <td>{{ $friend->birthdate->format('d/m/Y') }}</td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-3">
                            {{-- Edit --}}
                            <i class="fa-solid fa-pen-to-square fa-lg mt-3 text-primary"
                               style="cursor:pointer;"
                               wire:click.stop="edit({{ $friend->id }})"
                               title="Edit friend"></i>

                            {{-- Delete --}}
                            <i class="fa-solid fa-trash fa-lg mt-3 text-danger"
                                style="cursor:pointer;"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                wire:click.stop="delete({{ $friend->id }})">
                            </i>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $friends->links('pagination::bootstrap-5') }}
    </div>
</div>
