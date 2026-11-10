<!doctype html>
<html class="no-js" lang="en">

<!--   03:20:39 GMT -->
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
    <link rel="stylesheet" href="./assets/css/plugins.css">
    
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css">

</head>

<body>
    <div id="n8n-chat"></div>
    <script type="module">
        import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';

        createChat({
            webhookUrl: 'https://drone-store.app.n8n.cloud/webhook/99663231-e386-46ff-8e28-0fa8b4f8a1ee/chat',
            target: '#n8n-chat',
            mode: 'window',
            showWelcomeScreen: false,
            initialMessages: ['Hi! 👋 Ask me anything about our drones.'],
            i18n: {
                en: {
                    title: 'Drone Store',
                    subtitle: 'We are here to help',
                    footer: '',
                    getStarted: 'New Conversation',
                    inputPlaceholder: 'Type your question...'
                }
            }
        });
    </script>

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
                                <a href="?route=cart"><img src="assets/img/shopping-bag.png" alt="Cart"></a>
                                <span class="cart_quantity"><?= cartItemCount() ?></span>
                                <!--mini cart-->
                                 <div class="mini_cart">
                                    <p><?= cartItemCount() ?> item(s) in your cart.</p>
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
                                    <a href="?route=home">Home</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="?route=products">Products</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">pages </a>
                                    <ul class="sub-menu">
                                        <li><a href="?route=about">About Us</a></li>
										<li><a href="?route=contact">contact</a></li>
										<li><a href="?route=privacy-policy">privacy policy</a></li>
										<li><a href="?route=faq">Frequently Questions</a></li>
										<?php if (isset($_SESSION['user_id'])): ?>
                                            <li><a href="?route=my-account">My account</a></li>
                                            <li><form action="?route=Logout" method="post"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>"><button type="submit">Logout (<?= escapeHtml($_SESSION['username']) ?>)</button></form></li>
                                        <?php else: ?><li><a href="?route=Login">login</a></li><?php endif; ?>
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
                                    <a href="?route=my-account">my account</a>
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
                                        <span class="cart_quantity"><?= cartItemCount() ?></span>
                                        <!--mini cart-->
                                         <div class="mini_cart">
                                            <p><?= cartItemCount() ?> item(s) in your cart.</p>
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
                                        <li><a href="?route=products">Products</a></li>
                                        
                                        <li><a class="active" href="#">pages <i class="fa fa-angle-down"></i></a>
                                            <ul class="sub_menu pages">
                                                <li><a href="?route=about">About Us</a></li>
                                                <li><a href="?route=contact">contact</a></li>
                                                <li><a href="?route=privacy-policy">privacy policy</a></li>
                                                <li><a href="?route=faq">Frequently Questions</a></li>
                                                <?php if (isset($_SESSION['user_id'])): ?>
                                                    <li><a href="?route=my-account">My account</a></li>
                                                    <li><form action="?route=Logout" method="post"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>"><button type="submit">Logout (<?= escapeHtml($_SESSION['username']) ?>)</button></form></li>
                                                <?php else: ?><li><a href="?route=Login">login</a></li><?php endif; ?>
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