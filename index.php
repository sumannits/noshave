<?php
include_once 'platform/includes/db_connect.php';
include_once 'platform/includes/functions.php';
sec_session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="The goal of No-Shave November is to grow awareness by embracing our hair, which many cancer patients lose, and letting it grow wild and free. Donate the money you typically spend on shaving and grooming to educate about cancer prevention, save lives, and aid those fighting the battle.">
	<meta name="keywords" content="no,shave,november,beard,charity,reason,origin,rules,movement,meaning,month,cancer,no-shave,purpose,register,sign,up,login,cause,prostate,sup
	port,american,cancer,society,month">
	<meta name="author" content="No-Shave November">
	<link rel="icon" type="image/png" href="favicon.png">
	<title>No-Shave November</title>

	<!-- Bootstrap core CSS -->
	<link href="<?php echo base_url; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo base_url; ?>/assets/css/slick-theme.css" rel="stylesheet">
	<link href="<?php echo base_url; ?>/assets/css/slick.css" rel="stylesheet">   
	<link href="<?php echo base_url; ?>/assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="<?php echo base_url; ?>/assets/fonts/themify-icons.css" rel="stylesheet">   
	<!-- Custom styles for this template -->
	<link href="<?php echo base_url; ?>/assets/css/animations.css" rel="stylesheet">
	<link href="<?php echo base_url; ?>/assets/css/theme.css" rel="stylesheet">
	<link href="<?php echo base_url; ?>/assets/css/responsive.css" rel="stylesheet">
        <script src="<?php echo base_url; ?>/assets/js/jquery.min.js" type="text/javascript"></script>
    
  </head>
  <body id="nav">
       <header>   
        <?php include_once('platform/menu.php')?>
        </header>
        <!--end of header-->
        <section class="banner">
            <div class="clearfix">
                <ul class="banner-slide list-unstyled mb-0">
                    <li><figure><img src="<?php echo base_url; ?>/assets/images/banner.jpg" class="img-fluid" alt="banner"></figure></li>
                    <li><figure><img src="<?php echo base_url; ?>/assets/images/banner.jpg" class="img-fluid" alt="banner"></figure></li>
                    <li><figure><img src="<?php echo base_url; ?>/assets/images/banner.jpg" class="img-fluid" alt="banner"></figure></li>
                </ul>
            </div>
        </section>
        <!--end of banner-->
        <section class="what-is-nov py-5" id="about">
            <div class="container">
                <div class="top-heading text-center">
                    <span class="text-uppercase">What is No-Shave</span>
                    <h2>November?</h2>                    
                    <p>No-Shave November is a month-long journey during which participants forgo shaving and grooming in order to evoke conversation and raise cancer awareness. Learn more about how you can get involved and start getting hairy!</p>
                </div>
                <!--end of top heading-->
                
                <div class="row skew-box py-5 mb-3 animatedParent">
                    <div class="col-12 col-lg-4 animated bounceInLeft animate-1">
                        <div class="cr-box cr-box-1">
                            <div class="in-content">
                                <h2>The Concept</h2>
                                <p>The goal of No-Shave November is to grow awareness by embracing our hair, which many cancer patients lose,
                                    and letting it grow wild and free. Donate the money you typically spend on shaving and grooming to educate
                                    about cancer prevention, save lives, and aid those fighting the battle.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 animated bounceInDown animate-2">
                        <div class="cr-box cr-box-2">
                            <div class="in-content">
                                <h2>Get Involve</h2>
                                <p>Participate by growing a beard, cultivating a mustache, letting those legs go natural, and skipping that waxing appointment.
                                    Put down your razor and <a style="color:#b1bd1b !important" href="<?php echo base_url; ?>/register">set up your own</a> personal No-Shave November fundraising page. If you're not ready to get hairy,
                                    sit back and <a style="color:#b1bd1b !important" href="<?php echo base_url; ?>/leaderboard">support someone who is</a>.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 animated bounceInRight animate-1">
                        <div class="cr-box cr-box-3">
                            <div class="in-content">
                                <h2>The Rules</h2>
                                <p>The rules of No-Shave November are simple: put down your razor for 30 days and donate your monthly
                                    hair-maintenance expenses to the cause. Strict dress-code at work? Don't worry about it!
                                    We encourage participation of any kind; grooming and trimming are perfectly acceptable.</p>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
        </section>
        <!--end of what-is-nov-->
        <section class="our-mission" style="background-image:url(assets/images/partical-bg.jpg);">
            <div class="container">
                <div class="row align-items-center animatedParent">
                    <div class="col-12 col-md-7 animated bounceInDown">
                        <div class="content-m">
                            <h2 class="text-uppercase text-bold">Our Mission</h2>
                            <p>No-Shave November is a web-based, non-profit organization devoted to growing cancer awareness and raising funds to support cancer prevention, research, and education.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-5 animated growIn animate-2">
                        <figure>
                            <img src="<?php echo base_url; ?>/assets/images/heart.png" class="img-fluid" alt="help">
                        </figure>
                    </div>
                </div>
            </div>
        </section>
        <!--end of mission-->
        <section class="participate" id="participate">
            <div class="container">
                <div class="top-heading text-center">
                    <h2>Participate</h2>
                    <p>Put down your razor and join the fun! Create your own personal fundraising page by registering to participate in No-Shave November.</p>
                </div>
                <div class="bk-li">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="brwon-bg">
                            <div class="cont-text text-center">
                                <h3>Register to Participate</h3>
                                <p>Sign up to officially participate in No-Shave November. Stop shaving and start fundraising! 
                                    If you’ve got that competitive edge, get a group together and start your own team. Thinking maybe you’d rather 
                                    go solo? Sign up as an individual and set up your personal fundraising page today.</p>
                                <a href="<?php echo base_url; ?>/register" class="btn btn-light text-uppercase">Join Now</a>
                            </div>
                        </div>
                        <div class="green-bg">
                            <div class="cont-text text-center">
                                <h3>Support the Cause</h3>
                                <p>No-Shave November and its funded programs are putting your donation dollars to work, investing 
                                    in groundbreaking cancer research and providing free information and services to cancer patients and their caregivers. 
                                    Donate to your favorite team or participant or make a general donation.</p>
                                 <a href="<?php echo base_url; ?>/donate" class="btn btn-light">Donate</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
       <!--end of participate-->
       <section class="funded-programs" style="background-image:url(<?php echo base_url; ?>/assets/images/cans-bg.jpg);" id="fundedprogram">
           <div class="container">
               <div class="top-heading text-center">
                   <span>Funded Programs</span>
                   <h2>2017</h2>
                   <p>No-Shave November is proud to be working with the following organizations to achieve our mission.</p>
               </div>
               <div class="row">
                   <div class="col-12 col-md-4">
                       <div class="fund-box text-center">
                           <figure>
                               <img src="<?php echo base_url; ?>/assets/images/img1.png" class="img-fluid" alt="funding logo">
                           </figure>
                           <div class="clearfix text-center">
                               <h4>Prevent Cancer Foundation</h4>
                               <p>Prevent Cancer Foundation is the only U.S. nonprofit solely focused on the prevention and early detection of cancer. </p>
                               <a data-toggle="modal" data-target="#preventCancer" class="text-white">Learn More</a>
                           </div>
                       </div>
                   </div>
                   <div class="col-12 col-md-4">
                       <div class="fund-box text-center">
                           <figure>
                               <img src="<?php echo base_url; ?>/assets/images/img2.png" class="img-fluid" alt="funding logo">
                           </figure>
                           <div class="clearfix text-center">
                               <h4>Fight Colorectal Cancer</h4>
                               <p>Fight Colorectal Cancer is a community of activists committed to fighting colorectal cancer until there's a cure.</p>
                               <a data-toggle="modal" data-target="#fightColorectal" class="text-white">Learn More</a>
                           </div>
                       </div>
                   </div>
                   <div class="col-12 col-md-4">
                       <div class="fund-box text-center">
                           <figure>
                               <img src="<?php echo base_url; ?>/assets/images/img3.png" class="img-fluid" alt="funding logo">
                           </figure>
                           <div class="clearfix text-center">
                               <h4>St. Jude Children's Research Hospital</h4>
                               <p>St. Jude Children's Research Hospital is leading the way the world understands, treats and defeats childhood cancer. </p>
                               <a data-toggle="modal" data-target="#stJudes" class="text-white">Learn More</a>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </section>
       <!--end of funded programs-->
       <section class="spread-word" id="share">
           <div class="container">
               <div class="top-heading text-center">
                   <h2 class="mb-0">Spread the Word</h2>
                   <p class="mt-0">With your help, we can get the word out.</p>
               </div>
               <div class="row">
                   <div class="col-lg-6 col-12">
                       <div class="left-pink-box pink-box">
                           <figure>
                               <img src="<?php echo base_url; ?>/assets/images/social.png" class="img-fluid" alt="social">
                           </figure>
                           <div class="clearfix">
                               <h2>Like, Retweet, Snap & More</h2>
                               <p>Help us spread awareness by sharing No-Shave November on social media. Now that you're rocking that new look, 
                                   snap it and let the world know that you're participating in the fun! Every like and every retweet grows the 
                                   No-Shave November community.</p>
                               <div class="btn-inline">
                                   <a target="_blank" href="https://www.facebook.com/noshavenov" class="btn btn-fb"><i class="ti-facebook"></i> Facebook</a>
                                   <a target="_blank" href="https://twitter.com/no_shave" class="btn btn-twitter"><i class="ti-twitter"></i> Twitter</a>
                                   <a target="_blank" href="https://instagram.com/no_shave_november" class="btn btn-insta"><i class="ti-instagram"></i> Instagram</a>
                               </div>
                           </div>
                       </div>
                   </div>
                   <div class="col-lg-6 col-12">
                       <div class="right-pink-box pink-box">
                           <figure>
                               <img src="<?php echo base_url; ?>/assets/images/cerificate.png" class="img-fluid" alt="certificate" />
                           </figure>
                           <div class="clearfix text-center">
                               <h2>Print it & Post it</h2>
                               <p>Want everyone at the office to know why you're getting so hairy? Print out a flyer and post it on your school 
                                   bulletin board, fridge, cubicle wall, and on the telephone pole outside of your house! You can help No-Shave 
                                   November grow awareness in every community.</p>
                               
                               <a href="https://storage.googleapis.com/nsn-misc/no_shave_november_2016.pdf" target="_blank" class="btn btn-primary">Download Flyer</a>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </section>
       <!--end of spread word-->
       <section class="awerness animatedParent" style="background-image:url(<?php echo base_url; ?>/assets/images/green-bg.jpg);">
           <div class="container">
               <div class="d-flex align-items-center justify-content-center animated growIn">
                   <div class="clearfix text-center">
                       <h2>Awareness Initiatives</h2>
                       <p><a href="<?php echo base_url; ?>/awareness.html">Learn more</a> about Movember which is spreading awareness and raising funds for cancer research, just like us!</p>
                   </div>
               </div>
           </div>
       </section>
       <!--end of awerness-->
       <section class="shop-home"  id="shop">
           <div class="container">
               <div class="top-heading text-center">
                   <h2>Shop</h2>
                   <p>Support the cause by picking up some limited-edition No-Shave November gear. Not only do you get some sweet merchandise but also the proceeds benefit the cause.</p>
               </div>
               
               <div class="shop-cont row">
                <div id='product-component-5c11d0d3b9b'></div>
                    <script type="text/javascript">
                        /*<![CDATA[*/

                        (function () {
                            var scriptURL = 'https://sdks.shopifycdn.com/buy-button/latest/buy-button-storefront.min.js';
                            if (window.ShopifyBuy) {
                            if (window.ShopifyBuy.UI) {
                                ShopifyBuyInit();
                            } else {
                                loadScript();
                            }
                            } else {
                            loadScript();
                            }

                            function loadScript() {
                            var script = document.createElement('script');
                            script.async = true;
                            script.src = scriptURL;
                            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(script);
                            script.onload = ShopifyBuyInit;
                            }

                            function ShopifyBuyInit() {
                            var client = ShopifyBuy.buildClient({
                                domain: 'mhf-dba-no-shave-november.myshopify.com',
                                apiKey: '0007cd26e3c16354feca5d50133e707a',
                                appId: '6',
                            });

                            ShopifyBuy.UI.onReady(client).then(function (ui) {
                                ui.createComponent('product', {
                                id: [13107391179],
                                node: document.getElementById('product-component-5c11d0d3b9b'),
                                moneyFormat: '%24%7B%7Bamount%7D%7D',
                                options: {
                            "product": {
                            "variantId": "all",
                            "contents": {
                                "imgWithCarousel": false,
                                "variantTitle": false,
                                "description": false,
                                "buttonWithQuantity": false,
                                "quantity": false
                            },
                            "styles": {
                                "product": {
                                "@media (min-width: 601px)": {
                                    "max-width": "calc(25% - 20px)",
                                    "margin-left": "20px",
                                    "margin-bottom": "50px"
                                }
                                }
                            }
                            },
                            "cart": {
                            "contents": {
                                "button": true
                            },
                            "styles": {
                                "footer": {
                                "background-color": "#ffffff"
                                }
                            }
                            },
                            "modalProduct": {
                            "contents": {
                                "img": false,
                                "imgWithCarousel": true,
                                "variantTitle": false,
                                "buttonWithQuantity": true,
                                "button": false,
                                "quantity": false
                            },
                            "styles": {
                                "product": {
                                "@media (min-width: 601px)": {
                                    "max-width": "100%",
                                    "margin-left": "0px",
                                    "margin-bottom": "0px"
                                }
                                }
                            }
                            },
                            "productSet": {
                            "styles": {
                                "products": {
                                "@media (min-width: 601px)": {
                                    "margin-left": "-20px"
                                }
                                }
                            }
                            }
                        }
                                });
                            });
                            }
                        })();
                        /*]]>*/
                    </script>

                    <div id='product-component-e4bc1d66d8c'></div>
                    <script type="text/javascript">
                            /*<![CDATA[*/

                            (function () {
                              var scriptURL = 'https://sdks.shopifycdn.com/buy-button/latest/buy-button-storefront.min.js';
                              if (window.ShopifyBuy) {
                                if (window.ShopifyBuy.UI) {
                                  ShopifyBuyInit();
                                } else {
                                  loadScript();
                                }
                              } else {
                                loadScript();
                              }

                              function loadScript() {
                                var script = document.createElement('script');
                                script.async = true;
                                script.src = scriptURL;
                                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(script);
                                script.onload = ShopifyBuyInit;
                              }

                              function ShopifyBuyInit() {
                                var client = ShopifyBuy.buildClient({
                                  domain: 'mhf-dba-no-shave-november.myshopify.com',
                                  apiKey: '0007cd26e3c16354feca5d50133e707a',
                                  appId: '6',
                                });

                                ShopifyBuy.UI.onReady(client).then(function (ui) {
                                  ui.createComponent('product', {
                                    id: [13129540811],
                                    node: document.getElementById('product-component-e4bc1d66d8c'),
                                    moneyFormat: '%24%7B%7Bamount%7D%7D',
                                    options: {
                              "product": {
                                "variantId": "all",
                                "contents": {
                                  "imgWithCarousel": false,
                                  "variantTitle": false,
                                  "description": false,
                                  "buttonWithQuantity": false,
                                  "quantity": false
                                },
                                "styles": {
                                  "product": {
                                    "@media (min-width: 601px)": {
                                      "max-width": "calc(25% - 20px)",
                                      "margin-left": "20px",
                                      "margin-bottom": "50px"
                                    }
                                  }
                                }
                              },
                              "cart": {
                                "contents": {
                                  "button": true
                                },
                                "styles": {
                                  "footer": {
                                    "background-color": "#ffffff"
                                  }
                                }
                              },
                              "modalProduct": {
                                "contents": {
                                  "img": false,
                                  "imgWithCarousel": true,
                                  "variantTitle": false,
                                  "buttonWithQuantity": true,
                                  "button": false,
                                  "quantity": false
                                },
                                "styles": {
                                  "product": {
                                    "@media (min-width: 601px)": {
                                      "max-width": "100%",
                                      "margin-left": "0px",
                                      "margin-bottom": "0px"
                                    }
                                  }
                                }
                              },
                              "productSet": {
                                "styles": {
                                  "products": {
                                    "@media (min-width: 601px)": {
                                      "margin-left": "-20px"
                                    }
                                  }
                                }
                              }
                            }
                                  });
                                });
                              }
                            })();
                            /*]]>*/
                        </script>    

                   <!--<div class="col-12 col-md-6 card shadow ml-auto">
                    
                       <div class="slide-wrap">
                           <div class="price-tag">$<br>99.9</div>
                           <ul class="product-slide list-inline">
                               <li><figure><img src="<?php echo base_url; ?>/assets/images/t-shirt.png" class="img-fluid" alt=""></figure></li>
                               <li><figure><img src="<?php echo base_url; ?>/assets/images/t-shirt.png" class="img-fluid" alt=""></figure></li>
                               <li><figure><img src="<?php echo base_url; ?>/assets/images/t-shirt.png" class="img-fluid" alt=""></figure></li>
                           </ul>
                       </div>
                   </div>
                   <div class="col-12 col-md-6 col-lg-5 d-flex align-items-center">
                       <div class="filter-wrap">
                            <div class="form-group row">
                                <label  class="col-sm-3 col-form-label">Colour :</label>
                                <div class="col-sm-9">
                                    <select class="form-control">
                                        <option>White fleck triblend</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label  class="col-sm-3 col-form-label">Size :</label>
                                <div class="col-sm-9">
                                    <select class="form-control">
                                        <option>43</option>
                                    </select>
                                </div>
                            </div>
                           <div class="form-group row">
                               <div class="col-sm-9 ml-auto">
                                   <input type="submit" class="btn btn-light w-75" value="Add To cart">
                               </div>
                           </div>
                       </div>
                   </div>-->
               </div>
           </div>
       </section>
       <!--end of shop home-->
       <section class="our-story" id="story" style="background-image:url(<?php echo base_url; ?>/assets/images/blue-bg.jpg);">
           <div class="container">
               <div class="trans-blue">
                   <div class="row">
                       <div class="col-md-4 col-12">
                           <figure>
                               <img src="<?php echo base_url; ?>/assets/images/circle-img.jpg" class="img-fluid" alt="-">
                           </figure>
                       </div>
                       <div class="col-md-8 col-12">
                           <div class="story-cont">
                               <h2>Our Story</h2>
                               <p>No-Shave November has been a tradition for many years, but it wasn’t until the fall of 2009 that members of the
                                   Chicagoland Hill family decided to use it as a means to raise money for charity. 
                                   It was a project that held special meaning to the eight Hill children after their father, 
                                   Matthew Hill, passed away from colon cancer in November 2007.</p>
                               <a href="" data-toggle="modal" data-target="#ourSTORY" class="btn btn-light">Read Full Story</a>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </section>
       <!--end of our story-->
       <section class="press-release" id="press">
           <div class="container">
               <div class="top-heading text-center">
                   <h2 class="mb-2">Press Releases</h2>
                   <p>See who's covered our amazing story, as we continue to work towards our mission.</p>
               </div>
               <div class="brand-wrap">
                   <ul class="list-inline brand-slide">
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand1.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand2.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand3.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand4.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand5.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand6.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand1.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand2.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand3.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand4.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand5.png" class="img-fluid" alt="partner"></figure></li>
                       <li><figure><img src="<?php echo base_url; ?>/assets/images/brand6.png" class="img-fluid" alt="partner"></figure></li>
                   </ul>
               </div>
           </div>
       </section>
       
       <?php include_once('platform/footer.php')?>
  </body>
</html>
