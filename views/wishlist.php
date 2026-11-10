<?php
$userId = getCurrentUserId();
$wishlistResult = wishlistForUser($con, $userId);
$wishlistLoadError = !$wishlistResult;
include_once __DIR__ . '/../inc/header.php';
?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row"><div class="col-12"><div class="breadcrumb_content">
            <ul><li><a href="?route=home">Home</a></li><li>Wishlist</li></ul>
        </div></div></div>
    </div>
</div>

<section class="pt-60 pb-60">
    <div class="container">
        <h2>My Wishlist</h2>
        <?php if (!empty($_SESSION['error'])): ?>
            <p class="alert alert-danger"><?= escapeHtml($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
            <p class="alert alert-success"><?= escapeHtml($_SESSION['success']) ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if ($wishlistLoadError): ?>
            <p>Your wishlist is temporarily unavailable. Please try again later.</p>
        <?php elseif (mysqli_num_rows($wishlistResult) === 0): ?>
            <p>Your wishlist is empty.</p>
            <a class="button" href="?route=products">Browse products</a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Product</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php while ($product = mysqli_fetch_assoc($wishlistResult)): ?>
                            <tr>
                                <td>
                                    <a href="?route=product-details&amp;id=<?= (int) $product['id'] ?>">
                                        <?= escapeHtml($product['name']) ?>
                                    </a>
                                </td>
                                <td>$<?= number_format((float) $product['price'], 2) ?></td>
                                <td><?= (int) $product['stock'] > 0 ? (int) $product['stock'] : 'Out of stock' ?></td>
                                <td>
                                    <form action="?route=RemoveFromWishlist" method="post">
                                        <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                        <button class="button" type="submit">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
