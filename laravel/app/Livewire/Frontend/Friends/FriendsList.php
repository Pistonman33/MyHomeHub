<?php

namespace App\Livewire\Frontend\Friends;

use Livewire\Component;
use App\Models\Friend;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BirthdayMail;

class FriendsList extends Component
{
    use withPagination;
    public $search = '';
    public array $selectedFriends = [];

    public function sendSelected()
    {
        $this->validate([
             'selectedFriends' => 'required|array|min:1',
        ]);
        if(!empty($this->selectedFriends)){
            $friends = Friend::whereIn('id', $this->selectedFriends)->get();
            $contentMail = "<p>Friends List:</p>";
      	    foreach($friends as $friend){
                $birthdate = Carbon::parse($friend->birthdate);
          		$age = (int) $birthdate->diffInYears($friend->annivdate);
          		$contentMail.="<strong>$friend->lastname $friend->firstname</strong> à ".$age." ans (".$friend->birthdate->format('d/m/Y').")<br/>";
      	    }
            sleep(2); // Simulate a delay for sending email
            Mail::to(env('MAIL_TO'))->send(new BirthdayMail($contentMail));
        }
    }

    
    public function render()
    {        
        $query = Friend::query()
            ->orderBy('lastname');         
        if($this->search){
            $query->where('firstname', 'like', '%' . $this->search . '%')
                ->orWhere('lastname', 'like', '%' . $this->search . '%');
        }
        $friends = $query->paginate(10);
        return view('livewire.frontend.friends.friends-list', compact('friends'));
    }
}