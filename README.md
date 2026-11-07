# Drophut Drone Store


A procedural PHP and MySQL e-commerce project for a drone shop. The customer flow supports product browsing, accounts, carts, wishlists, cash-on-delivery orders, and order tracking. The admin area provides account access and product management.

## Features

- Database-backed product listing and details
- Customer registration, sign-in, and sign-out, with hashed passwords
- Guest carts that merge into a customer's cart when they sign in
- Signed-in wishlists
- Stock validation and transactional cash-on-delivery (COD) checkout
- Customer order history and private tracking lookup
- Admin sign-in and first-admin registration
- Admin product create, edit, and delete actions
- Admin product image uploads stored under `assets/img/uploads/products/` (JPEG, PNG, GIF, or WebP; maximum 5 MB)
- Responsive storefront theme and shared header/footer

Online card or PayPal payments are not connected. Password-reset email delivery is also not configured; the reset page explains this rather than accepting a request it cannot process. Product variants are not included in the active cart/checkout flow.

The blog and informational pages use the existing theme content and are not database-managed. Admin template pages deleted in the current worktree were left untouched.

## Requirements


- MySQL or MariaDB, with InnoDB support
- PHP `mysqli` extension and `mysqlnd` (for `mysqli_stmt_get_result`)
- Apache (for example, Laragon, XAMPP, or WAMP) or PHP's built-in development server

## Run locally

1. Put the project in your web server's document root.
2. Create and populate the database by importing [`query.sql`](query.sql) into MySQL/MariaDB. The dump creates/uses `dropuhut_ecommerce` and defines the tables used by the app.
3. Configure the database host, username, password, and database name in [`config/DbClasses.php`](config/DbClasses.php). The development defaults are `localhost`, `root`, an empty password, and `dropuhut_ecommerce`; change them to match your local setup. The storefront and admin use this shared connection.
4. Open the site through Apache, for example `http://localhost/dron_store/`.

Alternatively, run PHP's development server in the project directory:

```bash
php -S localhost:8000
```

Then open `http://localhost:8000/`.

The imported SQL includes an `admins` table. Visit `?route=admin-pages-register` to create the first administrator; registration closes once an admin account exists. Then sign in at `?route=admin-pages-login`.

## Important routes

All routes use `index.php` and the `route` query parameter.

| Page | URL |
| --- | --- |
| Home | `?route=home` |
| Product catalog | `?route=products` |
| Product details | `?route=product-details&id=PRODUCT_ID` |
| Cart | `?route=cart` |
| Checkout | `?route=checkout` |
| Wishlist | `?route=wishlist` |
| Sign in / Register | `?route=login` / `?route=register` |
| Customer account and orders | `?route=my-account` |
| Order tracking | `?route=tracking` |
| Admin sign-in | `?route=admin-pages-login` |
| Admin product management | `?route=admin-forms-elements` |

Checkout requires a signed-in customer. COD is the only supported payment method; payment is not collected online.

## n8n customer-support knowledge

[`n8n/store-knowledge.json`](n8n/store-knowledge.json) contains customer-facing answers based on confirmed store behavior. In n8n, load this JSON into the AI Agent's context (or read it with a file node if n8n can access the project directory). For n8n Cloud or an isolated deployment, provide the JSON through a URL or another shared source instead. The file is static: use an authenticated live store lookup for current product prices, stock, and private order details. Do not expose order data without verifying the customer, and do not invent policies or contact information that are marked as unconfirmed.

## Project structure

```text
.
├── admin/
│   ├── controllers/  Admin route handling and product actions
│   ├── models/       Procedural database queries for admins and products
│   └── views/        Admin pages and templates
├── config/           Shared procedural MySQLi connection and storefront helpers
├── controllers/      Registration, login, cart, wishlist, and checkout actions
├── inc/              Shared storefront header and footer
├── views/             Customer-facing pages
├── query.sql          MySQL schema
└── index.php          Route dispatcher
```

## Understanding the project

Study the project in this order:

1. **Routing:** Start with [`index.php`](index.php). It selects a view or delegates a form action based on `route`.
2. **Database connection and helpers:** Read [`config/DbClasses.php`](config/DbClasses.php) and [`config/storefront_db.php`](config/storefront_db.php). They provide one procedural MySQLi connection and shared functions for prepared statements, escaping, carts, CSRF, and messages.
3. **Accounts:** Follow the registration form in [`views/register.php`](views/register.php) to [`controllers/userreg.php`](controllers/userreg.php), then the login form in [`views/login.php`](views/login.php) to [`controllers/usersvaild.php`](controllers/usersvaild.php). The controller validates input, uses prepared SQL, and stores the signed-in customer ID in the session.
4. **Catalog and cart:** Read [`views/products.php`](views/products.php) and [`views/product-details.php`](views/product-details.php), then the cart page [`views/cart.php`](views/cart.php) and its POST handler [`controllers/storefront_actions.php`](controllers/storefront_actions.php). Forms send an action and product/item ID; the handler checks CSRF and validates stock before updating the database.
5. **Checkout and order tracking:** Follow [`views/checkout.php`](views/checkout.php) to [`controllers/checkout.php`](controllers/checkout.php). The controller locks/validates cart stock, then creates the address, order, order items, COD payment, and tracking log in one transaction. [`views/my-account.php`](views/my-account.php) and [`views/tracking.php`](views/tracking.php) read only the signed-in customer's orders.
6. **Admin products:** Start at the admin routes in [`admin/controllers/AdminController.php`](admin/controllers/AdminController.php), then follow the procedural validation in [`admin/controllers/ProductController.php`](admin/controllers/ProductController.php), database queries in [`admin/models/Product.php`](admin/models/Product.php), and form/list view in [`admin/views/pages/storeadmin.php`](admin/views/pages/storeadmin.php).
7. **Database relationships:** Review [`query.sql`](query.sql) alongside the code. `products` feed cart items and order items; `carts` belong to a user or guest session; orders connect a customer, shipping address, items, payment, and tracking logs.

When explaining a request to the team, trace it in this order: **form → route → controller validation → prepared database query/transaction → redirect or rendered view**. This is the main request pattern used throughout the procedural code.

## Shared task checklist

Open `tasks.html` through the same PHP-hosted project URL on every team computer (for example, `http://localhost/dron_store/tasks.html` on a shared development server). The checklist saves progress in the configured MySQL database through `tasks-progress.php`; the endpoint creates its `project_task_progress` table on first use. Opening the HTML file directly with `file://`, or using separate local project/database copies, will not share progress.
