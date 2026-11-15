<?php
$homeProductsResult = productAll($con);
$homeProducts = [];
$homeProductsError = !$homeProductsResult;

if ($homeProductsResult) {
    while ($homeProduct = mysqli_fetch_assoc($homeProductsResult)) {
        $homeProducts[] = $homeProduct;
    }
}
$homeProducts = array_slice($homeProducts, 0, 6);

include_once __DIR__ . '/../inc/header.php';
?>


    <section class="store-hero">
        <div class="container">
            <div class="store-hero-layout">
                <div class="store-hero-copy">
                    <p class="store-eyebrow">FOR THE LOVE OF FLIGHT</p>
                    <h1>Your next view changes <span>everything.</span></h1>
                    <p class="store-hero-description">
                        Discover camera drones, FPV flyers, and the gear to see the world from a new angle.
                    </p>
                    <div class="store-hero-actions">
                        <a class="store-button store-button-primary" href="?route=products">Explore the collection <i class="ion-arrow-right-c"></i></a>
                        <a class="store-button store-button-secondary" href="?route=about">Our store</a>
                    </div>
                    <div class="store-hero-note">
                        <i class="ion-ios-checkmark-circle-outline" aria-hidden="true"></i>
                        Browse available products. Pay cash on delivery.
                    </div>
                </div>
                <div class="store-hero-visual">
                    <video id="store-hero-video" class="store-hero-video" muted loop playsinline webkit-playsinline
                           preload="auto" controls poster="assets/img/product/store-camera-drone.webp"
                           aria-label="Drone flying above the water">
                        <source src="assets/img/product/vido-background-slider.mp4" type="video/mp4">
                    </video>
                    <p class="store-hero-video-status" role="status" aria-live="polite" hidden></p>
                    <div class="store-hero-photo-caption">
                        <span>TAKE THE SCENIC ROUTE</span>
                        <span>01 / EXPLORE</span>
                    </div>
                    <span class="store-hero-orbit" aria-hidden="true"></span>
                </div>
            </div>
        </div>
    </section>
    <script>
        (function () {
            var heroVideo = document.getElementById('store-hero-video');
            if (!heroVideo) return;

            var visual = heroVideo.parentNode;
            var status = visual.querySelector('.store-hero-video-status');
            var motionPreference = window.matchMedia
                ? window.matchMedia('(prefers-reduced-motion: reduce)')
                : null;

            function setControlsVisible(visible) {
                heroVideo.controls = visible;
                if (visual.classList) {
                    if (visible) {
                        visual.classList.add('has-video-controls');
                    } else {
                        visual.classList.remove('has-video-controls');
                    }
                }
            }

            function showFallback(message) {
                setControlsVisible(true);
                status.textContent = message;
                status.hidden = false;
            }

            function syncHeroVideo() {
                if (motionPreference && motionPreference.matches) {
                    heroVideo.pause();
                    showFallback('Video is paused for your reduced-motion preference. Use the video controls to play it.');
                    return;
                }

                if (typeof heroVideo.play !== 'function') {
                    showFallback('This browser cannot play the video. The preview image is shown instead.');
                    return;
                }

                heroVideo.muted = true;
                try {
                    var playback = heroVideo.play();
                    if (playback && typeof playback.then === 'function') {
                        playback.then(function () {
                            setControlsVisible(false);
                            status.hidden = true;
                        }, function () {
                            if (heroVideo.error) {
                                showFallback('The video could not load. The preview image is shown instead.');
                            } else {
                                showFallback('Autoplay is blocked. Use the video controls to play it.');
                            }
                        });
                    }
                } catch (error) {
                    showFallback('Autoplay is unavailable. Use the video controls to play it.');
                }
            }

            heroVideo.addEventListener('playing', function () {
                status.hidden = true;
            });
            heroVideo.addEventListener('error', function () {
                showFallback('The video could not load. The preview image is shown instead.');
            });

            if (motionPreference) {
                if (motionPreference.addEventListener) {
                    motionPreference.addEventListener('change', syncHeroVideo);
                } else if (motionPreference.addListener) {
                    motionPreference.addListener(syncHeroVideo);
                }
            }

            syncHeroVideo();
        })();
    </script>

    <section class="store-catalog-section">
        <div class="container">
            <div class="store-section-heading">
                <div>
                    <p class="store-eyebrow">THE CURRENT LINEUP</p>
                    <h2>Find your next drone.</h2>
                    <p>Explore the latest products in the store, with current prices and stock.</p>
                </div>
                <a class="store-text-link" href="?route=products">Shop all products <i class="ion-arrow-right-c"></i></a>
            </div>
            <div class="row">
                <?php if ($homeProductsError): ?>
                    <div class="col-12"><p class="alert alert-warning">Products are temporarily unavailable. Please try again later.</p></div>
                <?php elseif (!$homeProducts): ?>
                    <div class="col-12">
                        <div class="store-empty-state">
                            <img src="assets/img/product/drone-flight-sunset.webp" alt="" loading="lazy">
                            <h3>The runway is clear.</h3>
                            <p>Our next products will appear here as soon as they're added to the catalog.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($homeProducts as $product): ?>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                            <article class="store-product-card">
                                <a href="?route=product-details&amp;id=<?= (int) $product['id'] ?>">
                                    <div class="store-product-photo">
                                        <img src="<?= escapeHtml(productImageUrl($product['image_url'])) ?>"
                                             alt="<?= escapeHtml($product['name']) ?>"
                                             loading="lazy" decoding="async" width="640" height="480">
                                        <?php if ((int) $product['stock'] < 1): ?>
                                            <span class="store-stock-badge store-stock-empty">Out of stock</span>
                                        <?php else: ?>
                                            <span class="store-stock-badge"><?= (int) $product['stock'] ?> in stock</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="store-product-info">
                                        <h3><?= escapeHtml($product['name']) ?></h3>
                                        <span class="store-product-category"><?= escapeHtml($product['product_category'] ?: 'Flight gear') ?></span>
                                        <div class="store-product-bottom">
                                            <span class="store-product-price">$<?= number_format((float) $product['price'], 2) ?></span>
                                            <span class="store-product-link">View product <i class="ion-arrow-right-c"></i></span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php if ($homeProducts && !$homeProductsError): ?>
                <div class="store-catalog-footer"><a class="store-button store-button-dark" href="?route=products">Discover all products <i class="ion-arrow-right-c"></i></a></div>
            <?php endif; ?>
        </div>
    </section>

    <section class="store-flight-section">
        <div class="container">
            <div class="store-flight-card">
                <img src="assets/img/product/drone-flight-sunset.webp"
                     alt="Drone flying above the water at sunset"
                     loading="lazy" width="1800" height="1298">
                <div class="store-flight-copy">
                    <p class="store-eyebrow">YOUR NEXT ADVENTURE STARTS HERE</p>
                    <h2>There's more out there.</h2>
                    <p>Compare the drones and accessories in our live catalog. Check availability, choose your gear, and place a cash-on-delivery order when you're ready.</p>
                    <a class="store-button store-button-primary" href="?route=products">Browse the catalog <i class="ion-arrow-right-c"></i></a>
                </div>
            </div>
        </div>
    </section>

    <section class="store-promise-section">
        <div class="container">
            <div class="store-promise-grid">
                <article class="store-promise">
                    <span class="store-promise-icon"><i class="ion-ios-pricetags-outline" aria-hidden="true"></i></span>
                    <div>
                        <h3>Clear product details</h3>
                        <p>Review the product description and current price before adding an item to your cart.</p>
                    </div>
                </article>
                <article class="store-promise">
                    <span class="store-promise-icon"><i class="ion-ios-box-outline" aria-hidden="true"></i></span>
                    <div>
                        <h3>Stock checked at checkout</h3>
                        <p>Available quantities are checked again before your order is placed.</p>
                    </div>
                </article>
                <article class="store-promise">
                    <span class="store-promise-icon"><i class="ion-ios-location-outline" aria-hidden="true"></i></span>
                    <div>
                        <h3>Follow your order</h3>
                        <p>Sign in to see order details and the latest delivery status updates.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <?php include_once __DIR__ . '/../inc/footer.php'; ?>