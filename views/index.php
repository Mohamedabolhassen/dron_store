<?php
$homeProductsResult = productAll($con);
$homeProducts = [];
$homeProductsError = !$homeProductsResult;

if ($homeProductsResult) {
    while ($homeProduct = mysqli_fetch_assoc($homeProductsResult)) {
        $homeProducts[] = $homeProduct;
    }
}

include_once __DIR__ . '/../inc/header.php';
?>


    <!--slider area start-->
    <section class="slider_section d-flex align-items-center" data-bgimg="assets/img/slider/slider3.jpg">
        <div class="slider_area owl-carousel">
            <div class="single_slider d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-6 col-md-6">
                            <div class="slider_content">
                                <h1>Next level of Drone</h1>
                                <h2>Insane Quality for use</h2>
                                <p>Special offer <span> 20% off </span> this week</p>
                                <a class="button" href="?route=products">Buy now</a>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="slider_content">
                                <img src="assets/img/product/1.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="single_slider d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-6 col-md-6">
                            <div class="slider_content">
                                <h1>Best Camera Included</h1>
                                <h2>100% Flexible</h2>
                                <p>exclusive offer <span> 20% off </span> this week</p>
                                <a class="button" href="?route=products">Shop now</a>
                            </div>
                        </div>                        
                        <div class="col-xl-6 col-md-6">
                            <div class="slider_content">
                                <img src="assets/img/product/2.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="single_slider d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-6 col-md-6">
                            <div class="slider_content">
                                <h1>With some gifts</h1>
                                <h2>Special one for you</h2>
                                <p>exclusive offer <span> 20% off </span> this week</p>
                                <a class="button" href="?route=products">shopping now</a>
                            </div>
                        </div>                      
                        <div class="col-xl-6 col-md-6">
                            <div class="slider_content">
                                <img src="assets/img/product/3.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--slider area end-->

    <!--Tranding product-->
    <section class="pt-60 pb-30 gray-bg">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <div class="section-title">
                        <h2>Tranding Products</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <?php if ($homeProductsError): ?>
                    <p>Products are temporarily unavailable. Please try again later.</p>
                <?php elseif (!$homeProducts): ?>
                    <p>No products are available right now.</p>
                <?php else: ?>
                    <?php foreach ($homeProducts as $product): ?>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                            <div class="single-tranding">
                                <a href="?route=product-details&amp;id=<?= (int) $product['id'] ?>">
                                    <div class="tranding-pro-img">
                                        <img src="<?= escapeHtml(productImageUrl($product['image_url'])) ?>"
                                             alt="<?= escapeHtml($product['name']) ?>">
                                    </div>
                                    <div class="tranding-pro-title">
                                        <h3><?= escapeHtml($product['name']) ?></h3>
                                        <h4><?= escapeHtml($product['product_category'] ?: 'Drone') ?></h4>
                                    </div>
                                    <div class="tranding-pro-price">
                                        <div class="price_box">
                                            <span class="current_price">$<?= number_format((float) $product['price'], 2) ?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section><!--Tranding product-->

    <!--Features area-->
    <section class="features-area pt-60 pb-60">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <div class="section-title">
                        <h2>Awesome Features</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                    <div class="single-features">
                        <img src="assets/img/icon/1.png" alt="">
                        <h3>Impressive Distance</h3>
                        <p>consectetur adipisicing elit. Ut praesentium earum, blanditiis, voluptatem repellendus rerum voluptatibus dignissimos</p>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                    <div class="single-features">
                        <img src="assets/img/icon/2.png" alt="">
                        <h3>100% self safe</h3>
                        <p>consectetur adipisicing elit. Ut praesentium earum, blanditiis, voluptatem repellendus rerum voluptatibus dignissimos</p>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                    <div class="single-features">
                        <img src="assets/img/icon/3.png" alt="">
                        <h3>Awesome Support</h3>
                        <p>consectetur adipisicing elit. Ut praesentium earum, blanditiis, voluptatem repellendus rerum voluptatibus dignissimos</p>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
                    <a href="#"><img src="assets/img/product/2.png" alt=""></a>
                </div>
            </div>
        </div>
    </section><!--Features area-->

  
    
    
                      


    
    <!--Other product-->
    <section class="pt-60 pb-30">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <div class="section-title">
                        <h2>Other collections</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <?php if ($homeProductsError): ?>
                    <p>Products are temporarily unavailable. Please try again later.</p>
                <?php elseif (!$homeProducts): ?>
                    <p>No products are available right now.</p>
                <?php else: ?>
                    <?php foreach ($homeProducts as $product): ?>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                            <div class="single-tranding mb-30">
                                <a href="?route=product-details&amp;id=<?= (int) $product['id'] ?>">
                                    <div class="tranding-pro-img">
                                        <img src="<?= escapeHtml(productImageUrl($product['image_url'])) ?>"
                                             alt="<?= escapeHtml($product['name']) ?>">
                                    </div>
                                    <div class="tranding-pro-title">
                                        <h3><?= escapeHtml($product['name']) ?></h3>
                                        <h4><?= escapeHtml($product['product_category'] ?: 'Drone') ?></h4>
                                    </div>
                                    <div class="tranding-pro-price">
                                        <div class="price_box">
                                            <span class="current_price">$<?= number_format((float) $product['price'], 2) ?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section><!--Other product-->

    <!--Testimonials-->
    <section class="pb-60 pt-60 gray-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="testimonial_are">
                        <div class="testimonial_active owl-carousel">       
                            <article class="single_testimonial">
                                <figure>
                                    <div class="testimonial_thumb">
                                        <a href="#"><img src="assets/img/about/team-3.jpg" alt=""></a>
                                    </div>
                                    <figcaption class="testimonial_content">
                                        <p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45</p>
                                        <h3><a href="#">Kathy Young</a><span> - CEO of SunPark</span></h3>
                                    </figcaption>
                                    
                                </figure>
                            </article> 
                            <article class="single_testimonial">
                                <figure>
                                    <div class="testimonial_thumb">
                                        <a href="#"><img src="assets/img/about/team-1.jpg" alt=""></a>
                                    </div>
                                    <figcaption class="testimonial_content">
                                        <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even</p>
                                        <h3><a href="#">John Sullivan</a><span> - Customer</span></h3>
                                    </figcaption>
                                    
                                </figure>
                            </article> 
                            <article class="single_testimonial">
                                <figure>
                                    <div class="testimonial_thumb">
                                        <a href="#"><img src="assets/img/about/team-2.jpg" alt=""></a>
                                    </div>
                                    <figcaption class="testimonial_content">
                                        <p>College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites</p>
                                        <h3><a href="#">Jenifer Brown</a><span> - Manager of AZ</span></h3>
                                    </figcaption>
                                    
                                </figure>
                            </article>      
                        </div>   
                    </div>
                </div>
            </div>
        </div>
    </section><!--/Testimonials-->

    <!--Blog-->
    <section class="pt-60">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <div class="section-title">
                        <h2>Blog Posts</h2>
                    </div>
                </div>
            </div>
            <div class="row blog_wrapper">
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                    <article class="single_blog mb-60">
                        <figure>
                            <div class="blog_thumb">
                                <a href="?route=blog-details"><img src="assets/img/blog/blog2.jpg" alt=""></a>
                            </div>
                            <figcaption class="blog_content">
                                <h3><a href="?route=blog-details">How to start drone</a></h3>
                                <div class="blog_meta">                                        
                                    <span class="author">Posted by : <a href="#">Rahul</a> / </span>
                                    <span class="post_date"><a href="#">Sep 20, 2019</a></span>
                                </div>
                                <div class="blog_desc">
                                    <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less</p>
                                </div>
                                <footer class="readmore_button">
                                    <a href="?route=blog-details">read more</a>
                                </footer>
                            </figcaption>
                        </figure>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                    <article class="single_blog blog_bidio mb-60">
                        <figure>
                            <div class="blog_thumb">
                                <a href="?route=blog-details"><img src="assets/img/blog/blog1.jpg" alt=""></a>
                            </div>
                            <figcaption class="blog_content">
                                <h3><a href="?route=blog-details">See the tutorial</a></h3>
                                <div class="blog_meta">                                        
                                    <span class="author">Posted by : <a href="#">Rahul</a> / </span>
                                    <span class="post_date">On : <a href="#">Aug 25, 2019</a></span>
                                </div>
                                <div class="blog_desc">
                                    <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less</p>
                                </div>
                                <footer class="readmore_button">
                                    <a href="?route=blog-details">read more</a>
                                </footer>
                            </figcaption>
                        </figure>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                    <article class="single_blog mb-60">
                        <figure>
                            <div class="blog_thumb">
                                <a href="?route=blog-details"><img src="assets/img/blog/blog-details.jpg" alt=""></a>
                            </div>
                            <figcaption class="blog_content">
                                <h3><a href="?route=blog-details">How to start drone</a></h3>
                                <div class="blog_meta">                                        
                                    <span class="author">Posted by : <a href="#">Rahul</a> / </span>
                                    <span class="post_date"><a href="#">Sep 20, 2019</a></span>
                                </div>
                                <div class="blog_desc">
                                    <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less</p>
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
    </section><!--/Blog-->

    <!--shipping area start-->
    <section class="shipping_area">
        <div class="container">
            <div class=" row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <div class="single_shipping">
                        <div class="shipping_icone">
                            <img src="assets/img/about/shipping1.png" alt="">
                        </div>
                        <div class="shipping_content">
                            <h2>Free Shipping</h2>
                            <p>Free shipping on all US order</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <div class="single_shipping">
                        <div class="shipping_icone">
                            <img src="assets/img/about/shipping2.png" alt="">
                        </div>
                        <div class="shipping_content">
                            <h2>Support 24/7</h2>
                            <p>Contact us 24 hours a day</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <div class="single_shipping">
                        <div class="shipping_icone">
                            <img src="assets/img/about/shipping3.png" alt="">
                        </div>
                        <div class="shipping_content">
                            <h2>100% Money Back</h2>
                            <p>You have 30 days to Return</p>
                        </div>
                    </div>
                </div> 
                <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                    <div class="single_shipping">
                        <div class="shipping_icone">
                            <img src="assets/img/about/shipping4.png" alt="">
                        </div>
                        <div class="shipping_content">
                            <h2>Payment Secure</h2>
                            <p>We ensure secure payment</p>
                        </div>
                    </div>
                </div>                          
            </div>
        </div>
    </section>
    <!--shipping area end-->
	
	
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

<!--   03:22:07 GMT -->
</html>