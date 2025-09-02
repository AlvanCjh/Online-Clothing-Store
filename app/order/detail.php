<?php
include '../_base.php';

// ----------------------------------------------------------------------------

// (1) Authorization (member)
if (!isset($_SESSION['user_id'])) {
    die('Unauthorized access.');
}

$user_id = $_SESSION['user_id'];

// (2) Return order (based on id) belong to the user
$order_id = $_GET['order_id'] ?? $_POST['order_id'] ?? $_GET['id'] ?? $_POST['id'] ?? null;

if (!$order_id) {
    die('Error: Order ID is missing. Debug info: ' .print_r($_GET, true));
}

$stm = $_db->prepare("SELECT * FROM `order` WHERE id = ? AND user_id = ?");
$stm->execute([$order_id, $user_id]);
$o = $stm->fetch(PDO::FETCH_OBJ);

if (!$o) {
    die("Order not found. Debug info: order_id = $order_id, user_id = $user_id");
}

// (3) Return items (and products) belong to the order
$stm = $_db->prepare("SELECT i.product_id, p.name, i.price, i.unit, i.subtotal, p.photo FROM item i JOIN product p ON i.product_id = p.id WHERE i.order_id = ?");
$stm->execute([$order_id]);
$arr = $stm->fetchAll(PDO::FETCH_OBJ);

// ----------------------------------------------------------------------------

$_title = '<span style="color: white;">Order | Detail</span>';
include '../_head.php';
?>

<body>
    <link rel="stylesheet" href="../css/orderDetail.css">
</body>

<div class="print-receipt">
    <h2>Order Receipt</h2>

    <div class="receipt-header">
        <p><strong>Order ID:</strong> <?= htmlspecialchars($o->id) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($o->datetime) ?></p>
    </div>

    <table class="table">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Price (RM)</th>
            <th>Qty</th>
            <th>Subtotal (RM)</th>
        </tr>

        <?php foreach ($arr as $i): ?>
        <tr>
            <td><?= htmlspecialchars($i->product_id) ?></td>
            <td><?= htmlspecialchars($i->name) ?></td>
            <td class="right"><?= htmlspecialchars($i->price) ?></td>
            <td class="right"><?= htmlspecialchars($i->unit) ?></td>
            <td class="right"><?= htmlspecialchars($i->subtotal) ?></td>
        </tr>
        <?php endforeach ?>

        <tr>
            <th colspan="3">Total</th>
            <th class="right"><?= htmlspecialchars($o->count) ?>0</th>
            <th class="right">RM <?= htmlspecialchars($o->total) ?></th>
        </tr>
    </table>

    <div class="receipt-footer">
        <p>Thank you for your purchase!</p>
        <p>Printed on: <?= date('Y-m-d H:i:s') ?></p>
    </div>
    
</div>

<div class="no-print">
<form class="form">
    <label>Order Id</label>
    <b><?= htmlspecialchars($o->id) ?></b>
    <br>

    <label>Datetime</label>
    <div><?= htmlspecialchars($o->datetime) ?></div>
    <br>

    <label>Count</label>
    <div><?= htmlspecialchars($o->count) ?></div>
    <br>

    <label>Total</label>
    <div>RM <?= htmlspecialchars($o->total) ?></div>
    <br>
</form>

<p><?= count($arr) ?> item(s)</p>

<table class="table">
    <tr>
        <th>Product Id</th>
        <th>Product Name</th>
        <th>Price (RM)</th>
        <th>Unit</th>
        <th>Subtotal (RM)</th>
    </tr>

    <?php foreach ($arr as $i): ?>
    <tr>
        <td><?= htmlspecialchars($i->product_id) ?></td>
        <td><?= htmlspecialchars($i->name) ?></td>
        <td class="right"><?= htmlspecialchars($i->price) ?></td>
        <td class="right"><?= htmlspecialchars($i->unit) ?></td>
        <td class="right">
            <?= htmlspecialchars($i->subtotal) ?>
            <img src="/products/<?= htmlspecialchars($i->photo) ?>" class="popup">
        </td>
    </tr>
    <?php endforeach ?>

    <tr>
        <th colspan="3"></th>
        <th class="right"><?= htmlspecialchars($o->count) ?></th>
        <th class="right"><?= htmlspecialchars($o->total) ?></th>
    </tr>
</table>

<p class="no-print">

<button data-get="history.php">History</button>
<button id="Print">Print Receipt</button>

</p>
</div>

<script>
    document.getElementById('Print').addEventListener('click', function() {
        window.print();
    });
</script>


<?php
include '../_foot.php';