<?php
$adminName = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <base href="<?= htmlspecialchars($adminBasePath ?? './', ENT_QUOTES, 'UTF-8') ?>">
  <title>F.A.Q - Admin</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="../?route=admin" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Admin-Drons</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <nav class="header-nav ms-auto pe-4">
      <span class="d-none d-md-inline me-3"><?php echo $adminName; ?></span>
      <a href="../?route=admin-logout">Sign out</a>
    </nav>
  </header>

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin"><i class="bi bi-grid"></i><span>Dashboard</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin-forms-elements"><i class="bi bi-journal-text"></i><span>Form Elements</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin-users-profile"><i class="bi bi-person"></i><span>Profile</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin-pages-login"><i class="bi bi-box-arrow-in-right"></i><span>Login</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin-pages-register"><i class="bi bi-card-list"></i><span>Register</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../?route=admin-pages-faq"><i class="bi bi-question-circle"></i><span>F.A.Q</span></a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Frequently Asked Questions</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../?route=admin">Home</a></li>
          <li class="breadcrumb-item active">F.A.Q</li>
        </ol>
      </nav>
    </div>
    <section class="section">
      <div class="card">
        <div class="card-body">
          <h2 class="card-title">Store administration</h2>
          <div class="accordion" id="adminFaq">
            <div class="accordion-item">
              <h3 class="accordion-header" id="faqProductsHeading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqProducts" aria-expanded="true" aria-controls="faqProducts">
                  How do I add or update a product?
                </button>
              </h3>
              <div id="faqProducts" class="accordion-collapse collapse show" aria-labelledby="faqProductsHeading" data-bs-parent="#adminFaq">
                <div class="accordion-body">Open Form Elements from the sidebar. Use the product form to add a product, or choose Edit beside an existing product. The table shows the products currently stored in the database.</div>
              </div>
            </div>
            <div class="accordion-item">
              <h3 class="accordion-header" id="faqOrdersHeading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqOrders" aria-expanded="false" aria-controls="faqOrders">
                  How are customer orders paid for?
                </button>
              </h3>
              <div id="faqOrders" class="accordion-collapse collapse" aria-labelledby="faqOrdersHeading" data-bs-parent="#adminFaq">
                <div class="accordion-body">Checkout currently supports cash on delivery only. The site does not collect card or PayPal payments.</div>
              </div>
            </div>
            <div class="accordion-item">
              <h3 class="accordion-header" id="faqStockHeading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqStock" aria-expanded="false" aria-controls="faqStock">
                  When does product stock change?
                </button>
              </h3>
              <div id="faqStock" class="accordion-collapse collapse" aria-labelledby="faqStockHeading" data-bs-parent="#adminFaq">
                <div class="accordion-body">The storefront checks available stock when items are added and again during checkout. Stock is reduced only after a COD order is successfully saved.</div>
              </div>
            </div>
            <div class="accordion-item">
              <h3 class="accordion-header" id="faqResetHeading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqReset" aria-expanded="false" aria-controls="faqReset">
                  Can a customer reset a forgotten password by email?
                </button>
              </h3>
              <div id="faqReset" class="accordion-collapse collapse" aria-labelledby="faqResetHeading" data-bs-parent="#adminFaq">
                <div class="accordion-body">Not yet. Outgoing password-reset email is not configured, so customers should contact the store administrator for help.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
