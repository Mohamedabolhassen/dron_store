<?php
$adminName = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
$statusActions = [
    'pending' => ['processing' => 'Approve', 'cancelled' => 'Cancel'],
    'processing' => ['shipped' => 'Mark shipped', 'cancelled' => 'Cancel'],
    'shipped' => ['delivered' => 'Mark delivered'],
];
$analyticsStatusTotal = $dashboardAnalytics === false
    ? 0
    : array_sum($dashboardAnalytics['statuses']);
$orderMessage = $_SESSION['order_message'] ?? null;
unset($_SESSION['order_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Store dashboard</title>
  <base href="<?= htmlspecialchars($adminBasePath ?? './', ENT_QUOTES, 'UTF-8') ?>">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    .dashboard-metric { min-height: 132px; }
    .dashboard-metric .metric-value { font-size: 1.65rem; font-weight: 600; }
    #n8n-chat-card { height: 480px; overflow: hidden; }
    #n8n-chat { height: 100%; }
    .analytics-chart { position: relative; min-height: 320px; }
    .analytics-chart-wide { min-height: 350px; }
    .analytics-empty { padding: 48px 20px; color: #79859a; text-align: center; }
    .analytics-product-image { width: 52px; height: 46px; border-radius: 6px; object-fit: cover; background: #f0f3f5; }
    .analytics-stock-row + .analytics-stock-row { border-top: 1px solid #eef0f4; }
  </style>
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="../?route=admin" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Drone Store</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn" aria-label="Toggle navigation"></i>
    </div>
    <nav class="header-nav ms-auto pe-4">
      <span class="me-3">Welcome, <?= $adminName ?></span>
      <a href="../?route=admin-logout">Sign out</a>
    </nav>
  </header>

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link" href="../?route=admin"><i class="bi bi-grid"></i><span>Dashboard</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#store-analytics"><i class="bi bi-bar-chart-line"></i><span>Sales analytics</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin-forms-elements"><i class="bi bi-box-seam"></i><span>Products</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin-users-profile"><i class="bi bi-person"></i><span>Profile</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=admin-pages-faq"><i class="bi bi-question-circle"></i><span>Admin FAQ</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../?route=home"><i class="bi bi-shop"></i><span>View store</span></a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Store dashboard</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb"><li class="breadcrumb-item active">Overview</li></ol>
      </nav>
    </div>

    <section class="section dashboard">
      <?php if ($dashboardMetrics === false): ?>
        <div class="alert alert-danger" role="alert">Store metrics could not be loaded.</div>
      <?php else: ?>
        <div class="row">
          <div class="col-xxl-3 col-md-6">
            <div class="card info-card sales-card dashboard-metric"><div class="card-body">
              <h2 class="card-title">Orders today</h2>
              <div class="d-flex align-items-center"><div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-cart"></i></div>
                <div class="ps-3"><div class="metric-value"><?= number_format((int) $dashboardMetrics['orders_today']) ?></div><span class="text-muted small">All order statuses</span></div>
              </div>
            </div></div>
          </div>
          <div class="col-xxl-3 col-md-6">
            <div class="card info-card revenue-card dashboard-metric"><div class="card-body">
              <h2 class="card-title">Order value <span>| this month</span></h2>
              <div class="d-flex align-items-center"><div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-currency-dollar"></i></div>
                <div class="ps-3"><div class="metric-value">$<?= number_format((float) $dashboardMetrics['order_value_month'], 2) ?></div><span class="text-muted small">Excludes cancelled orders</span></div>
              </div>
            </div></div>
          </div>
          <div class="col-xxl-3 col-md-6">
            <div class="card info-card customers-card dashboard-metric"><div class="card-body">
              <h2 class="card-title">Customers</h2>
              <div class="d-flex align-items-center"><div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-people"></i></div>
                <div class="ps-3"><div class="metric-value"><?= number_format((int) $dashboardMetrics['total_customers']) ?></div><span class="text-muted small">Registered accounts</span></div>
              </div>
            </div></div>
          </div>
          <div class="col-xxl-3 col-md-6">
            <div class="card info-card dashboard-metric"><div class="card-body">
              <h2 class="card-title">Pending orders</h2>
              <div class="d-flex align-items-center"><div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="bi bi-hourglass-split"></i></div>
                <div class="ps-3"><div class="metric-value"><?= number_format((int) $dashboardMetrics['pending_orders']) ?></div>
                  <span class="text-muted small"><?= number_format((int) $dashboardMetrics['out_of_stock_products']) ?> products out of stock</span></div>
              </div>
            </div></div>
          </div>
        </div>
      <?php endif; ?>

      <div class="card" id="store-analytics">
        <div class="card-body">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
              <h2 class="card-title mb-1">Sales &amp; product analytics</h2>
              <p class="mb-0 text-muted small">Live order and inventory data. Choose a date range to compare sales performance.</p>
            </div>
            <form action="../index.php" method="get" class="d-flex align-items-center gap-2">
              <input type="hidden" name="route" value="admin">
              <label class="form-label mb-0" for="analytics-period">Show</label>
              <select class="form-select form-select-sm" id="analytics-period" name="period" aria-label="Analytics date range">
                <option value="7" <?= $analyticsPeriod === 7 ? 'selected' : '' ?>>Last 7 days</option>
                <option value="30" <?= $analyticsPeriod === 30 ? 'selected' : '' ?>>Last 30 days</option>
                <option value="90" <?= $analyticsPeriod === 90 ? 'selected' : '' ?>>Last 90 days</option>
              </select>
              <button type="submit" class="btn btn-primary btn-sm">Update</button>
            </form>
          </div>
        </div>
      </div>

      <?php if ($dashboardAnalytics === false): ?>
        <div class="alert alert-danger" role="alert">Sales and product analytics could not be loaded.</div>
      <?php else: ?>
        <div class="row">
          <div class="col-12">
            <section class="card" aria-labelledby="sales-trend-title">
              <div class="card-body">
                <h3 class="card-title" id="sales-trend-title">Sales and collected payments</h3>
                <p class="small text-muted">Order value excludes cancelled orders. Cash is counted as collected only after an order is marked delivered.</p>
                <div class="analytics-chart analytics-chart-wide">
                  <canvas id="sales-trend-chart" role="img" aria-label="Sales and cash collected across the selected date range"></canvas>
                </div>
              </div>
            </section>
          </div>
          <div class="col-lg-5">
            <section class="card h-100" aria-labelledby="order-status-title">
              <div class="card-body">
                <h3 class="card-title" id="order-status-title">Order fulfillment</h3>
                <p class="small text-muted">Orders by their current fulfillment status.</p>
                <?php if ($analyticsStatusTotal > 0): ?>
                  <div class="analytics-chart"><canvas id="order-status-chart" role="img" aria-label="Orders grouped by fulfillment status"></canvas></div>
                <?php else: ?>
                  <div class="analytics-empty">There are no orders to chart yet.</div>
                <?php endif; ?>
              </div>
            </section>
          </div>
          <div class="col-lg-7">
            <section class="card h-100" aria-labelledby="category-sales-title">
              <div class="card-body">
                <h3 class="card-title" id="category-sales-title">Order value by product category</h3>
                <p class="small text-muted">Category sales during the selected range; cancelled orders are excluded.</p>
                <?php if ($dashboardAnalytics['categories']): ?>
                  <div class="analytics-chart"><canvas id="category-sales-chart" role="img" aria-label="Order value by product category"></canvas></div>
                <?php else: ?>
                  <div class="analytics-empty">Category sales will appear after customers place orders.</div>
                <?php endif; ?>
              </div>
            </section>
          </div>
          <div class="col-12">
            <section class="card" aria-labelledby="stock-alert-title">
              <div class="card-body">
                <h3 class="card-title" id="stock-alert-title">Inventory to watch <span>| 5 or fewer in stock</span></h3>
                <?php if ($dashboardAnalytics['low_stock']): ?>
                  <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                      <thead><tr><th>Product</th><th>Category</th><th>Available stock</th></tr></thead>
                      <tbody>
                      <?php foreach ($dashboardAnalytics['low_stock'] as $product): ?>
                        <tr class="analytics-stock-row">
                          <td>
                            <img class="analytics-product-image me-2" src="../<?= htmlspecialchars(productImageUrl($product['image_url']), ENT_QUOTES, 'UTF-8') ?>"
                                 alt="" width="52" height="46" loading="lazy">
                            <?= htmlspecialchars($product['productName'], ENT_QUOTES, 'UTF-8') ?>
                          </td>
                          <td><?= htmlspecialchars($product['product_category'] ?: 'Uncategorized', ENT_QUOTES, 'UTF-8') ?></td>
                          <td><span class="badge bg-<?= (int) $product['stock'] === 0 ? 'danger' : 'warning text-dark' ?>">
                            <?= (int) $product['stock'] === 0 ? 'Out of stock' : (int) $product['stock'] . ' left' ?>
                          </span></td>
                        </tr>
                      <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <div class="analytics-empty">No low-stock products. Inventory levels are above 5 units.</div>
                <?php endif; ?>
              </div>
            </section>
          </div>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-lg-8">
          <div class="card recent-sales overflow-auto">
            <div class="card-body">
              <h2 class="card-title">Recent orders <span>| newest first</span></h2>
              <?php if ($orderMessage): ?>
                <div class="alert alert-<?= htmlspecialchars($orderMessage['type'], ENT_QUOTES, 'UTF-8') ?>" role="alert">
                  <?= htmlspecialchars($orderMessage['text'], ENT_QUOTES, 'UTF-8') ?>
                </div>
              <?php endif; ?>
              <?php if ($dashboardOrders === false): ?>
                <div class="alert alert-danger" role="alert">Orders could not be loaded.</div>
              <?php elseif (!$dashboardOrders): ?>
                <p>No orders have been placed yet.</p>
              <?php else: ?>
                <table class="table table-hover align-middle">
                  <thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
                  <tbody>
                  <?php foreach ($dashboardOrders as $order): ?>
                    <tr>
                      <td><strong><?= htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($order['created_at'], ENT_QUOTES, 'UTF-8') ?></small></td>
                      <td><?= htmlspecialchars($order['recipient_name'], ENT_QUOTES, 'UTF-8') ?><br>
                        <small class="text-muted"><?= htmlspecialchars($order['phone'], ENT_QUOTES, 'UTF-8') ?></small></td>
                      <td><?= htmlspecialchars($order['products'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                      <td>$<?= number_format((float) $order['total_amount'], 2) ?></td>
                      <td><span class="badge bg-<?= $order['order_status'] === 'cancelled' ? 'danger' : ($order['order_status'] === 'delivered' ? 'success' : 'primary') ?>">
                        <?= htmlspecialchars(ucfirst($order['order_status']), ENT_QUOTES, 'UTF-8') ?></span></td>
                      <td class="text-nowrap">
                        <?php if (!empty($statusActions[$order['order_status']])): ?>
                          <?php foreach ($statusActions[$order['order_status']] as $nextStatus => $label): ?>
                            <form action="../?route=admin-order-action" method="post" class="d-inline"
                                  <?= $nextStatus === 'cancelled' ? 'onsubmit="return confirm(\'Cancel this order?\');"' : '' ?>>
                              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                              <input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>">
                              <input type="hidden" name="order_action" value="<?= htmlspecialchars($nextStatus, ENT_QUOTES, 'UTF-8') ?>">
                              <button type="submit" class="btn btn-sm <?= $nextStatus === 'cancelled' ? 'btn-outline-danger' : 'btn-outline-primary' ?>">
                                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                              </button>
                            </form>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <span class="text-muted">No actions</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              <?php endif; ?>
            </div>
          </div>

          <section class="card" aria-labelledby="admin-chat-title">
            <div class="card-body">
              <h2 class="card-title" id="admin-chat-title">Store assistant</h2>
              <div id="n8n-chat-card"><div id="n8n-chat"></div></div>
            </div>
          </section>
        </div>

        <div class="col-lg-4">
          <div class="card top-selling overflow-auto">
            <div class="card-body">
              <h2 class="card-title">Top selling products</h2>
              <?php if ($dashboardTopProducts === false): ?>
                <div class="alert alert-danger" role="alert">Product sales could not be loaded.</div>
              <?php elseif (!$dashboardTopProducts): ?>
                <p>No completed orders yet.</p>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-borderless">
                    <thead><tr><th>Product</th><th>Sold</th><th>Order value</th></tr></thead>
                    <tbody>
                    <?php foreach ($dashboardTopProducts as $product): ?>
                      <tr>
                        <td>
                          <img class="analytics-product-image me-2" src="../<?= htmlspecialchars(productImageUrl($product['image_url']), ENT_QUOTES, 'UTF-8') ?>"
                               alt="" width="52" height="46" loading="lazy">
                          <?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td><?= number_format((int) $product['quantity_sold']) ?></td>
                        <td>$<?= number_format((float) $product['total_value'], 2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
              <p class="text-muted small">Cancelled orders are excluded.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer id="footer" class="footer"><div class="copyright">&copy; Drone Store</div></footer>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center" aria-label="Back to top"><i class="bi bi-arrow-up-short"></i></a>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <?php if ($dashboardAnalytics !== false): ?>
    <script id="dashboard-analytics-data" type="application/json"><?= json_encode($dashboardAnalytics, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
  <?php endif; ?>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <?php if ($dashboardAnalytics !== false): ?>
    <script>
      (() => {
        const chartDataElement = document.getElementById('dashboard-analytics-data');
        const data = JSON.parse(chartDataElement.textContent);

        if (typeof Chart === 'undefined') {
          document.querySelectorAll('.analytics-chart').forEach((container) => {
            container.innerHTML = '<p class="analytics-empty">Analytics charts are unavailable. Reload the page to try again.</p>';
          });
          return;
        }

        const axisColor = '#8993a4';
        const gridColor = '#edf0f4';
        const currency = (value) => '$' + Number(value).toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });

        const salesCanvas = document.getElementById('sales-trend-chart');
        new Chart(salesCanvas, {
          type: 'bar',
          data: {
            labels: data.labels,
            datasets: [
              {
                type: 'bar',
                label: 'Orders',
                data: data.orders,
                yAxisID: 'orders',
                backgroundColor: 'rgba(65, 84, 241, 0.18)',
                borderColor: '#4154f1',
                borderWidth: 1,
                borderRadius: 4,
                maxBarThickness: 18
              },
              {
                type: 'line',
                label: 'Order value',
                data: data.order_values,
                yAxisID: 'money',
                borderColor: '#20b69e',
                backgroundColor: 'rgba(32, 182, 158, 0.12)',
                pointRadius: data.days === 90 ? 0 : 2,
                pointHoverRadius: 5,
                borderWidth: 2,
                tension: 0.3,
                fill: true
              },
              {
                type: 'line',
                label: 'Collected (delivered COD)',
                data: data.collected_revenue,
                yAxisID: 'money',
                borderColor: '#f69c4e',
                backgroundColor: 'rgba(246, 156, 78, 0.08)',
                pointRadius: data.days === 90 ? 0 : 2,
                pointHoverRadius: 5,
                borderWidth: 2,
                tension: 0.3,
                fill: false
              }
            ]
          },
          options: {
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
              legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
              tooltip: {
                callbacks: {
                  label(context) {
                    return context.dataset.yAxisID === 'money'
                      ? ' ' + context.dataset.label + ': ' + currency(context.parsed.y)
                      : ' ' + context.dataset.label + ': ' + context.parsed.y;
                  }
                }
              }
            },
            scales: {
              x: { grid: { display: false }, ticks: { color: axisColor, maxTicksLimit: data.days === 90 ? 15 : data.days } },
              money: {
                type: 'linear',
                position: 'left',
                beginAtZero: true,
                grid: { color: gridColor },
                ticks: { color: axisColor, callback: currency }
              },
              orders: {
                type: 'linear',
                position: 'right',
                beginAtZero: true,
                grid: { drawOnChartArea: false },
                ticks: { color: axisColor, precision: 0, stepSize: 1 }
              }
            }
          }
        });

        const statusCanvas = document.getElementById('order-status-chart');
        if (statusCanvas) {
          const statusNames = {
            pending: 'Pending',
            processing: 'Processing',
            shipped: 'Shipped',
            delivered: 'Delivered',
            cancelled: 'Cancelled'
          };
          const statusColors = ['#f5c451', '#4154f1', '#20b69e', '#2eca6a', '#f27171'];
          new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
              labels: Object.keys(statusNames).map((status) => statusNames[status]),
              datasets: [{
                data: Object.keys(statusNames).map((status) => data.statuses[status]),
                backgroundColor: statusColors,
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 6
              }]
            },
            options: {
              maintainAspectRatio: false,
              cutout: '65%',
              plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 18 } },
                tooltip: { callbacks: { label: (context) => ' ' + context.label + ': ' + context.parsed } }
              }
            }
          });
        }

        const categoryCanvas = document.getElementById('category-sales-chart');
        if (categoryCanvas) {
          new Chart(categoryCanvas, {
            type: 'bar',
            data: {
              labels: data.categories,
              datasets: [{
                label: 'Order value',
                data: data.category_values,
                backgroundColor: ['#4154f1', '#20b69e', '#f69c4e', '#9373ed', '#2eca6a', '#f27171', '#55a8db', '#b57a64'],
                borderRadius: 4,
                maxBarThickness: 38
              }]
            },
            options: {
              indexAxis: 'y',
              maintainAspectRatio: false,
              plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (context) => ' ' + currency(context.parsed.x) } }
              },
              scales: {
                x: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: axisColor, callback: currency } },
                y: { grid: { display: false }, ticks: { color: axisColor } }
              }
            }
          });
        }
      })();
    </script>
  <?php endif; ?>
  <script type="module">
    import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';
    createChat({
      webhookUrl: 'https://drone-store.app.n8n.cloud/webhook/99663231-e386-46ff-8e28-0fa8b4f8a1ee/chat',
      target: '#n8n-chat',
      mode: 'fullscreen',
      showWelcomeScreen: false,
      initialMessages: ['Hi! Ask me anything about our drones.'],
      i18n: { en: { title: 'Drone Store', subtitle: 'We are here to help', footer: '', getStarted: 'New Conversation', inputPlaceholder: 'Type your question...' } }
    });
  </script>
  <script src="assets/js/main.js?v=2"></script>
</body>
</html>
