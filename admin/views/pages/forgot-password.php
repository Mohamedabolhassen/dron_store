<?php
$passwordResetError = $_SESSION['admin_password_reset_error'] ?? null;
$passwordResetSuccess = $_SESSION['admin_password_reset_success'] ?? null;
unset($_SESSION['admin_password_reset_error'], $_SESSION['admin_password_reset_success']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <base href="<?= htmlspecialchars($adminBasePath ?? './', ENT_QUOTES, 'UTF-8') ?>">
  <title>Reset Admin Password - Drophut</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="row justify-content-center w-100">
          <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
            <div class="d-flex justify-content-center py-4">
              <a href="../?route=admin-pages-login" class="logo d-flex align-items-center w-auto">
                <img src="assets/img/logo.png" alt="">
                <span class="d-none d-lg-block">Drophut Admin</span>
              </a>
            </div>

            <div class="card mb-3 w-100">
              <div class="card-body">
                <div class="pt-4 pb-2">
                  <h1 class="card-title text-center pb-0 fs-4">Reset your password</h1>
                  <p class="text-center small">Enter the email address for your Admin account.</p>
                </div>

                <?php if (is_string($passwordResetError) && $passwordResetError !== ''): ?>
                  <p class="alert alert-danger" role="alert"><?= htmlspecialchars($passwordResetError, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
                <?php if (is_string($passwordResetSuccess) && $passwordResetSuccess !== ''): ?>
                  <p class="alert alert-success" role="status"><?= htmlspecialchars($passwordResetSuccess, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <form class="row g-3" action="../?route=admin-password-reset-request" method="post">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <div class="col-12">
                    <label for="resetEmail" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" id="resetEmail" autocomplete="email" required>
                  </div>
                  <div class="col-12">
                    <button class="btn btn-primary w-100" type="submit">Send reset link</button>
                  </div>
                  <div class="col-12 text-center">
                    <a href="../?route=admin-pages-login">Back to Admin sign in</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</body>

</html>
