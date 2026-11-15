<?php include_once __DIR__ . '/../inc/header.php'; ?>

<main class="store-content-page">
    <div class="container">
        <header class="store-content-heading">
            <p class="store-eyebrow">A FEW QUICK ANSWERS</p>
            <h1>Frequently asked questions</h1>
            <p>Useful details about products, checkout, accounts, and order updates.</p>
        </header>

        <div class="store-faq-list">
            <details>
                <summary>How can I check if a product is available?</summary>
                <p>Product pages and catalog cards show the latest stock quantity. Stock is checked again when an order is placed.</p>
            </details>
            <details>
                <summary>Which payment method can I use?</summary>
                <p>Checkout currently supports cash on delivery. Online card and wallet payments are not enabled.</p>
            </details>
            <details>
                <summary>How do I follow an order?</summary>
                <p>Sign in and open your account to see your orders and tracking updates. You can also use the <a href="?route=tracking">order-tracking page</a>.</p>
            </details>
            <details>
                <summary>Do I need an account to order?</summary>
                <p>Yes. Sign in or create an account before placing an order so its details and tracking history can be linked to you.</p>
            </details>
            <details>
                <summary>Can I reset my password by email?</summary>
                <p>Password-reset email delivery is not configured yet. Visit the <a href="?route=forget-password">password support page</a> for the current options.</p>
            </details>
            <details>
                <summary>Where can I ask a support question?</summary>
                <p>The <a href="?route=contact">support page</a> links to the available account and order tools. A contact email and message form have not been set up yet.</p>
            </details>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
