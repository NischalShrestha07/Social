<?php

namespace Tests\Feature\Forum;

use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\User;
use App\Notifications\ForumPostReplied;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ForumTopicTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('forum'))->assertRedirect(route('login'));
    }

    public function test_forum_index_lists_categories(): void
    {
        $user = User::factory()->create();
        ForumCategory::factory()->create(['name' => 'General Chat']);

        Livewire::actingAs($user)
            ->test('pages::forum')
            ->assertOk()
            ->assertSee('General Chat');
    }

    public function test_category_page_lists_its_topics(): void
    {
        $user = User::factory()->create();
        $category = ForumCategory::factory()->create(['name' => 'Announcements']);
        ForumTopic::factory()->create([
            'forum_category_id' => $category->id,
            'title' => 'Welcome Everyone',
        ]);

        Livewire::actingAs($user)
            ->test('pages::forum-category', ['category' => $category])
            ->assertOk()
            ->assertSee('Welcome Everyone');
    }

    public function test_pinned_topics_sort_above_others(): void
    {
        $user = User::factory()->create();
        $category = ForumCategory::factory()->create();

        ForumTopic::factory()->create([
            'forum_category_id' => $category->id,
            'title' => 'Ordinary Topic',
            'last_posted_at' => now(),
        ]);
        ForumTopic::factory()->pinned()->create([
            'forum_category_id' => $category->id,
            'title' => 'Pinned Topic',
            'last_posted_at' => now()->subWeek(),
        ]);

        $component = Livewire::actingAs($user)
            ->test('pages::forum-category', ['category' => $category])
            ->assertOk();

        $rendered = $component->html();

        $this->assertLessThan(
            strpos($rendered, 'Ordinary Topic'),
            strpos($rendered, 'Pinned Topic'),
            'Pinned topic should render before the more recent unpinned one.',
        );
    }

    public function test_search_filters_topics_in_a_category(): void
    {
        $user = User::factory()->create();
        $category = ForumCategory::factory()->create();
        ForumTopic::factory()->create(['forum_category_id' => $category->id, 'title' => 'Laravel Tips']);
        ForumTopic::factory()->create(['forum_category_id' => $category->id, 'title' => 'Cat Photos']);

        Livewire::actingAs($user)
            ->test('pages::forum-category', ['category' => $category])
            ->set('search', 'Laravel')
            ->assertSee('Laravel Tips')
            ->assertDontSee('Cat Photos');
    }

    public function test_any_user_can_create_a_topic(): void
    {
        $user = User::factory()->create();
        $category = ForumCategory::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::forum-topic-create', ['category' => $category])
            ->set('title', 'How do I learn Blade?')
            ->set('body', 'I am new to Laravel.')
            ->call('save')
            ->assertHasNoErrors();

        $topic = ForumTopic::firstOrFail();

        $this->assertSame('How do I learn Blade?', $topic->title);
        $this->assertSame('how-do-i-learn-blade', $topic->slug);
        $this->assertSame($user->id, $topic->user_id);
        $this->assertSame($category->id, $topic->forum_category_id);
    }

    public function test_topic_creation_validates_input(): void
    {
        $category = ForumCategory::factory()->create();

        Livewire::actingAs(User::factory()->create())
            ->test('pages::forum-topic-create', ['category' => $category])
            ->set('title', '')
            ->set('body', '')
            ->call('save')
            ->assertHasErrors(['title', 'body']);

        $this->assertSame(0, ForumTopic::count());
    }

    public function test_duplicate_topic_titles_get_unique_slugs(): void
    {
        $category = ForumCategory::factory()->create();
        ForumTopic::factory()->create(['title' => 'Help Me', 'slug' => 'help-me']);

        Livewire::actingAs(User::factory()->create())
            ->test('pages::forum-topic-create', ['category' => $category])
            ->set('title', 'Help Me')
            ->set('body', 'Body')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('help-me-1', ForumTopic::latest('id')->first()->slug);
    }

    public function test_a_user_can_reply_to_an_open_topic(): void
    {
        Notification::fake();

        $topic = ForumTopic::factory()->create();
        $replier = User::factory()->create();

        Livewire::actingAs($replier)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->set('reply', 'Here is my answer.')
            ->call('addReply')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('forum_posts', [
            'forum_topic_id' => $topic->id,
            'user_id' => $replier->id,
            'body' => 'Here is my answer.',
        ]);

        Notification::assertSentTo($topic->user, ForumPostReplied::class);
    }

    public function test_replying_bumps_last_posted_at_and_notifies_participants_once(): void
    {
        Notification::fake();

        $topic = ForumTopic::factory()->create(['last_posted_at' => now()->subMonth()]);
        $firstReplier = User::factory()->create();
        $secondReplier = User::factory()->create();

        // The first replier posts twice.
        Livewire::actingAs($firstReplier)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->set('reply', 'First')
            ->call('addReply')
            ->assertHasNoErrors();

        Livewire::actingAs($firstReplier)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->set('reply', 'Second')
            ->call('addReply')
            ->assertHasNoErrors();

        $this->assertTrue($topic->refresh()->last_posted_at->isToday());

        Notification::assertSentToTimes($topic->user, ForumPostReplied::class, 2);

        Livewire::actingAs($secondReplier)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->set('reply', 'Third')
            ->call('addReply')
            ->assertHasNoErrors();

        Notification::assertSentToTimes($firstReplier, ForumPostReplied::class, 1);
    }

    public function test_nobody_can_reply_to_a_topic_locked_by_someone_else(): void
    {
        $topicAuthor = User::factory()->create();
        $moderator = User::factory()->create();
        $topic = ForumTopic::factory()->lockedBy($moderator)->create(['user_id' => $topicAuthor->id]);
        $intruder = User::factory()->create();

        Livewire::actingAs($intruder)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('addReply')
            ->assertForbidden();

        $this->assertSame(0, ForumPost::count());
    }

    public function test_the_locker_can_still_reply_after_locking(): void
    {
        $moderator = User::factory()->create();
        $topic = ForumTopic::factory()->lockedBy($moderator)->create();

        Livewire::actingAs($moderator)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->set('reply', 'Closing this out.')
            ->call('addReply')
            ->assertHasNoErrors();

        $this->assertSame(1, ForumPost::count());
    }

    public function test_the_topic_author_can_reply_after_locking(): void
    {
        $author = User::factory()->create();
        $moderator = User::factory()->create();
        $topic = ForumTopic::factory()->lockedBy($moderator)->create(['user_id' => $author->id]);

        Livewire::actingAs($author)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->set('reply', 'One more thing.')
            ->call('addReply')
            ->assertHasNoErrors();

        $this->assertSame(1, ForumPost::count());
    }

    public function test_author_can_lock_and_unlock_their_topic(): void
    {
        $author = User::factory()->create();
        $topic = ForumTopic::factory()->create(['user_id' => $author->id]);

        Livewire::actingAs($author)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('toggleLock')
            ->assertHasNoErrors();

        $topic->refresh();
        $this->assertTrue($topic->is_locked);
        $this->assertSame($author->id, $topic->locked_by);

        Livewire::actingAs($author)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('toggleLock')
            ->assertHasNoErrors();

        $topic->refresh();
        $this->assertFalse($topic->is_locked);
        $this->assertNull($topic->locked_by);
    }

    public function test_a_stranger_cannot_lock_a_topic(): void
    {
        $topic = ForumTopic::factory()->create();

        Livewire::actingAs(User::factory()->create())
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('toggleLock')
            ->assertForbidden();

        $this->assertFalse($topic->refresh()->is_locked);
    }

    public function test_only_the_lock_owner_can_unlock(): void
    {
        $topicAuthor = User::factory()->create();
        $moderator = User::factory()->create();
        $topic = ForumTopic::factory()->lockedBy($moderator)->create(['user_id' => $topicAuthor->id]);

        Livewire::actingAs($topicAuthor)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('toggleLock')
            ->assertForbidden();

        $this->assertTrue($topic->refresh()->is_locked);
    }

    public function test_author_can_pin_and_unpin(): void
    {
        $author = User::factory()->create();
        $topic = ForumTopic::factory()->create(['user_id' => $author->id]);

        Livewire::actingAs($author)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('togglePin')
            ->assertHasNoErrors();

        $this->assertTrue($topic->refresh()->is_pinned);
    }

    public function test_a_stranger_cannot_pin_a_topic(): void
    {
        $topic = ForumTopic::factory()->create();

        Livewire::actingAs(User::factory()->create())
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('togglePin')
            ->assertForbidden();

        $this->assertFalse($topic->refresh()->is_pinned);
    }

    public function test_only_the_topic_author_can_delete_the_topic(): void
    {
        $author = User::factory()->create();
        $topic = ForumTopic::factory()->create(['user_id' => $author->id]);

        Livewire::actingAs(User::factory()->create())
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('deleteTopic')
            ->assertForbidden();

        $this->assertDatabaseHas('forum_topics', ['id' => $topic->id]);

        Livewire::actingAs($author)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('deleteTopic')
            ->assertRedirect();

        $this->assertDatabaseMissing('forum_topics', ['id' => $topic->id]);
    }

    public function test_a_reply_author_can_delete_their_reply(): void
    {
        $topic = ForumTopic::factory()->create();
        $author = User::factory()->create();
        $post = ForumPost::factory()->create([
            'forum_topic_id' => $topic->id,
            'user_id' => $author->id,
        ]);

        Livewire::actingAs(User::factory()->create())
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('deletePost', $post->id)
            ->assertForbidden();

        $this->assertDatabaseHas('forum_posts', ['id' => $post->id]);

        Livewire::actingAs($author)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('deletePost', $post->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('forum_posts', ['id' => $post->id]);
    }

    public function test_a_user_cannot_touch_a_reply_from_another_topic(): void
    {
        $topic = ForumTopic::factory()->create();
        $otherTopic = ForumTopic::factory()->create();
        $foreignPost = ForumPost::factory()->create(['forum_topic_id' => $otherTopic->id]);

        Livewire::actingAs(User::factory()->create())
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('deletePost', $foreignPost->id)
            ->assertNotFound();

        $this->assertDatabaseHas('forum_posts', ['id' => $foreignPost->id]);
    }

    public function test_users_can_like_and_unlike_replies(): void
    {
        $topic = ForumTopic::factory()->create();
        $user = User::factory()->create();
        $post = ForumPost::factory()->create(['forum_topic_id' => $topic->id]);

        Livewire::actingAs($user)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('togglePostLike', $post->id)
            ->assertHasNoErrors();

        $this->assertSame(1, $post->refresh()->likesCount());
        $this->assertTrue($post->isLikedBy($user));

        Livewire::actingAs($user)
            ->test('pages::forum-topic', ['topic' => $topic])
            ->call('togglePostLike', $post->id)
            ->assertHasNoErrors();

        $this->assertSame(0, $post->refresh()->likesCount());
    }

    public function test_deleting_a_topic_cascades_to_its_replies(): void
    {
        $topic = ForumTopic::factory()->create();
        ForumPost::factory()->count(3)->create(['forum_topic_id' => $topic->id]);

        $topic->delete();

        $this->assertSame(0, ForumPost::count());
    }

    public function test_deleting_a_category_cascades_to_topics_and_replies(): void
    {
        $category = ForumCategory::factory()->create();
        $topic = ForumTopic::factory()->create(['forum_category_id' => $category->id]);
        ForumPost::factory()->count(2)->create(['forum_topic_id' => $topic->id]);

        $category->delete();

        $this->assertSame(0, ForumTopic::count());
        $this->assertSame(0, ForumPost::count());
    }
}
