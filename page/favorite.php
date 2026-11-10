<?php
require __DIR__ . '/common.php';
$email = require_login();
require __DIR__ . '/db_search.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post();
    $productId = positive_id($_POST['product_id'] ?? null);
    if (!$productId || !isset($_POST['delete_favorite'])) {
        http_response_code(400);
        exit('Invalid favorite.');
    }

    $conn_search->execute_query(
        'DELETE FROM favorites WHERE user_email = ? AND product_id = ?',
        [$email, $productId]
    );

    header('Location: favorite.php', true, 303);
    exit;
}

$result = $conn_search->execute_query(
    'SELECT products.* FROM favorites JOIN products ON favorites.product_id = products.id
     WHERE favorites.user_email = ? ORDER BY favorites.id',
    [$email]
);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Favorites</title>

<link href="../css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
    background-color: rgb(246,243,240);
}

.page-title{
    color:#8c1313;
    margin-left:100px;
    margin-top:70px;
}

.favorite-card{
    
    height:310px;
    width:200px;
    text-align:center;
    position:relative;
    padding-bottom:45px;
}

.favorite-card img{
    width:150px;
    height:150px;
    object-fit:cover;
    
}

.product-name{
    color:rgba(74,70,70,0.68);
    margin-top:10px;
}

.product-desc{
    color:#8c1313;
    font-size:14px;
}

.card-bottom{
    position:absolute;
    bottom:15px;
    left:15px;
    right:15px;

    display:flex;
    justify-content:space-between;
    align-items:center;
}

.price{
    color:#8c1313;
    font-weight:bold;
}

.delete-btn{
    border:none;
    background:transparent;
    color:#8c1313;
    font-size:18px;
    cursor:pointer;
}

.delete-btn:hover{
    color:#E9BB82;
}

.back-btn{
    display:inline-block;
    margin-left:40px;
    margin-bottom:25px;
    margin-left:80px;
    padding:10px 20px;
    background:rgba(247, 243, 241);;
    color:rgba(123, 122, 121);;
    text-decoration:none;
    border-radius:12px;
    font-weight:bold;
}

.back-btn:hover{
    background:rgba(123, 122, 121);
    color:rgba(246, 241, 235);
    text-decoration:none;
}

.empty{
    color:#8c1313;
    font-size:20px;
}

.product-col{
    width: 190px;
    margin: 0 40px 25px 8px;
}
</style>
</head>

<body>

<h2 class="page-title">Your Favorites</h2>

<a href="menu.php" class="back-btn">← Back to Menu</a>

<div class="container" style="max-width:1200px;">
    <div class="row justify-content-flex">

        <?php if ($result && $result->num_rows > 0) { ?>
            <?php while($product = $result->fetch_assoc()) { ?>

                <div class="product-col">
                    <div class="shadow p-3 mb-5 bg-gradient rounded-3 favorite-card">

                        <img src="<?= escape($product['image_url']) ?>" alt="">

                        <h5 class="product-name">
                            <?= escape($product['name']) ?>
                        </h5>

                        <p class="product-desc">
                            <?= escape(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')) ?>
                        </p>

                        <div class="card-bottom">

                            <span class="price">
                                <?= escape($product['price']) ?>$
                            </span>

                            <form method="POST" style="margin:0;">
                    <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= escape($product['id']) ?>">
                                <button type="submit" name="delete_favorite" class="delete-btn" aria-label="Remove favorite">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                        </div>

                    </div>
                </div>

            <?php } ?>
        <?php } else { ?>

            <p class="empty">Your favorites are empty.</p>

        <?php } ?>

    </div>
</div>

</body>
</html>