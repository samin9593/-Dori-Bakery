<?php
require __DIR__ . '/common.php';
$error = '';
$emailValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post();
    $emailValue = strtolower(post_text('email'));
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $error = 'Incorrect email or password.';

    if (strlen($emailValue) <= 60 && filter_var($emailValue, FILTER_VALIDATE_EMAIL) && valid_password($password)) {
        require __DIR__ . '/db.php';
        $result = $conn->execute_query('SELECT name, email, password FROM users WHERE email = ?', [$emailValue]);
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $conn->execute_query(
                "INSERT INTO user_logs (user_email, action_type) VALUES (?, 'login')",
                [$user['email']]
            );
            sign_in($user['email'], $user['name']);
            $_SESSION['login_success'] = 'Welcome! Logged in successfully.';
            header('Location: ../video/mainpage.php', true, 303);
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dori-login</title>
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

<h1 style="
position:absolute;
left:150px;
top:200px;
font-size:70px;
">
Log In
</h1>

    <form action="login.php" method="POST" style="margin-top: 230px; float:right; margin-right:150px;">
                    <?= csrf_field() ?>

        <div class="login-box">
            <label for="email" style="font-size:15px; margin-right:27px;">Email:</label>
            <input
                id="email" name="email"
                type="email"
                class="loin-box"
                value="<?php echo htmlspecialchars($emailValue); ?>"
                required
            >
            <br><br>
        </div>

        <div class="login-box">
            <label for="password" style="font-size:15px; margin-right:5px;">Password: </label>
            <input
                id="password" name="password"
                type="password"
                class="loin-box"
                required
            >

            <?php if (!empty($error)) { ?>
            <div class="error-message"><?= escape($error) ?></div>
            <?php } ?>

            <br><br>
        </div>

        <button class="button" type="submit">LOG IN</button>

        <p>New here? <a href="signup.php" style="color:inherit">Sign up</a>.</p>
    </form>
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
        margin-top: 30px;
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

    .error-message {
        color: #e32400;
        font-size: 14px;
        margin-top: 10px;
        margin-left: 80px;
        font-weight: bold;
        
    }
    .second-line {
    font-size: 12px;
    color: #f8efe4;
    
}
    .signup-link{
    color: #f8efe4;
    text-decoration: underline;
    font-weight: bold;
}

.signup-link:hover{
    color: #595353;
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