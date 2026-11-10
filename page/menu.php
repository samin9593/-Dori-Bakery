<?php
require __DIR__ . '/common.php';
require __DIR__ . '/db_search.php';

$cartCount = 0;
if (isset($_SESSION['user_email'])) {
    $result = $conn_search->execute_query(
        'SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_email = ?',
        [$_SESSION['user_email']]
    );
    $cartCount = (int) $result->fetch_assoc()['total'];
}

$search = is_string($_GET['search'] ?? null) ? mb_substr(trim($_GET['search']), 0, 100, 'UTF-8') : '';
$searchResults = [];
if ($search !== '') {
    $term = '%' . strtr($search, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
    $searchResults = $conn_search->execute_query(
        "SELECT * FROM products WHERE name LIKE ? ESCAPE '!' OR description LIKE ? ESCAPE '!' ORDER BY id",
        [$term, $term]
    )->fetch_all(MYSQLI_ASSOC);
}
?>

<!doctype html>

<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="ie=edge" http-equiv="X-UA-Compatible"/>
<title>DoriBakery-Menu</title>
<link href="../css/bootstrap.css" rel="stylesheet"/>
<link href="../css/style2.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>


<style>
    #cart-count{
    position:absolute;
    top:-8px;
    right:-10px;

    background:rgba(74, 70, 70, 0.68); /* کرم */
    color:#f8efe4;      /* قرمز تیره */

    border-radius:50%;
    min-width:18px;
    height:18px;

    font-size:11px;
    font-weight:bold;

    display:flex;
    align-items:center;
    justify-content:center;
    }
    
#toast {
    visibility: hidden;
    min-width: 250px;
    background: #333;
    color: #fff;
    text-align: center;
    border-radius: 10px;
    padding: 14px 20px;
    position: fixed;
    z-index: 9999;
    bottom: 30px;
    right: 30px;
    font-size: 14px;
    opacity: 0;
    transition: opacity 0.4s, bottom 0.4s;
}

#toast.show {
    visibility: visible;
    opacity: 1;
    bottom: 50px;
}
    .row3 {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        color: #8c1313;
        margin-top: -15px;
    }

    .item1 {
        margin-right: 70px;
    }

    .icon-btn {
    background: transparent !important;
    border: none !important;
    outline: none !important;
    padding: 0 !important;
    margin: 0 !important;
    color: #8c1313 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    position: relative;
    z-index: 50;
}

.icon-btn svg,
.icon-btn path {
    fill: currentColor !important;
    pointer-events: none;
}

.icon-btn:hover,
.icon-btn:hover svg,
.icon-btn:hover path {
    color: #E9BB82 !important;
    fill: #E9BB82 !important;
}
.favorite-floating-btn{
    position: fixed;
    right: 25px;
    bottom: 25px;
    width: 55px;
    height: 55px;
    background-color: #f8efe4;
    color: #8c1313;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    z-index: 9999;
    transition: 0.3s ease;
}

.favorite-floating-btn:hover{
    background-color: #8c1313;
    color: #f8efe4;
    transform: scale(1.1);
}

.favorite-floating-btn svg{
    fill: currentColor;
}
</style>
<script>
function showToast(message) {
    let toast = document.getElementById("toast");
    toast.innerText = message;
    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, 2500);
}
</script>
</head>
<body id="top"  style="background-color: rgb(246,243,240)">
<div id="toast" role="status"></div>
<div class="container toppage">
<a href="../video/mainpage.php#top" style="text-decoration:none;">
    <h3 style="float:left;color:#8c1313;margin-top:20px">
        Dori bakery
    </h3>
</a>
<style>
        .nav {
            margin-top: 10px;
            background-color: rgba(251,246,233,0.76)
        }
    </style>
<nav class="navbar navbar-expand-lg navbar-dark bg-light nav" style="background-color: rgb(246,243,240);">
<!-- <a class="navbar-brand" href="#">Navbar</a> -->
<style>
            nav.navbar{
                background-color: rgb(246,243,240);
            }
            .nav{
                background-color: rgb(246,243,240);
            }
        </style>
<button aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler" data-bs-target="#navbarNavDropdown" data-bs-toggle="collapse" type="button">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNavDropdown" style="background-color: rgb(246,243,240);">
<ul class="navbar-nav" style="background-color: rgb(246,243,240);">
<li class="nav-item" style="background-color: rgb(246,243,240);padding-left: -20px">
    <a class="nav-link" href="../video/mainpage.php">HOME</a>
</li>

<li class="nav-item" style="background-color: rgb(246,243,240);padding-left: 20px">
    <a class="nav-link" href="../video/mainpage.php#about">ABOUT</a>
</li>

<div class="product" style="float: left;margin-left: 40px;margin-top:10px;padding-left: 30px;color:rgba(74, 70, 70, 0.68);background-color: rgb(246,243,240)">
    <div id="productButton">PRODUCTS</div>

    <div id="dropdown" class="dropdown-menu product-menu"
         style="display: none;float:right;text-align: left;padding-left: 10px;width: 50px;height: 120px">

        <a class="items" href="#cookie">COOKIES</a><br>
        <a class="items" href="#Croissant">CROISSANTS</a><br>
        <a class="items" href="#CAKES">CAKES</a><br>
        <a class="items" href="#coffee">COFFEE</a>
    </div>
</div>
<style>
                        .items{
                            text-decoration: none;
                        }
                    </style>
</div>
<script>
                    var menu = document.getElementById('dropdown');
                    var productButton = document.getElementById('productButton');
                    productButton.addEventListener('click', function(event) {
                        event.stopPropagation();
                        if (menu.style.display === "block") {
                            menu.style.display = "none";
                        } else {
                            menu.style.display = "block";
                        }
                    });
                    document.addEventListener('click', function(event) {
                        if (!menu.contains(event.target) && !productButton.contains(event.target)) {
                            menu.style.display = "none";
                        }
                    });
                </script>
</ul>
</div>
</nav>
<br/>
<a href="cart.php"
   class="shopping-icon" aria-label="Shopping cart"
   onclick="return checkLogin();"
   style="margin-right:-900px; position:relative;">

    <svg class="bi bi-bag"
         fill="currentColor"
         height="20"
         width="20"
         viewBox="0 0 16 16">
        <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
    </svg>

    <?php if(isset($_SESSION['user_email']) && $cartCount > 0) { ?>
    <span id="cart-count">
        <?php echo $cartCount; ?>
    </span>
    <?php } ?>

</a>
<div class="profile" style="color: #8c1313;float: right;margin-right: -860px;margin-top: 2px">

    <div id="iconButton" style="cursor: pointer; font-size: 24px; display:flex; align-items:center;">

        <?php if(isset($_SESSION['user_name'])) { ?>
            <span style="font-size:16px; margin-right:8px;">
                <?= escape($_SESSION['user_name']) ?>
            </span>
        <?php } ?>

        <svg class="bi bi-person" fill="currentColor" height="25" viewBox="0 0 16 16" width="25" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"></path>
        </svg>

    </div>

    <div id="dropdownIcon" style="display: none; padding:10px; width:130px; background-color:rgba(74,70,70,0.68); border-radius:5px; margin-top:5px; position:absolute; z-index:9999;">

    <?php if(isset($_SESSION['user_name'])) { ?>

        <a class="items" href="order_tracking.php" style="text-decoration:none;">
            Track Order
        </a>

        <br>

        <form method="POST" action="logout.php" style="margin:0">
            <?= csrf_field() ?>
            <button class="items" type="submit"
                    style="background:none;border:0;padding:0;font:inherit;color:inherit;text-decoration:none">
                Log out
            </button>
        </form>

    <?php } else { ?>

        <a class="items" href="login.php" style="text-decoration:none;">
            Log in
        </a>

        <br>

        <a class="items" href="signup.php" style="text-decoration:none;">
            Sign up
        </a>

    <?php } ?>

</div>
</div>
</div>
<script>
        var profileMenu = document.getElementById('dropdownIcon');
        var profileButton = document.getElementById('iconButton');

        profileButton.addEventListener('click', function(event) {
            event.stopPropagation();
            profileMenu.style.display = (profileMenu.style.display === "block") ? "none" : "block";
        });

        document.addEventListener('click', function(event) {
            // اگه روی منو یا دکمه کلیک نشده بود، منو بسته شه
            if (!profileMenu.contains(event.target) && !profileButton.contains(event.target)) {
                profileMenu.style.display = "none";
            }
        });

        // درست کار کردن لینکا
        let links = profileMenu.querySelectorAll("a");
        links.forEach(link => {
            link.addEventListener("click", function(event) {
                profileMenu.style.display = "none";

                // مسیر لینک  صورت دستی باز شه
                const targetHref = link.getAttribute("href");
                if (targetHref && targetHref !== "#") {
                    window.location.href = targetHref;
                }
            });
        });
    </script>
</div>
<br/>
<br/>
<br/>
<div class="header-image">

    <img alt=""
         class="img-head"
         src="../image2/image52.png"
         width="100%"
         height="100%">

    <div class="header-text">
         BAKERY MENU
    </div>

</div>
<style>.header-image{
    position: relative;opacity: 0.5;
}
.img-head{
    width: 100%;
    height: 100%;
}
.header-image{
    position: relative;
}
.img-head{
    width: 100%;
    height: 100%;
}
.header-text{
    position: absolute;
    top: 75%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: black;
    font-size: 40px;
    font-weight: bold;
    text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
}
</style>
<br/>
<br/>
<style>
    .search-box {
        position: relative;
        display: flex;
        float: left;
        margin-top: -5px;
        margin-left: 500px;
        box-shadow: 0 8px 10px rgba(104, 96, 96, 0.6);
        border-radius: 15px;
        transition: box-shadow 0.3s ease;
    }
    .search-box input{
        background-color: rgb(255, 255, 253);
        width: 500px;
        height: 40px;
        border-radius: 15px;
        border: #8c1313;
        height: 50px;
       
        
    }
    .search-box input:focus{
        outline: none;
        border:2px solid #8c1313;
        
    }
   
    .button-search{
        position: absolute;
        margin-left: 455px;
        margin-top: 7px;
        width:35px;
        height: 35px;
        border-radius: 10px;
        background-color: #8c1313;
        color: white;
        float: right;
        border: #8c1313;
    }
    .button-search:hover{
        background-color: #595353;
        color: #f8efe4;
    }
</style>

<form class="search-box" method="GET" action="menu.php">
    <input 
        name="search" aria-label="Search products" maxlength="100"
        placeholder=" Search "
        type="text"
        value="<?= escape($search) ?>"
        style="text-align: left; color:#595353;"
    >

    <button class="button-search" type="submit" aria-label="Search">
        <i class="fa fa-search"></i>
    </button>
</form>
<?php if ($search !== '') { ?>
    <div class="container" style="margin-top: 90px;">
        <div class="row" style="padding: 10px; margin-left:40px;">

            <?php if (count($searchResults) > 0) { ?>
                <?php foreach ($searchResults as $product) { ?>

                    <div class="col-2">
                        <div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height:320px;width: 200px;margin-left: 30px;text-align: center">
                            <div class="img-card">
                                <img src="<?= escape($product['image_url']) ?>" alt="" width="150px" height="150px">
                            </div>

                            <h5 style="color:rgba(74, 70, 70, 0.68); margin-top: 5px">
                                <?= escape($product['name']) ?>
                            </h5>

                            <p style="margin-top: -10px;color:#8c1313">
                                <?= escape(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')) ?>
                            </p>

                            <div class="row3">

    <span class="item1"><?= escape($product['price']) ?>$</span>

    <button class="icon-btn favorite-btn"
            type="button"
            aria-label="Add to favorites"
            data-product-id="<?= escape($product['id']) ?>" data-name="<?= escape($product['name']) ?>">

        <svg xmlns="http://www.w3.org/2000/svg"
             width="16"
             height="16"
             fill="currentColor"
             class="bi bi-star"
             viewBox="0 0 16 16">

            <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256
            4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73
            3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696
            L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696
            c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73
            zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505
            L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223
            l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77
            a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894
            a.5.5 0 0 0-.461 0z"/>
        </svg>

    </button>

    <button class="icon-btn cart-btn"
            type="button"
            aria-label="Add to cart"
            data-product-id="<?= escape($product['id']) ?>" data-name="<?= escape($product['name']) ?>">

        <svg xmlns="http://www.w3.org/2000/svg"
             width="16"
             height="16"
             fill="currentColor"
             class="bi bi-bag"
             viewBox="0 0 16 16">

            <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5
            A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10
            a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3
            a1 1 0 0 1-1-1z"/>
        </svg>

    </button>

</div>
                        </div>
                    </div>

                <?php } ?>
            <?php } else { ?>
                <p style="color:#8c1313; margin-left:100px;">No product found.</p>
            <?php } ?>

        </div>
    </div>
<?php } ?>
<br/>
<br/>
<br/>
<br/>
<h5 id="cookie" style="padding: 20px;margin-left: 100px;color:#8c1313">Cookies</h5>
<p style="margin-left: 120px;margin-top: -15px;color: rgba(74, 70, 70, 0.68)">The cookies are composed of flour, brown sugar and white sugar in the same amount, oil and additives according to the type of cookies.</p>
<hr style="border: solid #8c1313;width: 1300px;margin-left:100px"/>
<div class="container">
<div class="row" style="padding: 10px;margin-left:40px">
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 280px;width: 200px;margin-left: 30px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cookie1.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">COCONAT cookie</h5>
<p style="margin-top: -10px;color:#8c1313 ">coconat oil &amp; powder</p>
<br/>
<div class="row3">
<span class="item1">3$</span>
<button class="icon-btn favorite-btn" data-product-id="1" data-name="COCONAT cookie" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="1" data-name="COCONAT cookie" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 280px;width: 200px;margin-left: 50px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cookie2.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">MATCHA cookie</h5>
<p style="margin-top: -10px;color:#8c1313 ">matcha powder &amp; white chocolate</p>
<div class="row3">
<span class="item1">5$</span>
<button class="icon-btn favorite-btn" data-product-id="3" data-name="MATCHA cookie" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="3" data-name="MATCHA cookie" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 280px;width: 200px;margin-left: 70px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cookie3.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">OREO cookie</h5>
<p style="margin-top: -10px;color:#8c1313 ">oreo biscuits</p>
<br/>
<div class="row3">
<span class="item1">5$</span>
<button class="icon-btn favorite-btn" data-product-id="5" data-name="OREO cookie" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="5" data-name="OREO cookie" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 280px;width: 200px;margin-left: 90px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cookie4.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">CHOCOLATE cookie</h5>
<p style="margin-top: -10px;color:#8c1313 ">chocolate pieces</p>
<div class="row3">
<span class="item1">4$</span>
<button class="icon-btn favorite-btn" data-product-id="7" data-name="CHOCOLATE cookie" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="7" data-name="CHOCOLATE cookie" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 280px;width: 200px;margin-left: 110px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cookie5.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">LOTUS cookie</h5>
<p style="margin-top: -10px;color:#8c1313 ">lotus biscuit</p>
<br/>
<div class="row3">
<span class="item1">5$</span>
<button class="icon-btn favorite-btn" data-product-id="9" data-name="LOTUS cookie" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="9" data-name="LOTUS cookie" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
</div>
</div>
<div class="container">
<div class="row" style="padding: 10px;margin-left:40px">
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 280px;width: 200px;margin-left: 30px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cookie6.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">PINUT cookie</h5>
<p style="margin-top: -10px;color:#8c1313 ">pinut piece &amp; butter</p>
<br/>
<div class="row3">
<span class="item1">5$</span>
<button class="icon-btn favorite-btn" data-product-id="11" data-name="PINUT cookie" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="11" data-name="PINUT cookie" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 280px;width: 200px;margin-left: 50px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cookie8.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">FRUIT cookie</h5>
<p style="margin-top: -10px;color:#8c1313 ">season fruit</p>
<br/>
<div class="row3">
<span class="item1">6$</span>
<button class="icon-btn favorite-btn" data-product-id="13" data-name="FRUIT cookie" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="13" data-name="FRUIT cookie" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 280px;width: 200px;margin-left: 70px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cookie7.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">REDVELVET cookie</h5>
<p style="margin-top: -10px;color:#8c1313 ">chocolate &amp; cheese</p>
<div class="row3">
<span class="item1">6$</span>
<button class="icon-btn favorite-btn" data-product-id="14" data-name="REDVELVET cookie" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="14" data-name="REDVELVET cookie" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 280px;width: 200px;margin-left: 90px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cookie9.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">STRAWBERRY cookie</h5>
<p style="margin-top: -10px;color:#8c1313 ">strawberry pieces</p>
<div class="row3">
<span class="item1">5$</span>
<button class="icon-btn favorite-btn" data-product-id="31" data-name="STRAWBERRY cookie" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="31" data-name="STRAWBERRY cookie" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
</div>
</div>
<br/>
<br/>
<h5 id="Croissant" style="padding: 20px;margin-left: 100px;color:#8c1313">Croissant</h5>
<p style="margin-left: 120px;margin-top: -15px;color: rgba(74, 70, 70, 0.68)">The croissants are buttery, flaky, viennoiserie pastry inspired by the shape of the Austrian kipferl.</p>
<hr style="border: solid #8c1313;width: 1300px;margin-left:100px"/>
<div class="container">
<div class="row" style="padding: 10px;margin-left:40px">
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 30px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/crs1.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">CHOCOLATE croissant</h5>
<p style="margin-top: -10px;color:#8c1313 ">chocolate cream </p>
<div class="row3">
<br/>
<br/>
<br/>
<span class="item1">6$</span>
<button class="icon-btn favorite-btn" data-product-id="17" data-name="CHOCOLATE croissant" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="17" data-name="CHOCOLATE croissant" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 50px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/crs2.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">MATCHA croissant</h5>
<p style="margin-top: -10px;color:#8c1313 ">matcha powder</p>
<div class="row3">
<br/>
<br/>
<br/>
<span class="item1">8$</span>
<button class="icon-btn favorite-btn" data-product-id="18" data-name="MATCHA croissant" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="18" data-name="MATCHA croissant" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 70px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/crs3.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">pinut croissant</h5>
<p style="margin-top: -10px;color:#8c1313 ">pinut pieces &amp; butter</p>
<br/>
<br/>
<div class="row3">
<span class="item1">6$</span>
<button class="icon-btn favorite-btn" data-product-id="19" data-name="pinut croissant" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="19" data-name="pinut croissant" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 90px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/crs4.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">HAZELNUT croissant</h5>
<p style="margin-top: -10px;color:#8c1313 ">hazelnut pieces &amp; butter</p>
<div class="row3">
<span class="item1">8$</span>
<button class="icon-btn favorite-btn" data-product-id="20" data-name="HAZELNUT croissant" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="20" data-name="HAZELNUT croissant" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 110px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/crs5.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">PISTACHIO croissant</h5>
<p style="margin-top: -10px;color:#8c1313 ">pistachio pieces % butter</p>
<div class="row3">
<span class="item1">8$</span>
<button class="icon-btn favorite-btn" data-product-id="21" data-name="PISTACHIO croissant" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="21" data-name="PISTACHIO croissant" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
<style>
                    .btn-success{
                        width: 230px;
                        background-color:#ef782c ;
                    }
                    .btn-success:hover{
                        background-color: #0b5ed7;
                    }
                </style>
</div>
</div>
</div>
</div>
<div class="container">
<div class="row" style="padding: 10px;margin-left:40px">
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 30px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/crs6.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">ICECREAM croissant</h5>
<p style="margin-top: -10px;color:#8c1313 ">croissant with icecream </p>
<div class="row3">
<span class="item1">6$</span>
<button class="icon-btn favorite-btn" data-product-id="22" data-name="ICECREAM croissant" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="22" data-name="ICECREAM croissant" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
<style>
                    .btn-success{
                        width: 230px;
                        background-color:#ef782c ;
                    }
                    .btn-success:hover{
                        background-color: #0b5ed7;
                    }
                </style>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 50px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/crs8.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">STRAWBERRY croissant</h5>
<p style="margin-top: -10px;color:#8c1313 ">strawberry with cream</p>
<div class="row3">
<br/>
<br/>
<br/>
<span class="item1">8$</span>
<button class="icon-btn favorite-btn" data-product-id="23" data-name="STRAWBERRY croissant" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="23" data-name="STRAWBERRY croissant" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
<style>
                    .btn-success{
                        width: 230px;
                        background-color:#ef782c ;
                    }
                    .btn-success:hover{
                        background-color: #0b5ed7;
                    }
                </style>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 70px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/crs7.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">LOTUS croissant</h5>
<p style="margin-top: -10px;color:#8c1313 ">lotus bisquit &amp; cream</p>
<br/>
<br/>
<div class="row3">
<span class="item1">7$</span>
<button class="icon-btn favorite-btn" data-product-id="24" data-name="LOTUS croissant" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="24" data-name="LOTUS croissant" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
<style>
                    .btn-success{
                        width: 230px;
                        background-color:#ef782c ;
                    }
                    .btn-success:hover{
                        background-color: #0b5ed7;
                    }
                </style>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 90px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/crs9.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">FRUIT croissant</h5>
<p style="margin-top: -10px;color:#8c1313 ">season fruit &amp; cream</p>
<div class="row3">
<br/>
<br/>
<br/>
<br/>
<br/>
<span class="item1">8$</span>
<button class="icon-btn favorite-btn" data-product-id="25" data-name="FRUIT croissant" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="25" data-name="FRUIT croissant" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
<style>
                    .btn-success{
                        width: 230px;
                        background-color:#ef782c ;
                    }
                    .btn-success:hover{
                        background-color: #0b5ed7;
                    }
                </style>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 110px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/crs10.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">COCONAT croissant</h5>
<p style="margin-top: -10px;color:#8c1313 ">coconat cream &amp; powder</p>
<div class="row3">
<span class="item1">6$</span>
<button class="icon-btn favorite-btn" data-product-id="26" data-name="COCONAT croissant" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="26" data-name="COCONAT croissant" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
<style>
                    .btn-success{
                        width: 230px;
                        background-color:#ef782c ;
                    }
                    .btn-success:hover{
                        background-color: #0b5ed7;
                    }
                </style>
</div>
</div>
</div>
</div>
<br/>
<h5 id="CAKES" style="padding: 20px;margin-left: 100px;color:#8c1313">CAKES</h5>
<p style="margin-left: 120px;margin-top: -15px;color: rgba(74, 70, 70, 0.68)">Cakes and desserts (sweets) that are mainly baked with flour, eggs, sugar, and oil.</p>
<hr style="border: solid #8c1313;width: 1300px;margin-left:100px"/>
<div class="container">
<div class="row" style="padding: 10px;margin-left:40px">
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 30px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cake1.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">TIRRAMISU</h5>
<p style="margin-top: -10px;color:#8c1313 ">designed with chocolare &amp; espresso powder</p>
<div class="row3">
<span class="item1">10$</span>
<button class="icon-btn favorite-btn" data-product-id="27" data-name="TIRRAMISU" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="27" data-name="TIRRAMISU" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
<style>
                    .btn-success{
                        width: 230px;
                        background-color:#ef782c ;
                    }
                    .btn-success:hover{
                        background-color: #0b5ed7;
                    }
                </style>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 50px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cake2.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">MATCHA TIRRAMISU</h5>
<p style="margin-top: -10px;color:#8c1313 ">matcha tea &amp; powder</p>
<div class="row3">
<br/>
<br/>
<br/>
<span class="item1">11$</span>
<button class="icon-btn favorite-btn" data-product-id="28" data-name="MATCHA TIRRAMISU" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="28" data-name="MATCHA TIRRAMISU" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
<style>
                    .btn-success{
                        width: 230px;
                        background-color:#ef782c ;
                    }
                    .btn-success:hover{
                        background-color: #0b5ed7;
                    }
                </style>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 70px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cake3.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">TART</h5>
<p style="margin-top: -10px;color:#8c1313 ">designed with season fruits</p>
<br/>
<div class="row3">
<span class="item1">7$</span>
<button class="icon-btn favorite-btn" data-product-id="29" data-name="TART" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="29" data-name="TART" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
<style>
                    .btn-success{
                        width: 230px;
                        background-color:#ef782c ;
                    }
                    .btn-success:hover{
                        background-color: #0b5ed7;
                    }
                </style>
</div>
</div>
<div class="col-2">
<div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 300px;width: 200px;margin-left: 90px;text-align: center">
<div class="img-card">
<img alt="" height="150px" src="../image2/cake4.jpeg" width="150px"/>
</div>
<h5 style=" color:rgba(74, 70, 70, 0.68) ;margin-top: 5px">CREAMY TART</h5>
<p style="margin-top: -10px;color:#8c1313 ">tart with full cream</p>
<div class="row3">
<br/>
<br/>
<br/>
<br/>
<br/>
<span class="item1">8$</span>
<button class="icon-btn favorite-btn" data-product-id="30" data-name="CREAMY TART" title="Add to favorites" type="button">
<svg class="bi bi-star" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"></path>
</svg>
</button>
<button class="icon-btn cart-btn" data-product-id="30" data-name="CREAMY TART" title="Add to cart" type="button">
<svg class="bi bi-bag" fill="currentColor" height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
<path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"></path>
</svg>
</button>
</div>
<style>
                    .btn-success{
                        width: 330px;
                        background-color:#ef782c ;
                    }
                    .btn-success:hover{
                        background-color: #0b5ed7;
                    }
                </style>
</div>
</div>
</div>
</div>

<section id="coffee" class="container mb-5">
    <h5 style="color:#8c1313">Coffee</h5>
    <p>Coffee is currently unavailable for online ordering.</p>
</section>

<script>
const csrf = <?= json_encode(csrf_token()) ?>;

document.addEventListener("click", async function(event) {
    const button = event.target.closest(".cart-btn, .favorite-btn");
    if (!button) return;

    const isCart = button.classList.contains("cart-btn");
    const url = isCart ? "add_cart.php" : "add_favorite.php";
    button.disabled = true;

    try {
        const response = await fetch(url, {
            method: "POST",
            body: new URLSearchParams({product_id: button.dataset.productId, csrf})
        });
        if (response.status === 401) {
            showToast("Please login first.");
        } else if (response.status === 409) {
            showToast("This item has reached the cart quantity limit.");
        } else if (!response.ok) {
            showToast("The item could not be added. Reload the page and try again.");
        } else if (isCart) {
            const data = await response.json();
            updateCartBadge(data.cartCount);
            showToast(button.dataset.name + " added to cart.");
        } else {
            showToast(button.dataset.name + " added to favorites.");
        }
    } catch (error) {
        showToast("Could not reach the site. Try again.");
    } finally {
        button.disabled = false;
    }
});
</script>

<a href="favorite.php"
   class="favorite-floating-btn" aria-label="Favorites"
   onclick="return checkLogin();">

    <svg xmlns="http://www.w3.org/2000/svg"
         width="20"
         height="20"
         fill="currentColor"
         class="bi bi-star-fill"
         viewBox="0 0 16 16">

        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592
        l.83-4.73L.173 6.765c-.33-.314-.158-.888.283-.95
        l4.898-.696L7.538.792c.197-.39.73-.39.927 0
        l2.184 4.327 4.898.696c.441.062.613.636.282.95
        l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592
        L8 13.187l-4.389 2.256z"/>

    </svg>

</a> 
<script>
function checkLogin() {

    const isLoggedIn =
    <?php echo isset($_SESSION['user_email']) ? 'true' : 'false'; ?>;

    if (!isLoggedIn) {

        showToast("Please login first.");

        return false; // جلوگیری از رفتن به صفحه
    }

    return true; // اجازه رفتن به cart.php یا favorite.php
}
</script>

<script>
function updateCartBadge(count) {
    let badge = document.getElementById("cart-count");
    if (!badge) {
        badge = document.createElement("span");
        badge.id = "cart-count";
        document.querySelector(".shopping-icon").appendChild(badge);
    }
    badge.textContent = count;
}
</script>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
