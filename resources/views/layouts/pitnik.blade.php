<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
	<title>{{ $pageTitle ?? 'Pitnik' }} - Pitnik Social Network</title>
    <link rel="icon" href="/vendor/pitnik/images/fav.png" type="image/png" sizes="16x16"> 
    
    <link rel="stylesheet" href="/vendor/pitnik/css/main.min.css">
	<link rel="stylesheet" href="/vendor/pitnik/css/weather-icons.min.css">
	<link rel="stylesheet" href="/vendor/pitnik/css/toast-notification.css">
    <link rel="stylesheet" href="/vendor/pitnik/css/style.css">
    <link rel="stylesheet" href="/vendor/pitnik/css/color.css">
    <link rel="stylesheet" href="/vendor/pitnik/css/responsive.css">
    <style>.logout-link{background:none;border:0;padding:0;width:100%;text-align:left;font:inherit;color:inherit;cursor:pointer;}</style>
</head>
<body>
<div class="pitnik-app">
<div class="wavy-wraper">
		<div class="wavy">
		  <span style="--i:1;">p</span>
		  <span style="--i:2;">i</span>
		  <span style="--i:3;">t</span>
		  <span style="--i:4;">n</span>
		  <span style="--i:5;">i</span>
		  <span style="--i:6;">k</span>
		  <span style="--i:7;">.</span>
		  <span style="--i:8;">.</span>
		  <span style="--i:9;">.</span>
		</div>
	</div>
<div class="theme-layout">
	
	<div class="postoverlay"></div>
	
	<div class="responsive-header">
		<div class="mh-head first Sticky">
			<span class="mh-btns-left">
				<a class="" href="#menu"><i class="fa fa-align-justify"></i></a>
			</span>
			<span class="mh-text">
				<a href="{{ route('feed') }}" title=""><img src="/vendor/pitnik/images/logo2.png" alt=""></a>
			</span>
			<span class="mh-btns-right">
				<a class="fa fa-sliders" href="#shoppingbag"></a>
			</span>
		</div>
		<div class="mh-head second">
			<form class="mh-form">
				<input placeholder="search" />
				<a href="#/" class="fa fa-search"></a>
			</form>
		</div>
		<nav id="menu" class="res-menu">
			<ul>
				<li><span>Home Pages</span> 
					<ul>
						<li><a href="index.html" title="">Home 1</a></li>
						<li><a href="index2.html" title="">Home 2</a></li>
						<li><a href="company-landing.html" title="">Company Landing</a></li>
						<li><a href="pitrest.html" title="">Pitrest</a></li>
						<li><a href="redpit.html" title="">Redpit</a></li>
						<li><a href="redpit-category.html" title="">Redpit Category</a></li>
						<li><a href="soundnik.html" title="">Soundnik</a></li>
						<li><a href="soundnik-detail.html" title="">Soundnik Single</a></li>
						<li><a href="career.html" title="">Pitjob</a></li>
						<li><a href="shop.html" title="">Shop</a></li>
						<li><a href="classified.html" title="">Classified</a></li>
						<li><a href="pitpoint.html" title="">PitPoint</a></li>
						<li><a href="pittube.html" title="">Pittube</a></li>
						<li><a href="{{ route('messages') }}" title="">Messenger</a></li>
					</ul>
				</li>
				<li><span>Pittube</span>
					<ul>
						<li><a href="pittube.html" title="">Pittube</a></li>
						<li><a href="pittube-detail.html" title="">Pittube single</a></li>
						<li><a href="pittube-category.html" title="">Pittube Category</a></li>
						<li><a href="pittube-channel.html" title="">Pittube Channel</a></li>
						<li><a href="pittube-search-result.html" title="">Pittube Search Result</a></li>
					</ul>
				</li>
				<li><span>PitPoint</span>
					<ul>
						<li><a href="pitpoint.html" title="">PitPoint</a></li>
						<li><a href="pitpoint-detail.html" title="">Pitpoint Detail</a></li>
						<li><a href="pitpoint-list.html" title="">Pitpoint List style</a></li>
						<li><a href="pitpoint-without-baner.html" title="">Pitpoint without Banner</a></li>
						<li><a href="pitpoint-search-result.html" title="">Pitpoint Search</a></li>
					</ul>
				</li>
				<li><span>Pitjob</span>
					<ul>
						<li><a href="career.html" title="">Pitjob</a></li>
						<li><a href="career-detail.html" title="">Pitjob Detail</a></li>
						<li><a href="career-search-result.html" title="">Job seach page</a></li>
						<li><a href="social-post-detail.html" title="">Social Post Detail</a></li>
					</ul>
				</li>
				<li><span>Timeline</span>
					<ul>
						<li><a href="timeline.html" title="">Timeline</a></li>
						<li><a href="timeline-photos.html" title="">Timeline Photos</a></li>
						<li><a href="timeline-videos.html" title="">Timeline Videos</a></li>
						<li><a href="timeline-groups.html" title="">Timeline Groups</a></li>
						<li><a href="{{ route('profile.friends', auth()->user()) }}" title="">Timeline Friends</a></li>
						<li><a href="timeline-friends2.html" title="">Timeline Friends-2</a></li>
						<li><a href="about.html" title="">Timeline About</a></li>
						<li><a href="blog-posts.html" title="">Timeline Blog</a></li>
						<li><a href="friends-birthday.html" title="">Friends' Birthday</a></li>
						<li><a href="{{ route('feed') }}" title="">Newsfeed</a></li>
						<li><a href="search-result.html" title="">Search Result</a></li>
					</ul>
				</li>
				<li><span>Favourit Page</span>
					<ul>
						<li><a href="fav-page.html" title="">Favourit Page</a></li>
						<li><a href="games.html" title="">Games</a></li>
						<li><a href="fav-favers.html" title="">Fav Page Likers</a></li>
						<li><a href="fav-events.html" title="">Fav Events</a></li>
						<li><a href="fav-event-invitations.html" title="">Fav Event Invitations</a></li>
						<li><a href="event-calendar.html" title="">Event Calendar</a></li>
						<li><a href="fav-page-create.html" title="">Create New Page</a></li>
						<li><a href="price-plans.html" title="">Price Plan</a></li>
					</ul>
				</li>
				<li><span>Forum</span>
					<ul>
						<li><a href="forum.html" title="">Forum</a></li>
						<li><a href="forum-create-topic.html" title="">Forum Create Topic</a></li>
						<li><a href="forum-open-topic.html" title="">Forum Open Topic</a></li>
						<li><a href="forums-category.html" title="">Forum Category</a></li>
					</ul>
				</li>
				<li><span>Featured</span>
					<ul>
						<li><a href="{{ route('messages') }}" title="">Messenger (Chatting)</a></li>
						<li><a href="{{ route('notifications') }}" title="">Notifications</a></li>
						<li><a href="badges.html" title="">Badges</a></li>
						<li><a href="faq.html" title="">Faq's</a></li>
						<li><a href="contribution.html" title="">Contriburion Page</a></li>
						<li><a href="manage-page.html" title="">Manage Page</a></li>
						<li><a href="weather-forecast.html" title="">weather-forecast</a></li>
						<li><a href="statistics.html" title="">Statics/Analytics</a></li>
						<li><a href="shop-cart.html" title="">Shop Cart</a></li>
					</ul>
				</li>
				<li><span>Account Setting</span>
					<ul>
						<li><a href="setting.html" title="">Setting</a></li>
						<li><a href="privacy.html" title="">Privacy</a></li>
						<li><a href="support-and-help.html" title="">Support & Help</a></li>
						<li><a href="support-and-help-detail.html" title="">Support Detail</a></li>
						<li><a href="support-and-help-search-result.html" title="">Support Search</a></li>
					</ul>
				</li>
				<li><span>Authentication</span>
					<ul>
						<li><a href="login.html" title="">Login Page</a></li>
						<li><a href="register.html" title="">Register Page</a></li>
						<li><a href="logout.html" title="">Logout Page</a></li>
						<li><a href="coming-soon.html" title="">Coming Soon</a></li>
						<li><a href="error-404.html" title="">Error 404</a></li>
						<li><a href="error-404-2.html" title="">Error 404-2</a></li>
						<li><a href="error-500.html" title="">Error 500</a></li>
					</ul>
				</li>
				<li><span>Tools</span>
					<ul>
						<li><a href="typography.html" title="">Typography</a></li>
						<li><a href="popup-modals.html" title="">Popups/Modals</a></li>
						<li><a href="post-versions.html" title="">Post Versions</a></li>
						<li><a href="sliders.html" title="">Sliders / Carousel</a></li>
						<li><a href="google-map.html" title="">Google Maps</a></li>
						<li><a href="widgets.html" title="">Widgets</a></li>
					</ul>
				</li>
			</ul>
		</nav>
		<nav id="shoppingbag">
			<div>
				<div class="">
					<form method="post">
						<div class="setting-row">
							<span>use night mode</span>
							<input type="checkbox" id="nightmode"/> 
							<label for="nightmode" data-on-label="ON" data-off-label="OFF"></label>
						</div>
						<div class="setting-row">
							<span>Notifications</span>
							<input type="checkbox" id="switch2"/> 
							<label for="switch2" data-on-label="ON" data-off-label="OFF"></label>
						</div>
						<div class="setting-row">
							<span>Notification sound</span>
							<input type="checkbox" id="switch3"/> 
							<label for="switch3" data-on-label="ON" data-off-label="OFF"></label>
						</div>
						<div class="setting-row">
							<span>My profile</span>
							<input type="checkbox" id="switch4"/> 
							<label for="switch4" data-on-label="ON" data-off-label="OFF"></label>
						</div>
						<div class="setting-row">
							<span>Show profile</span>
							<input type="checkbox" id="switch5"/> 
							<label for="switch5" data-on-label="ON" data-off-label="OFF"></label>
						</div>
					</form>
					<h4 class="panel-title">Account Setting</h4>
					<form method="post">
						<div class="setting-row">
							<span>Sub users</span>
							<input type="checkbox" id="switch6" /> 
							<label for="switch6" data-on-label="ON" data-off-label="OFF"></label>
						</div>
						<div class="setting-row">
							<span>personal account</span>
							<input type="checkbox" id="switch7" /> 
							<label for="switch7" data-on-label="ON" data-off-label="OFF"></label>
						</div>
						<div class="setting-row">
							<span>Business account</span>
							<input type="checkbox" id="switch8" /> 
							<label for="switch8" data-on-label="ON" data-off-label="OFF"></label>
						</div>
						<div class="setting-row">
							<span>Show me online</span>
							<input type="checkbox" id="switch9" /> 
							<label for="switch9" data-on-label="ON" data-off-label="OFF"></label>
						</div>
						<div class="setting-row">
							<span>Delete history</span>
							<input type="checkbox" id="switch10" /> 
							<label for="switch10" data-on-label="ON" data-off-label="OFF"></label>
						</div>
						<div class="setting-row">
							<span>Expose author name</span>
							<input type="checkbox" id="switch11" /> 
							<label for="switch11" data-on-label="ON" data-off-label="OFF"></label>
						</div>
					</form>
				</div>
			</div>
		</nav>
	</div><!-- responsive header -->
	
	<div class="topbar transparent stick">
		<div class="logo">
			<a title="" href="{{ route('feed') }}"><img src="/vendor/pitnik/images/logo2.png" alt=""></a>
		</div>
		<div class="top-area">
			<div class="top-search">
				<form method="post" class="">
					<input type="text" placeholder="Search People, Pages, #tags etc">
					<button data-ripple><i><svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 512 512" class="search-icon" height="15px" width="15px">
                        <path d="M455 455a25 25 0 01-35.4 0l-81.3-81.2a32.2 32.2 0 01-.9-44.7 137 137 0 00-101-229.5h-.5a136 136 0 00-97 40.9 137.3 137.3 0 00.6 192.7 136.4 136.4 0 00139.2 33.6 25 25 0 0115.3 47.6 187 187 0 01-190-46 187.5 187.5 0 01-.8-263 185.6 185.6 0 01132.5-55.8h.8A187 187 0 01385 350l69.9 69.8a25 25 0 010 35.4z"></path>
                        <path d="M0 0h512v512H0z" fill="none"></path>
                    </svg></i></button>
				</form>
			</div>
			<div class="page-name">
			    <span>{{ $pageTitle ?? '' }}</span>
			 </div>
			 <ul class="hot-links">
				<li><a class="dark" href="timeline.html" title="Timeline"><i class="fa-regular fa-face-smile fa-fw"></i></a></li>
				<li><a class="blue news-options" href="#" title="News Options"><i class="fa-regular fa-heart"></i></a></li>
				<li><a class="bloodred" href="pittube.html" title="Pittube"><i class="fa-regular fa-play"></i></a></li>
				<li><a class="pint" href="pitrest.html" title="Pitrest"><i class="fa-regular fa-thumbtack"></i></a></li>
				<li><a class="bizsound" href="soundnik.html" title="Soundnik"><i class="fa-regular fa-music"></i></a></li>
				<li><a class="navy" href="podcast.html" title="Podcast"><i class="fa-regular fa-microphone"></i></a></li>
			</ul>
			<ul class="setting-area">
				<livewire:friend-request-bell />
				<livewire:notification-bell />
				<livewire:message-bell />
			</ul>
			<div class="user-img">
				<img src="{{ auth()->user()->avatarUrl() }}" alt="">
				<span class="status f-online"></span>
				<div class="user-setting">
					<span class="seting-title">{{ auth()->user()->name }}</span>
					<ul class="chat-setting">
						<li><a href="#" title=""><span class="status f-online"></span>online</a></li>
						<li><a href="#" title=""><span class="status f-away"></span>away</a></li>
						<li><a href="#" title=""><span class="status f-off"></span>offline</a></li>
					</ul>
					<span class="seting-title">User setting</span>
					<ul class="log-out">
						<li><a href="{{ route('profile.show', auth()->user()) }}" title=""><i class="ti-user"></i> view profile</a></li>
						<li><a href="{{ route('profile.edit') }}" title=""><i class="ti-pencil-alt"></i>edit profile</a></li>
						<li><a href="{{ route('profile.edit') }}" title=""><i class="ti-settings"></i>account setting</a></li>
						<li>
							<form method="POST" action="{{ route('logout') }}">
								@csrf
								<button type="submit" class="logout-link"><i class="ti-power-off"></i>log out</button>
							</form>
						</li>
					</ul>
				</div>
			</div>
			<span class="main-menu" data-ripple="">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" viewBox="0 0 24 24" fill="none" stroke="#30305b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sliders">
					<line x1="4" y1="21" x2="4" y2="14"></line>
					<line x1="4" y1="10" x2="4" y2="3"></line>
					<line x1="12" y1="21" x2="12" y2="12"></line>
					<line x1="12" y1="8" x2="12" y2="3"></line>
					<line x1="20" y1="21" x2="20" y2="16"></line>
					<line x1="20" y1="12" x2="20" y2="3"></line>
					<line x1="1" y1="14" x2="7" y2="14"></line>
					<line x1="9" y1="8" x2="15" y2="8"></line>
					<line x1="17" y1="16" x2="23" y2="16"></line>
				</svg>
			</span>
		</div>
	</div><!-- topbar -->
	
	<div class="fixed-sidebar right">
		<div class="chat-friendz">
			<ul class="chat-users">
				@foreach(auth()->user()->acceptedFriends() as $friend)
					<li>
						<a href="{{ route('messages', $friend) }}" title="{{ $friend->name }}" data-toggle="tooltip" data-placement="left">
							<div class="author-thmb">
								<img src="{{ $friend->avatarUrl() }}" alt="">
							</div>
						</a>
					</li>
				@endforeach
			</ul>
		</div>
	</div><!-- right sidebar friends -->
	<div class="fixed-sidebar left">
		<div class="menu-left">
			<ul class="left-menu">
				<li>
					<a class="menu-small" href="#" title="">
						<i class="ti-menu"></i>
					</a>
				</li>
				
				<li>
					<a href="{{ route('feed') }}" title="Newsfeed Page" data-toggle="tooltip" data-placement="right">
						<i class="ti-magnet"></i>
					</a>
				</li>
				<li>
					<a href="forum.html" title="Forum" data-toggle="tooltip" data-placement="right">
						<i class="fa-brands fa-forumbee"></i>
					</a>
				</li>
				<li>
					<a href="{{ route('profile.friends', auth()->user()) }}" title="Friends" data-toggle="tooltip" data-placement="right">
						<i class="fa-regular fa-user"></i>
					</a>
				</li>
				<li>
					<a href="fav-page.html" title="Favourit page" data-toggle="tooltip" data-placement="right">
						<i class="fa-regular fa-star-christmas"></i>
					</a>
				</li>
				<li>
					<a href="{{ route('messages') }}" title="Messages" data-toggle="tooltip" data-placement="right">
						<i class="fa-regular fa-messages"></i>
					</a>
				</li>
				<li>
					<a href="{{ route('notifications') }}" title="Notification" data-toggle="tooltip" data-placement="right">
						<i class="fa-regular fa-bell"></i>
					</a>
				</li>
				
				<li>
					<a href="statistics.html" title="Account Stats" data-toggle="tooltip" data-placement="right">
						<i class="fa-regular fa-chart-line"></i>
					</a>
				</li>
				
				<li>
					<a href="support-and-help.html" title="Help" data-toggle="tooltip" data-placement="right">
						<i class="fa-regular fa-square-question"></i>
					</a>
				</li>
				<li>
					<a href="faq.html" title="Faq's" data-toggle="tooltip" data-placement="right">
						<i class="fa-regular fa-lightbulb-on"></i>
					</a>
				</li>
			</ul>
		</div>
		<div class="left-menu-full">
			<ul class="menu-slide">
				<li><a class="closd-f-menu" href="#" title=""><i class="fa-sharp fa-regular fa-xmark"></i> close Menu</a></li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-regular fa-house"></i> Home Pages</a>
					<ul class="submenu">
						<li><a href="index.html" title="">Home 1</a></li>
						<li><a href="index2.html" title="">Home 2</a></li>
						<li><a href="company-landing.html" title="">Company Landing</a></li>
						<li><a href="pitrest.html" title="">Pitrest</a></li>
						<li><a href="redpit.html" title="">Redpit</a></li>
						<li><a href="redpit-category.html" title="">Redpit Category</a></li>
						<li><a href="soundnik.html" title="">Soundnik</a></li>
						<li><a href="soundnik-detail.html" title="">Soundnik Single</a></li>
						<li><a href="career.html" title="">Pitjob</a></li>
						<li><a href="shop.html" title="">Shop</a></li>
						<li><a href="classified.html" title="">Classified</a></li>
						<li><a href="pitpoint.html" title="">PitPoint</a></li>
						<li><a href="pittube.html" title="">Pittube</a></li>
						<li><a href="{{ route('messages') }}" title="">Messenger</a></li>
					</ul>
				</li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-regular fa-microphone"></i> Podcast</a>
					<ul class="submenu">
						<li><a href="podcast.html" title="">Podcast</a></li>
						<li><a href="podcast-all.html" title="">Pittube All</a></li>
						<li><a href="podcast-personal.html" title="">Podcast Personal</a></li>
					</ul>
				</li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-regular fa-play"></i> Pittube</a>
					<ul class="submenu">
						<li><a href="pittube.html" title="">Pittube</a></li>
						<li><a href="pittube-detail.html" title="">Pittube single</a></li>
						<li><a href="pittube-category.html" title="">Pittube Category</a></li>
						<li><a href="pittube-channel.html" title="">Pittube Channel</a></li>
						<li><a href="pittube-search-result.html" title="">Pittube Search Result</a></li>
					</ul>
				</li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-regular fa-heart"></i> PitPoint</a>
					<ul class="submenu">
						<li><a href="pitpoint.html" title="">PitPoint</a></li>
						<li><a href="pitpoint-detail.html" title="">Pitpoint Detail</a></li>
						<li><a href="pitpoint-list.html" title="">Pitpoint List style</a></li>
						<li><a href="pitpoint-without-baner.html" title="">Pitpoint without Banner</a></li>
						<li><a href="pitpoint-search-result.html" title="">Pitpoint Search</a></li>
					</ul>
				</li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-regular fa-graduation-cap"></i> Pitjob</a>
					<ul class="submenu">
						<li><a href="career.html" title="">Pitjob</a></li>
						<li><a href="career-detail.html" title="">Pitjob Detail</a></li>
						<li><a href="career-search-result.html" title="">Job seach page</a></li>
						<li><a href="social-post-detail.html" title="">Social Post Detail</a></li>
					</ul>
				</li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-regular fa-list-timeline"></i> Timeline</a>
					<ul class="submenu">
						<li><a href="timeline.html" title="">Timeline</a></li>
						<li><a href="timeline-photos.html" title="">Timeline Photos</a></li>
						<li><a href="timeline-videos.html" title="">Timeline Videos</a></li>
						<li><a href="timeline-groups.html" title="">Timeline Groups</a></li>
						<li><a href="{{ route('profile.friends', auth()->user()) }}" title="">Timeline Friends</a></li>
						<li><a href="timeline-friends2.html" title="">Timeline Friends-2</a></li>
						<li><a href="about.html" title="">Timeline About</a></li>
						<li><a href="blog-posts.html" title="">Timeline Blog</a></li>
						<li><a href="friends-birthday.html" title="">Friends' Birthday</a></li>
						<li><a href="{{ route('feed') }}" title="">Newsfeed</a></li>
						<li><a href="search-result.html" title="">Search Result</a></li>
						<li><a href="library.html" title="">Library</a></li>
						<li><a href="library-detail.html" title="">Library Detail</a></li>
						<li><a href="ad-center.html" title="">Ad Center</a></li>
					</ul>
				</li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-regular fa-heart"></i> Favourit Page</a>
					<ul class="submenu">
						<li><a href="fav-page.html" title="">Favourit Page</a></li>
						<li><a href="games.html" title="">Games</a></li>
						<li><a href="fav-favers.html" title="">Fav Page Likers</a></li>
						<li><a href="fav-events.html" title="">Fav Events</a></li>
						<li><a href="fav-event-invitations.html" title="">Fav Event Invitations</a></li>
						<li><a href="event-calendar.html" title="">Event Calendar</a></li>
						<li><a href="fav-page-create.html" title="">Create New Page</a></li>
						<li><a href="price-plans.html" title="">Price Plan</a></li>
					</ul>
				</li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-brands fa-forumbee"></i> Forum</a>
					<ul class="submenu">
						<li><a href="forum.html" title="">Forum</a></li>
						<li><a href="forum-create-topic.html" title="">Forum Create Topic</a></li>
						<li><a href="forum-open-topic.html" title="">Forum Open Topic</a></li>
						<li><a href="forums-category.html" title="">Forum Category</a></li>
					</ul>
				</li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-regular fa-star-christmas"></i> Featured</a>
					<ul class="submenu">
						<li><a href="{{ route('messages') }}" title="">Messenger (Chatting)</a></li>
						<li><a href="{{ route('notifications') }}" title="">Notifications</a></li>
						<li><a href="badges.html" title="">Badges</a></li>
						<li><a href="faq.html" title="">Faq's</a></li>
						<li><a href="contribution.html" title="">Contriburion Page</a></li>
						<li><a href="manage-page.html" title="">Manage Page</a></li>
						<li><a href="weather-forecast.html" title="">weather-forecast</a></li>
						<li><a href="statistics.html" title="">Statics/Analytics</a></li>
						<li><a href="shop-cart.html" title="">Shop Cart</a></li>
					</ul>
				</li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-regular fa-sliders"></i> Account Setting</a>
					<ul class="submenu">
						<li><a href="setting.html" title="">Setting</a></li>
						<li><a href="privacy.html" title="">Privacy</a></li>
						<li><a href="support-and-help.html" title="">Support & Help</a></li>
						<li><a href="support-and-help-detail.html" title="">Support Detail</a></li>
						<li><a href="support-and-help-search-result.html" title="">Support Search</a></li>
					</ul>
				</li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-regular fa-lock"></i> Authentication</a>
					<ul class="submenu">
						<li><a href="login.html" title="">Login Page</a></li>
						<li><a href="register.html" title="">User Register</a></li>
						<li><a href="register2.html" title="">User Register2</a></li>
						<li><a href="logout.html" title="">Logout Page</a></li>
						<li><a href="coming-soon.html" title="">Coming Soon</a></li>
						<li><a href="error-404.html" title="">Error 404</a></li>
						<li><a href="error-404-2.html" title="">Error 404-2</a></li>
						<li><a href="error-500.html" title="">Error 500</a></li>
					</ul>
				</li>
				<li class="menu-item-has-children"><a class="" href="#" title=""><i class="fa-regular fa-gear"></i> </i>Tools</a>
					<ul class="submenu">
						<li><a href="typography.html" title="">Typography</a></li>
						<li><a href="popup-modals.html" title="">Popups/Modals</a></li>
						<li><a href="post-versions.html" title="">Post Versions</a></li>
						<li><a href="sliders.html" title="">Sliders</a></li>
						<li><a href="google-map.html" title="">Google Maps</a></li>
						<li><a href="widgets.html" title="">Widgets</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div><!-- left sidebar menu -->

{{ $slot }}

	<a title="Your Cart Items" href="shop-cart.html" class="shopping-cart" data-toggle="tooltip">Cart <i class="fa fa-shopping-bag"></i><span>02</span></a>

	<div class="bottombar">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<span class="copyright">© Pitnik {{ now()->year }}. All rights reserved.</span>
					<i><img src="/vendor/pitnik/images/credit-cards.png" alt=""></i>
				</div>
			</div>
		</div>
	</div>
</div>

	<div class="side-panel">
		<h4 class="panel-title">General Setting</h4>
		<form method="post">
			<div class="setting-row">
				<span>use night mode</span>
				<input type="checkbox" id="nightmode1"/> 
				<label for="nightmode1" data-on-label="ON" data-off-label="OFF"></label>
			</div>
			<div class="setting-row">
				<span>Notifications</span>
				<input type="checkbox" id="switch22" /> 
				<label for="switch22" data-on-label="ON" data-off-label="OFF"></label>
			</div>
			<div class="setting-row">
				<span>Notification sound</span>
				<input type="checkbox" id="switch33" /> 
				<label for="switch33" data-on-label="ON" data-off-label="OFF"></label>
			</div>
			<div class="setting-row">
				<span>My profile</span>
				<input type="checkbox" id="switch44" /> 
				<label for="switch44" data-on-label="ON" data-off-label="OFF"></label>
			</div>
			<div class="setting-row">
				<span>Show profile</span>
				<input type="checkbox" id="switch55" /> 
				<label for="switch55" data-on-label="ON" data-off-label="OFF"></label>
			</div>
		</form>
		<h4 class="panel-title">Account Setting</h4>
		<form method="post">
			<div class="setting-row">
				<span>Sub users</span>
				<input type="checkbox" id="switch66" /> 
				<label for="switch66" data-on-label="ON" data-off-label="OFF"></label>
			</div>
			<div class="setting-row">
				<span>personal account</span>
				<input type="checkbox" id="switch77" /> 
				<label for="switch77" data-on-label="ON" data-off-label="OFF"></label>
			</div>
			<div class="setting-row">
				<span>Business account</span>
				<input type="checkbox" id="switch88" /> 
				<label for="switch88" data-on-label="ON" data-off-label="OFF"></label>
			</div>
			<div class="setting-row">
				<span>Show me online</span>
				<input type="checkbox" id="switch99" /> 
				<label for="switch99" data-on-label="ON" data-off-label="OFF"></label>
			</div>
			<div class="setting-row">
				<span>Delete history</span>
				<input type="checkbox" id="switch101" /> 
				<label for="switch101" data-on-label="ON" data-off-label="OFF"></label>
			</div>
			<div class="setting-row">
				<span>Expose author name</span>
				<input type="checkbox" id="switch111" /> 
				<label for="switch111" data-on-label="ON" data-off-label="OFF"></label>
			</div>
		</form>
	</div><!-- side panel -->

	<script src="/vendor/pitnik/js/main.min.js"></script>
	<script src="/vendor/pitnik/js/jquery-stories.js"></script>
	<script src="/vendor/pitnik/js/toast-notificatons.js"></script>
	<script src="/vendor/pitnik/js/stories-zuck.js"></script>
	<script src="/vendor/pitnik/js/stories-zuck-necessery.js"></script>
	<script src="/vendor/pitnik/js/stories-zuck-init.js"></script>
	<script src="/vendor/pitnik/js/script.js"></script>

</div><!-- pitnik-app -->
</body>
</html>
