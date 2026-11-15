<?php
$passwordResetError = $_SESSION['admin_password_reset_error'] ?? null;
unset($_SESSION['admin_password_reset_error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="referrer" content="no-referrer">
  <base href="<?= htmlspecialchars($adminBasePath ?? './', ENT_QUOTES, 'UTF-8') ?>">
  <title>Choose a New Admin Password - Drophut</title>
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
                  <h1 class="card-title text-center pb-0 fs-4">Choose a new password</h1>
                  <p class="text-center small">Use at least 8 characters.</p>
                </div>

                <?php if (is_string($passwordResetError) && $passwordResetError !== ''): ?>
                  <p class="alert alert-danger" role="alert"><?= htmlspecialchars($passwordResetError, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <?php if (!empty($passwordResetTokenValid)): ?>
                  <form class="row g-3" action="../?route=admin-password-reset-submit" method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($passwordResetToken, ENT_QUOTES, 'UTF-8') ?>">
                    <div class="col-12">
                      <label for="newPassword" class="form-label">New password</label>
                      <input type="password" name="password" class="form-control" id="newPassword" minlength="8" maxlength="4096" autocomplete="new-password" required>
                    </div>
                    <div class="col-12">
                      <label for="confirmPassword" class="form-label">Confirm new password</label>
                      <input type="password" name="password_confirm" class="form-control" id="confirmPassword" minlength="8" maxlength="4096" autocomplete="new-password" required>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Update password</button>
                    </div>
                  </form>
                <?php else: ?>
                  <p class="alert alert-warning" role="status">This reset link is invalid or expired. Request a new one to continue.</p>
                  <a class="btn btn-primary w-100" href="../?route=admin-pages-forgot-password">Request another reset link</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</body>

</html>
