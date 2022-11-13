<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{$tr('SixReviews')}}</title>
	<meta name="description" content="{name}" />
	<meta name="keywords" content="" />
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="http://sixreviews.com/favicon.ico">

	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet"/>

	<!-- TikTok pixel -->
	<script>
		let url=new URL(document.location);
		let x=new URLSearchParams(url.search);
		let px_id=x.get("geektt");
		let camp_id = x.get("geekcid");
		let fb_id =x.get("fbid");

		// ..................................................................................................
		// ................................. EDIT HERE ...................................................
		// ....................................>> .........................................................
		if (camp_id) {
			var a=document.createElement("script");a.setAttribute("id","geekdyn");a.setAttribute("type","text/javascript");a.setAttribute("src","https://{{ $yourDomain }}/track.js?rtkcmpid="+camp_id);document.head.appendChild(a);
		}

		// ................................... END EDIT ................................................
		if (px_id) {
			!function (w, d, t) {
				w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
				ttq.load(px_id);
				ttq.page();
                w.ttq = ttq
				window.onload = function() {
					let btn = document.getElementsByClassName("sponsored-button");
					for (i = 0; i < btn.length; i++) {
						btn[i].addEventListener("click", function () {ttq.track('ClickButton')});
					}
				}
			}(window, document, 'ttq');
		}

     //only on FB --- DONT TOUCH BELOW

     if (fb_id) {
     	!function(f,b,e,v,n,t,s)
     	{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
     		n.callMethod.apply(n,arguments):n.queue.push(arguments)};
     		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
     		n.queue=[];t=b.createElement(e);t.async=!0;
     		t.src=v;s=b.getElementsByTagName(e)[0];
     		s.parentNode.insertBefore(t,s)}(window, document,'script',
     			'https://connect.facebook.net/en_US/fbevents.js');
     		fbq('init', '{your-pixel-id-goes-here}');
     		fbq('track', fb_id);
     	}

     </script>
</head>
<body x-data="app()">
	<div class="top-header">
		<div class="container">
			<a href="https://sixreviews.com/"><img class="navbar-logo" src="images/logo.png" alt="logo"></a>
		</div>
	</div>

	<!-- .............................................BODY OF THE PAGE............................................  -->
	<div class="slider">
		<div class="container">
			<div class="slider-text">

				<!-- EDIT THE TITLE HERE -->
				<h1>{{ $headline }}</h1>
				<!-- TITLE EDIT END-->

				<!-- ....................................SUBTITLE.....................................................  -->

				<!-- EDIT THE SUBTITLE HERE -->
				<p id="intro">
					{{ $subheadline }}
				</p>
				<!-- SUBTITLE EDIT END-->

				<!-- .........................................................................................  -->

				<div class="container">
					<div><i>{{$tr('Sponsored Listing')}}</i></div>
				</div>
				<!-- ..................................... BUTTONS....................................................  -->

				<!-- BUTTONS EDIT -->
				<div>

					<a href="https://{{ $yourDomain }}/click" class="button block button1" onclick="ttq.track('ClickButton')">
						{{ $button1_label }}
					</a>

					<a href="https://{{ $yourDomain }}/click" class="button block button2" onclick="ttq.track('ClickButton')">
                        {{ $button2_label }}
					</a>

					<a href="https://{{ $yourDomain }}/click" class="button button3 block" onclick="ttq.track('ClickButton')">
                        {{ $button3_label }}
					</a>

					<a href="https://{{ $yourDomain }}/click" class="button button4 block" onclick="ttq.track('ClickButton')">
                        {{ $button4_label }}
					</a>


				</div>
				<!-- BUTTONS EDIT END-->
				<!-- .........................................................................................  -->

			</div>

			<!-- TEXT AND IMAGE EDIT -->
			<div class="text-content">
				<p>
                    {!! $paragraph1 !!}
				</p>



				<!-- IMAGE EDIT -->
				<div class="slider-img">
					<img src="{{ $header_image }}" class="img-fluid" alt="slider" />
				</div>
				<!-- IMAGE EDIT END-->


				<p>
                    {!! $paragraph2 !!}
				</p>
			</div>
			<!-- TEXT AND IMAGE EDIT END-->


		</div>
	</div>

	<!-- ...........................................LIST ITEMS ALL..............................................  -->

	<div class="icon-section" id="section-02">
		<div class="container">

			<!-- .................................. LIST ITEM 1.......................................................  -->
			<div class="icon-row">
				<div class="row">
					<div class="col-md-2">
						<div class="icon-section-img"><img src="{{ $list_item_1_image }}" class="img-fluid" alt="icon-01" /></div>
					</div>
					<div class="col-md-10">
                        <div class="col-md-10">
                            <div class="text-content">
                                <div class="icon-head">{{ $list_item_1 }}</div>
                                <div class="review-star">
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                </div>
                                <p>
                                    {{ $list_item_1_text }}
                                </p>
                            </div>
                        </div>
					</div>
				</div>
			</div>

			<!-- ....................................LIST ITEM 2.....................................................  -->


			<div class="icon-row">
				<div class="row">
					<div class="col-md-2">
						<div class="icon-section-img"><img src="{{ $list_item_2_image }}" class="img-fluid" alt="icon-02" /></div>
					</div>
					<div class="col-md-10">
						<div class="text-content">
							<div class="icon-head">{{ $list_item_2 }}</div>
							<div class="review-star">
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
							</div>
							<p>
                                {{ $list_item_2_text }}
							</p>
						</div>
					</div>
				</div>
			</div>

			<!-- ....................................LIST ITEM 3.....................................................  -->


			<div class="icon-row">
				<div class="row">
					<div class="col-md-2">
						<div class="icon-section-img"><img src="{{ $list_item_3_image }}" class="img-fluid" alt="icon-03" /></div>
					</div>
                    <div class="col-md-10">
                        <div class="text-content">
                            <div class="icon-head">{{ $list_item_3 }}</div>
                            <div class="review-star">
                                <span class="fa fa-star"></span>
                                <span class="fa fa-star"></span>
                                <span class="fa fa-star"></span>
                                <span class="fa fa-star"></span>
                                <span class="fa fa-star"></span>
                            </div>
                            <p>
                                {{ $list_item_3_text }}
                            </p>
                        </div>
                    </div>
				</div>
			</div>

			<!-- ..............................COPY LIST ITEMS HERE ................................................  -->


			<!-- ..............................END COPY LIST ITEMS................................................  -->

			<!-- ........................................BANNER.................................................  -->

			<div class="banner-section">
				<div class="container">
					<div class="section-img">
						<img src="images/img-03.jpg" class="img-fluid" alt="img-03" />
						<div class="absolute-head">
							<h4>{{ $banner_text ?? '' }}</h4>
						</div>
					</div>
				</div>
			</div>
			<!-- ........................................TEXT UNDER BANNER.................................................  -->

			<div class="text-content">
				<p>
					{{ $text_under_banner }}
				</p>
			</div>
			<!-- .........................................................................................  -->



			<div class="container">
				<div><i>{{$tr('Sponsored Listing')}}</i></div>
			</div>

			<!-- .....................................BUTTONS....................................................  -->

			<!-- BUTTONS EDIT -->
			<div>

                <a href="https://{{ $yourDomain }}/click" class="button block button1" onclick="ttq.track('ClickButton')">
                    {{ $button1_label }}
                </a>

                <a href="https://{{ $yourDomain }}/click" class="button block button2" onclick="ttq.track('ClickButton')">
                    {{ $button2_label }}
                </a>

                <a href="https://{{ $yourDomain }}/click" class="button button3 block" onclick="ttq.track('ClickButton')">
                    {{ $button3_label }}
                </a>

                <a href="https://{{ $yourDomain }}/click" class="button button4 block" onclick="ttq.track('ClickButton')">
                    {{ $button4_label }}
                </a>


			</div>
			<!-- BUTTONS EDIT END-->

			<br><br>

			<!-- .......................REFERENCES EDIT........................-->
			<div class="container">
				<div>
                    {{$tr('References used in this article')}} :

                    <x-print-references :references="$references1" />
				</div>
			</div>
			<!-- REFERENCES EDIT END-->

			<br><br>
			<!-- .........................................................................................  -->
			<!--DONT TOUCH BELOW-->
			<!--DONT TOUCH BELOW-->
			<!--DONT TOUCH BELOW-->
			<!--DONT TOUCH BELOW-->

			<div class="footer">
				<div class="footer-menu">
					<div class="container">
						<div class="footer-menu-flex">
							<li><a href="{{ $privacyLink }}">{{$tr('Privacy policy')}}</a></li>
							<li><a href="https://sixreviews.com/terms-of-service/">{{($tr('Terms & Conditions'))}}</a></li>
						</div>
					</div>
				</div>
				<div class="disclaimer">
					<div class="container">
						<div class="text-content">
							<h5>{{$tr('Disclaimer')}}</h5>
							<p>{{$tr('We dont sell the services advertised in this ad. We are an introductionary service that
                                helps users decide what they want to learn more about. All the companies mentioned are
                                licenced in their own respected countries.')}}</p>
						</div>
					</div>
				</div>
			</div>


		</div>
	</div>

{{--    <x-template-translation-script :defaultLocale="$default_locale" :translations="$translations" :locales="$locales" :privacyLinks="$privacyLinks" />--}}

</body>
</html>
