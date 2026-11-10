<?php


include "../../config/connect.php" ;

 $sql = " SELECT o.id AS order_id, o.order_number, o.total_amount, o.order_status,
       sa.recipient_name, sa.phone, sa.city, sa.street_address,
       oi.product_name_snap, oi.quantity, oi.unit_price, oi.subtotal
FROM orders o
JOIN shipping_addresses sa ON sa.id = o.shipping_address_id
JOIN order_items oi        ON oi.order_id = o.id
ORDER BY o.created_at DESC " ;
 
$result = mysqli_query($con, $sql);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);   


foreach ($orders as $order) {

echo htmlspecialchars($order['order_number']);


}













?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>طلبات التوصيل</title>
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f5f5;
            color: #222;
            margin: 0;
            padding: 15px;
        }

        .container { max-width: 600px; margin: 0 auto; }

        h2 { margin: 0 0 15px 0; }

        .msg {
            background: #e7f6e7;
            border: 1px solid #99cc99;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .card h3 { margin: 0 0 10px 0; }
        .card p  { margin: 5px 0; }
        .card ul { margin: 5px 0 10px 0; padding-right: 20px; }

        .badge {
            display: inline-block;
            background: #fff3cd;
            color: #856404;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 13px;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 15px 0;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
        }

        .check input { width: 20px; height: 20px; }

        button {
            width: 100%;
            background: #2a7d2a;
            color: #fff;
            border: 0;
            padding: 12px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover { background: #226622; }
    </style>
</head>
<body>

<div class="container">

    <h2>طلبات التوصيل</h2>



    <!-- كارت أوردر (كرر الكارت ده لكل أوردر) -->
<?php  foreach ($orders as $order) {?>
    <div class="card">
        <h3>أوردر <?php echo htmlspecialchars($order['order_number']); ?> <span class="badge">في الشحن</span></h3>

        <p><strong>المستلم:</strong> <?php echo htmlspecialchars($order['recipient_name']); ?></p>
        <p><strong>التليفون:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
        <p><strong>العنوان:</strong> <?php echo htmlspecialchars($order['street_address']); ?></p>

        <p><strong>المنتجات:</strong><?php echo htmlspecialchars($order['product_name_snap']); ?></p>
        <ul>
            <li>منتج أول × 2</li>
            <li>منتج تاني × 1</li>
        </ul>

        <p><strong>الإجمالي:</strong> 850 (عند الاستلام)</p>

        <form method="POST">
            <label class="check">
                <input type="checkbox" name="delivered">
                تم التوصيل
            </label>
            <button type="submit">تأكيد</button>
        </form>
    </div>
<?php }?>
   

</div>

</body>
</html>