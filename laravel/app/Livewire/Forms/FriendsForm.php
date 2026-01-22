<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Friend;
use App\Models\FriendGroup;

class FriendsForm extends Form
{
    #[Validate('nullable|exists:friends,id')]
    public $id; // For editing existing friends

    #[Validate('required|string|min:4|max:255')]
    public $firstname;

    #[Validate('required|string|min:4|max:255')]
    public $lastname;

    #[Validate('required|date|before:today')]
    public $birthdate;

    #[Validate('required|exists:friend_groups,id')]
    public $fk_id_friend_group;

    public function store()
    {
        $this->validate();

        if ($this->id) {
            // Update existing friend
            $friend = Friend::find($this->id);
            $friend->update([
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'birthdate' => $this->birthdate,
                'fk_id_friend_group' => $this->fk_id_friend_group,
            ]);
        }else {
            Friend::create([
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'birthdate' => $this->birthdate,
                'fk_id_friend_group' => $this->fk_id_friend_group,
            ]);
        }

    }

}
