<?php
$cartItems = getCartItems();
$cartLoadError = $cartItems === null;
if ($cartLoadError) {
    $cartItems = [];
}
$subtotal = cartSubtotal($cartItems);
include_once __DIR__ . '/../inc/header.php';
?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row"><div class="col-12"><div class="breadcrumb_content">
            <ul><li><a href="?route=home">Home</a></li><li>Shopping cart</li></ul>
        </div></div></div>
    </div>
</div>

<section class="pt-60 pb-60">
    <div class="container">
        <h2>Your Shopping Cart</h2>
        <?php if (!empty($_SESSION['error'])): ?>
            <p class="alert alert-danger"><?= escapeHtml($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
            <p class="alert alert-success"><?= escapeHtml($_SESSION['success']) ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if ($cartLoadError): ?>
            <p>Your cart is temporarily unavailable. Please try again later.</p>
        <?php elseif (!$cartItems): ?>
            <p>Your cart is empty.</p>
            <a class="button" href="?route=products">Continue shopping</a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <a href="?route=product-details&amp;id=<?= (int) $item['id'] ?>">
                                        <?= escapeHtml($item['name']) ?>
                                    </a>
                                    <?php if ((int) $item['quantity'] > (int) $item['stock']): ?>
                                        <p class="text-danger">Only <?= (int) $item['stock'] ?> left in stock.</p>
                                    <?php endif; ?>
                                </td>
                                <td>$<?= number_format((float) $item['price'], 2) ?></td>
                                <td>
                                    <form action="?route=UpdateCart" method="post">
                                        <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                                        <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                                        <input type="number" name="qty" min="1" max="<?= (int) $item['stock'] ?>"
                                               value="<?= (int) $item['quantity'] ?>" required>
                                        <button class="button" type="submit">Update</button>
                                    </form>
                                </td>
                                <td>$<?= number_format((float) $item['line_total'], 2) ?></td>
                                <td>
                                    <form action="?route=RemoveFromCart" method="post">
                                        <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                                        <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                                        <button class="button" type="submit">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr><th colspan="3">Subtotal</th><td colspan="2">$<?= number_format($subtotal, 2) ?></td></tr>
                        <tr><th colspan="3">Total</th><td colspan="2">$<?= number_format($subtotal, 2) ?></td></tr>
                    </tfoot>
                </table>
            </div>
            <a class="button" href="?route=products">Continue shopping</a>
            <a class="button" href="?route=checkout">Proceed to checkout</a>
        <?php endif; ?>
    </div>
</section>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
