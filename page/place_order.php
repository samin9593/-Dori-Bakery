<?php
require __DIR__ . '/common.php';
$email = require_login();
require __DIR__ . '/db_search.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post();

    $checkoutToken = post_text('checkout_token');
    if (!isset($_SESSION['checkout_token']) || !hash_equals($_SESSION['checkout_token'], $checkoutToken)) {
        header('Location: cart.php', true, 303);
        exit;
    }

    $name = post_text('full_name');
    $phone = post_text('phone');
    $address = post_text('address');
    $payment = post_text('payment_method');

    if ($name === '' || mb_strlen($name) > 100 || !valid_phone($phone, 30)
        || $address === '' || mb_strlen($address) > 500 || !in_array($payment, ['Cash', 'Card'], true)) {
        http_response_code(422);
        exit('Enter valid billing details. <a href="checkout.php">Back to checkout</a>');
    }

    $conn_search->begin_transaction();
    try {
        $cart = $conn_search->execute_query(
            'SELECT cart.id AS cart_id, cart.product_id, cart.quantity, products.name, products.price
             FROM cart JOIN products ON cart.product_id = products.id
             WHERE cart.user_email = ? ORDER BY cart.id FOR UPDATE',
            [$email]
        )->fetch_all(MYSQLI_ASSOC);

        if (!$cart) {
            $conn_search->rollback();
            header('Location: cart.php', true, 303);
            exit;
        }

        $subtotal = 0;
        foreach ($cart as $product) {
            if ($product['quantity'] < 1 || $product['quantity'] > MAX_CART_QUANTITY || $product['price'] < 0) {
                $conn_search->rollback();
                http_response_code(422);
                exit('An item has an invalid price or quantity. <a href="cart.php">Back to the cart</a>');
            }
            $subtotal += $product['price'] * $product['quantity'];
        }

        $conn_search->execute_query(
            "INSERT INTO orders
             (user_email, full_name, phone, address, subtotal, delivery_fee, total, payment_method, payment_status, order_status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'simulated', 'received')",
            [$email, $name, $phone, $address, $subtotal, DELIVERY_FEE, money($subtotal + DELIVERY_FEE - order_discount($subtotal)), $payment]
        );
        $order_id = $conn_search->insert_id;

        foreach ($cart as $product) {
            $conn_search->execute_query(
                'INSERT INTO order_items (order_id, product_id, product_name, price, quantity, total)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [$order_id, $product['product_id'], $product['name'], $product['price'], $product['quantity'],
                 $product['price'] * $product['quantity']]
            );
            $conn_search->execute_query('DELETE FROM cart WHERE id = ? AND user_email = ?', [$product['cart_id'], $email]);
        }
        $conn_search->commit();
    } catch (mysqli_sql_exception $exception) {
        $conn_search->rollback();
        error_log($exception->getMessage());
        http_response_code(503);
        exit('The order could not be saved. <a href="checkout.php">Back to checkout</a>');
    }

    unset($_SESSION['checkout_token'], $_SESSION['discount_applied']);
    header('Location: place_order.php?order_id=' . $order_id, true, 303);
    exit;
}

$order_id = positive_id($_GET['order_id'] ?? null);
$order = $conn_search->execute_query('SELECT id FROM orders WHERE id = ? AND user_email = ?', [$order_id, $email]);
if (!$order->num_rows) {
    http_response_code(404);
    exit('No order found. <a href="menu.php">Back to the menu</a>');
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Confirmed</title>

<style>
body{
    background:rgb(246,243,240);
    font-family:Arial,sans-serif;
}

.confirm-box{
    width:700px;
    margin:120px auto;
    background:white;
    border-radius:30px;
    padding:50px;
    text-align:center;
    box-shadow:0 10px 25px rgba(0,0,0,.1);
}

.check{
    width:100px;
    height:100px;
    margin:auto;
    border-radius:50%;
    background:#8c1313;
    color:white;
    font-size:50px;
    line-height:100px;
}

.title{
    margin-top:25px;
    color:#8c1313;
    font-size:40px;
    font-weight:bold;
}

.text{
    margin-top:15px;
    color:#666;
    font-size:18px;
}

.order-id{
    margin-top:25px;
    background:#f8efe4;
    padding:15px;
    border-radius:15px;
    color:#8c1313;
    font-weight:bold;
}

.buttons{
    margin-top:35px;
}

.btn{
    display:inline-block;
    text-decoration:none;
    padding:14px 30px;
    border-radius:15px;
    margin:0 10px;
    font-weight:bold;
}

.track{
    background:#8c1313;
    color:white;
}

.menu{
    background:#f8efe4;
    color:#8c1313;
}

@media (max-width: 700px) {
    .confirm-box { width: auto; margin: 30px 16px; padding: 25px; }
    .buttons { flex-wrap: wrap; }
}
</style>
</head>

<body>

<div class="confirm-box">

    <div class="check">✓</div>

    <div class="title">Order Confirmed</div>

    <div class="text">
        Thank you for your order.<br>
        Your demo order has been received.
    </div>

    <div class="order-id">
        Order #<?php echo $order_id; ?>
    </div>

    <div class="buttons">
        <a href="order_tracking.php?order_id=<?php echo $order_id; ?>" class="btn track">
            Track Order
        </a>

        <a href="menu.php" class="btn menu">
            Continue Shopping
        </a>
    </div>

</div>

</body>
</html>
