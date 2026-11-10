<?php
include_once __DIR__ . '/../inc/header.php';
?>


<!doctype html>
<html class="no-js" lang="en">

<!-- blog.html  03:25:02 GMT -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Drophut - Single Product eCommerce Template</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <!-- CSS 
    ========================= -->
   

    <!-- Plugins CSS -->
    <link rel="stylesheet" href="assets/css/plugins.css">
    
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">

</head>

<body>

    <!--header area start-->
    <!--Offcanvas menu area start-->
    <div class="off_canvars_overlay">
            
    </div>
    <div class="Offcanvas_menu">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="canvas_open">
                        <a href="javascript:void(0)"><i class="ion-navicon"></i></a>
                    </div>
                    <div class="Offcanvas_menu_wrapper">
                        <div class="canvas_close">
                              <a href="javascript:void(0)"><i class="ion-android-close"></i></a>  
                        </div>
                        <div class="support_info">
                            <p>Any Enquiry: <a href="tel:">+56985475235</a></p>
                        </div>
                        <div class="top_right text-right">
                            <ul>
                               <li><a href="?route=my-account"> My Account </a></li> 
                               <li><a href="?route=checkout"> Checkout </a></li> 
                            </ul>
                        </div> 
                        <div class="search_container">
                           <form action="#">
                                <div class="search_box">
                                    <input placeholder="Search product..." type="text">
                                    <button type="submit">Search</button> 
                                </div>
                            </form>
                        </div> 
                        
                        <div class="middel_right_info">
                            <div class="header_wishlist">
                                <a href="?route=wishlist"><img src="assets/img/user.png" alt=""></a>
                            </div>
                            <div class="mini_cart_wrapper">
                                <a href="javascript:void(0)"><img src="assets/img/shopping-bag.png" alt=""></a>
                                <span class="cart_quantity">2</span>
                                <!--mini cart-->
                                 <div class="mini_cart">
                                    <div class="cart_item">
                                       <div class="cart_img">
                                           <a href="#"><img src="assets/img/s-product/product.jpg" alt=""></a>
                                       </div>
                                        <div class="cart_info">
                                            <a href="#">Sit voluptatem rhoncus sem lectus</a>
                                            <p>Qty: 1 X <span> $60.00 </span></p>    
                                        </div>
                                        <div class="cart_remove">
                                            <a href="#"><i class="ion-android-close"></i></a>
                                        </div>
                                    </div>
                                    <div class="cart_item">
                                       <div class="cart_img">
                                           <a href="#"><img src="assets/img/s-product/product2.jpg" alt=""></a>
                                       </div>
                                        <div class="cart_info">
                                            <a href="#">Natus erro at congue massa commodo</a>
                                            <p>Qty: 1 X <span> $60.00 </span></p>   
                                        </div>
                                        <div class="cart_remove">
                                            <a href="#"><i class="ion-android-close"></i></a>
                                        </div>
                                    </div>
                                    <div class="mini_cart_table">
                                        <div class="cart_total">
                                            <span>Sub total:</span>
                                            <span class="price">$138.00</span>
                                        </div>
                                        <div class="cart_total mt-10">
                                            <span>total:</span>
                                            <span class="price">$138.00</span>
                                        </div>
                                    </div>

                                    <div class="mini_cart_footer">
                                       <div class="cart_button">
                                            <a href="?route=cart">View cart</a>
                                        </div>
                                        <div class="cart_button">
                                            <a href="?route=checkout">Checkout</a>
                                        </div>

                                    </div>

                                </div>
                                <!--mini cart end-->
                            </div>
                        </div>
                        <div id="menu" class="text-left ">
                            <ul class="offcanvas_main_menu">
                                <li class="menu-item-has-children active">
                                    <a href="#">Home</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="?route=product-details">product</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">pages </a>
                                    <ul class="sub-menu">
                                        <li><a href="?route=about">About Us</a></li>
										<li><a href="?route=contact">contact</a></li>
										<li><a href="?route=privacy-policy">privacy policy</a></li>
										<li><a href="?route=faq">Frequently Questions</a></li>
										<li><a href="?route=Login">login</a></li>
										<li><a href="?route=Register">register</a></li>
										<li><a href="?route=forget-password">Forget Password</a></li>
										<li><a href="?route=404">Error 404</a></li>
										<li><a href="?route=cart">cart</a></li>
										<li><a href="?route=tracking">tracking</a></li>
										<li><a href="?route=checkout">checkout</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">blog</a>
                                    <ul class="sub-menu">
                                        <li><a href="?route=blog">blog</a></li>
                                        <li><a href="?route=blog-details">blog details</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="?route=Login">my account</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="?route=contact"> Contact Us</a> 
                                </li>
                            </ul>
                        </div>

                        <div class="Offcanvas_footer">
                            <span><a href="#"><i class="fa fa-envelope-o"></i> info@drophunt.com</a></span>
                            <ul>
                                <li class="facebook"><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li class="twitter"><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li class="pinterest"><a href="#"><i class="fa fa-pinterest-p"></i></a></li>
                                <li class="google-plus"><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                <li class="linkedin"><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Offcanvas menu area end-->
    
    <header>
        <div class="main_header">
            <!--header top start-->
            <div class="header_top">
                <div class="container">  
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-6">
                            <div class="support_info">
                                <p>Email: <a href="mailto:">support@drophunt.com</a></p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="top_right text-right">
                                <ul>
                                   <li><a href="?route=my-account">Account</a></li> 
                                   <li><a href="?route=checkout">Checkout</a></li> 
                                </ul>
                            </div>   
                        </div>
                    </div>
                </div>
            </div>
            <!--header top start-->
            <!--header middel start-->
            <div class="header_middle">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-3 col-md-6">
                            <div class="logo">
                                <a href="?route=home"><img src="assets/img/logo/logo.png" alt=""></a>
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-6">
                            <div class="middel_right">
                                <div class="search_container">
                                   <form action="#">
                                        <div class="search_box">
                                            <input placeholder="Search product..." type="text">
                                            <button type="submit">Search</button> 
                                        </div>
                                    </form>
                                </div>
                                <div class="middel_right_info">
                                    <div class="header_wishlist">
                                        <a href="#"><img src="assets/img/user.png" alt=""></a>
                                    </div>
                                    <div class="mini_cart_wrapper">
                                        <a href="javascript:void(0)"><img src="assets/img/shopping-bag.png" alt=""></a>
                                        <span class="cart_quantity">2</span>
                                        <!--mini cart-->
                                         <div class="mini_cart">
                                            <div class="cart_item">
                                               <div class="cart_img">
                                                   <a href="#"><img src="assets/img/s-product/product.jpg" alt=""></a>
                                               </div>
                                                <div class="cart_info">
                                                    <a href="#">Sit voluptatem rhoncus sem lectus</a>
                                                    <p>Qty: 1 X <span> $60.00 </span></p>    
                                                </div>
                                                <div class="cart_remove">
                                                    <a href="#"><i class="ion-android-close"></i></a>
                                                </div>
                                            </div>
                                            <div class="cart_item">
                                               <div class="cart_img">
                                                   <a href="#"><img src="assets/img/s-product/product2.jpg" alt=""></a>
                                               </div>
                                                <div class="cart_info">
                                                    <a href="#">Natus erro at congue massa commodo</a>
                                                    <p>Qty: 1 X <span> $60.00 </span></p>   
                                                </div>
                                                <div class="cart_remove">
                                                    <a href="#"><i class="ion-android-close"></i></a>
                                                </div>
                                            </div>
                                            <div class="mini_cart_table">
                                                <div class="cart_total">
                                                    <span>Sub total:</span>
                                                    <span class="price">$138.00</span>
                                                </div>
                                                <div class="cart_total mt-10">
                                                    <span>total:</span>
                                                    <span class="price">$138.00</span>
                                                </div>
                                            </div>

                                            <div class="mini_cart_footer">
                                               <div class="cart_button">
                                                    <a href="?route=cart">View cart</a>
                                                </div>
                                                <div class="cart_button">
                                                    <a href="?route=checkout">Checkout</a>
                                                </div>

                                            </div>

                                        </div>
                                        <!--mini cart end-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--header middel end-->
            <!--header bottom satrt-->
            <div class="main_menu_area">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-12 col-md-12">
                            <div class="main_menu menu_position"> 
                                <nav>  
                                    <ul>
                                        <li><a href="?route=home">home</a></li>
                                        <li><a href="?route=product-details">Product</a></li>
                                        
                                        <li><a class="active" href="#">pages <i class="fa fa-angle-down"></i></a>
                                            <ul class="sub_menu pages">
                                                <li><a href="?route=about">About Us</a></li>
                                                <li><a href="?route=contact">contact</a></li>
                                                <li><a href="?route=privacy-policy">privacy policy</a></li>
                                                <li><a href="?route=faq">Frequently Questions</a></li>
                                                <li><a href="?route=Login">login</a></li>
                                                <li><a href="?route=Register">register</a></li>
                                                <li><a href="?route=forget-password">Forget Password</a></li>
                                                <li><a href="?route=404">Error 404</a></li>
                                                <li><a href="?route=cart">cart</a></li>
                                                <li><a href="?route=tracking">tracking</a></li>
                                                <li><a href="?route=checkout">checkout</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="?route=blog">blog<i class="fa fa-angle-down"></i></a>
                                            <ul class="sub_menu pages">
                                                <li><a href="?route=blog">blog</a></li>
                                                <li><a href="?route=blog-details">blog details</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="?route=contact"> Contact Us</a></li>
                                    </ul>  
                                </nav> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--header bottom end-->
        </div> 
    </header>
    <!--header area end-->

    <!--breadcrumbs area start-->
    <div class="breadcrumbs_area">
        <div class="container">   
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <ul>
                            <li><a href="?route=home">home</a></li>
                            <li>All blog</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>         
    </div>
    <!--breadcrumbs area end-->
	
	
	<!--blog body area start-->
    <div class="blog_details mt-60">
        <div class="container">
            <div class="row">
                
                <div class="col-lg-9">
                    <div class="blog_wrapper">
                        <div class="section-title">
                            <h2>All Blog</h2>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <article class="single_blog mb-60">
                                    <figure>
                                        <div class="blog_thumb">
                                            <a href="?route=blog-details"><img src="assets/img/blog/blog1.jpg" alt=""></a>
                                        </div>
                                        <figcaption class="blog_content">
                                            <h3><a href="?route=blog-details">bibendum massa nec, fermentum odio</a></h3>
                                            <div class="blog_meta">                                        
                                                <span class="author">Posted by : <a href="#">admin</a> / </span>
                                                <span class="post_date">On : <a href="#">April 10, 2019</a></span>
                                            </div>
                                            <div class="blog_desc">
                                                <p>Donec vitae hendrerit arcu, sit amet faucibus nisl. Cras pretium arcu ex. Aenean posuere libero eu augue condimentum rhoncus. Praesent ornare tortor ac ante egestas hendrerit. Aliquam et metus pharetra, bibendum massa nec, fermentum odio. </p>
                                            </div>
                                            <footer class="readmore_button">
                                                <a href="?route=blog-details">read more</a>
                                            </footer>
                                        </figcaption>
                                    </figure>
                                </article>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <article class="single_blog mb-60">
                                    <figure>
                                        <div class="blog_thumb">
                                            <a href="?route=blog-details"><img src="assets/img/blog/blog2.jpg" alt=""></a>
                                        </div>
                                        <figcaption class="blog_content">
                                            <h3><a href="?route=blog-details">Aenean posuere libero eu augue</a></h3>
                                            <div class="blog_meta">                                        
                                                <span class="author">Posted by : <a href="#">admin</a> / </span>
                                                <span class="post_date">On : <a href="#">April 10, 2019</a></span>
                                            </div>
                                            <div class="blog_desc">
                                                <p>Donec vitae hendrerit arcu, sit amet faucibus nisl. Cras pretium arcu ex. Aenean posuere libero eu augue condimentum rhoncus. Praesent ornare tortor ac ante egestas hendrerit. Aliquam et metus pharetra, bibendum massa nec, fermentum odio. </p>
                                            </div>
                                            <footer class="readmore_button">
                                                <a href="?route=blog-details">read more</a>
                                            </footer>
                                        </figcaption>
                                    </figure>
                                </article>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <article class="single_blog">
                                    <figure>
                                        <div class="blog_thumb">
                                            <a href="?route=blog-details"><img src="assets/img/blog/blog2.jpg" alt=""></a>
                                        </div>
                                        <figcaption class="blog_content">
                                            <h3><a href="?route=blog-details">Donec vitae hendrerit arcu, sit amet</a></h3>
                                            <div class="blog_meta">                                        
                                                <span class="author">Posted by : <a href="#">admin</a> / </span>
                                                <span class="post_date">On : <a href="#">April 10, 2019</a></span>
                                            </div>
                                            <div class="blog_desc">
                                                <p>Donec vitae hendrerit arcu, sit amet faucibus nisl. Cras pretium arcu ex. Aenean posuere libero eu augue condimentum rhoncus. Praesent ornare tortor ac ante egestas hendrerit. Aliquam et metus pharetra, bibendum massa nec, fermentum odio. Cras et vehicula orci. Curabitur aliquet ullamcorper suscipit. Aliquam erat volutpat. Cras convallis libero mi, sit amet scelerisque elit suscipit id. </p>
                                            </div>
                                            <footer class="readmore_button">
                                                <a href="?route=blog-details">read more</a>
                                            </footer>
                                        </figcaption>
                                    </figure>
                                </article>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <article class="single_blog">
                                    <figure>
                                        <div class="blog_thumb">
                                            <a href="?route=blog-details"><img src="assets/img/blog/blog1.jpg" alt=""></a>
                                        </div>
                                        <figcaption class="blog_content">
                                            <h3><a href="?route=blog-details">Cras pretium arcu ex. Aenean</a></h3>
                                            <div class="blog_meta">                                        
                                                <span class="author">Posted by : <a href="#">admin</a> / </span>
                                                <span class="post_date">On : <a href="#">April 10, 2019</a></span>
                                            </div>
                                            <div class="blog_desc">
                                                <p>Donec vitae hendrerit arcu, sit amet faucibus nisl. Cras pretium arcu ex. Aenean posuere libero eu augue condimentum rhoncus. Praesent ornare tortor ac ante egestas hendrerit. Aliquam et metus pharetra, bibendum massa nec, fermentum odio. Cras et vehicula orci. Curabitur aliquet ullamcorper suscipit. Aliquam erat volutpat. Cras convallis libero mi, sit amet scelerisque elit suscipit id. </p>
                                            </div>
                                            <footer class="readmore_button">
                                                <a href="?route=blog-details">read more</a>
                                            </footer>
                                        </figcaption>
                                    </figure>
                                </article>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="col-lg-3 col-md-12">
                    <div class="blog_sidebar_widget">
                        <div class="widget_list widget_search">
                            <h3>Search</h3>
                            <form action="#">
                                <input placeholder="Search..." type="text">
                                <button type="submit">search</button>
                            </form>
                        </div>
                        <div class="widget_list widget_post">
                            <h3>Recent Posts</h3>
                            <div class="post_wrapper">
                                <div class="post_thumb">
                                    <a href="?route=blog-details"><img src="assets/img/blog/blog1.jpg" alt=""></a>
                                </div>
                                <div class="post_info">
                                    <h3><a href="?route=blog-details">Blog image post</a></h3>
                                    <span>March 16, 2018 </span>
                                </div>
                            </div>
                             <div class="post_wrapper">
                                <div class="post_thumb">
                                    <a href="?route=blog-details"><img src="assets/img/blog/blog2.jpg" alt=""></a>
                                </div>
                                <div class="post_info">
                                    <h3><a href="?route=blog-details">Post with Gallery</a></h3>
                                    <span>March 16, 2018 </span>
                                </div>
                            </div>
                             <div class="post_wrapper">
                                <div class="post_thumb">
                                    <a href="?route=blog-details"><img src="assets/img/blog/blog1.jpg" alt=""></a>
                                </div>
                                <div class="post_info">
                                    <h3><a href="?route=blog-details">Post with Audio</a></h3>
                                    <span>March 16, 2018 </span>
                                </div>
                            </div>
                        </div>
                        <div class="widget_list widget_tag">
                            <h3>Tag products</h3>
                            <div class="tag_widget">
                                <ul>
                                    <li><a href="#">Drone</a></li>
                                    <li><a href="#">Sky</a></li>
                                    <li><a href="#">Fly</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--blog section area end-->
	
	    
    <!--blog pagination area start-->
    <div class="blog_pagination">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="pagination">
                        <ul>
                            <li class="current">1</li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li class="next"><a href="#">next</a></li>
                            <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--blog pagination area end-->
	
	
    <!--footer area start-->
    <footer class="footer_widgets">
        <div class="footer_top">
            <div class="container">
                <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="widgets_container contact_us">
                        <div class="footer_logo">
                            <a href="#"><img src="assets/img/logo/logo.png" alt=""></a>
                        </div>
                        <div class="footer_contact">
                            <p>John draw real poor on call my from. May she mrs furnished discourse extremely. Ask doubt noisy shade guest Lose away off why half led have near bed. At engage simple father of period others except</p>
                            <p>Ask doubt noisy shade guest Lose away off why half led have near bed. At engage simple father of period others except</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="widgets_container widget_menu">
                        <h3>Information</h3>
                        <div class="footer_menu">
                            <ul>
                                <li><a href="?route=about">About Us</a></li>
                                <li><a href="?route=blog">Delivery Information</a></li>
                                <li><a href="?route=contact">Privacy Policy</a></li>
                                <li><a href="?route=services">Terms & Conditions</a></li>
                                <li><a href="#">Returns</a></li>
                                <li><a href="#">Gift Certificates</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="widgets_container widget_menu">
                        <h3>My Account</h3>
                        <div class="footer_menu">
                            <ul>
                                <li><a href="#">My Account</a></li>
                                <li><a href="#">Order History</a></li>
                                <li><a href="?route=wishlist">Wish List</a></li>
                                <li><a href="#">Newsletter</a></li>
                                <li><a href="#">Affiliate</a></li>
                                <li><a href="?route=faq">International Orders</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="widgets_container newsletter">
                        <h3>Follow Us</h3>
                        <div class="footer_social_link">
                            <ul>
                                <li><a class="facebook" href="#" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                                <li><a class="twitter" href="#" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                                <li><a class="instagram" href="#" title="instagram"><i class="fa fa-instagram"></i></a></li>
                                <li><a class="linkedin" href="#" title="linkedin"><i class="fa fa-linkedin"></i></a></li>
                                <li><a class="rss" href="#" title="rss"><i class="fa fa-rss"></i></a></li>
                            </ul>
                        </div>
                        <div class="subscribe_form">
                            <h3>Join Our Newsletter Now</h3>
                            <form id="mc-form" class="mc-form footer-newsletter" >
                                <input id="mc-email" type="email" autocomplete="off" placeholder="Your email address..." />
                                <button id="mc-submit">Subscribe!</button>
                            </form>
                            <!-- mailchimp-alerts Start -->
                            <div class="mailchimp-alerts text-centre">
                                <div class="mailchimp-submitting"></div><!-- mailchimp-submitting end -->
                                <div class="mailchimp-success"></div><!-- mailchimp-success end -->
                                <div class="mailchimp-error"></div><!-- mailchimp-error end -->
                            </div><!-- mailchimp-alerts end -->
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <div class="footer_bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6">
                        <div class="copyright_area">
                            <p> <a href="templateshub.net">Templates Hub</a></p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="footer_payment text-right">
                            <a href="#"><img src="assets/img/icon/payment.png" alt=""></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </footer>
    <!--footer area end-->


<!-- JS
============================================ -->



<!-- Plugins JS -->
<script src="assets/js/plugins.js"></script>

<!-- Main JS -->
<script src="assets/js/main.js"></script>



</body>

<!-- blog.html  03:25:02 GMT -->
</html>