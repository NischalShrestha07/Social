@props(['post'])

<div class="central-meta item">
	<div class="user-post">
		<div class="friend-info">
			<figure>
				<a href="{{ route('profile.show', $post->user) }}" title="">
					<img src="{{ $post->user->avatarUrl() }}" alt="">
				</a>
			</figure>
			<div class="friend-name">
				<ins><a href="{{ route('profile.show', $post->user) }}" title="">{{ $post->user->name }}</a></ins>
				<span><i class="fa fa-globe"></i> published: {{ $post->created_at->diffForHumans() }}</span>
			</div>
			<div class="post-meta">
				@if($post->body)
					<p>{{ $post->body }}</p>
				@endif

				@if($post->image_path)
					<figure>
						<img src="{{ asset('storage/'.$post->image_path) }}" alt="">
					</figure>
				@endif

				<ul class="like-dislike">
					<li>
						<a
							class="bg-blue @if($post->isLikedBy(auth()->user())) active @endif"
							href="#"
							title="Like Post"
							wire:click.prevent="toggleLike({{ $post->id }})"
						><i class="ti-thumb-up"></i></a>
					</li>
				</ul>

				<div class="we-video-info">
					<ul>
						<li>
							<div class="likes heart" title="Like/Dislike">❤ <span>{{ $post->likesCount() }}</span></div>
						</li>
						<li>
							<span class="comment" title="Comments">
								<i class="fa fa-commenting"></i>
								<ins>{{ $post->comments->count() }}</ins>
							</span>
						</li>
					</ul>
				</div>
			</div>

			<div class="coment-area" style="display: block;">
				<ul class="we-comet">
					@foreach($post->comments as $comment)
						<li>
							<div class="comet-avatar">
								<img src="{{ $comment->user->avatarUrl() }}" alt="">
							</div>
							<div class="we-comment">
								<h5><a href="{{ route('profile.show', $comment->user) }}" title="">{{ $comment->user->name }}</a></h5>
								<p>{{ $comment->body }}</p>
								<div class="inline-itms">
									<span>{{ $comment->created_at->diffForHumans() }}</span>
									<a href="#" title="" wire:click.prevent="toggleCommentLike({{ $comment->id }})">
										<i class="fa fa-heart"></i><span>{{ $comment->reactions->where('type', 'like')->count() }}</span>
									</a>
								</div>
							</div>
						</li>

						@foreach($comment->replies as $reply)
							<li style="margin-left: 40px;">
								<div class="comet-avatar">
									<img src="{{ $reply->user->avatarUrl() }}" alt="">
								</div>
								<div class="we-comment">
									<h5><a href="{{ route('profile.show', $reply->user) }}" title="">{{ $reply->user->name }}</a></h5>
									<p>{{ $reply->body }}</p>
									<div class="inline-itms">
										<span>{{ $reply->created_at->diffForHumans() }}</span>
										<a href="#" title="" wire:click.prevent="toggleCommentLike({{ $reply->id }})">
											<i class="fa fa-heart"></i><span>{{ $reply->reactions->where('type', 'like')->count() }}</span>
										</a>
									</div>
								</div>
							</li>
						@endforeach
					@endforeach

					<li
						class="post-comment"
						x-data="{ body: '' }"
						x-on:submit.prevent="$wire.addComment({{ $post->id }}, body).then(() => body = '')"
					>
						<div class="comet-avatar">
							<img src="{{ auth()->user()->avatarUrl() }}" alt="">
						</div>
						<div class="post-comt-box">
							<form method="post" x-on:submit.prevent="$wire.addComment({{ $post->id }}, body).then(() => body = '')">
								<textarea placeholder="Post your comment" x-model="body"></textarea>
								<button type="submit"></button>
							</form>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
