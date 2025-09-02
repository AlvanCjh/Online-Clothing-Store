<?php
include '_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email = req('email');
    $password = req('password');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }

    // Validate: password
    if ($password == '') {
        $_err['password'] = 'Required';
    }

    // Login user
    if (!$_err) {
        $stm = $_db->prepare('
            SELECT * FROM user
            WHERE email = ? AND password = SHA1(?)
        ');
        $stm->execute([$email, $password]);
        $u = $stm->fetch();

        if ($u) {

            if ($u->role == 'Admin') {
            temp('info', 'Login successfully');
            $_SESSION['user'] = $u;
            $_SESSION['user_id'] = $u->id;
            login($u);
            } else {
                $_err['password'] = 'Only admin can login here.';
            }
        }
        else {
            $_err['password'] = 'Not matched';
        }
    }
}

// ----------------------------------------------------------------------------

$_title = '';
include '_head.php';
?>

<link rel="stylesheet" href="/css/login.css"> 



<form method="post" class="login">
    <h1>Admin Log in</h1>
    <div class="input-box">
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>
    </div>

    <div class="input-box">
    <label for="password">Password</label>
    <?= html_password('password', 'maxlength="100"') ?>
    <?= err('password') ?>
    </div>

    <section>
    <button class=button1>Login</button>
    <button class=button1 type="reset">Reset</button>
    </section>

    <div class="register">
        <p>Are you a member? <a href="login.php">Member Login</a></p>
    </div>
</form>


<?php
include '_foot.php';