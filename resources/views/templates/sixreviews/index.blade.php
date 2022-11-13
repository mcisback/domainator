<!doctype html>
<html lang="en">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MX565BP');</script>
    <!-- End Google Tag Manager -->
    <!-- Snap Pixel Code -->
    <script type='text/javascript'>
        (function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function()
        {a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};
            a.queue=[];var s='script';r=t.createElement(s);r.async=!0;
            r.src=n;var u=t.getElementsByTagName(s)[0];
            u.parentNode.insertBefore(r,u);})(window,document,
            'https://sc-static.net/scevent.min.js');

        snaptr('init', '619d3bfc-6e2d-43e1-86d0-b5092250e271', {

        });

        snaptr('track', 'VIEW_CONTENT');

    </script>
    <!-- End Snap Pixel Code -->
    <script>
        let url=new URL(document.location);
        let x=new URLSearchParams(url.search);
        let px_id=x.get("geektt");
        let camp_id = x.get("geekcid");
        let fb_id =x.get("fbid");

        if (camp_id) {
            var a=document.createElement("script");a.setAttribute("id","geekdyn");a.setAttribute("type","text/javascript");a.setAttribute("src","https://{{ $yourDomain
 }}/track.js?rtkcmpid="+camp_id);document.head.appendChild(a);
        }

        if (px_id) {
            !function (w, d, t) {
                w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
                ttq.load(px_id);
                ttq.page();
                window.onload = function() {
                    let btn = document.getElementsByClassName("sponsored-button");
                    for (i = 0; i < btn.length; i++) {
                        btn[i].addEventListener("click", function () {ttq.track('ClickButton')});
                    }
                }
            }(window, document, 'ttq');
        }
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

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Informations sur le service client des services publics en France - Six Reviews</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="app()">
<nav class="navbar" id="header">
    <a href="https://sixreviews.com/"><img class="navbar-logo" src="images/logo.png" alt="logo"></a>
    <ul class="navbar-list">
<li class="navbar-list-item">
            <a class="navbar-list-item" href="https://sixreviews.com/">
                <span trans>Home page</span>
            </a>
        </li>
        <li class="navbar-list-item dropdown">
            <span trans>Online knowledge</span>
            <i class="arrow down"></i>
            <div class="dropdown-content">
                <ul class="dropdown-content-list">
                    <li class="dropdown-content-list-item">
                        <a class="dropdown-content-list-item" href="https://sixreviews.com/categories/finances/">
                            <span trans>Finance</span>
                        </a>
                    </li>
                    <li class="dropdown-content-list-item">
                        <a class="dropdown-content-list-item"
                           href="https://sixreviews.com/categories/online-knowledge/">
                            <span trans>Online knowledge</span>
                        </a>
                    </li>
                    <li class="dropdown-content-list-item">
                        <a class="dropdown-content-list-item"
                           href="https://sixreviews.com/categories/computers-and-gadgets/">
                            <span trans>Computers and Gadgets</span>
                        </a>
                    </li>
                    <li class="dropdown-content-list-item">
                        <a class="dropdown-content-list-item" href="https://sixreviews.com/categories/automotive/">
                            <span trans>Car</span>
                        </a>
                    </li>
                    <li class="dropdown-content-list-item">
                        <a class="dropdown-content-list-item" href="https://sixreviews.com/categories/entertaiment/">
                            <span trans>Entertainment</span>
                        </a>
                    </li>
                    <li class="dropdown-content-list-item">
                        <a class="dropdown-content-list-item" href="https://sixreviews.com/categories/cosmetics/">
                            <span trans>Cosmetics</span>
                        </a>
                    </li>
                    <li class="dropdown-content-list-item">
                        <a class="dropdown-content-list-item" href="https://sixreviews.com/categories/lifestyle/">
                            <span trans>Way of life</span>
                        </a>
                    </li>
                    <li class="dropdown-content-list-item">
                        <a class="dropdown-content-list-item"
                           href="https://sixreviews.com/categories/health/">
                            <span trans>Health</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="navbar-list-item">
            <a class="navbar-list-item" href="https://sixreviews.com/about-us/">
                <span trans>About Us</span>
            </a>
        </li>
        <li class="navbar-list-item">
            <a class="navbar-list-item" href="https://sixreviews.com/contact-us/">
                <span trans>Contact us</span>
            </a>
        </li>
        <li class="navbar-list-item dropdown">
            <span trans>Change Language</span>
            <i class="arrow down"></i>
            <div class="dropdown-content">
                <ul class="dropdown-content-list">
                    <template x-for="(locale, index) in locales" :key="index">
                        <li class="dropdown-content-list-item">
                            <a class="dropdown-content-list-item text-uppercase" :href="`#${locale}`" x-on:click.prevent="changeLanguage(`${locale}`)">
                                <span x-text="locale"></span>
                            </a>
                        </li>
                    </template>
                </ul>
            </div>
        </li>
    </ul>
    <div class="navbar-mobile">
        <div class="navbar-mobile-button">
            <svg class="svg-icon" aria-hidden="true" role="img" focusable="false" xmlns="http://www.w3.org/2000/svg"
                 width="26" height="7" viewBox="0 0 26 7">
                <path fill-rule="evenodd"
                      d="M332.5,45 C330.567003,45 329,43.4329966 329,41.5 C329,39.5670034 330.567003,38 332.5,38 C334.432997,38 336,39.5670034 336,41.5 C336,43.4329966 334.432997,45 332.5,45 Z M342,45 C340.067003,45 338.5,43.4329966 338.5,41.5 C338.5,39.5670034 340.067003,38 342,38 C343.932997,38 345.5,39.5670034 345.5,41.5 C345.5,43.4329966 343.932997,45 342,45 Z M351.5,45 C349.567003,45 348,43.4329966 348,41.5 C348,39.5670034 349.567003,38 351.5,38 C353.432997,38 355,39.5670034 355,41.5 C355,43.4329966 353.432997,45 351.5,45 Z"
                      transform="translate(-329 -38)"></path>
            </svg>
            <span class="text vsm">Menu</span>
        </div>
        <div class="navbar-mobile-wrap">
            <div class="navbar-mobile-content">
                <div class="navbar-mobile-close">
                    <span>Close Menu</span>
                    <svg class="svg-icon" aria-hidden="true" role="img" focusable="false"
                         xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16">
                        <polygon fill="" fill-rule="evenodd"
                                 points="6.852 7.649 .399 1.195 1.445 .149 7.899 6.602 14.352 .149 15.399 1.195 8.945 7.649 15.399 14.102 14.352 15.149 7.899 8.695 1.445 15.149 .399 14.102"></polygon>
                    </svg>
                </div>
                <ul class="navbar-mobile-list">
                    <li class="navbar-mobile-list-item">
                        <a class="navbar-mobile-list-item" href="https://sixreviews.com/">
                            <span trans>Home page</span>
                        </a>
                    </li>
                    <li class="navbar-mobile-list-item navbar-mobile-accordion">
                        <span trans>Explore our content</span>
                        <ul class="navbar-mobile-panel">
                            <li>
                             <a href="https://sixreviews.com/categories/finances/">
                                 <span trans>Finance</span>
                             </a>
                            </li>
                            <li>
                                <a href="https://sixreviews.com/categories/online-knowledge/">
                                    <span trans>Online knowledge</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://sixreviews.com/categories/computers-and-gadgets/">
                                    <span trans>Computers and Gadgets</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://sixreviews.com/categories/automotive/">
                                    <span trans>Car</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://sixreviews.com/categories/entertainment/">
                                    <span trans>Entertainment</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://sixreviews.com/categories/cosmetics/">
                                    <span trans>Cosmetics</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://sixreviews.com/categories/lifestyle/">
                                    <span trans>Way of life</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://sixreviews.com/categories/health/">
                                    <span trans>Health</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="navbar-mobile-list-item">
                       <a class="navbar-mobile-list-item" href="https://sixreviews.com/">
                           <span trans>Àbout Us</span>
                         </a>
                     </li>
                     <li class="navbar-mobile-list-item">
                         <a class="navbar-mobile-list-item" href="https://sixreviews.com/">
                             <span trans>Contact us</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<main class="container">
  <h1 class="title">{{ $headline  }}</h1>
  <p class="text margin">{{ $subheadline  }}</p>
  <div class="sponsored">
        <p class="text margin bold italic">Sponsored Listings</p>
        <div class="sponsored-buttons">
            <a trackAll href="https://{{ $yourDomain }}/click" class="sponsored-button green">
                <i aria-hidden="true" class="fas fa-angle-double-right"></i>{{ $button1_label  }}
            </a>
            <a trackAll href="https://{{ $yourDomain }}/click" class="sponsored-button blue">
                <i aria-hidden="true" class="fas fa-angle-double-right"></i>{{ $button2_label  }}
            </a>
            <a trackAll href="https://{{ $yourDomain }}/click" class="sponsored-button red">
                <i aria-hidden="true" class="fas fa-angle-double-right"></i>{{ $button3_label  }}
            </a>
            <a trackAll href="https://{{ $yourDomain }}/click" class="sponsored-button yellow">
                <i aria-hidden="true" class="fas fa-angle-double-right"></i>{{ $button4_label  }}
            </a>
        </div>
    </div>
    <div class="content">
        {!! $paragraph1 !!}

        <a href="https://{{ $yourDomain }}/click">
            <img class="content-img" src="{{ $header_image  }}" alt="header_image">
        </a>

        <div class="info">
            <div class="info-block">
                {!! $paragraph2 !!}
            </div>
        </div>
        <div class="sponsored">
            <p class="text margin bold italic">Sponsored Listings</p>
            <div class="sponsored-buttons">
                <a trackAll href="https://{{ $yourDomain }}/click" class="sponsored-button green">
                    <i aria-hidden="true" class="fas fa-angle-double-right"></i>{{ $button1_label  }}
                </a>
                <a trackAll href="https://{{ $yourDomain }}/click" class="sponsored-button blue">
                    <i aria-hidden="true" class="fas fa-angle-double-right"></i>{{ $button2_label  }}
                </a>
                <a trackAll href="https://{{ $yourDomain }}/click" class="sponsored-button red">
                    <i aria-hidden="true" class="fas fa-angle-double-right"></i>{{ $button3_label  }}
                </a>
                <a trackAll href="https://{{ $yourDomain }}/click" class="sponsored-button yellow">
                    <i aria-hidden="true" class="fas fa-angle-double-right"></i>{{ $button4_label  }}
                </a>
            </div>
        </div>
        <div class="info">
            <div class="info-ref">
                <p class="text vsm margin">
                    References used in this article:
                </p>

                <x-print-references :references="$references" />

            </div>
            <div class="info-contacts">
            <p classe = "text">
                      <span class = "bold" x-text="trans(`Contact:`)"></span> P. J. Williams / Six Reviews Public Relations
                  </p>
                  <p classe = "text">
                      <span class = "bold" x-text="trans(`Phone:`)"></span> (416) 531-8107
                  </p>
                  <p classe = "text">
                      <span class = "bold" x-text="trans(`Address:`)"></span> 18 Tinder Cres North York ON M4A 1L4, Canada
                  </p>
                  <p classe = "text">
                      <span class = "bold" x-text="trans(`E-mail:`)"> E-mail: </span> pr@sixreviews.com
                </p>
            </div>
        </div>
    </div>
</main>

<footer class="footer">
    <a class="footer-copyright bold" href="https://sixreviews.com/">© 2021 Six Reviews</a>
    <a class="footer-copyright" :href="privacyLink">
        <span trans>Privacy policy</span>
    </a>
    <a class="footer-copyright" href="https://sixreviews.com/terms-of-service/">
        <span trans>Terms of service</span>
    </a>
    <a class="footer-scroll" href="#header">
        <span trans>Go to the top</span> ↑
    </a>
</footer>

<script src="https://kit.fontawesome.com/ff903b2c1f.js" crossorigin="anonymous"></script>
<script src="script.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        [...document.querySelectorAll('[trackAll]')]
            .forEach(a => {
                // console.log('Add Tracking ADD_TO_CART to: ', a.innerText)

                a.addEventListener('click', (e) => {
                    // console.log('Tracking ADD_TO_CART')

                    // e.preventDefault();

                    snaptr('track','ADD_CART')
                    fbq('track', 'AddtoCart')
                    ttq.track('AddToCart')
                })
            })
    }, false)

</script>

<x-template-translation-script :translations="$translations" :locales="$locales" :privacyLinks="$privacyLinks" :defaultLocale="$default_locale" />

</body>
</html>
