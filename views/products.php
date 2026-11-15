<?php
$catalogSearch = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
$catalogCategory = isset($_GET['category']) && is_string($_GET['category']) ? trim($_GET['category']) : '';
$catalogSort = isset($_GET['sort']) && is_string($_GET['sort']) ? $_GET['sort'] : 'newest';
$catalogPage = isset($_GET['page']) && is_scalar($_GET['page']) ? (int) $_GET['page'] : 1;
$catalog = productCatalogSearch(
    $con,
    $catalogSearch,
    $catalogCategory,
    $catalogSort,
    $catalogPage
);
$productsLoadError = $catalog === null;
include_once __DIR__ . '/../inc/header.php';
?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row"><div class="col-12"><div class="breadcrumb_content">
            <ul><li><a href="?route=home">Home</a></li><li>Products</li></ul>
        </div></div></div>
    </div>
</div>

<section class="pt-60 pb-60">
    <div class="container">
        <div class="row"><div class="col-12">
            <div class="section-title text-center"><h2>Our Products</h2></div>
            <?php if (!empty($_SESSION['error'])): ?>
                <p class="alert alert-danger"><?= escapeHtml($_SESSION['error']) ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['success'])): ?>
                <p class="alert alert-success"><?= escapeHtml($_SESSION['success']) ?></p>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if ($productsLoadError): ?>
                <p>Products are temporarily unavailable. Please try again later.</p>
            <?php else: ?>
                <?php
                $catalogQuery = [
                    'route' => 'products',
                    'q' => $catalogSearch,
                    'category' => $catalogCategory,
                    'sort' => $catalogSort,
                ];
                $catalogUrl = static function ($page) use ($catalogQuery) {
                    $query = $catalogQuery;
                    $query['page'] = $page;
                    return '?' . http_build_query($query);
                };
                ?>
                <form method="get" action="index.php" class="row g-2 align-items-end mb-4">
                    <input type="hidden" name="route" value="products">
                    <div class="col-md-5">
                        <label for="catalog-search" class="form-label">Search products</label>
                        <input id="catalog-search" type="search" name="q" class="form-control"
                               value="<?= escapeHtml($catalogSearch) ?>" maxlength="100"
                               placeholder="Search by name, category, or description">
                    </div>
                    <div class="col-md-3">
                        <label for="catalog-category" class="form-label">Category</label>
                        <select id="catalog-category" name="category" class="form-control">
                            <option value="">All categories</option>
                            <?php foreach ($catalog['categories'] as $category): ?>
                                <option value="<?= escapeHtml($category) ?>" <?= $catalogCategory === $category ? 'selected' : '' ?>>
                                    <?= escapeHtml($category) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="catalog-sort" class="form-label">Sort by</label>
                        <select id="catalog-sort" name="sort" class="form-control">
                            <option value="newest" <?= $catalogSort === 'newest' ? 'selected' : '' ?>>Newest</option>
                            <option value="price_asc" <?= $catalogSort === 'price_asc' ? 'selected' : '' ?>>Price: low to high</option>
                            <option value="price_desc" <?= $catalogSort === 'price_desc' ? 'selected' : '' ?>>Price: high to low</option>
                            <option value="name_asc" <?= $catalogSort === 'name_asc' ? 'selected' : '' ?>>Name: A to Z</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button class="button" type="submit">Apply</button>
                        <a class="button" href="?route=products">Reset</a>
                    </div>
                </form>

                <?php if (!$catalog['total']): ?>
                    <p>No products match those filters. Try a different search or category.</p>
                <?php else: ?>
                    <?php
                    $visibleFirstPage = max(1, $catalog['page'] - 2);
                    $visibleLastPage = min($catalog['pages'], $catalog['page'] + 2);
                    ?>
                    <p class="text-muted">
                        Showing <?= number_format(min($catalog['total'], ($catalog['page'] - 1) * 12 + 1)) ?>
                        &ndash; <?= number_format(min($catalog['total'], $catalog['page'] * 12)) ?>
                        of <?= number_format($catalog['total']) ?> products
                    </p>
                    <div class="row justify-content-center">
                        <?php while ($product = mysqli_fetch_assoc($catalog['products'])): ?>
                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <article class="single-tranding mb-30">
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
                                    <p><?= (int) $product['stock'] > 0
                                        ? 'In stock: ' . (int) $product['stock']
                                        : 'Out of stock' ?></p>
                                    <?php if ((int) $product['stock'] > 0): ?>
                                        <form action="?route=AddToCart" method="post">
                                            <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                            <input type="hidden" name="qty" value="1">
                                            <button class="button" type="submit">Add to cart</button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="?route=AddToWishlist" method="post">
                                        <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                        <button class="button" type="submit">Add to wishlist</button>
                                    </form>
                                </article>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php if ($catalog['pages'] > 1): ?>
                        <nav aria-label="Product pages">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $catalog['page'] <= 1 ? 'disabled' : '' ?>">
                                    <?php if ($catalog['page'] > 1): ?>
                                        <a class="page-link" href="<?= escapeHtml($catalogUrl($catalog['page'] - 1)) ?>">Previous</a>
                                    <?php else: ?>
                                        <span class="page-link">Previous</span>
                                    <?php endif; ?>
                                </li>
                                <?php if ($visibleFirstPage > 1): ?>
                                    <li class="page-item"><a class="page-link" href="<?= escapeHtml($catalogUrl(1)) ?>">1</a></li>
                                    <?php if ($visibleFirstPage > 2): ?>
                                        <li class="page-item disabled"><span class="page-link" aria-hidden="true">&hellip;</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php for ($page = $visibleFirstPage; $page <= $visibleLastPage; $page++): ?>
                                    <li class="page-item <?= $page === $catalog['page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= escapeHtml($catalogUrl($page)) ?>"
                                           <?= $page === $catalog['page'] ? 'aria-current="page"' : '' ?>>
                                            <?= $page ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                <?php if ($visibleLastPage < $catalog['pages']): ?>
                                    <?php if ($visibleLastPage < $catalog['pages'] - 1): ?>
                                        <li class="page-item disabled"><span class="page-link" aria-hidden="true">&hellip;</span></li>
                                    <?php endif; ?>
                                    <li class="page-item"><a class="page-link" href="<?= escapeHtml($catalogUrl($catalog['pages'])) ?>"><?= $catalog['pages'] ?></a></li>
                                <?php endif; ?>
                                <li class="page-item <?= $catalog['page'] >= $catalog['pages'] ? 'disabled' : '' ?>">
                                    <?php if ($catalog['page'] < $catalog['pages']): ?>
                                        <a class="page-link" href="<?= escapeHtml($catalogUrl($catalog['page'] + 1)) ?>">Next</a>
                                    <?php else: ?>
                                        <span class="page-link">Next</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div></div>
    </div>
</section>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
