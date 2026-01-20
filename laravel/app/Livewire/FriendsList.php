<?php

namespace App\Livewire;

use Livewire\Component;
use App\Friend;
use Livewire\Attributes\Locked;
use Livewire\WithPagination;


class FriendsList extends Component
{
    use WithPagination; // Dynmic pagination

    protected $paginationTheme = 'bootstrap';
    
    public string $search = '';

    #[Locked]
    public string $sortField = 'lastname';

    #[Locked]
    public string $sortDirection = 'asc';

    private array $sortableFields = [
        'firstname',
        'lastname',
        'birthdate',
        'group'
    ];

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
        }

        $this->sortField = $field;
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

            case 'firstname':
            case 'lastname':
            case 'birthdate':
                $query->orderBy("friends.{$this->sortField}", $this->sortDirection);
                break;

            default:
                $query->orderBy('friends.id', 'asc');
        }
        return view('livewire.friends.friends-list',[
            'friends' => $query->paginate(10)
        ]);
    }
}
