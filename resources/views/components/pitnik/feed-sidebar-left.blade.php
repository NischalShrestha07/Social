							<div class="col-lg-3">
								<aside class="sidebar static left">
									<div class="widget">
										<h4 class="widget-title">Profile</h4>	
										<div class="your-page">
											<figure>
												<a href="{{ route('profile.show', auth()->user()) }}" title=""><img src="{{ auth()->user()->avatarUrl() }}" alt=""></a>
												<span>{{ '@'.auth()->user()->username }}</span>
											</figure>
											<div class="page-meta">
												<a href="{{ route('profile.show', auth()->user()) }}" title="" class="underline">{{ auth()->user()->name }}</a>
											</div>
											<ul class="page-publishes">
												<li>
													<span><i class="ti-pencil-alt"></i>Publish</span>
												</li>
												<li>
													<span><i class="ti-camera"></i>Photo</span>
												</li>
												<li>
													<span><i class="ti-video-camera"></i>Live</span>
												</li>
												<li>
													<span><i class="ti-user"></i>Invite</span>
												</li>
											</ul>
											<div class="page-likes">
												<ul class="nav nav-tabs likes-btn">
													<li class="nav-item"><a class="active" href="#folower" data-toggle="tab" data-ripple="">Followers</a></li>
													 <li class="nav-item"><a class="" href="#folwing" data-toggle="tab" data-ripple="">Following</a></li>
												</ul>
												<!-- Tab panes -->
												<div class="tab-content">
												  <div class="tab-pane active fade show " id="folower" >
													<span><i class="ti-heart"></i>120K</span>
													  <a href="#" title="weekly-likes">22 new Folowers this week</a>
													  <div class="users-thumb-list">
														<a href="#" title="Anderw" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-1.jpg" alt="">  
														</a>
														<a href="#" title="frank" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-2.jpg" alt="">  
														</a>
														<a href="#" title="Sara" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-3.jpg" alt="">  
														</a>
														<a href="#" title="Amy" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-4.jpg" alt="">  
														</a>
														<a href="#" title="Ema" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-5.jpg" alt="">  
														</a>
														<a href="#" title="Sophie" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-6.jpg" alt="">  
														</a>
														<a href="#" title="Maria" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-7.jpg" alt="">  
														</a>  
													  </div>
												  </div>
												  <div class="tab-pane fade" id="folwing" >
													  <span><i class="fa fa-eye"></i>440k</span>
													  <a href="#" title="weekly-likes">440 new views this week</a>
													  <div class="users-thumb-list">
														<a href="#" title="Anderw" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-1.jpg" alt="">  
														</a>
														<a href="#" title="frank" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-2.jpg" alt="">  
														</a>
														<a href="#" title="Sara" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-3.jpg" alt="">  
														</a>
														<a href="#" title="Amy" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-4.jpg" alt="">  
														</a>
														<a href="#" title="Ema" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-5.jpg" alt="">  
														</a>
														<a href="#" title="Sophie" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-6.jpg" alt="">  
														</a>
														<a href="#" title="Maria" data-toggle="tooltip">
															<img src="/vendor/pitnik/images/resources/userlist-7.jpg" alt="">  
														</a>  
													  </div>
												  </div>
												</div>
											</div>
										</div>
									</div><!-- My Profile -->
									<div class="widget">
										<h4 class="widget-title">Nearby Places</h4>
										<ul class="contact-widget">
											<li class="contact-box">
												<div id="map-canvas"></div>
											</li>
										</ul>
									</div><!-- location-->
									<div class="widget">
										<h4 class="widget-title">Latest Videos</h4>
										<ul class="widget-video">
											<li>
												<a href="https://www.youtube.com/watch?v=UXnxgRyGzJQ" title="" data-strip-group="mygroup" class="strip" data-strip-options="width: 700,height: 450,youtube: { autoplay: 1 }"><img src="/vendor/pitnik/images/resources/last-vid1.jpg" alt="">
													<i>
														<svg version="1.1" class="play" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" height="50px" width="50px"
														 viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
													  <path class="stroke-solid" fill="none" stroke=""  d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
														C97.3,23.7,75.7,2.3,49.9,2.5"/>
													  <path class="icon" fill="" d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z"/>
														</svg>
													</i>
												</a>
												<em>2:35</em>
												<span> Beautiful red truck free style stunts</span>
											</li>
											<li>
												<a href="https://www.youtube.com/watch?v=PDluztBSaBc" title="" data-strip-group="mygroup" class="strip" data-strip-options="width: 700,height: 450,youtube: { autoplay: 1 }"><img src="/vendor/pitnik/images/resources/last-vid2.jpg" alt="">
													
													<i>
														<svg version="1.1" class="play" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" height="50px" width="50px"
														 viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
													  <path class="stroke-solid" fill="none" stroke=""  d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
														C97.3,23.7,75.7,2.3,49.9,2.5"/>
													  <path class="icon" fill="" d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z"/>
														</svg>
													</i>
												</a>
												<em>4:30</em>
												<span>Travel to south africa forest</span>
											</li>
										</ul>
									</div><!-- latest video-->
									<div class="widget whitish low-opacity">
										<div style="background-image: url(/vendor/pitnik/images/resources/dob2.png)" class="bg-image"></div>
										<div class="dob-head">
											<img src="/vendor/pitnik/images/resources/sug-page-5.jpg" alt="">
											<span>22nd Birthday</span>
											<div class="dob">
												<i>19</i>
												<span>September</span>
											</div>
										</div>
										<div class="dob-meta">
											<figure><img src="/vendor/pitnik/images/resources/dob-cake.gif" alt=""></figure>
											<h6><a href="#" title="">Lucy Carbel</a> valentine's birthday</h6>
											<p>leave a message with your best wishes on his profile.</p>
										</div>
									</div><!-- birthday widget -->
									<div class="advertisment-box">
										<h4 class="">advertisment</h4>
										<figure>
											<a href="#" title="Advertisment"><img src="/vendor/pitnik/images/resources/ad-widget.gif" alt=""></a>
										</figure>
									</div><!-- advertisment box -->
									<div class="widget">
										<h4 class="widget-title">Recent Activity</h4>
										<ul class="activitiez">
											<li>
												<div class="activity-meta">
													<i>10 hours Ago</i>
													<span><a href="#" title="">Commented on Video posted </a></span>
													<h6>by <a href="time-line.html">black demon.</a></h6>
												</div>
											</li>
											<li>
												<div class="activity-meta">
													<i>30 Days Ago</i>
													<span><a href="#" title="">Posted your status. “Hello guys, how are you?”</a></span>
												</div>
											</li>
											<li>
												<div class="activity-meta">
													<i>2 Years Ago</i>
													<span><a href="#" title="">Share a video on her timeline.</a></span>
													<h6>"<a href="#">you are so funny mr.been.</a>"</h6>
												</div>
											</li>
										</ul>
									</div><!-- recent activites -->
									<div class="widget stick-widget">
										<h4 class="widget-title">Who's follownig</h4>
										<ul class="followers">
											<li>
												<figure><img src="/vendor/pitnik/images/resources/friend-avatar2.jpg" alt=""></figure>
												<div class="friend-meta">
													<h4><a href="time-line.html" title="">Kelly Bill</a></h4>
													<a href="#" title="" class="underline">Add Friend</a>
												</div>
											</li>
											<li>
												<figure><img src="/vendor/pitnik/images/resources/friend-avatar4.jpg" alt=""></figure>
												<div class="friend-meta">
													<h4><a href="time-line.html" title="">Issabel</a></h4>
													<a href="#" title="" class="underline">Add Friend</a>
												</div>
											</li>
											<li>
												<figure><img src="/vendor/pitnik/images/resources/friend-avatar6.jpg" alt=""></figure>
												<div class="friend-meta">
													<h4><a href="time-line.html" title="">Andrew</a></h4>
													<a href="#" title="" class="underline">Add Friend</a>
												</div>
											</li>
											<li>
												<figure><img src="/vendor/pitnik/images/resources/friend-avatar8.jpg" alt=""></figure>
												<div class="friend-meta">
													<h4><a href="time-line.html" title="">Sophia</a></h4>
													<a href="#" title="" class="underline">Add Friend</a>
												</div>
											</li>
											<li>
												<figure><img src="/vendor/pitnik/images/resources/friend-avatar3.jpg" alt=""></figure>
												<div class="friend-meta">
													<h4><a href="time-line.html" title="">Allen</a></h4>
													<a href="#" title="" class="underline">Add Friend</a>
												</div>
											</li>
										</ul>
									</div><!-- who's following -->
								</aside>
							</div><!-- sidebar -->
