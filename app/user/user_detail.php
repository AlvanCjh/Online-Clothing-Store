<?php
include '../_base.php';

// ----------------------------------------------------------------------------

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    temp('error', 'Please login to view user details');
    redirect('/login.php');
}

// Fetch member details from database
try {
    $stm = $_db->prepare("SELECT * FROM user");
    $stm->execute();
    $members = $stm->fetchAll(PDO::FETCH_OBJ);
    
    if (!$members) {
        temp('info', 'No members found');
        redirect('/');
    }
} catch (PDOException $e) {
    temp('error', 'Database error');
    redirect('/');
}

// Fetch user order history
try {
    $stm = $_db->prepare("SELECT o.user_id, o.id AS order_id, o.datetime AS order_date, i.product_id, i.price, i.unit, i.subtotal
                          FROM `order` o 
                          JOIN item i ON o.id = i.order_id
                          ORDER BY o.datetime DESC");
    $stm->execute();
    $orders = $stm->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    temp('error', 'Database error');
    redirect('/');
}

$userOrders = [];
foreach ($orders as $order) {
    $userOrders[$order->user_id][] = $order;
}

// ----------------------------------------------------------------------------

$_title = '';
include '../_head.php';
?>

<body>
    <link rel="stylesheet" href="../css/userDetail.css">
</body>

<body>
    <div class="user-details">
        <h1>User Information</h1>
        <table border="1">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Profile Picture</th>
                <th>Order History</th>
            </tr>
            </thead>
            <?php foreach ($members as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user->name) ?></td>
                <td><?= htmlspecialchars($user->email) ?></td>
                <td>
                    <?php if (!empty($user->photo)): ?>
                        <img src="../photos/<?= htmlspecialchars($user->photo) ?>" alt="Profile Picture" width="50">
                    <?php endif; ?>
                </td>
                <td>
                    <div class="popup">
                        <table>
                            <tr>
                                <th>Oder ID</th>
                                <th>Product ID</th>
                                <th>Price</th>
                                <th>Unit</th>
                                <th>Subtotal</th>
                            </tr>
                            <?php if (isset($userOrders[$user->id])): ?>
                                <?php foreach ($userOrders[$user->id] as $order): ?>
                                    <tbody>
                                    <tr>
                                        <td><?= htmlspecialchars($order->order_id) ?></td>
                                        <td><?= htmlspecialchars($order->product_id) ?></td>
                                        <td><?= htmlspecialchars($order->price) ?></td>
                                        <td><?= htmlspecialchars($order->unit) ?></td>
                                        <td><?= htmlspecialchars($order->subtotal) ?></td>
                                    </tr>
                                    </tbody>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">No Order Records</td>
                                    </tr>
                                <?php endif; ?>
                        </table>
                    </div>
                    Hover to view orders
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

<?php
include '../_foot.php';