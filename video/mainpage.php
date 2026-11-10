<?php
require __DIR__ . '/../page/common.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DoriBakery</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body id="top" style="background-color: rgb(246,243,240)">
<div class="container toppage">
    <h3 style="float: left;color: #8c1313;margin-top: 20px">Dori bakery</h3>

    <nav class="navbar navbar-expand-lg navbar-dark bg-light nav"style="background-color: rgb(246,243,240);">
        <!-- <a class="navbar-brand" href="#">Navbar</a> -->
        <style>
            nav.navbar{
                background-color: rgb(246,243,240);
            }
            .nav{
                background-color: rgb(246,243,240);
            }
        </style>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown"style="background-color: rgb(246,243,240);">
            <ul class="navbar-nav"style="background-color: rgb(246,243,240);">
                <li class="nav-item"style="background-color: rgb(246,243,240);padding-left: 5px">
                    <a class="nav-link" href="mainpage.php">HOME</a>
                </li>
                <li class="nav-item"style="background-color: rgb(246,243,240);padding-left: 30px">
                    <a class="nav-link" href="mainpage.php#about">ABOUT</a>
                </li>
                <div class="product"style="float: left;margin-left: 50px;margin-top:10px;padding-left: 30px;color:rgba(74, 70, 70, 0.68);background-color: rgb(246,243,240)" >
                    <div id="productButton">
                        PRODUCTS
                    </div>
                    <div id="dropdown" class="dropdown-menu product-menu"aria-labelledby="productButton"
                         style="display: none;float:right ; text-align: left;;padding-left: 10px;width: 50px;height: 120px" >
                         <a class="items" href="../page/menu.php#cookie">COOKIES</a><br>
                         <a class="items" href="../page/menu.php#Croissant">CROISSANTS</a><br>
                         <a class="items" href="../page/menu.php#CAKES">CAKES</a><br>
                         <a class="items" href="../page/menu.php#coffee">COFFEE</a>
                    </div>
                    <style>
                        .items{
                            text-decoration: none;
                        }
                    </style>
                </div>
                <script>
                    var productMenu = document.getElementById('dropdown');
                    var productButton = document.getElementById('productButton');

                    productButton.addEventListener('click', function(event) {
                        event.stopPropagation();
                        productMenu.style.display = (productMenu.style.display === "block") ? "none" : "block";
                    });

                    document.addEventListener('click', function(event) {
                        if (!productMenu.contains(event.target) && !productButton.contains(event.target)) {
                            productMenu.style.display = "none";
                        }
                    });
                </script>
            </ul>
        </div>
    </nav>
    <br>
    <?php if(isset($_SESSION['user_name'])) { ?>

        <a href="../page/cart.php"
   class="shopping-icon" aria-label="Shopping cart"
   onclick="<?php echo isset($_SESSION['user_email']) ? 'return true;' : 'showToast(\'Please login first.\'); return false;'; ?>"
   style="margin-right:-900px;color:#8c1313;text-decoration:none;cursor:pointer;">

    <svg xmlns="http://www.w3.org/2000/svg"
         width="20"
         height="20"
         fill="currentColor"
         class="bi bi-bag"
         viewBox="0 0 16 16">

        <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>

    </svg>
    </a>



<?php } else { ?>

<a href="#"
   onclick="showToast('Please login first.'); return false;"
   class="shopping-icon" aria-label="Shopping cart"
   style="margin-right:-900px;color:#8c1313;text-decoration:none;cursor:pointer;">

    <svg xmlns="http://www.w3.org/2000/svg"
         width="20"
         height="20"
         fill="currentColor"
         class="bi bi-bag"
         viewBox="0 0 16 16">

        <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>

    </svg>

</a>

<?php } ?>
    <div class="profile" style="color: #8c1313; float: right; margin-right: -860px; margin-top: 2px; position: relative;">

    <div id="iconButton" style="cursor: pointer; font-size: 24px; display: flex; align-items: center; gap: 8px;">

        <?php if (isset($_SESSION['user_name'])) { ?>
            <span style="font-size: 16px; font-weight: bold;">
                <?= escape($_SESSION['user_name']) ?>
            </span>
        <?php } ?>

        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
        </svg>

    </div>

    <div id="dropdownIcon" style="display: none; padding: 10px; width: 120px; background-color:rgba(74, 70, 70, 0.68); border-radius:5px; margin-top: 5px; position: absolute; z-index: 9999; right: 0;">
        <?php if (isset($_SESSION['user_name'])) { ?>

        <a class="items" href="../page/order_tracking.php" style="text-decoration: none;">
        Track Order
        </a>
        <br>

        <form method="POST" action="../page/logout.php" style="margin:0">
            <?= csrf_field() ?>
            <button class="items" type="submit"
                    style="background:none;border:0;padding:0;font:inherit;color:inherit;text-decoration:none">
                Log out
            </button>
        </form>

        <?php } else { ?>
            <a class="items" href="../page/login.php" style="text-decoration: none;">Log in</a><br>
            <a class="items" href="../page/signup.php" style="text-decoration: none;">Sign up</a>
        <?php } ?>

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
        if (!profileMenu.contains(event.target) && !profileButton.contains(event.target)) {
            profileMenu.style.display = "none";
        }
    });
</script>
    <img src="../image2/image5.png" style="float: right;margin-top:-90px;margin-right: -800px;width:450px;pointer-events:none">
</div>
<br>
<br>
<br>
<div class="banner"style="margin-top: -30px">
    <div class="banner-text">

        <h4 style="color: #8c1313;">Welcome to Dori</h4>
        <br>
        <h1 style="color: rgba(74,70,70,0.9)">FROM OUR OVEN</h1>
        <h1 style="color: rgba(74,70,70,0.9)">TO</h1>
        <h1 style="color: rgba(74,70,70,0.9)">YOUR HOME</h1>
        <br>
        <p style="color: rgba(74,70,70,0.9);width: 450px ">Crafted with love and the finest ingredients, our bakery brings fresh, delicious treats to every table.</p>
    </div>
    <img src="../image2/image22.jpeg" alt="" class="img-head" width="100%"style="opacity: 0.2;margin-top: -530px;">
</div>
<br>
<div class="examples">
    <p style="color: #8c1313">our products</p>
    <h3 style="color: rgba(74, 70, 70, 0.68)">WHAT WE MADE</h3>
    <h3 style="color: rgba(74, 70, 70, 0.68)">FOR YOU</h3>
    <hr style="border: 2px solid rgba(74, 70, 70, 0.68)">
</div>

<div class="container"style="margin-left:150px;margin-top: -60px">
    <div class="row">
            <div class="img-card"style="margin-top: -80px">
                <img src="../image2/IMAGE6.png" width="200px"alt=" "style="width: 160px">
                <img src="../image2/IMAGE7.png" width="200px"alt=" "style="width: 160px">

                <div class="text2"style="width:700px;margin-right:180px;margin-top:110px;float: right;color: #8c1313">
                    <h5>Croissant</h5>
                    <p>Flaky, buttery, and freshly baked — our croissants bring a little piece of Paris to your morning. Perfectly golden and irresistibly light, ideal with coffee or on their own.
                    </p>
                </div>

            </div>
    </div>
    <div class="row">
        <div class="img-card"style="margin-top: -80px;float: right">
            <img src="../image2/image1.png" width="200px"alt=" "style="width: 160px;margin-left:-60px">
            <img src="../image2/image2.png" width="200px"alt=" "style="width: 150px;margin-left:-30px">

            <div class="text2"style="width:700px;margin-right:180px;margin-top:110px;float: left;color: #8c1313">
                <h5>Cookie</h5>
                <p>Crispy on the edges, soft in the center — our cookies are baked to perfection. Loaded with premium chocolate chips for a rich, gooey treat in every bite.
                </p>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="img-card"style="margin-top: -80px">
            <img src="../image2/IMAGE8.png" width="200px"alt=" "style="width: 160px">
            <img src="../image2/IMAGE9.png" width="200px"alt=" "style="width: 170px;margin-top: 30px">

            <div class="text2"style="width:700px;margin-right:180px;margin-top:110px;float: right;color: #8c1313">
                <h5>Cake</h5>
                <p>Celebrate every moment with our handcrafted cakes — soft, moist, and full of flavor. Each slice is a sweet story, made with love and the finest ingredients.</p>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="img-card"style="margin-top: -80px;float: right">
            <img src="../image2/IMAGE10.png" width="200px"alt=" "style="width: 120px;margin-left:-60px;margin-top: 30px">
            <img src="../image2/IMAGE11.png" width="200px"alt=" "style="width: 125px;margin-top: 40px">

            <div class="text2"style="width:700px;margin-right:180px;margin-top:110px;float: left;color: #8c1313">
                <h5>Coffee</h5>
                <p>From rich espresso to smooth matcha lattes — we've brewed the best of both worlds.Whether you're craving bold coffee or calming matcha, your perfect cup is here.</p>
            </div>

        </div>
    </div>
</div>
<br>
<br>
<hr>

<div class="banner"style="margin-top:-15px">
    <div class="banner-text"style="float: right;margin-right: 350px">
        <h4 style="color: #8c1313;">SPRING 2025</h4>
        <br>
        <h1 style="color: rgba(74,70,70,0.9);">BAKERY MENU</h1>
        <br>
        <p style="color: rgba(74,70,70,0.9);width: 380px ">Craving something sweet? Order your favorites online with just a few clicks. Fast, easy, and delivered fresh to your door — your bakery, anytime, anywhere</p>
        <br>
        <style>
            .go-menu {
                background-color: #8c1313;
                color: #f8efe4;
                padding: 10px 20px;
                border: none;
                text-decoration: none;
                border-radius: 5px;
                transition: 0.3s;
                display: inline-block;
                border-radius: 10px;
            }

            .go-menu:hover {
                background-color: #595353;
                color: #f8efe4;
            }
        </style>
        <a href="../page/menu.php" class="go-menu">order online</a>
    </div>
    <img src="../image2/image18.jpeg" alt="" class="img-head" width="100%"
         style="opacity: 0.2; margin-top: -480px; pointer-events: none;">
    <img src="../image2/image13.png" alt=""style="margin-top: -650px;margin-left:300px;width: 300px;pointer-events:none">
</div>
<br>
<br>
<div class="col-4"id="about">
    <div class="shadow p-3 mb-5 bg-gradient rounded-3" style="height: 600px;width: 1300px;margin-left: 90px;text-align: center;margin-top:600px;">
        <h5 style="color:#8c1313 " >ABOUT</h5>
        <div class="img-card"style="margin-top: 30px;margin-left: 70px;float:left">
            <img src="../image2/baker.jpeg" style="width:250px;height: 250px;border-radius:50%;">
        </div>
        <h5 style="float: left;margin-left: 30px;margin-top: 30px">HI THERE , I'M LILY</h5>
        <br>
        <p style="float: left;margin-left: -170px;margin-top: 40px;width:500px;text-align: justify">thank you for being with our bakery. This is the 10th year that I've been baking cakes for you, but it's been 4 years since we founded Dori and we've been(with my dear team) baking sweets with love for our fans .
        </p>
        <br>
        <br>
        <div class="img-card">
            <img src="../image2/bakers.jpeg" style="width:500px;height: 250px;border-radius: 25px;float: left;margin-left: -500px;margin-top: 200px">
        </div>
        <div class="int" style="float: left;margin-left: -700px;margin-top: 250px">
            <h5 style="color:#8c1313 ">THE TEAM</h5>
            <br>
            <p>Pedro</p>
            <p>Ellie</p>
            <p>Emma</p>
            <p>Magi</p>
        </div>
        <video controls autoplay loop muted playsinline style="float: right;width:300px;height: 480px;border-radius: 20px;margin-top: -30px;margin-right: 20px">
           <source src="../video/video2.m4v" type="video/mp4">
       </video>
    </div>
</div>
<footer class="footer" style="background-color: #8c1313;height: 300px;margin-top: 120px">
    <div class="container">
        <div class="row" style="padding-top:30px">
            <div class="col-12 col-lg-2"style="margin-left: 70px">
                <h5 style="padding: 10px; color: #E9BB82">BAKERY</h5>
                <p style="text-align: justify ; margin-left:10px;color:rgb(246,243,240)">
                 Made with love, baked to perfection. ©2025 Dori.
                </p>
                <p style="text-align: justify ; margin-left:10px;color:rgb(246,243,240)"> all rights reserved.</p>
                <div style="color:rgb(246,243,240);padding:10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16" style="margin-left:10px">
                        <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16" style="margin-left:15px">
                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telegram" viewBox="0 0 16 16" style="margin-left:15px">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pinterest" viewBox="0 0 16 16" style="margin-left:15px">
                        <path d="M8 0a8 8 0 0 0-2.915 15.452c-.07-.633-.134-1.606.027-2.297.146-.625.938-3.977.938-3.977s-.239-.479-.239-1.187c0-1.113.645-1.943 1.448-1.943.682 0 1.012.512 1.012 1.127 0 .686-.437 1.712-.663 2.663-.188.796.4 1.446 1.185 1.446 1.422 0 2.515-1.5 2.515-3.664 0-1.915-1.377-3.254-3.342-3.254-2.276 0-3.612 1.707-3.612 3.471 0 .688.265 1.425.595 1.826a.24.24 0 0 1 .056.23c-.061.252-.196.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.919l-.498 1.902c-.181.695-.669 1.566-.995 2.097A8 8 0 1 0 8 0"/>
                    </svg>
                </div>
            </div>

            <div class="col-12 col-lg-2"style="margin-left: 100px">
                <h5 style="padding: 10px; color: #E9BB82">Our Stores</h5>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16" style="margin-left:10px;color:rgb(246,243,240)">
                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                </svg><span style="color:rgb(246,243,240)">New York City</span>
                <br>
                <br>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16" style="margin-left:10px;color:rgb(246,243,240)">
                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                </svg><span style="color:rgb(246,243,240)">buffalo </span>
                <br>
                <br>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16" style="margin-left:10px;color:rgb(246,243,240);margin-left:10px">
                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                </svg><span style="color:rgb(246,243,240)">Albany </span>

            </div>
            <div class="col-8 col-lg-3"style="margin-left: 20px">
                <h5 style="padding: 10px; color: #E9BB82">Get In Touch</h5>
                <i class="fa fa-envelope" style="color:rgb(246,243,240);margin-left:10px"></i><span><span style="color:rgb(246,243,240);margin-left:10px">contact@example.com</span></span>
                <br>
                <br>
                <i class="fa fa-phone" style="color:rgb(246,243,240);margin-left:10px"></i><span style="color:rgb(246,243,240);margin-left:10px">  555-0100</span>
            </div>
            <div class="col-12 col-lg-2" style="margin-left: 40px">
                <img src="../image2/image14.png" alt=""width="250px" height="250px">
            </div>
        </div>
       
        
</footer>
<!--<footer>
    <p style="text-align: center ; padding-top: 20px">تمامی حقوق این سایت متعلق به دانشگاه ازاد می باشد
    </p>
</footer>-->
<script src="../js/bootstrap.bundle.min.js"></script>

<?php
if (isset($_SESSION['login_success'])) {
    echo '<div id="success-box">'.escape($_SESSION['login_success']).'<button onclick="closeBox()">OK</button></div>';
    unset($_SESSION['login_success']);
}

if (isset($_SESSION['signup_success'])) {
    echo '<div id="success-box">'.escape($_SESSION['signup_success']).'<br><button onclick="closeBox()">OK</button></div>';
    unset($_SESSION['signup_success']);
}
?>
<style>
#success-box{
    position: fixed;
    top: 40px;
    right: 500px;
    background: rgb(246,243,240);
    color: #8c1313;
    padding: 25px;
    width: 350px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    z-index: 9999;
    font-size: 15px;
    text-align: center;
}

#success-box button{
    margin-top: 20px;
    background: #8c1313;
    color: rgb(246,243,240);
    border: none;
    padding: 10px 25px;
    border-radius: 10px;
    cursor: pointer;
}

#success-box button:hover{
    background: rgba(74, 70, 70, 0.68);
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
</style>
<script>
function closeBox(){
    const box = document.getElementById("success-box");
    if(box){
        box.remove();
    }
}
</script>


<div id="toast" role="status"></div>

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

</body>
</html>