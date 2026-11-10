<?php
require __DIR__ . '/common.php';
$nameValue = $mobileValue = $addressValue = $emailValue = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post();
    $nameValue = post_text('name');
    $mobileValue = post_text('mobile');
    $addressValue = post_text('address');
    $emailValue = strtolower(post_text('email'));
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $confirmation = $_POST['confirm_password'] ?? null;

    if ($nameValue === '' || mb_strlen($nameValue) > 30 || !valid_phone($mobileValue, 15)
        || $addressValue === '' || mb_strlen($addressValue) > 500
        || strlen($emailValue) > 60 || !filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid name, phone number, address and email.';
    } elseif (!valid_password($password)) {
        $error = 'Use a password between 8 and 72 bytes.';
    } elseif ($password !== $confirmation) {
        $error = 'Passwords do not match.';
    } else {
        require __DIR__ . '/db.php';
        try {
            $conn->execute_query(
                'INSERT INTO users (name, mobile, address, email, password) VALUES (?, ?, ?, ?, ?)',
                [$nameValue, $mobileValue, $addressValue, $emailValue, password_hash($password, PASSWORD_BCRYPT)]
            );
            sign_in($emailValue, $nameValue);
            $_SESSION['signup_success'] = 'Registration successful!';
            header('Location: ../video/mainpage.php', true, 303);
            exit;
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() !== 1062) {
                throw $exception;
            }
            $error = 'This email is already registered.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dori-signup</title>
</head>

<body style="background-image: url('../image2/login7.jpeg'); background-size:cover;">

<div class="box" style="
background: rgba(89,83,83,0.38);
float: left;
margin-left: 200px;
margin-top: 120px;
width:1000px;
height:550px;
backdrop-filter: blur(10px);
border: 1px solid rgba(255, 255, 255, 0.2);
border-radius: 50px;
color: #f8efe4;
padding: 20px;">

    <h1 style="font-size: 70px; margin-top:100px; margin-left: 30px;">Sign up</h1>
    <h5 style="font-size: 30px; width:500px; margin-left: 30px;">
        If you would like to be informed about discounts, events and upcoming holidays
    </h5>

    <form action="signup.php" method="POST" style="margin-top: -250px; float:right; margin-right:25px ; margin-top:-300px">
                    <?= csrf_field() ?>

        <div class="login-box">
            <label style="font-size:15px; margin-right:27px;" for="name">Name:</label>
            <input id="name" name="name" type="text" class="loin-box"
                   value="<?php echo htmlspecialchars($nameValue); ?>" required>
            <br><br>
        </div>

        <div class="login-box">
            <label style="font-size:15px; margin-right:20px;" for="mobile">Mobile:</label>
            <input id="mobile" name="mobile" type="text" class="loin-box"
                   value="<?php echo htmlspecialchars($mobileValue); ?>" required>
            <br><br>
        </div>

        <div class="login-box">
            <label style="font-size:15px; margin-right:14px;" for="address">Address:</label>
            <input id="address" name="address" type="text" class="loin-box"
                   value="<?php echo htmlspecialchars($addressValue); ?>" required>
            <br><br>
        </div>

        <div class="login-box">
            <label style="font-size:15px; margin-right:27px;" for="email">Email:</label>
            <input id="email" name="email" type="email" class="loin-box"
                   value="<?php echo htmlspecialchars($emailValue); ?>" required>
            <br><br>
        </div>

        <div class="login-box">
            <label style="font-size:15px; margin-right:5px;" for="password">Password:</label>
            <input id="password" name="password" type="password" class="loin-box" required>
            <br><br>
        </div>

        <div class="login-box" style="margin-left:-40px;">
            <label style="font-size:15px;" for="confirm_password">Repeat Password:</label>
            <input id="confirm_password" name="confirm_password" type="password" class="loin-box" required>

            <?php if (!empty($error)) { ?>
                <div class="error-message"><?= escape($error) ?></div>
            <?php } ?>
            <br><br>
        </div>
        <br>
        <button class="button" type="submit">SIGN UP</button>

    </form>

    <footer style="margin-left:30px; color: #b51a00">
        if you have already signed up, just
        <a href="login.php" class="login">login</a>
    </footer>
</div>

<style>
    .loin-box {
        border-radius: 10px;
        background-color: #f8efe4;
        width: 300px;
        height: 35px;
        font-size: 15px;
        box-shadow: 0 8px 10px rgba(248, 239, 228, 0.59);
        border: none;
        padding-left: 10px;
    }

    
    .login-box input {
        color: #8c1313;
    }

    .login-box input:focus {
        outline: none;
        border: 2px solid #f8efe4;
    }

    .button {
        background-color: #f8efe4;
        float: right;
        width: 100px;
        margin-right: 100px;
        color: #595353;
        border-radius: 10px;
        height: 35px;
        border: none;
        cursor: pointer;
    }

    .button:hover {
        background-color: #8c1313;
        color: #f8efe4;
    }

    .login {
        margin-left: 10px;
        color: #f8efe4;
        text-decoration: none;
    }

    .login:hover {
        color: #595353;
    }

    .error-message {
        color: #b51a00;
        font-size: 14px;
        margin-top: 12px;
        margin-left: 120px;
    }

    @media (max-width: 1260px) {
        .box { box-sizing: border-box; float: none !important; width: min(100%, 600px) !important;
               height: auto !important; margin: 35px auto !important; }
        .box h1 { position: static !important; font-size: 44px !important; margin: 0 0 20px !important; }
        .box h5 { width: auto !important; margin: 0 0 20px !important; font-size: 20px !important; }
        .box form { float: none !important; margin: 0 !important; }
        .login-box { margin: 0 !important; }
        .loin-box { box-sizing: border-box; max-width: 100%; display: block; }
    }
</style>

</body>
</html>