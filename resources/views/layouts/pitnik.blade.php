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
						<li><a href="chat-messenger.html" title="">Messenger</a></li>
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
						<li><a href="timeline-friends.html" title="">Timeline Friends</a></li>
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
						<li><a href="chat-messenger.html" title="">Messenger (Chatting)</a></li>
						<li><a href="notifications.html" title="">Notifications</a></li>
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
				<li>
					<a href="" title="Friend Requests" data-ripple=""><em class="bg-red">5</em>
						<i><svg id="Layer_2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 499.3 512" height="16px" fill="#30305b">
							<g>
								<path d="M196.3 239.5c30.4 0 58.1-11.4 79.3-30.1 7.2 12.8 17.4 23.7 30 32 15.5 10.1 33.1 15.4 51.2 15.4 6.4 0 12.8-.7 19.2-2 24.5-5.1 45.6-19.4 59.3-40.3 13.7-20.9 18.5-45.9 13.4-70.4-7.7-36.9-36.5-65.5-73.5-72.9-10.8-2.1-21.4 4.9-23.5 15.7-2.1 10.8 4.9 21.4 15.7 23.5 21.2 4.2 37.8 20.6 42.1 41.8 2.9 14 .2 28.4-7.7 40.3-7.9 12-19.9 20.2-34 23.1-14 2.9-28.4.2-40.3-7.7-12-7.9-20.2-19.9-23.1-34-.1-.6-.3-1.3-.5-1.9 7.7-15.8 12.1-33.6 12.1-52.4C316 53.7 262.3 0 196.3 0S76.5 53.7 76.5 119.8s53.7 119.8 119.8 119.8zm0-199.5c44 0 79.8 35.8 79.8 79.8s-35.8 79.8-79.8 79.8-79.8-35.8-79.8-79.8S152.3 40 196.3 40zM498.4 415.2c-15.6-51.9-57.9-92.1-110.5-105.1-10.7-2.6-21.6 3.9-24.2 14.6-2.6 10.7 3.9 21.6 14.6 24.2 38.9 9.6 70.2 39.3 81.7 77.7 2.6 8.7 10.5 14.3 19.1 14.3s3.8-.3 5.8-.8c10.6-3.2 16.6-14.3 13.4-24.9zM323.4 365.5c-7.8-7.8-20.5-7.8-28.3 0-7.8 7.8-7.8 20.5 0 28.3l25.7 25.7c-24.5 3.1-64.3 7.5-106.6 9.4-115.7 5-154.1-12.4-166.7-23.2-6.8-5.8-7.6-11.2-7.6-15.3 0-9.5 3.7-17.3 11.4-24.5C88.6 331 197 329.1 235.6 331.3c11 .6 20.5-7.8 21.1-18.8.6-11-7.8-20.5-18.8-21.1-1.6 0-38.8-2.2-83.1 1.9-62 5.6-106 20.3-130.8 43.5C8.5 351.3.2 369.7 0 390c-.2 17.8 7.3 33.7 21.5 46 26.3 22.7 76.7 33.8 153.5 33.8s26.8-.3 41.4-1c36.6-1.6 70.8-5 95.7-7.9l-16.9 16.9c-7.8 7.8-7.8 20.5 0 28.3 3.9 3.9 9 5.9 14.1 5.9s10.2-2 14.1-5.9l56.2-56.2c3.8-3.8 5.9-8.8 5.9-14.1s-2.1-10.4-5.9-14.1l-56.2-56.2z"></path>
							</g>
						</svg></i>
					</a>
					<div class="dropdowns">
						<span>5 New Requests <a href="#" title="">View all Requests</a></span>
						<ul class="drops-menu">
							<li>
								<div>
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-2.jpg" alt="">
									</figure>
									<div class="mesg-meta">
										<h6><a href="#" title="">Loren</a></h6>
										<span><b>Amy</b> is mutule friend</span>
										<i>yesterday</i>
									</div>
									<div class="add-del-friends">
										<a href="#" title=""><i class="ti-heart"></i></a>
										<a href="#" title=""><i class="ti-trash"></i></a>
									</div>
								</div>	
							</li>
							<li>
								<div>
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-3.jpg" alt="">
									</figure>
									<div class="mesg-meta">
										<h6><a href="#" title="">Tina Trump</a></h6>
										<span><b>Simson</b> is mutule friend</span>
										<i>2 days ago</i>
									</div>
									<div class="add-del-friends">
										<a href="#" title=""><i class="ti-heart"></i></a>
										<a href="#" title=""><i class="ti-trash"></i></a>
									</div>
								</div>	
							</li>
							<li>
								<div>
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-4.jpg" alt="">
									</figure>
									<div class="mesg-meta">
										<h6><a href="#" title="">Andrew</a></h6>
										<span><b>Bikra</b> is mutule friend</span>
										<i>4 hours ago</i>
									</div>
									<div class="add-del-friends">
										<a href="#" title=""><i class="ti-heart"></i></a>
										<a href="#" title=""><i class="ti-trash"></i></a>
									</div>
								</div>
							</li>
							<li>
								<div>
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-5.jpg" alt="">
									</figure>
									<div class="mesg-meta">
										<h6><a href="#" title="">Dasha</a></h6>
										<span><b>Sarah</b> is mutule friend</span>
										<i>9 hours ago</i>
									</div>
									<div class="add-del-friends">
										<a href="#" title=""><i class="ti-heart"></i></a>
										<a href="#" title=""><i class="ti-trash"></i></a>
									</div>
								</div>	
							</li>
							<li>
								<div>
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-1.jpg" alt="">
									</figure>
									<div class="mesg-meta">
										<h6><a href="#" title="">Emily</a></h6>
										<span><b>Amy</b> is mutule friend</span>
										<i>4 hours ago</i>
									</div>
									<div class="add-del-friends">
										<a href="#" title=""><i class="ti-heart"></i></a>
										<a href="#" title=""><i class="ti-trash"></i></a>
									</div>
								</div>	
							</li>
						</ul>
						<a href="notifications.html" title="" class="more-mesg">View All</a>
					</div>
				</li>
				<li>
					<a href="#" title="Notification" data-ripple="">
						<i><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
							<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
							<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
						</svg></i><em class="bg-purple">7</em>
					</a>
					<div class="dropdowns">
						<span>4 New Notifications <a href="#" title="">Mark all as read</a></span>
						<ul class="drops-menu">
							<li>
								<a href="notifications.html" title="">
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-1.jpg" alt="">
										<span class="status f-online"></span>
									</figure>
									<div class="mesg-meta">
										<h6>sarah Loren</h6>
										<span>commented on your new profile status</span>
										<i>2 min ago</i>
									</div>
								</a>
							</li>
							<li>
								<a href="notifications.html" title="">
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-2.jpg" alt="">
										<span class="status f-online"></span>
									</figure>
									<div class="mesg-meta">
										<h6>Jhon doe</h6>
										<span>Nicholas Grissom just became friends. Write on his wall.</span>
										<i>4 hours ago</i>
										<figure>
											<span>Today is Marina Valentine’s Birthday! wish for celebrating</span>
											<img src="/vendor/pitnik/images/birthday.png" alt="">
										</figure>
									</div>
								</a>
							</li>
							<li>
								<a href="notifications.html" title="">
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-3.jpg" alt="">
										<span class="status f-online"></span>
									</figure>
									<div class="mesg-meta">
										<h6>Andrew</h6>
										<span>commented on your photo.</span>
										<i>Sunday</i>
										<figure>
											<span>"Celebrity looks Beautiful in that outfit! We should see each"</span>
											<img src="/vendor/pitnik/images/resources/admin.jpg" alt="">
										</figure>
									</div>
								</a>
							</li>
							<li>
								<a href="notifications.html" title="">
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-4.jpg" alt="">
										<span class="status f-online"></span>
									</figure>
									<div class="mesg-meta">
										<h6>Tom cruse</h6>
										<span>nvited you to attend to his event Goo in</span>
										<i>May 19</i>
									</div>
								</a>
								<span class="tag">New</span>
							</li>
							<li>
								<a href="notifications.html" title="">
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-5.jpg" alt="">
										<span class="status f-online"></span>
									</figure>
									<div class="mesg-meta">
										<h6>Amy</h6>
										<span>Andrew Changed his profile picture. </span>
										<i>dec 18</i>
									</div>
								</a>
								<span class="tag">New</span>
							</li>
						</ul>
						<a href="notifications.html" title="" class="more-mesg">View All</a>
					</div>
				</li>
				<li>
					<a href="#" title="Messages" data-ripple=""><i><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#30305b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square">
						<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
					</svg></i><em class="bg-blue">9</em></a>
					<div class="dropdowns">
						<span>5 New Messages <a href="#" title="">Mark all as read</a></span>
						<ul class="drops-menu">
							<li>
								<a class="show-mesg" href="#" title="">
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-1.jpg" alt="">
										<span class="status f-online"></span>
									</figure>
									<div class="mesg-meta">
										<h6>sarah Loren</h6>
										<span><i class="ti-check"></i> Hi, how r u dear ...?</span>
										<i>2 min ago</i>
									</div>
								</a>
							</li>
							<li>
								<a class="show-mesg" href="#" title="">
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-2.jpg" alt="">
										<span class="status f-offline"></span>
									</figure>
									<div class="mesg-meta">
										<h6>Jhon doe</h6>
										<span><i class="ti-check"></i> We’ll have to check that at the office and see if the client is on board with</span>
										<i>2 min ago</i>
									</div>
								</a>
							</li>
							<li>
								<a class="show-mesg" href="#" title="">
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-3.jpg" alt="">
										<span class="status f-online"></span>
									</figure>
									<div class="mesg-meta">
										<h6>Andrew</h6>
										<span> <i class="fa fa-paperclip"></i>Hi Jack's! It’s Diana, I just wanted to let you know that we have to reschedule..</span>
										<i>2 min ago</i>
									</div>
								</a>
							</li>
							<li>
								<a class="show-mesg" href="#" title="">
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-4.jpg" alt="">
										<span class="status f-offline"></span>
									</figure>
									<div class="mesg-meta">
										<h6>Tom cruse</h6>
										<span><i class="ti-check"></i> Great, I’ll see you tomorrow!.</span>
										<i>2 min ago</i>
									</div>
								</a>
								<span class="tag">New</span>
							</li>
							<li>
								<a class="show-mesg" href="#" title="">
									<figure>
										<img src="/vendor/pitnik/images/resources/thumb-5.jpg" alt="">
										<span class="status f-away"></span>
									</figure>
									<div class="mesg-meta">
										<h6>Amy</h6>
										<span><i class="fa fa-paperclip"></i> Sed ut perspiciatis unde omnis iste natus error sit </span>
										<i>2 min ago</i>
									</div>
								</a>
								<span class="tag">New</span>
							</li>
						</ul>
						<a href="chat-messenger.html" title="" class="more-mesg">View All</a>
					</div>
				</li>
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
				<li>
					<div class="author-thmb">
						<img src="/vendor/pitnik/images/resources/side-friend1.jpg" alt="">
						<span class="status f-online"></span>
					</div>
				</li>
				<li>
					<div class="author-thmb">
						<img src="/vendor/pitnik/images/resources/side-friend2.jpg" alt="">
						<span class="status f-away"></span>
					</div>
				</li>
				<li>
					<div class="author-thmb">
						<img src="/vendor/pitnik/images/resources/side-friend3.jpg" alt="">
						<span class="status f-online"></span>
					</div>
				</li>
				<li>
					<div class="author-thmb">
						<img src="/vendor/pitnik/images/resources/side-friend4.jpg" alt="">
						<span class="status f-offline"></span>
					</div>
				</li>
				<li>
					<div class="author-thmb">
						<img src="/vendor/pitnik/images/resources/side-friend5.jpg" alt="">
						<span class="status f-online"></span>
					</div>
				</li>
				<li>
					<div class="author-thmb">
						<img src="/vendor/pitnik/images/resources/side-friend6.jpg" alt="">
						<span class="status f-online"></span>
					</div>
				</li>
				<li>
					<div class="author-thmb">
						<img src="/vendor/pitnik/images/resources/side-friend7.jpg" alt="">
						<span class="status f-offline"></span>
					</div>
				</li>
				<li>
					<div class="author-thmb">
						<img src="/vendor/pitnik/images/resources/side-friend8.jpg" alt="">
						<span class="status f-online"></span>
					</div>
				</li>
				<li>
					<div class="author-thmb">
						<img src="/vendor/pitnik/images/resources/side-friend9.jpg" alt="">
						<span class="status f-away"></span>
					</div>
				</li>
				<li>
					<div class="author-thmb">
						<img src="/vendor/pitnik/images/resources/side-friend10.jpg" alt="">
						<span class="status f-away"></span>
					</div>
				</li>
				<li>
					<div class="author-thmb">
						<img src="/vendor/pitnik/images/resources/side-friend8.jpg" alt="">
						<span class="status f-online"></span>
					</div>
				</li>
			</ul>
			<div class="chat-box">
				<div class="chat-head">
					<span class="status f-online"></span>
					<h6>Bucky Barnes</h6>
					<div class="more">
						<div class="more-optns"><i class="ti-more-alt"></i>
							<ul>
								<li>block chat</li>
								<li>unblock chat</li>
								<li>conversation</li>
							</ul>
						</div>
						<span class="close-mesage"><i class="ti-close"></i></span>
					</div>
				</div>
				<div class="chat-list">
					<ul>
						<li class="me">
							<div class="chat-thumb"><img src="/vendor/pitnik/images/resources/chatlist1.jpg" alt=""></div>
							<div class="notification-event">
								<span class="chat-message-item">
									HI, Jack i have faced a problem with your software. are you available now, when i install this i have to received an error.
								</span>
								<span class="notification-date"><time datetime="2004-07-24T18:18" class="entry-date updated">Today at 2:12pm</time></span>
							</div>
						</li>
						<li class="you">
							<div class="chat-thumb"><img src="/vendor/pitnik/images/resources/chatlist2.jpg" alt=""></div>
							<div class="notification-event">
								<span class="chat-message-item">
									Hi tina, Please let me know your purchase code, and show me the screnshot of error.
								</span>
								<span class="notification-date"><time datetime="2004-07-24T18:18" class="entry-date updated">Today at 2:14pm</time></span>
							</div>
						</li>
						<li class="me">
							<div class="chat-thumb"><img src="/vendor/pitnik/images/resources/chatlist1.jpg" alt=""></div>
							<div class="notification-event">
								<span class="chat-message-item">
									Yes, sure please wait a while, i ll show you the complete error list. Thank you.
								</span>
								<span class="notification-date"><time datetime="2004-07-24T18:18" class="entry-date updated">Today at 2:15pm</time></span>
							</div>
						</li>
					</ul>
					<form class="text-box">
						<textarea placeholder="Post enter to post..."></textarea>
						<div class="add-smiles">
							<span title="add icon" class="em em-expressionless"></span>
							<div class="smiles-bunch">
								<i class="em em---1"></i>
								<i class="em em-smiley"></i>
								<i class="em em-anguished"></i>
								<i class="em em-laughing"></i>
								<i class="em em-angry"></i>
								<i class="em em-astonished"></i>
								<i class="em em-blush"></i>
								<i class="em em-disappointed"></i>
								<i class="em em-worried"></i>
								<i class="em em-kissing_heart"></i>
								<i class="em em-rage"></i>
								<i class="em em-stuck_out_tongue"></i>
							</div>
						</div>
						
						<button type="submit"></button>
					</form>
				</div>
			</div>
		</div>
	</div><!-- right sidebar user chat -->
	
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
					<a href="timeline-friends.html" title="Friends" data-toggle="tooltip" data-placement="right">
						<i class="fa-regular fa-user"></i>
					</a>
				</li>
				<li>
					<a href="fav-page.html" title="Favourit page" data-toggle="tooltip" data-placement="right">
						<i class="fa-regular fa-star-christmas"></i>
					</a>
				</li>
				<li>
					<a href="chat-messenger.html" title="Messages" data-toggle="tooltip" data-placement="right">
						<i class="fa-regular fa-messages"></i>
					</a>
				</li>
				<li>
					<a href="notifications.html" title="Notification" data-toggle="tooltip" data-placement="right">
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
						<li><a href="chat-messenger.html" title="">Messenger</a></li>
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
						<li><a href="timeline-friends.html" title="">Timeline Friends</a></li>
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
						<li><a href="chat-messenger.html" title="">Messenger (Chatting)</a></li>
						<li><a href="notifications.html" title="">Notifications</a></li>
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
