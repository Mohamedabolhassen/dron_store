<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config/db.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($con) || !($con instanceof mysqli)) {
    throw new RuntimeException('The store database connection is unavailable.');
}

$products = [
    [
        'DS-DEMO-001',
        'aeroscout-4k-camera-drone',
        'AeroScout 4K Camera Drone',
        'Camera Drones',
        689.00,
        16,
        'Capture smooth aerial footage with a stabilized 4K camera, intelligent flight modes, and an easy-to-read live view.',
        'assets/img/product/yuhan-chang-Dw4CrTVc6ic-unsplash.jpg',
    ],
    [
        'DS-DEMO-002',
        'skyline-pro-folding-drone',
        'Skyline Pro Folding Drone',
        'Camera Drones',
        949.00,
        9,
        'A travel-ready camera drone with a folding frame, long-range controls, and steady cinematic flight.',
        'assets/img/product/tymek-niciak-WnEWPncYguE-unsplash.jpg',
    ],
    [
        'DS-DEMO-003',
        'racer-x-fpv-kit',
        'Racer X FPV Starter Kit',
        'FPV Drones',
        429.00,
        12,
        'A responsive FPV quadcopter and beginner-friendly setup for learning fast, immersive flight.',
        'assets/img/product/kaleb-kendall-6nRjHtBDk4o-unsplash.jpg',
    ],
    [
        'DS-DEMO-004',
        'microhawk-indoor-fpv',
        'MicroHawk Indoor FPV',
        'FPV Drones',
        189.00,
        24,
        'A compact, protected-frame flyer designed for indoor practice and confident first flights.',
        'assets/img/product/jesse-barker-TVgBDd_Xgg8-unsplash.jpg',
    ],
    [
        'DS-DEMO-005',
        'airlink-smart-controller',
        'AirLink Smart Controller',
        'Controllers',
        239.00,
        7,
        'A bright-screen controller with tactile controls and a clear, comfortable grip for longer sessions.',
        'assets/img/product/jason-mavrommatis-XYrjl3j7smo-unsplash.jpg',
    ],
    [
        'DS-DEMO-006',
        'flightpack-travel-kit',
        'FlightPack Travel Kit',
        'Accessories',
        119.00,
        31,
        'A protective travel case with organized storage for a drone, controller, batteries, and everyday essentials.',
        'assets/img/product/jared-brashier-duNHkmSkW6M-unsplash.jpg',
    ],
    [
        'DS-DEMO-007',
        'longview-spare-battery',
        'LongView Flight Battery',
        'Batteries',
        79.00,
        5,
        'A practical spare battery for extending compatible drone sessions between charging stops.',
        'assets/img/product/gustaf-von-zeipel-NsrkkaxBIQA-unsplash.jpg',
    ],
    [
        'DS-DEMO-008',
        'propeller-care-pack',
        'Propeller Care Pack',
        'Accessories',
        34.00,
        42,
        'A convenient replacement propeller set for routine maintenance and keeping compatible aircraft ready to fly.',
        'assets/img/product/alessio-soggetti-rSFxBGpnluw-unsplash.jpg',
    ],
];

$customers = [
    ['Ava Mitchell', 'ava.mitchell@demo.example.invalid', 'Cairo', '11835'],
    ['Mateo Rivera', 'mateo.rivera@demo.example.invalid', 'Giza', '12511'],
    ['Leila Hassan', 'leila.hassan@demo.example.invalid', 'Alexandria', '21500'],
    ['Oliver Bennett', 'oliver.bennett@demo.example.invalid', 'Cairo', '11765'],
    ['Noor El-Sayed', 'noor.elsayed@demo.example.invalid', 'Mansoura', '35511'],
    ['Daniel Brooks', 'daniel.brooks@demo.example.invalid', 'Giza', '12611'],
    ['Sofia Martinez', 'sofia.martinez@demo.example.invalid', 'Cairo', '11511'],
    ['Elias Nassar', 'elias.nassar@demo.example.invalid', 'Alexandria', '21615'],
    ['Priya Shah', 'priya.shah@demo.example.invalid', 'Tanta', '31512'],
    ['Marcus Reed', 'marcus.reed@demo.example.invalid', 'Cairo', '11371'],
    ['Hana Farouk', 'hana.farouk@demo.example.invalid', 'Ismailia', '41511'],
    ['Lucas Bennett', 'lucas.bennett@demo.example.invalid', 'Giza', '12588'],
];

$orderStatuses = [
    'delivered',
    'shipped',
    'delivered',
    'processing',
    'shipped',
    'pending',
    'delivered',
    'cancelled',
    'delivered',
    'shipped',
    'pending',
    'delivered',
];

$findId = static function ($table, $column, $value) use ($con) {
    $allowedColumns = [
        'users' => 'email',
        'products' => 'sku',
        'orders' => 'order_number',
    ];
    if (!isset($allowedColumns[$table]) || $allowedColumns[$table] !== $column) {
        throw new InvalidArgumentException('Unsupported demo-data lookup.');
    }

    $statement = mysqli_prepare($con, "SELECT id FROM `$table` WHERE `$column` = ? LIMIT 1");
    mysqli_stmt_bind_param($statement, 's', $value);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $id);
    $found = mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);

    return $found ? (int) $id : null;
};

$added = [
    'products' => 0,
    'customers' => 0,
    'orders' => 0,
];
$existing = [
    'products' => 0,
    'customers' => 0,
    'orders' => 0,
];

$con->begin_transaction();

try {
    $productIds = [];
    foreach ($products as $product) {
        [$sku, $slug, $name, $category, $price, $stock, $description, $image] = $product;
        $productId = $findId('products', 'sku', $sku);

        if ($productId === null) {
            $statement = mysqli_prepare(
                $con,
                'INSERT INTO products '
                . '(productName, slug, sku, short_description, Description, price, stock, product_category) '
                . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            mysqli_stmt_bind_param(
                $statement,
                'sssssdis',
                $name,
                $slug,
                $sku,
                $description,
                $description,
                $price,
                $stock,
                $category
            );
            mysqli_stmt_execute($statement);
            $productId = (int) mysqli_insert_id($con);
            mysqli_stmt_close($statement);
            $added['products']++;
        } else {
            $existing['products']++;
        }

        $imageStatement = mysqli_prepare(
            $con,
            'SELECT id FROM product_images WHERE product_id = ? AND is_primary = 1 LIMIT 1'
        );
        mysqli_stmt_bind_param($imageStatement, 'i', $productId);
        mysqli_stmt_execute($imageStatement);
        mysqli_stmt_store_result($imageStatement);
        $hasPrimaryImage = mysqli_stmt_num_rows($imageStatement) > 0;
        mysqli_stmt_close($imageStatement);

        if (!$hasPrimaryImage) {
            $imageInsert = mysqli_prepare(
                $con,
                'INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, 1)'
            );
            mysqli_stmt_bind_param($imageInsert, 'is', $productId, $image);
            mysqli_stmt_execute($imageInsert);
            mysqli_stmt_close($imageInsert);
        }

        $productIds[] = $productId;
    }

    $customerIds = [];
    foreach ($customers as $customer) {
        [$name, $email] = $customer;
        $userId = $findId('users', 'email', $email);

        if ($userId === null) {
            $randomPassword = bin2hex(random_bytes(32));
            $passwordHash = password_hash($randomPassword, PASSWORD_DEFAULT);
            $statement = mysqli_prepare(
                $con,
                'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
            );
            mysqli_stmt_bind_param($statement, 'sss', $name, $email, $passwordHash);
            mysqli_stmt_execute($statement);
            $userId = (int) mysqli_insert_id($con);
            mysqli_stmt_close($statement);
            $added['customers']++;
        } else {
            $existing['customers']++;
        }

        $customerIds[] = $userId;
    }

    foreach (range(1, 36) as $orderIndex) {
        $orderNumber = sprintf('DEMO-ORDER-%03d', $orderIndex);
        if ($findId('orders', 'order_number', $orderNumber) !== null) {
            $existing['orders']++;
            continue;
        }

        $customerIndex = (($orderIndex - 1) * 5) % count($customers);
        $productIndex = (($orderIndex - 1) * 3) % count($products);
        $customer = $customers[$customerIndex];
        $product = $products[$productIndex];
        $userId = $customerIds[$customerIndex];
        $productId = $productIds[$productIndex];
        $quantity = $orderIndex % 6 === 0 ? 2 : 1;
        $unitPrice = (float) $product[4];
        $subtotal = $unitPrice * $quantity;
        $shippingCost = 0.00;
        $total = $subtotal + $shippingCost;
        $status = $orderStatuses[($orderIndex - 1) % count($orderStatuses)];
        $paymentStatus = $status === 'delivered' ? 'paid' : 'pending';
        $ageDays = 90 - (int) floor(($orderIndex - 1) * 90 / 35);
        $orderTimestamp = time() - ($ageDays * 86400) - 3600 - (($orderIndex % 3) * 3600);
        $createdAt = date('Y-m-d H:i:s', $orderTimestamp);
        $trackingCode = sprintf('DEMO-TRACK-%03d', $orderIndex);
        $phone = sprintf('000000000%02d', $customerIndex + 1);
        $streetAddress = sprintf('Demo Street %d', 10 + $customerIndex);
        $postalCode = $customer[3];

        $addressStatement = mysqli_prepare(
            $con,
            'INSERT INTO shipping_addresses '
            . '(user_id, recipient_name, phone, country, state, city, street_address, address_line2, postal_code) '
            . "VALUES (?, ?, ?, 'Egypt', 'Demo Region', ?, ?, '', ?)"
        );
        mysqli_stmt_bind_param(
            $addressStatement,
            'isssss',
            $userId,
            $customer[0],
            $phone,
            $customer[2],
            $streetAddress,
            $postalCode
        );
        mysqli_stmt_execute($addressStatement);
        $addressId = (int) mysqli_insert_id($con);
        mysqli_stmt_close($addressStatement);

        $orderStatement = mysqli_prepare(
            $con,
            'INSERT INTO orders '
            . '(user_id, shipping_address_id, order_number, tracking_code, subtotal, shipping_cost, total_amount, order_status, payment_status, created_at) '
            . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param(
            $orderStatement,
            'iissdddsss',
            $userId,
            $addressId,
            $orderNumber,
            $trackingCode,
            $subtotal,
            $shippingCost,
            $total,
            $status,
            $paymentStatus,
            $createdAt
        );
        mysqli_stmt_execute($orderStatement);
        $orderId = (int) mysqli_insert_id($con);
        mysqli_stmt_close($orderStatement);

        $itemName = $product[2];
        $lineTotal = $unitPrice * $quantity;
        $itemStatement = mysqli_prepare(
            $con,
            'INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, line_total) '
            . 'VALUES (?, ?, ?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param(
            $itemStatement,
            'iisidd',
            $orderId,
            $productId,
            $itemName,
            $quantity,
            $unitPrice,
            $lineTotal
        );
        mysqli_stmt_execute($itemStatement);
        mysqli_stmt_close($itemStatement);

        $paymentMethod = 'cash_on_delivery';
        $paymentStatement = mysqli_prepare(
            $con,
            'INSERT INTO payments (order_id, payment_method, amount, status, created_at) VALUES (?, ?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param(
            $paymentStatement,
            'isdss',
            $orderId,
            $paymentMethod,
            $total,
            $paymentStatus,
            $createdAt
        );
        mysqli_stmt_execute($paymentStatement);
        mysqli_stmt_close($paymentStatement);

        $trackingSteps = [
            'pending' => ['Order placed'],
            'processing' => ['Order placed', 'Processing'],
            'shipped' => ['Order placed', 'Processing', 'Shipped'],
            'delivered' => ['Order placed', 'Processing', 'Shipped', 'Delivered'],
            'cancelled' => ['Order placed', 'Cancelled'],
        ];
        $trackingNotes = [
            'Order placed' => 'Demo order created with sample store data.',
            'Processing' => 'The demo order is being prepared.',
            'Shipped' => 'The demo order is on its way.',
            'Delivered' => 'The demo order was delivered.',
            'Cancelled' => 'The demo order was cancelled.',
        ];
        $steps = $trackingSteps[$status];
        foreach ($steps as $stepIndex => $step) {
            $loggedAt = date(
                'Y-m-d H:i:s',
                min($orderTimestamp + ($stepIndex * 7200), time() - 60)
            );
            $location = $customer[2] . ', Egypt';
            $notes = $trackingNotes[$step];
            $trackingStatement = mysqli_prepare(
                $con,
                'INSERT INTO tracking_logs (order_id, status_step, location, notes, logged_at) '
                . 'VALUES (?, ?, ?, ?, ?)'
            );
            mysqli_stmt_bind_param(
                $trackingStatement,
                'issss',
                $orderId,
                $step,
                $location,
                $notes,
                $loggedAt
            );
            mysqli_stmt_execute($trackingStatement);
            mysqli_stmt_close($trackingStatement);
        }

        $added['orders']++;
    }

    $con->commit();
} catch (Throwable $exception) {
    $con->rollback();
    throw $exception;
}

echo 'Demo data added: '
    . $added['products'] . ' products, '
    . $added['customers'] . ' fictional customers, '
    . $added['orders'] . ' orders.' . PHP_EOL;
echo 'Existing demo records left unchanged: '
    . $existing['products'] . ' products, '
    . $existing['customers'] . ' customers, '
    . $existing['orders'] . ' orders.' . PHP_EOL;
echo 'Demo emails use the reserved .invalid domain; generated passwords are random and are not shared.' . PHP_EOL;
