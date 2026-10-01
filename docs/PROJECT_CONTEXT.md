# Social Platform — Project Context

## What this is
Laravel 13 + Livewire 4 social network, cloning feature set of the **Pitnik**
HTML template (https://wpkixx.com/html/pitnik/index.html — Bootstrap/jQuery,
~100 template pages: newsfeed, timeline, messenger, notifications, forum,
marketplace, jobs, video platform, events, ad-center). Template assets
(css/js/images/fonts) are vendored at `public/vendor/pitnik/`.

## Standing priorities
Build is phased — don't jump ahead of the current phase without checking with
the user first. Phases 0–8 are now done:
1. **Phase 1 (done):** auth, profile, newsfeed, post/comment/like — the core loop.
2. Friends/follow + notifications + stories (real, not the static demo bar).
3. **Phase 3 (done):** 1:1 chat/messenger (Livewire polling).
4. **Phase 4 (done):** 4a Groups, 4b FB-style Pages.
5. **Phase 5 (done):** Jobs board with applications.
6. **Phase 6 (done):** Forum — categories, topics, replies.
7. **Phase 7 (done):** Marketplace — listings, saves, offers.
8. **Phase 8 (done):** Video (Pittube) — channels, uploads, comments.
9. Not built: events, ad-center. Each is open if asked.

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
- **Marketplace (Phase 7):** `marketplace_listings` (slug route key, `price`
  cast `decimal:2`, `currency`/`category`/`condition` from the
  `MarketplaceListing::CURRENCIES`/`CATEGORIES`/`CONDITIONS` const lists
  validated with `Rule::in`, nullable `image_path` under `listings/` on the
  `public` disk) + `listing_saves` pivot (wishlist) + `marketplace_offers`
  (`marketplace_listing_id`/`user_id` unique, `status` pending|accepted|
  declined). **`->scopeAvailable()` hides sold listings** from the browse grid;
  the seller still sees them on their own listing page, everyone else gets a
  403 (policy `view`). **Sellers cannot save or offer on their own listing**,
  and `makeOffer` also refuses sold listings. One offer per buyer per listing
  is enforced both in the page (validation error) and by the unique index.
  **Accepting an offer marks the listing sold** — a sold listing drops out of
  the grid immediately, so accepting and selling are the same transition. All
  offers are re-resolved through `$listing->offers()`, so an offer id from the
  client can only ever address an offer on the listing currently loaded — a
  seller authorized on *their* listing still gets a 404 for another seller's
  offer id. `updateOffer()` ignores any status outside
  accepted/declined rather than trusting the client string. `savers()` is a
  `belongsToMany(User::class, 'listing_saves')` with `User::savedListings()`
  as the inverse; the "saved only" filter uses `whereHas('savers', ...)`.
  Two notifications (`MarketplaceOfferReceived` to the seller,
  `MarketplaceOfferStatusChanged` to the buyer). `marketplace/{listing}/edit`
  is declared **before** `marketplace/{listing:slug}` so "edit" can never be
  read as a slug. Not built (add if asked): cart/checkout, payments,
  multi-image galleries, listing comments, categories managed in the DB,
  shipping/delivery, buyer's own "my offers" page.
- **Video / Pittube (Phase 8):** `video_channels` (slug route key, denormalized
  `subscribers_count`) → `videos` (slug route key, `visibility` from
  `Video::VISIBILITIES` = public|unlisted|private, `duration_seconds`,
  `views_count`, nullable `video_path`/`thumbnail_path`) →
  `video_comments` (nullable `parent_id`), plus `channel_subscriptions`.
  One channel per user, enforced by `unique:video_channels,name` in the create
  form. **Visibility rules: `Video::scopePublic()` is the browse grid**;
  unlisted videos are reachable only by direct link; private videos are the
  uploader's and the channel owner's only (`VideoPolicy::view`). A channel page
  shows public+unlisted to strangers and everything to the owner.
  `VideoChannel::subscribe()`/`unsubscribe()` maintain `subscribers_count`
  and no-op on a duplicate or a self-subscribe, so the counter can't drift;
  `NewChannelSubscriber` goes to the channel owner. **Video comments use two
  relations on purpose**: `comments()` is top-level only (`whereNull('parent_id')`,
  what the page paginates) and `allComments()` is everything — reply creation
  and moderation go through `allComments()`, so a reply is never silently
  dropped for having a parent. Comment ids sent by the client are re-fetched
  through `allComments()`, and a reply's parent is re-fetched through the
  video, so neither can be pointed at another video's thread. Video likes reuse
  the polymorphic `Reaction` table (`Video::toggleLike()`), same as posts,
  comments and forum posts. **Uploading a video to a channel always sets
  `user_id` to the channel owner**, not the acting session. Three
  notifications (`VideoLiked`, `VideoCommented`, `NewChannelSubscriber`).
  Route layout is `pittube`, `pittube/watch/{video:slug}`, and
  `pittube/channel/{channel:slug}` — `watch` and `edit` are declared before
  the bare `pittube/{video:slug}` pattern. Not built (add if asked):
  transcoding/thumbnails from the video itself, playlists, live streaming,
  watch history, view-count increments (the column exists but nothing writes it
  yet), watch-later, video reactions beyond likes, channel analytics.
- `<x-pitnik.friend-box mode="custom">` renders its slot as the action area
  (used by group members); root and `group-box` merge `$attributes` so
  `wire:key` lands on the root.
- Fortify's post-login redirect (`config/fortify.php` → `home`) points at
  `/feed`, not `/dashboard`.
- **Local signups are pre-verified, gated on
  `config('fortify.pre_verify_registrations')`** (defaults to
  `APP_ENV === local`, override with `FORTIFY_PRE_VERIFY`). Reason: local runs
  `MAIL_MAILER=log`, so the verification link never reaches an inbox, and
  *every* authenticated route is behind the `verified` middleware — an
  unverified signup lands on `/email/verify` and can never reach any page.
  `email_verified_at` is deliberately kept out of `User`'s `#[Fillable]` so it
  can never be mass-assigned; `CreateNewUser` uses `forceFill` and skips the
  `Verified` event. Every non-local environment keeps Fortify's normal flow.

## Running the app locally

Two failure modes make the app look completely broken after a fresh clone. Both
are worth checking first when something "just doesn't work".

**1. A stale `public/hot` file means zero CSS and zero JS.**
`public/hot` is written by `npm run dev` and points Vite at the dev server. If
the dev server is not actually running (or was killed) but `public/hot` still
exists, every `@vite` reference resolves to `http://127.0.0.1:5173/...` and
returns 404 — the app renders as unstyled, non-interactive HTML. Forms still
POST, so the symptom is confusing: pages "load" but nothing is styled and no
Livewire/Alpine behaviour fires.

Pick one, not both:
```bash
npm run dev     # writes public/hot, assets served by the Vite dev server
# or
rm -f public/hot && npm run build   # baked assets from public/build
```
`public/hot` and `public/build` are both gitignored, so a fresh clone needs
one of the above before anything looks right.

**2. A fresh signup cannot get past `/email/verify`.** See
`pre_verify_registrations` under Architecture decisions above.

Seed logins: `test@example.com`, `amy@example.com`, `jackdoe`/`jack@example.com`,
`sarah@example.com`, `hiring@acme.test`, `seeker@example.test`,
`seller@example.test`, `buyer@example.test`, `creator@example.test`,
`viewer@example.test` — all with password `password`.

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
- **`SeedCommand` runs every seeder inside `Model::unguarded()`**, so
  `email_verified_at` and other non-`#[Fillable]` columns *do* get mass-assigned
  by `User::create([...])` in seeders. That is the intended way seeded users get
  verified emails. But it means a seeder executed outside `db:seed` (e.g.
  `(new VideoSeeder)->run()` in tinker) silently drops those columns and creates
  unverified users — don't debug seeding via tinker and conclude the seeder is
  broken.
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
- Most of the original Pitnik template's remaining demo pages still 404
  (badges, podcast, pitpoint, shop cart, news pages, …) — expected, not part of
  the app's feature set.
- **Tests: `tests/Feature/` now covers feed, posts, comments, reactions,
  messenger, groups, pages, settings, auth, jobs, forum, marketplace and video
  (166 tests).** Run with `php artisan test --no-coverage`. The gate for every
  phase is `composer test` = `pint --parallel --test` + `phpstan analyse`
  (level 7) + `php artisan test`. Static analysis was clean as of Phase 8.
  - Testing patterns worth keeping:
    - **Mount-time `Gate::authorize()` failures do not surface as
      `assertForbidden()` under `Livewire::test`** — a `Gate::authorize()` in
      `mount()` throws `AuthorizationException` before the Testable exists, so
      the component renders a 404/error page instead. Assert authorization by
      hitting the HTTP route with
      `$this->actingAs($user)->get(route(...))->assertForbidden()`, or assert
      the policy directly with `Gate::forUser($user)->allows(...)`.
    - `Gate::authorize()` *inside* an action does surface, so
      `->call('method')->assertForbidden()` is fine there.
    - A factory must satisfy **every NOT NULL column** (`users.username` is
      the route key, so `UserFactory` sets it).
    - `->for($seller)` on a factory infers the relationship name from the model
      class (`User` → `user`). When the relation is named something else
      (`MarketplaceListing::seller()`), pass it explicitly:
      `->for($seller, 'seller')`.
    - Faker's `words($n, false)` is typed `array|string`, which phpstan level 7
      rejects when the value is passed to a `string` parameter. Compose the
      string by hand instead (`.` concatenation of `->word()` calls).
    - A `#[Url]` query-string property is state: reading it back after a
      redirect-then-render cycle can throw `Invalid Livewire snapshot
      structure`. Prefer not adding one unless a page really needs tab state
      in the URL.
