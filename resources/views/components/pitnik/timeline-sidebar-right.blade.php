							@props(['user'])
							<div class="col-lg-3">
								<aside class="sidebar static right">
									<div class="widget">
										<h4 class="widget-title">Your page</h4>	
										<div class="your-page">
											<figure>
												<a href="#" title=""><img src="/vendor/pitnik/images/resources/friend-avatar9.jpg" alt=""></a>
											</figure>
											<div class="page-meta">
												<a href="#" title="" class="underline">My Creative Page</a>
												<span><i class="ti-comment"></i><a href="insight.html" title="">Messages <em>9</em></a></span>
												<span><i class="ti-bell"></i><a href="insight.html" title="">Notifications <em>2</em></a></span>
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
													<li class="nav-item"><a class="active" href="#link1" data-toggle="tab" data-ripple="">likes</a></li>
													 <li class="nav-item"><a class="" href="#link2" data-toggle="tab" data-ripple="">views</a></li>
												</ul>
												<!-- Tab panes -->
												<div class="tab-content">
												  <div class="tab-pane active fade show " id="link1" >
													<span><i class="ti-heart"></i>884</span>
													  <a href="#" title="weekly-likes">35 new likes this week</a>
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
												  <div class="tab-pane fade" id="link2" >
													  <span><i class="fa fa-eye"></i>440</span>
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
									</div><!-- page like widget -->
									<div class="widget">
										<h4 class="widget-title">Profile intro</h4>
										<ul class="short-profile">
											<li>
												<span>about</span>
												<p>{{ $user->bio ?: 'No bio yet.' }}</p>
											</li>
										</ul>
									</div>
									<div class="widget">
										<div class="banner medium-opacity purple">
											<div class="bg-image" style="background-image: url(/vendor/pitnik/images/resources/baner-widgetbg.jpg)"></div>
											<div class="baner-top">
												<span><img alt="" src="/vendor/pitnik/images/book-icon.png"></span>
												<i class="fa fa-ellipsis-h"></i>
											</div>
											<div class="banermeta">
												<p>
													create your own favourit page.
												</p>
												<span>like them all</span>
												<a data-ripple="" title="" href="#">start now!</a>
											</div>
										</div>											
									</div>
									<div class="widget pitnik-links stick-widget">
										<ul>
											<li><a href="#" title="">about</a></li>
											<li><a href="#" title="">career</a></li>
											<li><a href="#" title="">advertise</a></li>
											<li><a href="#" title="">Pitnik Apps</a></li>
											<li><a href="#" title="">Pitnik Blog</a></li>
											<li><a href="#" title="">Help</a></li>
											<li><a href="#" title="">Pitnik Gifts</a></li>
											<li><a href="#" title="">contetn policy</a></li>
											<li><a href="#" title="">User Policy</a></li>
										</ul>
										<p>© Pitnik 2024. All Rights Reserved.</p>
									</div><!-- little links -->
								</aside>
							</div><!-- sidebar -->
