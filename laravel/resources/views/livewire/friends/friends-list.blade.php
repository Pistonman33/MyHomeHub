<div class="p-4 bg-white rounded shadow">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4">Friends List</h2>
    </div>
    <input type="text" class="form-control mb-3" placeholder="Search friends by firstname, lastname" wire:model.live.debounce.300ms="search">

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th wire:click="sortBy('firstname')" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Firstname</span>
                        @if ($sortField == 'firstname')
                            @if ($sortDirection == 'asc')
                                <i class="fa-solid fa-sort-up"></i>
                            @else
                                <i class="fa-solid fa-sort-down"></i>
                            @endif
                        @endif
                    </div>
                </th>
                <th wire:click="sortBy('lastname')" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Lastname</span>
                        @if ($sortField == 'lastname')
                            @if ($sortDirection == 'asc')
                                <i class="fa-solid fa-sort-up"></i>
                            @else
                                <i class="fa-solid fa-sort-down"></i>
                            @endif
                        @endif
                    </div>
                </th>
                <th wire:click="sortBy('group')" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Group</span>
                        @if ($sortField == 'group')
                            @if ($sortDirection == 'asc')
                                <i class="fa-solid fa-sort-up"></i>
                            @else
                                <i class="fa-solid fa-sort-down"></i>
                            @endif
                        @endif
                    </div>
                </th>                
                <th wire:click="sortBy('birthdate')" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Birthdate</span>
                        @if ($sortField == 'birthdate')
                            @if ($sortDirection == 'asc')
                                <i class="fa-solid fa-sort-up"></i>
                            @else
                                <i class="fa-solid fa-sort-down"></i>
                            @endif
                        @endif
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($friends as $friend)
                <tr>
                    <td>{{ $friend->firstname }}</td>
                    <td>{{ $friend->lastname }}</td>
                    <td>{{ $friend->group->name }}</td>
                    <td>{{ $friend->birthdate->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination Bootstrap --}}
    <div class="mt-3">
        {{ $friends->links('pagination::bootstrap-5') }}
    </div>
</div>
