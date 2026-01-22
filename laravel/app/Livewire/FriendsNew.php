<?php

namespace App\Livewire;

use Livewire\Component;
use App\Livewire\Forms\FriendsForm;
use App\Models\FriendGroup;
use App\Models\Friend;
use Livewire\Attributes\On; 

class FriendsNew extends Component
{
    public FriendsForm $form;

    public $groups = [];

    // initialize form values
    public function mount()
    {
        $this->groups = FriendGroup::orderBy('name')->get();
        $this->form->fk_id_friend_group = $this->groups->first()->id ?? null;
    }

    public function openModal()
    {
        $this->dispatch('open-friend-modal');
    }

    public function save()
    {
        $this->form->store();
        $this->form->reset();

        $this->dispatch('close-friend-modal'); // Notify to close modal
        $this->dispatch('friendAdded'); // Notify list need to be refreshed
    }
    #[On('edit-friend')] 
    public function edit($friendId)
    {
        $friend = Friend::find($friendId)->first();
        // Fill form with existing friend data
        $this->form->fill([
            'id' => $friend->id,
            'firstname' => $friend->firstname,
            'lastname' => $friend->lastname,
            'birthdate' => $friend->birthdate->format('Y-m-d'),
            'fk_id_friend_group' => $friend->fk_id_friend_group,
        ]);

        $this->dispatch('open-friend-modal'); // ouvre la modal
    }


    public function render()
    {
        return view('livewire.friends.friends-new');
    }
}