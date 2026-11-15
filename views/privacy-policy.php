<?php include_once __DIR__ . '/../inc/header.php'; ?>

<main class="store-content-page">
    <div class="container" style="max-width: 900px;">
        <header class="store-content-heading">
            <p class="store-eyebrow">YOUR INFORMATION</p>
            <h1>Privacy overview</h1>
            <p>This overview describes the information used by the current store application.</p>
        </header>

        <section class="store-content-card">
            <h2>Account and order information</h2>
            <p>When you create an account, the store saves your name, email address, and a password hash. Orders also use your delivery details, chosen products, quantities, totals, payment status, and tracking history so they can be managed in your account.</p>

            <h2>Cart and site operation</h2>
            <p>The shopping cart uses a server-side session. The site uses the information above to provide account, checkout, order-history, and administration features.</p>

            <h2>Store assistant</h2>
            <p>The optional chat assistant is provided by an external n8n service. Messages entered in the chat are sent to that service; avoid including passwords, payment details, or other sensitive information.</p>

            <h2>Development and demo records</h2>
            <p>This development store may contain clearly marked fictional demo orders and customer profiles. Demo email addresses use the reserved <code>.invalid</code> domain and are not contactable.</p>

            <h2>Before public launch</h2>
            <p>This page is a practical project overview, not legal advice. Review the store's actual hosting, analytics, retention, and support practices and obtain appropriate legal guidance before using the site with real customers.</p>
        </section>
    </div>
</main>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
