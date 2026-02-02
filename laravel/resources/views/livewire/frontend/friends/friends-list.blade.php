<div>
    <h1 class="text-3xl mb-10">Friends List</h1>
    <div class="mb-4 flex space-x-4">
        <input type="text" placeholder="Search friends..." wire:model.live="search"
            class="w-full border border-gray-300 rounded px-4 py-2" />
        <button wire:click="sendSelected" wire:loading.class="opacity-50"
            class="bg-red-500 text-white px-4 py-2 rounded ">Send</button>
    </div>
    <div wire:loading>
        Sending...
    </div>
    @error('selectedFriends')
        <span class="text-red-500">{{ $message }}</span>
    @enderror

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">

                    </th>
                    <th scope="col" class="px-6 py-3">
                        Firstname
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Lastname
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Date of Birth
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($friends as $friend)
                    <tr
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">

                        <td><input type="checkbox" wire:model.live="selectedFriends" value="{{ $friend->id }}"
                                class="mx-5" />
                        </td>
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $friend->firstname }}</th>
                        <td class="px-6 py-4"> {{ $friend->lastname }}</td>
                        <td class="px-6 py-4">{{ $friend->birthdate->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $friends->links() }}
    </div>
</div>
