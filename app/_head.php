<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Untitled' ?></title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/footer.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="/js/app.js"></script>
</head>
<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <header>
        <h1><a href="/">
            <img src="/images/Y.png" alt="Yobisual Logo" class="logo">
            Yobisual
            </a>
        </h1>

        <?php if ($_user): ?>
            <div>
                <?= $_user->name ?><br>
                <?= $_user->role ?>
            </div>
            <img src="/photos/<?= $_user->photo ?>">
        <?php endif ?>
    </header>

    <nav>
        <a href="/">Menu</a>
        <a href="/product/list.php">Product List</a>
        <?php if ($_user && $_user->role != 'Admin'): ?>
            <a href="/order/cart.php">Shopping Cart</a>
            <?php
                $cart = get_cart();
                $count = count($cart);
                if ($count) echo "($count)";
            ?>
        <?php endif ?>

        <?php if ($_user && $_user->role == 'Admin'): ?>
            <a href="/user/user_detail.php">User Detail</a>
        <?php endif ?>
        

        <?php if ($_user && $_user->role != 'Admin'): ?>
            <a href="/order/history.php">Order History</a>
        <?php endif ?>

        <div></div>

        <?php if ($_user): ?>
            <a href="/user/profile.php">Profile</a>
            <a href="/user/edit_profile.php">Edit Profile</a>
            <a href="/logout.php">Logout</a>
        <?php else: ?>
            <a href="/user/register.php">Register</a>
            <a href="/login.php">Login</a>
        <?php endif ?>
    </nav>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>