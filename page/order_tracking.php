<?php
require __DIR__ . '/common.php';
$email = require_login();
require __DIR__ . '/db_search.php';

if (isset($_GET['order_id'])) {
    $orderId = positive_id($_GET['order_id']);
    $orderResult = $conn_search->execute_query(
        'SELECT * FROM orders WHERE id = ? AND user_email = ?',
        [$orderId, $email]
    );
} else {
    $orderResult = $conn_search->execute_query(
        'SELECT * FROM orders WHERE user_email = ? ORDER BY id DESC LIMIT 1',
        [$email]
    );
}

$order = $orderResult->fetch_assoc();
if (!$order) {
    http_response_code(404);
    exit('No order found. <a href="menu.php">Back to the menu</a>');
}

$itemsResult = $conn_search->execute_query('SELECT * FROM order_items WHERE order_id = ? ORDER BY id', [$order['id']]);
$status = $order['order_status'];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Tracking</title>

<style>
body{
    margin:0;
    background:rgb(246,243,240);
    font-family:Arial, sans-serif;
    color:rgba(74,70,70,0.9);
}

.back-btn{
    display:inline-block;
    margin:35px 0 0 90px;
    color:#8c1313;
    text-decoration:none;
    font-weight:bold;
}

.tracking-page{
    width:1000px;
    margin:40px auto;
}

.tracking-title{
    color:#8c1313;
    font-size:38px;
    margin-bottom:30px;
}

.tracking-card{
    background:white;
    border-radius:25px;
    padding:35px;
    box-shadow:0 10px 25px rgba(0,0,0,0.10);
}

.order-number{
    color:#8c1313;
    font-size:26px;
    font-weight:bold;
}

.status{
    margin-top:10px;
    font-size:18px;
    color:rgba(74,70,70,0.68);
}

.info-row{
    display:flex;
    justify-content:space-between;
    margin-top:18px;
    color:rgba(74,70,70,0.8);
}

.timeline{
    margin-top:40px;
}

.step{
    display:flex;
    align-items:center;
    margin-bottom:25px;
    color:#999;
    font-size:17px;
}

.dot{
    width:18px;
    height:18px;
    border-radius:50%;
    border:3px solid #d8caca;
    margin-right:15px;
}

.step.active{
    color:#8c1313;
    font-weight:bold;
}

.step.active .dot{
    background:#8c1313;
    border-color:#8c1313;
}

.items-box{
    margin-top:35px;
    background:#f8efe4;
    border-radius:18px;
    padding:20px;
}

.item-row{
    display:flex;
    justify-content:space-between;
    margin-bottom:12px;
    color:#8c1313;
}

.delivery-box{
    margin-top:25px;
    background:#f8efe4;
    color:#8c1313;
    padding:18px;
    border-radius:18px;
    font-weight:bold;
}

.home-btn{
    display:inline-block;
    margin-top:30px;
    background:#8c1313;
    color:#f8efe4;
    text-decoration:none;
    padding:12px 25px;
    border-radius:12px;
}

.home-btn:hover{
    background:#595353;
}

@media (max-width: 1050px) {
    .back-btn { margin-left: 16px; }
    .tracking-page { width: auto; margin: 25px 16px; }
    .tracking-card { padding: 20px; }
    .info-row, .item-row { flex-wrap: wrap; gap: 8px; }
}
</style>
</head>

<body>

<a href="../video/mainpage.php" class="back-btn">← Back to Home</a>

<div class="tracking-page">

    <h1 class="tracking-title">ORDER TRACKING</h1>

    <div class="tracking-card">

        <div class="order-number">
            Order #<?= escape($order['id']) ?>
        </div>

        <div class="status">
            Status: <?= escape(ucfirst($order['order_status'])) ?>
        </div>

        <div class="info-row">
            <span>Customer:</span>
            <strong><?= escape($order['full_name']) ?></strong>
        </div>

        <div class="info-row">
            <span>Email:</span>
            <strong><?= escape($order['user_email']) ?></strong>
        </div>

        <div class="info-row">
            <span>Payment:</span>
            <strong><?= escape($order['payment_method']) ?> / <?= escape($order['payment_status']) ?></strong>
        </div>

        <div class="timeline">

            <div class="step <?php if(in_array($status, ['received','preparing','baking','delivery','delivered'])) echo 'active'; ?>">
                <span class="dot"></span>
                Order Received
            </div>

            <div class="step <?php if(in_array($status, ['preparing','baking','delivery','delivered'])) echo 'active'; ?>">
                <span class="dot"></span>
                Preparing
            </div>

            <div class="step <?php if(in_array($status, ['baking','delivery','delivered'])) echo 'active'; ?>">
                <span class="dot"></span>
                Baking
            </div>

            <div class="step <?php if(in_array($status, ['delivery','delivered'])) echo 'active'; ?>">
                <span class="dot"></span>
                Out for Delivery
            </div>

            <div class="step <?php if($status == 'delivered') echo 'active'; ?>">
                <span class="dot"></span>
                Delivered
            </div>

        </div>

        <div class="items-box">
            <h3 style="color:#8c1313;">Order Items</h3>

            <?php while($item = $itemsResult->fetch_assoc()) { ?>
                <div class="item-row">
                    <span><?= escape($item['product_name']) ?> × <?= escape($item['quantity']) ?></span>
                    <strong>$<?= escape($item['total']) ?></strong>
                </div>
            <?php } ?>
        </div>

        <div class="delivery-box">
            Order Total: $<?= escape($order['total']) ?>
        </div>

        <a href="../page/menu.php" class="home-btn">Continue Shopping</a>

    </div>

</div>

</body>
</html>