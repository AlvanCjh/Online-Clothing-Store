<?php
include '../_base.php';

// ----------------------------------------------------------------------------

$user_id = $_SESSION['user_id'];

$stm = $_db->prepare("SELECT name, email, photo, role FROM user WHERE id = ?");
$stm->execute([$user_id]);
$user = $stm->fetch();

if (is_post()) {
    $btn = req('btn');
    if ($btn == 'clear') {
        set_cart();
        redirect('?');
    }

    $id   = req('id');
    $unit = req('unit');
    update_cart($id, $unit);
    redirect();
}

// ----------------------------------------------------------------------------

$_title = '<span style="color: white;">Order | Shopping Cart</span>';
include '../_head.php';
?>

<style>
    .popup {
        width: 100px;
        height: 100px;
    }
</style>

<table class="table">
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Price (RM)</th>
        <th>Unit</th>
        <th>Subtotal (RM)</th>
    </tr>

    <?php
        $count = 0;
        $total = 0; 
        
        $stm = $_db->prepare('SELECT * FROM product WHERE id = ?');
        $cart = get_cart();
        
        foreach ($cart as $id => $unit):
            $stm->execute([$id]);
            $p = $stm->fetch();

            if (!$p) {
                continue;
            }
            
            $subtotal = $p->price * $unit;
            $count += $unit;
            $total += $subtotal; 
    ?>
        <tr>
            <td><?= htmlspecialchars($p->id) ?></td>
            <td><?= htmlspecialchars($p->name) ?></td>
            <td class="right"><?= sprintf('%.2f', $p->price) ?></td>
            <td>
                <form method="post">
                    <?= html_hidden('id', $id) ?>
                    <?= html_select('unit', $_units, $unit) ?>
                </form>            
            </td>
            <td class="right">
                <?= sprintf('%.2f', $subtotal) ?>
                <img src="/products/<?= $p->photo ?>" class="popup">
            </td>
        </tr>
    <?php endforeach ?>

    <tr>
        <th colspan="3"></th>
        <th class="right"><?= $count ?></th>
        <th class="right"><?= sprintf('%.2f', $total) ?></th>
    </tr>
</table>

<p>
    <?php if ($cart): ?>
        <button data-post="?btn=clear">Clear</button>

        <?php if ($user && $user->role != 'Admin'): ?>
            <button data-post="checkout.php">Checkout</button>
        <?php else: ?>
            Please <a href="/login.php">login</a> as member to checkout
        <?php endif ?>
    <?php endif ?>
</p>

<script>
    $('select').on('change', e => e.target.form.submit());
</script>

<?php
include '../_foot.php';