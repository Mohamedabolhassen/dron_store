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
            <ul><li><a href="?route=home">Home</a></li><li>Checkout</li></ul>
        </div></div></div>
    </div>
</div>

<section class="Checkout_section mt-60 mb-60">
    <div class="container">
        <h2>Checkout</h2>
        <?php if (!empty($_SESSION['error'])): ?>
            <p class="alert alert-danger"><?= escapeHtml($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if ($cartLoadError): ?>
            <p>Your cart is temporarily unavailable. Please try again later.</p>
        <?php elseif (!$cartItems): ?>
            <p>Your cart is empty. Add products before checking out.</p>
            <a class="button" href="?route=products">Browse products</a>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-6">
                    <form action="?route=Checkout" method="post">
                        <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                        <h3>Shipping information</h3>
                        <div class="form_group">
                            <label for="recipient_name">Full name <span>*</span></label>
                            <input id="recipient_name" name="recipient_name" type="text" maxlength="150" required>
                        </div>
                        <div class="form_group">
                            <label for="phone">Phone <span>*</span></label>
                            <input id="phone" name="phone" type="tel" maxlength="30" required>
                        </div>
                        <div class="form_group">
                            <label for="country">Country <span>*</span></label>
                            <input id="country" name="country" type="text" maxlength="100" required>
                        </div>
                        <div class="form_group">
                            <label for="state">State / region</label>
                            <input id="state" name="state" type="text" maxlength="100">
                        </div>
                        <div class="form_group">
                            <label for="city">City <span>*</span></label>
                            <input id="city" name="city" type="text" maxlength="100" required>
                        </div>
                        <div class="form_group">
                            <label for="street_address">Street address <span>*</span></label>
                            <input id="street_address" name="street_address" type="text" maxlength="255" required>
                        </div>
                        <div class="form_group">
                            <label for="address_line2">Apartment, suite, etc.</label>
                            <input id="address_line2" name="address_line2" type="text" maxlength="255">
                        </div>
                        <div class="form_group">
                            <label for="postal_code">Postal code</label>
                            <input id="postal_code" name="postal_code" type="text" maxlength="20">
                        </div>
                        <p>Payment method: Cash on delivery</p>
                        <button class="button" type="submit">Place order</button>
                    </form>
                </div>
                <div class="col-lg-6">
                    <h3>Your order</h3>
                    <div class="order_table table-responsive">
                        <table class="table">
                            <thead><tr><th>Product</th><th>Total</th></tr></thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr>
                                        <td><?= escapeHtml($item['name']) ?> × <?= (int) $item['quantity'] ?></td>
                                        <td>$<?= number_format((float) $item['line_total'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr><th>Subtotal</th><td>$<?= number_format($subtotal, 2) ?></td></tr>
                                <tr><th>Shipping</th><td>$0.00</td></tr>
                                <tr class="order_total"><th>Total</th><td><strong>$<?= number_format($subtotal, 2) ?></strong></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
