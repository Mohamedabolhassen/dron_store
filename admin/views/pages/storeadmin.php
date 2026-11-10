<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <base href="<?= htmlspecialchars($adminBasePath ?? './', ENT_QUOTES, 'UTF-8') ?>">

  <title>Products - Admin</title>

  <link href="assets/img/favicon.png" rel="icon">
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
  </header>

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../?route=admin-forms-elements">
          <i class="bi bi-journal-text"></i>
          <span>Form Elements</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin-users-profile">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin-pages-login">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Login</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin-pages-register">
          <i class="bi bi-card-list"></i>
          <span>Register</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin-pages-faq">
          <i class="bi bi-question-circle"></i>
          <span>F.A.Q</span>
        </a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Products</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../?route=admin">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Products</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?= is_array($editingProduct) ? 'Edit Product' : 'Add Product' ?></h5>
              <?php if (!empty($_SESSION['product_message'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['product_message']['type'], ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert">
                  <?= htmlspecialchars($_SESSION['product_message']['text'], ENT_QUOTES, 'UTF-8') ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['product_message']); ?>
              <?php endif; ?>

              <?php $isEditing = is_array($editingProduct); ?>
              <form method="post" enctype="multipart/form-data" action="../?route=<?= $isEditing ? 'admin-product-update' : 'admin-product-create' ?>" class="row g-3">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <?php if ($isEditing): ?>
                  <input type="hidden" name="id" value="<?= htmlspecialchars($productForm['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <?php endif; ?>
                <div class="col-12">
                  <label for="productName" class="form-label">Product Name</label>
                  <input type="text" name="productName" class="form-control" id="productName" value="<?= htmlspecialchars($productForm['productName'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Enter product name" required>
                </div>

                <div class="col-md-6">
                  <label for="productSku" class="form-label">SKU</label>
                  <input type="text" name="sku" class="form-control" id="productSku" value="<?= htmlspecialchars($productForm['sku'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g. DRN-001" required>
                </div>

                <div class="col-md-6">
                  <label for="productCategory" class="form-label">Category</label>
                  <select name="product_category" id="productCategory" class="form-select" required>
                    <option value="" disabled <?= !$isEditing ? 'selected' : '' ?>>Choose...</option>
                    <?php
                    $categories = ['Camera Drones', 'FPV Drones', 'Accessories'];
                    if ($isEditing && !in_array($productForm['product_category'] ?? '', $categories, true)): ?>
                      <option value="<?= htmlspecialchars($productForm['product_category'] ?? '', ENT_QUOTES, 'UTF-8') ?>" selected><?= htmlspecialchars($productForm['product_category'] ?? '', ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endif; ?>
                    <option value="Camera Drones" <?= ($productForm['product_category'] ?? '') === 'Camera Drones' ? 'selected' : '' ?>>Camera Drones</option>
                    <option value="FPV Drones" <?= ($productForm['product_category'] ?? '') === 'FPV Drones' ? 'selected' : '' ?>>FPV Drones</option>
                    <option value="Accessories" <?= ($productForm['product_category'] ?? '') === 'Accessories' ? 'selected' : '' ?>>Accessories</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label  for="productPrice" class="form-label">Price</label>
                  <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="price" class="form-control" id="productPrice" value="<?= htmlspecialchars($productForm['price'] ?? '', ENT_QUOTES, 'UTF-8') ?>" min="0" step="0.01" placeholder="0.00" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="productStock" class="form-label">Stock</label>
                  <input type="number" name="stock" class="form-control" id="productStock" value="<?= htmlspecialchars($productForm['stock'] ?? '', ENT_QUOTES, 'UTF-8') ?>" min="0" step="1" placeholder="0" required>
                </div>

                <div class="col-12">
                  <label for="productDescription" class="form-label">Description</label>
                  <textarea name="Description" class="form-control" id="productDescription" rows="3" placeholder="Product details"><?= htmlspecialchars($productForm['Description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="col-12">
                  <label for="productImage" class="form-label">Product Image</label>
                  <?php if (!empty($productForm['image_url'])): ?>
                    <div class="mb-2">
                      <img src="../<?= htmlspecialchars($productForm['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($productForm['productName'] ?? 'Current product image', ENT_QUOTES, 'UTF-8') ?>" style="width: 100px; height: 80px; object-fit: contain;">
                    </div>
                  <?php endif; ?>
                  <input type="file" name="image" class="form-control" id="productImage" accept="image/jpeg,image/png,image/gif,image/webp">
                  <div class="form-text">JPEG, PNG, GIF, or WebP. Maximum file size: 5 MB. Leave empty to keep the current image.</div>
                </div>

                <div class="col-12 d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-<?= $isEditing ? 'check-circle' : 'plus-circle' ?> me-1"></i><?= $isEditing ? 'Save Changes' : 'Add Product' ?>
                  </button>
                  <?php if ($isEditing): ?>
                    <a href="../?route=admin-forms-elements" class="btn btn-outline-secondary">Cancel</a>
                  <?php else: ?>
                    <button type="reset" class="btn btn-outline-secondary">Clear</button>
                  <?php endif; ?>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0">Product List</h5>
                <div class="input-group" style="max-width: 280px;">
                  <span class="input-group-text"><i class="bi bi-search"></i></span>
                  <input type="search" class="form-control" aria-label="Search products" placeholder="Search products">
                </div>
              </div>



              <div class="table-responsive mt-3">
                <table class="table table-hover align-middle mb-0">
                  <thead>
                    <tr>
                      <th scope="col">Product</th>
                      <th scope="col">SKU</th>
                      <th scope="col">Category</th>
                      <th scope="col">Price</th>
                      <th scope="col">Stock</th>
                      <th scope="col" class="text-end">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($products as $row): ?>
                    <tr>
                      <td>
                        <?php if (!empty($row['image_url'])): ?>
                          <img src="../<?= htmlspecialchars($row['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="rounded me-2" style="width: 48px; height: 48px; object-fit: contain;">
                        <?php endif; ?>
                        <div class="fw-semibold"><?= htmlspecialchars($row['productName'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                      </td>
                      <td><?= htmlspecialchars($row['sku'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($row['product_category'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($row['price'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                      <td><span class="badge bg-success"><?= htmlspecialchars($row['stock'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></td>
                      <td class="text-end text-nowrap">
                        <a href="../?route=admin-product-edit&amp;id=<?= rawurlencode((string) ($row['id'] ?? '')) ?>" class="btn btn-outline-primary btn-sm" aria-label="Edit <?= htmlspecialchars($row['productName'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <form method="post" action="../?route=admin-product-delete" class="d-inline" onsubmit="return confirm('Delete this product?');">
                          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                          <input type="hidden" name="id" value="<?= htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                          <button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Delete <?= htmlspecialchars($row['productName'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-end mt-3">
                <nav aria-label="Product list pages">
                  <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled" aria-disabled="true">
                      <span class="page-link">Previous</span>
                    </li>
                    <li class="page-item active" aria-current="page">
                      <span class="page-link">1</span>
                    </li>
                    <li class="page-item disabled" aria-disabled="true">
                      <span class="page-link">Next</span>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center" aria-label="Back to top">
    <i class="bi bi-arrow-up-short"></i>
  </a>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>
