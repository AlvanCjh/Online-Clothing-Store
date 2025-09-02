<?php
include '../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    if ($_user->role == 'Admin' && isset($_POST['add_product'])) {
        $id = req('id');
        $name = req('name');
        $price = req('price');
        $photo = req('photo');
        $description = req('description');

        if (!$id || !$name || !$price || !$photo || !$description) {
            $error = 'All fields are required';
        } else {
            $allowedExtensions = ['png', 'jpg', 'gif', 'jpeg'];
            $fileExtension = strtolower(pathinfo($photo, PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                $error = 'Invalid photo file type. Allowed types: png, jpg, gif, jpeg. ';
            } else {
                $stm = $_db->prepare('INSERT INTO product (id, name, price, photo, description) VALUES (?, ?, ?, ?, ?)');
                $stm->execute([$id, $name, $price, $photo, $description]);
                
                temp('info', 'Product added successfully!');
                redirect();
            }
        }
    } elseif ($_user->role != 'Admin') {
        $id   = req('id');
        $unit = req('unit');
        update_cart($id, $unit);
        redirect();
    }
}

$arr = $_db->query('SELECT * FROM product');

// ----------------------------------------------------------------------------


$_title = '<span style="color: white;">Product | List</span>';
include '../_head.php';
?>

<head>
    <link rel="stylesheet" href="../css/productList.css">
</head>

<div id="products">
    <?php foreach ($arr as $p): ?>
        <?php
        $cart = get_cart();
        $id   = $p->id;
        $unit = $cart[$p->id] ?? 0;
        ?>
        <div class="product">
            <form method="post">
                <?= $unit ? '✅' : '' ?>
                <?= html_hidden('id') ?>
                <?= html_select('unit', $_units, '') ?>
            </form>
                
            <img src="/products/<?= $p->photo ?>"
                 data-get="/product/detail.php?id=<?= $p->id ?>">

            <div><?= $p->name ?> | RM <?= $p->price ?></div>
        </div>
    <?php endforeach ?>
</div>

<?php if ($_user->role == 'Admin'): ?>
<div class="add-card">
    <h2>Add New Product</h2>
    <form method="post">
        <div class="input-box">
        <label>Product ID:</label>
        <input name="id" required><br>
        </div>

        <div class="input-box">
        <label>Product Name:</label>
        <input name="name" required><br>
        </div>

        <div class="input-box">
        <label>Price (RM):</label>
        <input type="number" name="price" step="0.01" required><br>
        </div>

        <label>Photo:</label>
        <input type="file" input name="photo" required><br>

        <div class="input-box">
        <label>Description:</label>
        <input type="text" name="description" required><br>
        </div>
        
        <button class=button1 name="add_product">Add Product</button>
    </form>
</div>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif ?>
<?php endif ?>

<script>
    $('select').on('change', e => e.target.form.submit());
</script>

<?php
include '../_foot.php';