<?php
namespace App\Livewire\Posts;

use Livewire\Component;
use Livewire\Attributes\Session;
use Livewire\WithPagination;
use App\Models\Post;

class PostList extends Component
{
    use WithPagination; // Dynmic pagination

    protected $paginationTheme = 'bootstrap';
    
    #[Session]
    public string $search = '';

    #[Session]
    public string $sortField = 'created_at';

    #[Session]
    public string $sortDirection = 'desc';

    private array $sortableFields = [
        'title',
        'status',
        'created_at',
        'updated_at',
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
        $query = Post::with(['categories', 'tags'])
                    ->orderBy($this->sortField, $this->sortDirection);

        if ($this->search) {
            $query->where(function($q) {
            $q->where('title', 'like', "%{$this->search}%")
              ->orWhereHas('categories', function($q2) {
                  $q2->where('name', 'like', "%{$this->search}%");
              })
              ->orWhereHas('tags', function($q3) {
                  $q3->where('name', 'like', "%{$this->search}%");
              });
        });
    }

    return view('livewire.posts.post-list', [
        'posts' => $query->paginate(10)->withPath(route('admin.blog.posts'))
    ]);
    }
}
