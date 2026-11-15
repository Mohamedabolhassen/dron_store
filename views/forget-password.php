<?php include_once __DIR__ . '/../inc/header.php'; ?>

<main class="store-content-page">
    <div class="container" style="max-width: 760px;">
        <section class="store-content-card" aria-labelledby="reset-title">
            <p class="store-eyebrow">ACCOUNT SUPPORT</p>
            <h1 id="reset-title">Password reset</h1>
            <p>Password-reset email is not configured for this store yet, so we can't securely send a reset link.</p>
            <p>If you can still access your account, return to sign in. For order questions, the FAQ and order-tracking pages are available below.</p>
            <div class="store-hero-actions">
                <a class="store-button store-button-primary" href="?route=Login">Back to sign in</a>
                <a class="store-button store-button-dark" href="?route=faq">Read the FAQ</a>
                <a class="store-button store-button-secondary" href="?route=tracking">Track an order</a>
            </div>
        </section>
    </div>
</main>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
