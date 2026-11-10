<?php
require __DIR__ . '/common.php';
$email = require_login();
require __DIR__ . '/db_search.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post();
    if (isset($_POST['apply_discount'])) {
        $code = post_text('discount_code');
        $_SESSION['discount_applied'] = $code === DISCOUNT_CODE;
        if ($code !== '' && !$_SESSION['discount_applied']) {
            $_SESSION['discount_error'] = 'Invalid discount code.';
        }
        header('Location: cart.php', true, 303);
        exit;
    }

    $productId = positive_id($_POST['product_id'] ?? null);
    $action = post_text('action');

    if (!$productId || !in_array($action, ['plus', 'minus', 'delete'], true)) {
        http_response_code(400);
        exit('Invalid cart action.');
    }

    if ($action === 'plus') {
        $conn_search->execute_query(
            'UPDATE cart SET quantity = quantity + 1 WHERE user_email = ? AND product_id = ? AND quantity < ?',
            [$email, $productId, MAX_CART_QUANTITY]
        );
    } elseif ($action === 'minus') {
        $conn_search->execute_query(
            'UPDATE cart SET quantity = quantity - 1 WHERE user_email = ? AND product_id = ? AND quantity > 1',
            [$email, $productId]
        );
    } else {
        $conn_search->execute_query('DELETE FROM cart WHERE user_email = ? AND product_id = ?', [$email, $productId]);
    }

    header('Location: cart.php', true, 303);
    exit;
}

$result = $conn_search->execute_query(
    'SELECT products.*, cart.quantity FROM cart JOIN products ON cart.product_id = products.id
     WHERE cart.user_email = ? ORDER BY cart.id',
    [$email]
);
$subtotal = 0;
$hasItems = $result->num_rows > 0;
if (!$hasItems) {
    unset($_SESSION['discount_applied']);
}
$discountError = $_SESSION['discount_error'] ?? '';
unset($_SESSION['discount_error']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Shopping Cart</title>

<style>
body{
    background: rgb(246,243,240);
    font-family: Arial, sans-serif;
    color: #222;
}

.cart-page{
    width: 1100px;
    margin: 50px auto;
}

h1{
    color : #8c1313;
    font-size: 42px;
    margin-bottom: 35px;
}

.cart-layout{
    display: flex;
    gap: 30px;
}

.cart-table{
    width: 760px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 20px;
}

.cart-header,
.cart-row{
    display: grid;
    grid-template-columns: 1.8fr 1fr 1fr 0.5fr;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #ddd;
}

.cart-header{
    font-size: 13px;
    font-weight: bold;
    color : #8c1313;
}

.product-info{
    display: flex;
    align-items: center;
    gap: 15px;
}

.product-info img{
    width: 85px;
    height: 85px;
    border-radius: 15px;
    object-fit: cover;
}

.product-name{
    font-size: 18px;
    font-weight: bold;
    color: #777;
}

.product-desc{
    font-size: 13px;
    color: #777;
    margin-top: 6px;
}

.qty-box{
    color : #8c1313;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 85px;
    border: 1px solid #8c1313;
    border-radius: 20px;
    padding: 5px 8px;
    gap: 12px;
}

.qty-box button{
    border: none;
    background: transparent;
    font-size: 16px;
    cursor: pointer;
    color : #8c1313;
}

.total-price{
    font-size: 16px;
    font-weight: bold;
    color : #8c1313;
}

.delete-btn{
    border:none;
    background:transparent;
    cursor:pointer;
    color:#8c1313;
    font-size:18px;
}

.summary{
    width: 300px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 25px;
    height: fit-content;
    color: #777;
}

.summary h3{
    margin-bottom: 25px;
    color : #8c1313;
}

.summary-row{
    display: flex;
    justify-content: space-between;
    margin: 13px 0;
    font-size: 14px;
}

.summary-total{
    border-top: 1px solid #ddd;
    margin-top: 20px;
    padding-top: 20px;
    font-size: 20px;
    font-weight: bold;
    color : #8c1313;
}

.checkout-btn{
    display:block;
    text-align:center;
    text-decoration:none;
    margin-top:30px;
    width:250px;
    background:#606060;
    color:white;
    border:none;
    border-radius:25px;
    padding:14px;
    font-weight:bold;
    cursor:pointer;
}
.checkout-btn:hover{
    background:rgba(123, 122, 121);
    color:rgba(246, 241, 235);;
}
.empty{
    color:#8c1313;
    font-size:20px;
    padding:30px;
}
.back-btn{
    display:inline-block;
    margin-bottom:25px;
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

.discount-box{
    display:flex;
    gap:8px;
    margin-top:20px;
}

.discount-box input{
    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:20px;
    outline:none;
}

.discount-box button{
    padding:10px 18px;
    border:none;
    border-radius:20px;
    background:#8c1313;
    color:#f8efe4;
    cursor:pointer;
}

#discountMessage{
    color:#8c1313;
    font-size:13px;
    margin-top:10px;
}

@media (max-width: 1150px) {
    .cart-page { width: auto; margin: 25px 16px; }
    .cart-layout { flex-wrap: wrap; }
    .cart-table, .summary { width: 100%; box-sizing: border-box; }
}
@media (max-width: 540px) {
    .cart-header { display: none; }
    .cart-row { grid-template-columns: 1fr auto; gap: 12px; }
}
</style>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

<div class="cart-page">

<h1>Shopping Cart</h1>

<a href="menu.php" class="back-btn">
    ← Back to Menu
</a>
<div class="cart-layout">

    <div class="cart-table">

        <div class="cart-header">
            <div>Products</div>
            <div>Quantity</div>
            <div>Total</div>
            <div>Action</div>
        </div>

        <?php if ($result && $result->num_rows > 0) { ?>

            <?php while($product = $result->fetch_assoc()) { 
                $rowTotal = $product['price'] * $product['quantity'];
                $subtotal += $rowTotal;
            ?>

            <div class="cart-row">

                <div class="product-info">
                    <img src="<?= escape($product['image_url']) ?>" alt="">

                    <div>
                        <div class="product-name">
                            <?= escape($product['name']) ?>
                        </div>

                        <div class="product-desc">
                            <?= escape(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                    </div>
                </div>

                <div class="qty-box">

                    <form method="POST" style="margin:0;">
                    <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= escape($product['id']) ?>">
                        <input type="hidden" name="action" value="plus">
                        <button type="submit" aria-label="Increase quantity">+</button>
                    </form>

                    <span><?= escape($product['quantity']) ?></span>

                    <form method="POST" style="margin:0;">
                    <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= escape($product['id']) ?>">
                        <input type="hidden" name="action" value="minus">
                        <button type="submit" aria-label="Decrease quantity">−</button>
                    </form>

                </div>

                <div class="total-price">
                    $<?php echo $rowTotal; ?>
                </div>

                <form method="POST" style="margin:0;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= escape($product['id']) ?>">
                    <input type="hidden" name="action" value="delete">
                    <button class="delete-btn" type="submit" aria-label="Remove product">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>

            </div>

            <?php } ?>

        <?php } else { ?>

            <p class="empty">Your cart is empty.</p>

        <?php } ?>

    </div>

    <div class="summary">
        <h3>Order Summary</h3>

        <div class="summary-row">
        <span>Sub Total</span>
        <strong>$<?php echo $subtotal; ?></strong>
        </div>

        <div class="summary-row">
        <span>Delivery Fee</span>
        <strong>$<?= $hasItems ? money(DELIVERY_FEE) : money(0) ?></strong>
        </div>

        <?php $discount = order_discount($subtotal); ?>
        <?php if ($discount): ?>
            <div class="summary-row"><span>Discount (10%)</span><strong>−$<?= money($discount) ?></strong></div>
        <?php endif; ?>

        <div class="summary-row summary-total">
            <span>Total</span>
            <span>$<?= money($hasItems ? $subtotal + DELIVERY_FEE - $discount : 0) ?></span>
        </div>

        <?php if ($hasItems): ?>
            <form class="discount-box" method="POST">
                <?= csrf_field() ?>
                <input type="text" placeholder="Discount code" name="discount_code"
                       aria-label="Discount code" maxlength="20" value="<?= $discount ? DISCOUNT_CODE : '' ?>">
                <button type="submit" name="apply_discount" value="1">Apply</button>
            </form>

            <?php if ($discountError): ?>
                <p role="status"><?= escape($discountError) ?></p>
            <?php endif; ?>

            <a href="checkout.php" class="checkout-btn">Checkout Now</a>
        <?php endif; ?>
    </div>

</div>

</div>

</body>
</html>
