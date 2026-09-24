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
3. Chat/messenger (real-time).
4. Groups/pages.
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
- Fortify's post-login redirect (`config/fortify.php` → `home`) points at
  `/feed`, not `/dashboard`.

## Recurring gotchas
- **Every Pitnik full-page component needs
  `#[\Livewire\Attributes\Layout('layouts::bare')]`.** Livewire's default
  full-page layout is `layouts::app` (Flux sidebar); Pitnik pages pull in
  `layouts::pitnik` themselves via `@component`, so without the bare
  (`{{ $slot }}` only) layout the whole Pitnik page gets nested inside the Flux
  shell — Flux sidebar overlay top-left, Pitnik header pushed mid-page.
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
- Stories bar is static/decorative (real stories = later phase).
- Left/right sidebar widgets (weather, badges, "who's following", trending,
  birthdays, etc.) are static demo content, not wired to real data.
- ~100 other template pages still 404 (messenger, notifications, forum,
  marketplace, jobs, video, groups) — expected, later phases.
- No dedicated test suite — verification is via `php -l`, `php artisan
  route:list`, and manual/curl render checks (matches this session's approach;
  revisit if the user wants formal tests later).
