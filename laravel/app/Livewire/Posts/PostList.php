<?php
namespace App\Livewire\Posts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Post;

class PostList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        return view('livewire.posts.post-list', [
            'posts' => Post::orderBy('created_at', 'desc')->paginate(10)
        ]);
    }
}
