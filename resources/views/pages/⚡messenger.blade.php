<?php

use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Messages'), \Livewire\Attributes\Layout('layouts::bare')] class extends Component
{
    public ?User $with = null;

    public function mount(?User $user = null): void
    {
        if ($user?->is(auth()->user())) {
            $this->redirectRoute('messages');

            return;
        }

        $this->with = $user;
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Messages'])
	<section>
		<div class="gap2 no-gap gray-bg">
			<div class="container-fluid no-padding">
				<div class="row">
					<div class="col-lg-12">
						<livewire:messenger :with="$with" />
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
