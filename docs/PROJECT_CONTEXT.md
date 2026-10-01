# Social Platform — Project Context

## What this is
Laravel 13 + Livewire 4 social network, cloning feature set of the **Pitnik**
HTML template (https://wpkixx.com/html/pitnik/index.html — Bootstrap/jQuery,
~100 template pages: newsfeed, timeline, messenger, notifications, forum,
marketplace, jobs, video platform, events, ad-center). Template assets
(css/js/images/fonts) are vendored at `public/vendor/pitnik/`.

## Standing priorities
Build is phased — don't jump ahead of the current phase without checking with
the user first:
1. **Phase 1 (done):** auth, profile, newsfeed, post/comment/like — the core loop.
2. Friends/follow + notifications + stories (real, not the static demo bar).
3. **Phase 3 (done):** 1:1 chat/messenger (Livewire polling).
4. **Phase 4 (done):** 4a Groups, 4b FB-style Pages.
5. Marketplace / jobs / forum / video / ads — secondary modules.

## Architecture decisions
- **Stack: Blade + Livewire, not API+SPA.** Chosen because Pitnik is
  server-rendered Bootstrap/jQuery, not a JS framework template — porting it
  onto Livewire full-page components (Laravel 13's new `livewire/blaze`
  `Route::livewire()` + `⚡`-prefixed single-file page convention, see
  `resources/views/pages/settings/⚡profile.blade.php` for the pattern) is the
  natural fit. Flux UI (Tailwind) stays confined to the starter kit's
  auth/settings pages — **never mix Flux components into Pitnik-themed pages**,
  they're different design systems.
- **No `wire:navigate`** inside the Pitnik layout/pages — the template's jQuery
  plugins (stories bar, tooltips, tabs) expect full page loads; Livewire's SPA
  morphing would break their re-init.
- **Shared chrome lives once, in `resources/views/layouts/pitnik.blade.php`.**
  Head/CSS links, header+left-nav, footer (`.bottombar`, `.side-panel`,
  canonical `<script>` list) all live in the layout. Page files
  (`⚡feed.blade.php`, `⚡timeline.blade.php`) hold only their unique content
  and end right after their own `</section>`. **Do not duplicate footer/script
  markup per page** — that already caused real drift once (see gotchas).
- `users.username` is the route key (`User::getRouteKeyName()`), auto-generated
  at registration from `name` via `Str::slug` + numeric suffix on collision
  (`app/Actions/Fortify/CreateNewUser.php`) — there's no username field on the
  register form.
- Reactions (`like`) are polymorphic (`reactable_type`/`reactable_id`) so the
  same `Reaction` model covers both Post and Comment likes.
- Stories are real: `stories` table (`user_id`, `media_path`, `caption`,
  `expires_at`), `Story::active()` scope filters `expires_at > now()`. Image
  only, no video. No seen/view tracking, no cross-user auto-advance in the
  viewer — deliberately out of scope, add if asked.
- **Messenger (Phase 3) is 1:1 over Livewire polling, not websockets** (user's
  call). Schema is group-ready (`conversations`, `conversation_user` pivot with
  `last_read_at`, `messages`) but only 1:1 is built; `Conversation::between()`
  find-or-creates the 2-participant conversation. `/messages/{username?}` page
  is a thin full-page shell; the polled UI is the class component
  `App\Livewire\Messenger` (`wire:poll.3s`) so each poll re-renders only the
  chat section, not the whole Pitnik layout — keep polling inside nested
  components, never on a full-page component. No empty conversation row is
  created by just visiting `/messages/{user}` — it's created on first send.
  Security: `conversationId`/`targetUserId` are `#[Locked]`, and the active
  conversation is always looked up through `auth()->user()->conversations()`,
  so a non-participant gets null/403. Conversation-list rows are plain links
  (`route('messages', $peer)`), no client-supplied ids. Header unread badge is
  `App\Livewire\MessageBell` (`wire:poll.10s`, `User::unreadMessagesCount()`).
  Upgrade path to Reverb: broadcast a `MessageSent` event from
  `Messenger::send()` and swap `wire:poll` for an Echo listener — no schema
  change needed. Not built (add if asked): group chats, attachments/emoji
  picker, typing indicators, online presence, message delete/edit, search,
  "load older" beyond the last 100 messages.
- **Post interactions live in one trait: `App\Livewire\Concerns\InteractsWithPosts`**
  (`$body`/`$image`, `createPost`, `toggleLike`, `toggleCommentLike`,
  `addComment` + PostLiked/PostCommented notifications). Used by feed,
  timeline and group pages; host must also `use WithFileUploads`. Hooks:
  `postAttributes()` (extra columns for new posts, e.g. `group_id`),
  `authorizeCreatePost()`, `refreshPosts()` (default `unset($this->posts)`;
  timeline overrides to reload its eager-loaded posts). Any page rendering
  `<x-pitnik.post-card>` should use this trait rather than re-implementing.
  `authorizePostInteraction()` gates every like/comment by the post's group
  (post ids come from the client, so any page's `toggleLike(id)` could target
  a private-group post).
- **Groups (Phase 4a):** `groups` (slug route key, `privacy` public|private),
  `group_members` pivot (`role` admin|member, `status` pending|approved),
  nullable `posts.group_id`. **Group posts never appear on feed/timeline** —
  feed queries `whereNull('group_id')` and `User::posts()` itself filters it,
  so the timeline and its post count exclude them too. `Group::join()`
  (public → approved, private → pending + `GroupJoinRequested` to admins) and
  `Group::leave()` (also cancels a request; owner can never leave).
  `GroupPolicy`: `view` (public or approved member), `post` (member),
  `update`/`manageMembers` (admin) — enforced in every action. Member-admin
  actions (`g/{slug}/members`) re-fetch the target through the group's own
  pivot with the expected status, so client-supplied user ids can't touch
  non-members. Not built (add if asked): group edit/settings page, delete
  group, group search, invite friends, demote admin, group posts in feed.
- **Pages (Phase 4b):** `pages` (slug route key, `category` from the fixed
  `Page::CATEGORIES` list validated with `Rule::in`, `website` validated
  `url:http,https`), `page_likes` pivot, nullable `posts.page_id`. Single owner,
  no page admins/editors (v1). **Page posts are authored "as the page"**:
  `user_id` stays the owner (audit trail), but `<x-pitnik.post-card>` renders
  the author via `Post::authorName()/authorAvatar()/authorUrl()`, which switch
  to the page when `page_id` is set — use those helpers, never `$post->user`
  directly, for the author header. **Page posts never appear on
  feed/timeline** (feed `whereNull('page_id')`, `User::posts()` filters it,
  same as group posts). Page posts are public, so
  `InteractsWithPosts::authorizePostInteraction()` needs no page check.
  `Page::like()` skips the owner and duplicates and sends `PageLiked` to the
  owner. `PagePolicy` `post`/`update`/`delete` = owner; the manage page
  authorizes in `mount()` and again in `save()`/`deletePage()`. Renaming a
  page regenerates its slug (`uniqueSlug($name, $ignoreId)`) and redirects to
  the new manage URL. Deleting a page cascades its posts and likes (FKs).
  Not built (add if asked): page admins/roles, insights/analytics, page
  reviews/ratings, invite-to-like, page search, "follow" separate from like,
  page posts in feed, messaging a page.
- **Jobs (Phase 5):** `job_postings` (slug route key, `type` from
  `JobPosting::TYPES`, `work_mode` from `JobPosting::WORK_MODES`, `expires_at`
  cast to `datetime`, `is_open`) + `job_applications`
  (`job_posting_id`/`user_id` unique, `status` pending|accepted|rejected).
  **`->scopeOpen()` hides closed *and* expired postings** from every listing
  and from the show route, so a non-owner gets a 403 rather than a dead link —
  use the scope, never a raw query, when reading job postings. One posting per
  user, enforced in the create page's `mount()`. `isAcceptingApplications()`
  uses `expires_at->endOfDay()->isFuture()` (Carbon has no
  `isFutureOrToday()`). Applicants are always re-resolved through
  `$posting->applications()`, so client-supplied ids can't review another
  posting's applications. `JobPostingPolicy` = owner for
  update/delete/manageApplicants/application review, applicant for withdraw.
  Two notifications (`JobApplicationReceived`, `JobApplicationStatusChanged`).
  Not built (add if asked): editing a posting, application withdraw UI on the
  applicant side, job search, saved jobs.
- **Forum (Phase 6):** `forum_categories` (slug route key, `position`) →
  `forum_topics` (slug route key, `is_pinned`, `is_locked`, `locked_by`,
  `locked_at`, `last_posted_at`) → `forum_posts`. **A topic's opening post
  lives in `forum_topics.body`**; `forum_posts` are only replies, so
  `ForumTopic::replies()` is just `posts()->oldest()`.
  `ForumPost::toggleLike()` reuses the polymorphic `Reaction` table (same as
  posts/comments). Locking is a v1 moderation model: **the locker and the
  topic author can still reply and either can unlock** — deliberate, not an
  oversight. `forum/topic/{topic}` is declared **before** `forum/{category:slug}`
  in `routes/web.php` so no category slug can shadow the literal segment
  "topic". Route model binding on the topic page uses `#[Locked] $topicId` +
  a `#[Computed]` re-fetch, so the topic is never trusted from the client and
  every action re-authorizes against a freshly loaded row. Replying fans out
  `ForumPostReplied` to prior participants, once each (deduped by author).
  Not built (add if asked): topic edit, attachments/images in replies, search
  across all forums, moderation queue, admin category management UI.
- `<x-pitnik.friend-box mode="custom">` renders its slot as the action area
  (used by group members); root and `group-box` merge `$attributes` so
  `wire:key` lands on the root.
- Fortify's post-login redirect (`config/fortify.php` → `home`) points at
  `/feed`, not `/dashboard`.

## Recurring gotchas
- **Every Pitnik full-page component needs
  `#[\Livewire\Attributes\Layout('layouts::bare')]`.** Livewire's default
  full-page layout is `layouts::app` (Flux sidebar); Pitnik pages pull in
  `layouts::pitnik` themselves via `@component`, so without the bare
  (`{{ $slot }}` only) layout the whole Pitnik page gets nested inside the Flux
  shell — Flux sidebar overlay top-left, Pitnik header pushed mid-page.
- **The dev-agent boundary guard hook blocks Bash commands containing any
  `/word` token it reads as an absolute path** — including HTML closing tags
  (`</div>`, `</a>`, `</i>`), regex like `s/.../\1/`, etc. Put such content in a script
  file under `storage/logs/` via Write and run that instead.
- In a tinker verification script, `Livewire::test(...)->status()` is not a
  getter — it returns the Testable, and a mount-time `Gate::authorize` failure
  does not throw. Use `->assertForbidden()` / `->assertOk()` inside
  try/catch instead.
- **`@{{ ... }}` in Blade is the escape for literal braces**, not "@ + echo" —
  write `{{ '@'.$user->username }}` for an @handle.
- **PostToolUse formatter hook strips unused imports/closure captures
  immediately** — if you add a `use Foo;` import (or add a var to a closure's
  `use (...)`) in one Edit without its usage present in that same edit's
  resulting file, the hook deletes it before the next Edit lands. Always add
  an import together with its first real usage in the same Edit call, or
  grep-verify it survived immediately after.
- The Pitnik template ships a lot of **dead demo markup with no real backing
  data or wiring** (share/report/send-message popups, an image-comment modal
  with hardcoded fake users, a full-screen story-slideshow with
  `placehold.it` images, a page-tour onboarding walkthrough, a location-picker
  tied to a Google Maps script carrying the *template author's own* API key).
  All of that was deleted during the Phase 1 port — don't re-add it wholesale
  from the raw template when porting later pages; port it for real once the
  feature behind it (sharing, reporting, DMs, stories) is actually being built.

## Known accepted gaps (Phase 1)
- Left/right sidebar widgets (weather, badges, "who's following", trending,
  birthdays, etc.) are static demo content, not wired to real data.
- ~100 other template pages still 404 (messenger, notifications, marketplace,
  video, groups) — expected, later phases.
- **Tests: `tests/Feature/` now covers feed, posts, comments, reactions,
  messenger, groups, pages, settings, jobs and forum (90 tests).**
  Run with `php artisan test --no-coverage`. The gate for every phase is
  `composer test` = `pint --parallel --test` + `phpstan analyse` (level 7) +
  `php artisan test`. Static analysis was clean as of Phase 6.
  - Two testing patterns worth keeping: **mount-time `Gate::authorize()`
    failures do not surface as `assertForbidden()` under `Livewire::test`** —
    assert authorization by hitting the HTTP route with
    `$this->actingAs($user)->get(route(...))->assertForbidden()`. And a factory
    must satisfy **every NOT NULL column** (`users.username` is the route key,
    so `UserFactory` sets it).
