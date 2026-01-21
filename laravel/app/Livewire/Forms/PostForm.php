<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Post;

class PostForm extends Form
{
    public ?int $id = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string')]
    public string $content = '';

    #[Validate('required|in:draft,published')]
    public string $status = 'draft';

    /**
     * Remplit le formulaire depuis un Post
     */
    public function fillFromModel(Post $post): void
    {
        $this->id      = $post->id;
        $this->title   = $post->title;
        $this->content = $post->content;
        $this->status  = $post->status;
    }

    /**
     * Create ou Update le post
     */
    public function save(): Post
    {
        $this->validate();

        return Post::updateOrCreate(
            ['id' => $this->id],
            [
                'title'   => $this->title,
                'content' => $this->content,
                'status'  => $this->status,
            ]
        );
    }
}
