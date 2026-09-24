<?php

use App\Models\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pages'), Layout('layouts::bare')] class extends Component
{
    #[Computed]
    public function managed()
    {
        return auth()->user()->ownedPages()->withCount('likers')->orderBy('name')->get();
    }

    #[Computed]
    public function liked()
    {
        return auth()->user()->likedPages()->withCount('likers')->orderBy('name')->get();
    }

    #[Computed]
    public function discover()
    {
        return Page::withCount('likers')
            ->where('owner_id', '!=', auth()->id())
            ->whereDoesntHave('likers', fn ($q) => $q->where('users.id', auth()->id()))
            ->latest()
            ->get();
    }

    public function like(int $pageId): void
    {
        Page::findOrFail($pageId)->like(auth()->user());
        unset($this->liked, $this->discover);
    }

    public function unlike(int $pageId): void
    {
        Page::findOrFail($pageId)->unlike(auth()->user());
        unset($this->liked, $this->discover);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Pages'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Pages You Manage <span>{{ $this->managed->count() }}</span></h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								<div class="col-lg-3 col-md-4 col-sm-6">
									<a href="{{ route('pages.create') }}">
										<div class="addgroup">
											<div class="item-upload">
												<i class="fa fa-plus-circle"></i>
												<div class="upload-meta">
													<h5>Create Page</h5>
													<span>for your brand, business or community</span>
												</div>
											</div>
										</div>
									</a>
								</div>
								@foreach($this->managed as $page)
									<x-pitnik.page-box :page="$page" status="owner" wire:key="managed-{{ $page->id }}" />
								@endforeach
							</div>
						</div>

						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Pages You Like <span>{{ $this->liked->count() }}</span></h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->liked as $page)
									<x-pitnik.page-box :page="$page" status="liked" wire:key="liked-{{ $page->id }}" />
								@empty
									<div class="col-lg-12"><p>You haven't liked any pages yet.</p></div>
								@endforelse
							</div>
						</div>

						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Discover Pages <span>{{ $this->discover->count() }}</span></h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->discover as $page)
									<x-pitnik.page-box :page="$page" status="none" wire:key="discover-{{ $page->id }}" />
								@empty
									<div class="col-lg-12"><p>No other pages to discover yet.</p></div>
								@endforelse
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
