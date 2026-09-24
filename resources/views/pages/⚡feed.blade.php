<?php

use App\Livewire\Concerns\InteractsWithPosts;
use App\Models\Post;
use App\Models\Story;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Newsfeed'), \Livewire\Attributes\Layout('layouts::bare')] class extends Component
{
    use InteractsWithPosts, WithFileUploads;

    public $storyImage = null;

    public string $storyCaption = '';

    #[Computed]
    public function posts()
    {
        return Post::whereNull('group_id')->with([
            'user',
            'comments.user',
            'comments.reactions',
            'comments.replies.user',
            'comments.replies.reactions',
            'reactions',
        ])->latest()->get();
    }

    #[Computed]
    public function activeStoryGroups()
    {
        return Story::active()
            ->where('user_id', '!=', auth()->id())
            ->with('user')
            ->latest()
            ->get()
            ->groupBy('user_id');
    }

    public function createStory(): void
    {
        $this->validate([
            'storyImage' => 'required|image|max:5120',
            'storyCaption' => 'nullable|string|max:255',
        ]);

        auth()->user()->stories()->create([
            'media_path' => $this->storyImage->store('stories', 'public'),
            'caption' => $this->storyCaption ?: null,
            'expires_at' => now()->addDay(),
        ]);

        $this->reset(['storyImage', 'storyCaption']);
        unset($this->activeStoryGroups);
        $this->dispatch('story-created');
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Newsfeed'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="row merged20" id="page-contents">
							<x-pitnik.feed-sidebar-left />
							<div class="col-lg-6">

									<x-pitnik.post-composer :image="$image" />

								<div class="central-meta">
									<x-pitnik.story-bar :story-image="$storyImage" :groups="$this->activeStoryGroups" />
								</div><!-- top stories -->

									@foreach($this->posts as $post)
										<x-pitnik.post-card :post="$post" wire:key="post-{{ $post->id }}" />
									@endforeach

							</div><!-- centerl meta -->
							<x-pitnik.feed-sidebar-right />
						</div>	
					</div>
				</div>
			</div>
		</div>	
	</section>
@endcomponent
