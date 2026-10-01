<?php

namespace Tests\Feature\Video;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoChannel;
use App\Models\VideoComment;
use App\Notifications\NewChannelSubscriber;
use App\Notifications\VideoCommented;
use App\Notifications\VideoLiked;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('videos'))->assertRedirect(route('login'));
    }

    public function test_pittube_renders(): void
    {
        $user = User::factory()->create();
        Video::factory()->create(['title' => 'Livewire Deep Dive']);

        Livewire::actingAs($user)
            ->test('pages::videos')
            ->assertOk()
            ->assertSee('Livewire Deep Dive');
    }

    public function test_only_public_videos_appear_in_the_browse_grid(): void
    {
        $user = User::factory()->create();
        Video::factory()->create(['title' => 'Public Video']);
        Video::factory()->unlisted()->create(['title' => 'Unlisted Video']);
        Video::factory()->private()->create(['title' => 'Private Video']);

        Livewire::actingAs($user)
            ->test('pages::videos')
            ->assertSee('Public Video')
            ->assertDontSee('Unlisted Video')
            ->assertDontSee('Private Video');
    }

    public function test_search_filters_by_title(): void
    {
        $user = User::factory()->create();
        Video::factory()->create(['title' => 'Queues In Production']);
        Video::factory()->create(['title' => 'Sourdough Basics']);

        Livewire::actingAs($user)
            ->test('pages::videos')
            ->set('search', 'Queues')
            ->assertSee('Queues In Production')
            ->assertDontSee('Sourdough Basics');
    }

    public function test_videos_can_be_sorted_by_views(): void
    {
        $user = User::factory()->create();
        Video::factory()->create(['title' => 'Unwatched', 'views_count' => 10]);
        Video::factory()->create(['title' => 'Viral', 'views_count' => 50000]);

        Livewire::actingAs($user)
            ->test('pages::videos')
            ->set('sort', 'views')
            ->assertSeeInOrder(['Viral', 'Unwatched']);
    }

    public function test_clear_filters_resets_the_query_string(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::videos')
            ->set('search', 'Queues')
            ->set('sort', 'views')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('sort', 'newest');
    }

    public function test_a_user_can_create_a_channel(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::channel-create')
            ->set('name', 'Laravel Lens')
            ->set('description', 'Weekly deep dives.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('video_channels', [
            'user_id' => $user->id,
            'name' => 'Laravel Lens',
            'slug' => 'laravel-lens',
        ]);
    }

    public function test_channel_creation_validates_input(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::channel-create')
            ->set('name', '')
            ->call('save')
            ->assertHasErrors('name');

        $this->assertDatabaseCount('video_channels', 0);
    }

    public function test_channel_names_are_unique(): void
    {
        VideoChannel::factory()->create(['name' => 'Laravel Lens']);
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::channel-create')
            ->set('name', 'Laravel Lens')
            ->call('save')
            ->assertHasErrors('name');

        $this->assertDatabaseCount('video_channels', 1);
    }

    public function test_duplicate_channel_names_get_unique_slugs(): void
    {
        VideoChannel::factory()->create(['name' => 'Kitchen Chronicles', 'slug' => 'kitchen-chronicles']);
        $channel = VideoChannel::factory()->create([
            'name' => 'Kitchen Chronicles',
            'slug' => 'kitchen-chronicles-1',
        ]);

        $this->assertSame('kitchen-chronicles-1', $channel->slug);
    }

    public function test_channel_page_lists_videos(): void
    {
        $user = User::factory()->create();
        $channel = VideoChannel::factory()->create(['name' => 'Kitchen Chronicles']);
        Video::factory()->for($channel, 'channel')->create(['title' => 'Sourdough']);

        Livewire::actingAs($user)
            ->test('pages::channel', ['channel' => $channel])
            ->assertOk()
            ->assertSee('Kitchen Chronicles')
            ->assertSee('Sourdough');
    }

    public function test_only_the_owner_can_open_the_manage_page(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create();

        $this->actingAs($stranger)->get(route('videos.channel.manage', $channel))->assertForbidden();

        $this->actingAs($owner)
            ->get(route('videos.channel.manage', $channel))
            ->assertOk();
    }

    public function test_the_owner_can_rename_their_channel(): void
    {
        $owner = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create([
            'name' => 'Old Name',
            'slug' => 'old-name',
        ]);

        Livewire::actingAs($owner)
            ->test('pages::channel-manage', ['channel' => $channel])
            ->set('name', 'New Name')
            ->call('save')
            ->assertHasNoErrors();

        $channel->refresh();

        $this->assertSame('New Name', $channel->name);
        $this->assertSame('new-name', $channel->slug);
    }

    public function test_only_the_owner_can_delete_a_channel(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create();

        // Authorization happens in mount(), so assert it on the HTTP route.
        $this->actingAs($stranger)->get(route('videos.channel.manage', $channel))->assertForbidden();

        Livewire::actingAs($owner)
            ->test('pages::channel-manage', ['channel' => $channel])
            ->call('deleteChannel');

        $this->assertDatabaseMissing('video_channels', ['id' => $channel->id]);
    }

    public function test_users_can_subscribe_and_unsubscribe(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create();

        Livewire::actingAs($viewer)
            ->test('pages::channel', ['channel' => $channel])
            ->call('toggleSubscribe');

        $this->assertTrue($channel->fresh()->isSubscribedBy($viewer));
        $this->assertSame(1, $channel->fresh()->subscribers_count);

        Notification::assertSentTo($owner, NewChannelSubscriber::class);

        Livewire::actingAs($viewer)
            ->test('pages::channel', ['channel' => $channel])
            ->call('toggleSubscribe');

        $this->assertFalse($channel->fresh()->isSubscribedBy($viewer));
        $this->assertSame(0, $channel->fresh()->subscribers_count);
    }

    public function test_owners_cannot_subscribe_to_their_own_channel(): void
    {
        $owner = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create();

        $channel->subscribe($owner);

        $this->assertFalse($channel->isSubscribedBy($owner));
        $this->assertSame(0, $channel->subscribers_count);
    }

    public function test_subscribing_twice_does_not_double_count(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create();

        $channel->subscribe($viewer);
        $channel->subscribe($viewer);

        $this->assertSame(1, $channel->fresh()->subscribers_count);
    }

    public function test_unsubscribing_without_a_subscription_is_a_no_op(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create();

        $channel->unsubscribe($viewer);

        $this->assertSame(0, $channel->subscribers_count);
    }

    public function test_only_the_owner_can_upload_to_a_channel(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create();

        $this->actingAs($stranger)->get(route('videos.upload', $channel))->assertForbidden();

        $this->actingAs($owner)
            ->get(route('videos.upload', $channel))
            ->assertOk();
    }

    public function test_the_owner_can_upload_a_video(): void
    {
        $owner = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create();

        Livewire::actingAs($owner)
            ->test('pages::video-upload', ['channel' => $channel])
            ->set('title', 'Queues In Production')
            ->set('durationMinutes', '12')
            ->set('durationSeconds', '30')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('videos', [
            'video_channel_id' => $channel->id,
            // The uploader is the channel owner, not the person who pressed upload.
            'user_id' => $owner->id,
            'title' => 'Queues In Production',
            'duration_seconds' => 750,
        ]);
    }

    public function test_upload_validates_input_and_visibility(): void
    {
        $owner = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create();

        Livewire::actingAs($owner)
            ->test('pages::video-upload', ['channel' => $channel])
            ->set('title', '')
            ->set('durationSeconds', '99')
            ->set('visibility', 'secret')
            ->call('save')
            ->assertHasErrors(['title', 'durationSeconds', 'visibility']);

        $this->assertDatabaseCount('videos', 0);
    }

    public function test_video_page_renders(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->create(['title' => 'Sourdough For The Impatient']);

        Livewire::actingAs($user)
            ->test('pages::video', ['video' => $video])
            ->assertOk()
            ->assertSee('Sourdough For The Impatient');
    }

    public function test_private_videos_are_hidden_from_strangers(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $video = Video::factory()->private()->for($owner, 'uploader')->create();

        $this->actingAs($stranger)->get(route('videos.show', $video))->assertForbidden();

        $this->actingAs($owner)
            ->get(route('videos.show', $video))
            ->assertOk();
    }

    public function test_unlisted_videos_are_reachable_by_link(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->unlisted()->create();

        $this->actingAs($user)
            ->get(route('videos.show', $video))
            ->assertOk();
    }

    public function test_strangers_see_only_public_and_unlisted_videos_on_a_channel(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create();
        Video::factory()->for($channel, 'channel')->create(['title' => 'Public One']);
        Video::factory()->unlisted()->for($channel, 'channel')->create(['title' => 'Unlisted One']);
        Video::factory()->private()->for($channel, 'channel')->create(['title' => 'Private One']);

        Livewire::actingAs($stranger)
            ->test('pages::channel', ['channel' => $channel])
            ->assertSee('Public One')
            ->assertSee('Unlisted One')
            ->assertDontSee('Private One');

        Livewire::actingAs($owner)
            ->test('pages::channel', ['channel' => $channel])
            ->assertSee('Private One');
    }

    public function test_only_the_uploader_can_edit_a_video(): void
    {
        $uploader = User::factory()->create();
        $stranger = User::factory()->create();
        $video = Video::factory()->for($uploader, 'uploader')->create();

        $this->actingAs($stranger)->get(route('videos.edit', $video))->assertForbidden();

        $this->actingAs($uploader)
            ->get(route('videos.edit', $video))
            ->assertOk();
    }

    public function test_the_uploader_can_edit_their_video(): void
    {
        $uploader = User::factory()->create();
        $video = Video::factory()->for($uploader, 'uploader')->create([
            'title' => 'Old Title',
            'slug' => 'old-title',
        ]);

        Livewire::actingAs($uploader)
            ->test('pages::video-edit', ['video' => $video])
            ->set('title', 'Brand New Title')
            ->set('visibility', 'unlisted')
            ->call('save')
            ->assertHasNoErrors();

        $video->refresh();

        $this->assertSame('Brand New Title', $video->title);
        $this->assertSame('brand-new-title', $video->slug);
        $this->assertSame('unlisted', $video->visibility);
    }

    public function test_only_the_uploader_can_delete_a_video(): void
    {
        $uploader = User::factory()->create();
        $stranger = User::factory()->create();
        $video = Video::factory()->for($uploader, 'uploader')->create();

        Livewire::actingAs($stranger)
            ->test('pages::video', ['video' => $video])
            ->call('deleteVideo')
            ->assertForbidden();

        $this->assertDatabaseHas('videos', ['id' => $video->id]);

        Livewire::actingAs($uploader)
            ->test('pages::video', ['video' => $video])
            ->call('deleteVideo');

        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }

    public function test_users_can_like_and_unlike_a_video(): void
    {
        Notification::fake();

        $uploader = User::factory()->create();
        $viewer = User::factory()->create();
        $video = Video::factory()->for($uploader, 'uploader')->create();

        Livewire::actingAs($viewer)
            ->test('pages::video', ['video' => $video])
            ->call('toggleLike');

        $this->assertTrue($video->fresh()->isLikedBy($viewer));
        $this->assertSame(1, $video->fresh()->likesCount());

        Notification::assertSentTo($uploader, VideoLiked::class);

        Livewire::actingAs($viewer)
            ->test('pages::video', ['video' => $video])
            ->call('toggleLike');

        $this->assertFalse($video->fresh()->isLikedBy($viewer));
    }

    public function test_a_user_can_comment_on_a_video(): void
    {
        Notification::fake();

        $uploader = User::factory()->create();
        $viewer = User::factory()->create();
        $video = Video::factory()->for($uploader, 'uploader')->create();

        Livewire::actingAs($viewer)
            ->test('pages::video', ['video' => $video])
            ->set('commentBody', 'Great explanation.')
            ->call('addComment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('video_comments', [
            'video_id' => $video->id,
            'user_id' => $viewer->id,
            'parent_id' => null,
        ]);

        Notification::assertSentTo($uploader, VideoCommented::class);
    }

    public function test_commenting_validates_input(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::video', ['video' => $video])
            ->set('commentBody', '')
            ->call('addComment')
            ->assertHasErrors('commentBody');
    }

    public function test_a_user_can_reply_to_a_comment(): void
    {
        Notification::fake();

        $uploader = User::factory()->create();
        $viewer = User::factory()->create();
        $replier = User::factory()->create();
        $video = Video::factory()->for($uploader, 'uploader')->create();
        $comment = VideoComment::factory()->create([
            'video_id' => $video->id,
            'user_id' => $viewer->id,
        ]);

        Livewire::actingAs($replier)
            ->test('pages::video', ['video' => $video])
            ->set('commentBody', 'Same here.')
            ->set('replyTo', $comment->id)
            ->call('addComment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('video_comments', [
            'video_id' => $video->id,
            'user_id' => $replier->id,
            'parent_id' => $comment->id,
        ]);

        Notification::assertSentTo($viewer, VideoCommented::class);
    }

    public function test_a_reply_cannot_be_parented_to_a_comment_on_another_video(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->create();
        $foreignComment = VideoComment::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::video', ['video' => $video])
            ->set('commentBody', 'Hijacking another thread.')
            ->set('replyTo', $foreignComment->id)
            ->call('addComment')
            ->assertNotFound();

        $this->assertDatabaseMissing('video_comments', ['user_id' => $user->id]);
    }

    public function test_replying_to_your_own_comment_notifies_nobody(): void
    {
        Notification::fake();

        $viewer = User::factory()->create();
        $video = Video::factory()->create();
        $comment = VideoComment::factory()->create([
            'video_id' => $video->id,
            'user_id' => $viewer->id,
        ]);

        Livewire::actingAs($viewer)
            ->test('pages::video', ['video' => $video])
            ->set('commentBody', 'Adding to my own point.')
            ->set('replyTo', $comment->id)
            ->call('addComment');

        Notification::assertNothingSent();
    }

    public function test_comment_author_can_delete_their_comment(): void
    {
        $uploader = User::factory()->create();
        $viewer = User::factory()->create();
        $video = Video::factory()->for($uploader, 'uploader')->create();
        $comment = VideoComment::factory()->create([
            'video_id' => $video->id,
            'user_id' => $viewer->id,
        ]);

        Livewire::actingAs($viewer)
            ->test('pages::video', ['video' => $video])
            ->call('deleteComment', $comment->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('video_comments', ['id' => $comment->id]);
    }

    public function test_the_uploader_can_delete_any_comment_on_their_video(): void
    {
        $uploader = User::factory()->create();
        $viewer = User::factory()->create();
        $video = Video::factory()->for($uploader, 'uploader')->create();
        $comment = VideoComment::factory()->create([
            'video_id' => $video->id,
            'user_id' => $viewer->id,
        ]);

        Livewire::actingAs($uploader)
            ->test('pages::video', ['video' => $video])
            ->call('deleteComment', $comment->id);

        $this->assertDatabaseMissing('video_comments', ['id' => $comment->id]);
    }

    public function test_strangers_cannot_delete_comments(): void
    {
        $uploader = User::factory()->create();
        $viewer = User::factory()->create();
        $stranger = User::factory()->create();
        $video = Video::factory()->for($uploader, 'uploader')->create();
        $comment = VideoComment::factory()->create([
            'video_id' => $video->id,
            'user_id' => $viewer->id,
        ]);

        Livewire::actingAs($stranger)
            ->test('pages::video', ['video' => $video])
            ->call('deleteComment', $comment->id)
            ->assertForbidden();

        $this->assertDatabaseHas('video_comments', ['id' => $comment->id]);
    }

    public function test_a_comment_cannot_be_deleted_through_another_video(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->for($user, 'uploader')->create();
        $foreignComment = VideoComment::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::video', ['video' => $video])
            ->call('deleteComment', $foreignComment->id)
            ->assertNotFound();

        $this->assertDatabaseHas('video_comments', ['id' => $foreignComment->id]);
    }

    public function test_replies_can_be_deleted_from_the_video_page(): void
    {
        $viewer = User::factory()->create();
        $video = Video::factory()->create();
        $parent = VideoComment::factory()->create(['video_id' => $video->id]);
        $reply = VideoComment::factory()->create([
            'video_id' => $video->id,
            'user_id' => $viewer->id,
            'parent_id' => $parent->id,
        ]);

        Livewire::actingAs($viewer)
            ->test('pages::video', ['video' => $video])
            ->call('deleteComment', $reply->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('video_comments', ['id' => $reply->id]);
        $this->assertDatabaseHas('video_comments', ['id' => $parent->id]);
    }

    public function test_the_channel_owner_can_delete_comments_on_channel_videos(): void
    {
        $channelOwner = User::factory()->create();
        $channel = VideoChannel::factory()->for($channelOwner, 'owner')->create();
        $video = Video::factory()->for($channel, 'channel')->create([
            'user_id' => $channelOwner->id,
        ]);
        $comment = VideoComment::factory()->create(['video_id' => $video->id]);

        Livewire::actingAs($channelOwner)
            ->test('pages::video', ['video' => $video])
            ->call('deleteComment', $comment->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('video_comments', ['id' => $comment->id]);
    }

    public function test_private_videos_reject_comments_from_strangers(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $video = Video::factory()->private()->for($owner, 'uploader')->create();

        $this->assertFalse(Gate::forUser($stranger)->allows('comment', $video));
        $this->assertTrue(Gate::forUser($owner)->allows('comment', $video));
    }

    public function test_deleting_a_video_cascades_to_comments(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->create();
        VideoComment::factory()->create(['video_id' => $video->id]);

        $video->delete();

        $this->assertDatabaseCount('video_comments', 0);
    }

    public function test_deleting_a_channel_cascades_to_videos_and_subscriptions(): void
    {
        $owner = User::factory()->create();
        $channel = VideoChannel::factory()->for($owner, 'owner')->create();
        $video = Video::factory()->for($channel, 'channel')->create();
        VideoComment::factory()->create(['video_id' => $video->id]);
        $channel->subscribe(User::factory()->create());

        $channel->delete();

        $this->assertDatabaseCount('videos', 0);
        $this->assertDatabaseCount('video_comments', 0);
        $this->assertDatabaseCount('channel_subscriptions', 0);
    }

    public function test_duration_labels_are_formatted(): void
    {
        $this->assertNull((new Video(['duration_seconds' => null]))->durationLabel());
        $this->assertSame('2:05', (new Video(['duration_seconds' => 125]))->durationLabel());
        $this->assertSame('1:01:01', (new Video(['duration_seconds' => 3661]))->durationLabel());
    }
}
