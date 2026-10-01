<?php

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('New Topic'), Layout('layouts::bare')] class extends Component
{
    public ForumCategory $category;

    public string $title = '';

    public string $body = '';

    public function mount(ForumCategory $category): void
    {
        $this->category = $category;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string|max:20000',
        ]);

        $topic = ForumTopic::create([
            'forum_category_id' => $this->category->id,
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => ForumTopic::uniqueSlug($validated['title']),
            'body' => $validated['body'],
            'last_posted_at' => now(),
        ]);

        $this->redirectRoute('forum.topic', $topic);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'New Topic'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-pencil-alt"></i> New Topic in {{ $this->category->name }}</h5>
								<form wire:submit="save" class="c-form">
									<div>
										<label>Topic title</label>
										<input type="text" wire:model="title" required maxlength="200" placeholder="What do you want to discuss?">
										@error('title') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Your post</label>
										<textarea rows="10" wire:model="body" required maxlength="20000" placeholder="Write your topic here"></textarea>
										@error('body') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Create Topic</button>
										<a href="{{ route('forum.category', $this->category) }}" class="main-btn3">Cancel</a>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent