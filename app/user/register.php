<?php
include '../_base.php';

// ----------------------------------------------------------------------------

$_err = [];
$name ='';
$email ='';
$password ='';

if (is_post()) {
    $name = req('name');
    $email = req('email');
    $password = req('password');
}

if ($name == '') {
    $_err['name'] = 'Name is required';
} else if ($email == '') {
    $_err['email'] = 'Email is required';
} else if (!is_email($email)) {
    $_err['email'] = 'Invalid email format';
} else {
    $stm = $_db->prepare('SELECT * FROM user WHERE email = ?');
    $stm->execute([$email]);
    $existing_user = $stm->fetch();

    if ($existing_user) {
        $_err['email'] = 'Email is already registered';
    }  
}

if ($password == '') {
    $_err['password'] = 'Password is required'; 
} else if (strlen($password) < 6) {
    $_err['password'] = 'Password must be at least 6 characters long';
}

if (!$_err) {
    $hashed_password = sha1($password);

    $stm = $_db->prepare('
        INSERT INTO user (name, email, password)
        VALUES (?, ?, ?)
    ');
    $stm->execute([$name, $email, $hashed_password]);

    if ($stm->rowCount() > 0) {
        temp('info', 'Registration successful! Please login.');
        redirect('../login.php');
    } else {
        temp('error', 'Registration failed. Please try again.');
  }
}






// ----------------------------------------------------------------------------

$_title = '';
include '../_head.php';
?>

<head>
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>
    <form method="post" class="register">
        <h1>Register</h1>

        <?php if (temp('info')): ?>
            <div style="color: red;"><?= temp('info') ?></div>
        <?php endif; ?>
        <?php if (temp('error')): ?>
            <div style="color: red;"><?= temp('error') ?></div>
        <?php endif; ?>

        <div class="input-box">
            <label for="name">Name</label>
            <?= html_text('name', 'maxlength="100"') ?>
            <?= err('name') ?>
        </div>

        <div class="input-box">
            <label for="email">Email</label>
            <?= html_text('email', 'maxlength="100"') ?>
            <?= err('email') ?>
        </div>

        <div class="input-box">
            <label for="password">Password</label>
            <?= html_text('password', 'maxlength="100"') ?>
            <?= err('password') ?>
        </div>

        <section>
            <button>Register</button>
            <button type="reset">Reset</button>
        </section>

        <div class="register-link">
            <p>Already have an account? <a href="../login.php">Login</a></p>
        </div>
    </form>
</body>

<?php
include '../_foot.php';