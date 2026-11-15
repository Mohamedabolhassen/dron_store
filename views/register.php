<?php include_once __DIR__ . '/../inc/header.php'; ?>

<main class="store-content-page">
    <div class="container store-auth-layout">
        <section class="store-auth-story" aria-labelledby="register-story-title">
            <p class="store-eyebrow">YOUR NEXT VIEW STARTS HERE</p>
            <h1 id="register-story-title">Create your store account.</h1>
            <p>Save your details, manage your wishlist, and follow your orders from one place.</p>
        </section>

        <section class="store-auth-card" aria-labelledby="register-title">
            <h2 id="register-title">Create an account</h2>
            <p>It only takes a moment to get started.</p>
            <?php if (!empty($_SESSION['error'])): ?>
                <p class="alert alert-danger" role="alert"><?= escapeHtml($_SESSION['error']) ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['success'])): ?>
                <p class="alert alert-success" role="status"><?= escapeHtml($_SESSION['success']) ?></p>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form class="store-form" action="?route=NewRegister" method="post">
                <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                <div class="form_group">
                    <label for="name">Full name</label>
                    <input id="name" name="name" type="text" autocomplete="name" maxlength="150" required>
                </div>
                <div class="form_group">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" maxlength="150" required>
                </div>
                <div class="form_group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password"
                           minlength="6" aria-describedby="password-hint" required>
                    <small id="password-hint">Use at least 6 characters.</small>
                </div>
                <button class="button" type="submit">Create account</button>
            </form>

            <p>Already have an account? <a class="store-form-link" href="?route=Login">Sign in</a></p>
        </section>
    </div>
</main>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
