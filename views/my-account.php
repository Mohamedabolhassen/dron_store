<?php
$userId = getCurrentUserId();
$user = userFindById($con, $userId);
$ordersResult = orderAllForUser($con, $userId);
$accountLoadError = $user === null || !$ordersResult;
include_once __DIR__ . '/../inc/header.php';
?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row"><div class="col-12"><div class="breadcrumb_content">
            <ul><li><a href="?route=home">Home</a></li><li>My Account</li></ul>
        </div></div></div>
    </div>
</div>

<section class="pt-60 pb-60">
    <div class="container">
        <h2>My Account</h2>
        <?php if (!empty($_SESSION['error'])): ?>
            <p class="alert alert-danger"><?= escapeHtml($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
            <p class="alert alert-success"><?= escapeHtml($_SESSION['success']) ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if ($accountLoadError || !$user): ?>
            <p>Your account information is temporarily unavailable. Please try again later.</p>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-5">
                    <h3>Profile information</h3>
                    <form action="?route=UpdateProfile" method="post">
                        <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                        <div class="form_group">
                            <label for="name">Name</label>
                            <input id="name" name="name" type="text" maxlength="150"
                                   value="<?= escapeHtml($user['name']) ?>" required>
                        </div>
                        <div class="form_group">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" maxlength="150"
                                   value="<?= escapeHtml($user['email']) ?>" required>
                        </div>
                        <button class="button" type="submit">Update profile</button>
                    </form>
                </div>
                <div class="col-lg-7">
                    <h3>My Orders</h3>
                    <?php if (mysqli_num_rows($ordersResult) === 0): ?>
                        <p>You have not placed any orders yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead><tr><th>Order</th><th>Date</th><th>Status</th><th>Total</th><th></th></tr></thead>
                                <tbody>
                                    <?php while ($order = mysqli_fetch_assoc($ordersResult)): ?>
                                        <tr>
                                            <td><?= escapeHtml($order['order_number']) ?></td>
                                            <td><?= escapeHtml($order['created_at']) ?></td>
                                            <td><?= escapeHtml($order['order_status']) ?></td>
                                            <td>$<?= number_format((float) $order['total_amount'], 2) ?></td>
                                            <td><a href="?route=order-details&amp;id=<?= (int) $order['id'] ?>">Details</a></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
