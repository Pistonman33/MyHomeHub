<?php

namespace App\Livewire\Backend\Friends;

use Livewire\Component;
use App\Models\Friend;
use Livewire\WithPagination;
use Livewire\Attributes\Session;

class FriendsList extends Component
{
    use WithPagination; // Dynmic pagination

    protected $paginationTheme = 'bootstrap';
    
    #[Session]
    public string $search = '';

    #[Session]
    public string $sortField = 'lastname';

    #[Session]
    public string $sortDirection = 'asc';

    // Create a listener to refresh the list when a new friend is added
    protected $listeners = ['friendAdded' => '$refresh'];

    private array $sortableFields = [
        'name',
        'birthdate',
        'group'
    ];

    public function edit($friendId)
    {
        $this->dispatch('edit-friend', ['friendId' => $friendId]);
    }

    public function sortBy($field)
    {
        if (!in_array($field, $this->sortableFields)) {
            return;
        }
        $this->resetPage();
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
            $this->sortField = $field;
        }
    }

    public function delete(int $friendId): void
    {
        $friend = Friend::findOrFail($friendId);
        $friend->group()->dissociate();
        $friend->delete();

        session()->flash('message', 'Friend deleted successfully.');

    }

    public function render()
    {
        $query = Friend::query()
                ->join('friend_groups', 'friends.fk_id_friend_group', '=', 'friend_groups.id')
                ->select('friends.*')
                ->with('group');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('firstname', 'like', "%{$this->search}%")
                  ->orWhere('lastname', 'like', "%{$this->search}%");
            });
        }

        switch ($this->sortField) {
            case 'group':
                $query->orderBy('friend_groups.name', $this->sortDirection);
                break;

            case 'name':
                $query->orderBy('firstname', $this->sortDirection)
                       ->orderBy('lastname', $this->sortDirection);
                break;
            case 'birthdate':
                $query->orderBy("friends.{$this->sortField}", $this->sortDirection);
                break;

            default:
                $query->orderBy('friends.id', 'asc');
        }
        return view('livewire.backend.friends.friends-list',[
            'friends' => $query->paginate(10)->withPath(route('admin.friends.index'))
        ]);
    }
    public function avatarColor(string $firstname, string $lastname): string
    {
        $hash = crc32(strtolower($firstname));
        $hue = $hash % 360;
        $color = "hsl($hue, 70%, 50%)";
        return $color;
    }
}