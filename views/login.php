<?php include_once __DIR__ . '/../inc/header.php'; ?>

<main class="store-content-page">
    <div class="container store-auth-layout">
        <section class="store-auth-story" aria-labelledby="login-story-title">
            <p class="store-eyebrow">WELCOME BACK</p>
            <h1 id="login-story-title">Ready for your next flight?</h1>
            <p>Sign in to review your orders, track deliveries, and keep your wishlist close.</p>
        </section>

        <section class="store-auth-card" aria-labelledby="login-title">
            <h2 id="login-title">Sign in</h2>
            <p>Use the email address and password for your store account.</p>
            <?php if (!empty($_SESSION['error'])): ?>
                <p class="alert alert-danger" role="alert"><?= escapeHtml($_SESSION['error']) ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['success'])): ?>
                <p class="alert alert-success" role="status"><?= escapeHtml($_SESSION['success']) ?></p>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form class="store-form" action="?route=CheckLogin" method="post">
                <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                <div class="form_group">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="username" maxlength="150" required>
                </div>
                <div class="form_group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                </div>
                <button class="button" type="submit">Sign in</button>
            </form>

            <a class="store-form-link" href="?route=forget-password">Forgot your password?</a>
            <p>New to the store? <a class="store-form-link" href="?route=Register">Create an account</a></p>
        </section>
    </div>
</main>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
