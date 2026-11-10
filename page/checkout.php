<?php
require __DIR__ . '/common.php';
$email = require_login();
require __DIR__ . '/db_search.php';

$result = $conn_search->execute_query(
    'SELECT products.*, cart.quantity FROM cart JOIN products ON cart.product_id = products.id
     WHERE cart.user_email = ? ORDER BY cart.id',
    [$email]
);

if (!$result->num_rows) {
    header('Location: cart.php');
    exit;
}

$subtotal = 0;
$deliveryFee = DELIVERY_FEE;
$checkoutToken = $_SESSION['checkout_token'] ??= bin2hex(random_bytes(32));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Checkout</title>

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

.checkout-page{
    width:1100px;
    margin:40px auto;
}

.checkout-title{
    color:#8c1313;
    font-size:38px;
    margin-bottom:30px;
}

.checkout-layout{
    display:flex;
    gap:30px;
}

.checkout-form,
.order-summary{
    background:white;
    border-radius:25px;
    padding:35px;
    box-shadow:0 10px 25px rgba(0,0,0,0.10);
}

.checkout-form{
    width:650px;
}

.order-summary{
    width:350px;
    height:fit-content;
}

.form-title,
.summary-title{
    color:#8c1313;
    margin-bottom:25px;
}

.input-group{
    margin-bottom:18px;
}

.input-group label{
    display:block;
    margin-bottom:7px;
    color:#8c1313;
    font-size:14px;
    font-weight:bold;
}

.input-group input,
.input-group textarea,
.input-group select{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:12px;
    outline:none;
    background:#f8efe4;
    color:#595353;
}

.input-group textarea{
    height:90px;
    resize:none;
}

.payment-methods{
    display:flex;
    gap:15px;
    margin-top:10px;
}

.payment-option{
    flex:1;
    background:#f8efe4;
    border:1px solid #ddd;
    border-radius:15px;
    padding:15px;
    text-align:center;
    color:#8c1313;
    font-weight:bold;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    margin:15px 0;
    font-size:15px;
}

.summary-product{
    border-bottom:1px solid #eee;
    padding:12px 0;
}

.summary-product-name{
    color:#8c1313;
    font-weight:bold;
}

.summary-total{
    border-top:1px solid #ddd;
    margin-top:20px;
    padding-top:20px;
    font-size:20px;
    font-weight:bold;
    color:#8c1313;
}

.place-order-btn{
    margin-top:30px;
    width:100%;
    background:#8c1313;
    color:#f8efe4;
    border:none;
    border-radius:15px;
    padding:15px;
    font-weight:bold;
    cursor:pointer;
}

.place-order-btn:hover{
    background:#595353;
}

.empty{
    color:#8c1313;
    font-size:18px;
}
.back-btn{
    display:inline-block;
    margin-top:60px;
    margin-left:170px;
    padding:10px 20px;

    background:rgba(247, 243, 241);
    color:rgba(123, 122, 121);

    text-decoration:none;
    border-radius:12px;
    

    font-weight:bold;
    transition:0.3s;
}

.back-btn:hover{
    background:rgba(123, 122, 121);
    color:rgba(246, 241, 235);;
}

@media (max-width: 1150px) {
    .back-btn { margin-left: 16px; }
    .checkout-page { width: auto; margin: 25px 16px; }
    .checkout-layout { flex-wrap: wrap; }
    .checkout-form, .order-summary { width: 100%; box-sizing: border-box; }
}
</style>
</head>

<body>

<a href="cart.php" class="back-btn">← Back to Cart</a>

<div class="checkout-page">

    <h1 class="checkout-title">CHECKOUT</h1>

    <div class="checkout-layout">

        <div class="checkout-form">

            <h2 class="form-title">Billing Details</h2>

            <form method="POST" action="place_order.php">
                    <?= csrf_field() ?>
                <input type="hidden" name="checkout_token" value="<?= escape($checkoutToken) ?>">

                <div class="input-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name"
                           value="<?= escape($_SESSION['user_name']) ?>" maxlength="100"
                           required>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= escape($email) ?>" readonly>
                </div>

                <div class="input-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" maxlength="30" required>
                </div>

                <div class="input-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" maxlength="500" required></textarea>
                </div>

                <div class="input-group">
                    <label>Payment Method (demo)</label>

                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="Cash" checked>
                            Cash
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="Card">
                            Card
                        </label>
                    </div>
                </div>

                <button class="place-order-btn" type="submit">
                    Place Order
                </button>

            </form>

        </div>

        <div class="order-summary">

            <h2 class="summary-title">Order Summary</h2>

            <?php if ($result && $result->num_rows > 0) { ?>

                <?php while($product = $result->fetch_assoc()) {
                    $rowTotal = $product['price'] * $product['quantity'];
                    $subtotal += $rowTotal;
                ?>

                    <div class="summary-product">
                        <div class="summary-product-name">
                            <?= escape($product['name']) ?>
                        </div>

                        <div class="summary-row">
                            <span>Qty: <?= escape($product['quantity']) ?></span>
                            <strong>$<?php echo $rowTotal; ?></strong>
                        </div>
                    </div>

                <?php } ?>

                <div class="summary-row">
                    <span>Sub Total</span>
                    <strong>$<?php echo $subtotal; ?></strong>
                </div>

                <div class="summary-row">
                    <span>Delivery Fee</span>
                    <strong>$<?php echo $deliveryFee; ?></strong>
                </div>

                <?php $discount = order_discount($subtotal); ?>
                <?php if ($discount): ?>
                    <div class="summary-row"><span>Discount (10%)</span><strong>−$<?= money($discount) ?></strong></div>
                <?php endif; ?>

                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>$<?= money($subtotal + $deliveryFee - $discount) ?></span>
                </div>

            <?php } else { ?>

                <p class="empty">Your cart is empty.</p>

            <?php } ?>

        </div>

    </div>

</div>

</body>
</html>