<?php
include '../_base.php';

// ----------------------------------------------------------------------------

auth('Admin');

$id = req('id');
$stm = $_db->prepare('SELECT * FROM product WHERE id = ?');
$stm->execute([$id]);
$product = $stm->fetch();

if (is_post()) {
    if (isset($_POST['delete'])) {
        $stm = $_db->prepare('DELETE FROM product WHERE id = ?');
        $stm->execute([$id]);

        temp('info', 'Product deleted successfully!');
        redirect('/product/list.php');
    } else {
        $name = req('name');
        $price = req('price');
        $description = req('description');
        $photo = $product->photo; // Default to existing photo

        if (!$name || !$price || !$description) {
            $error = 'All fields are required';
        } else {
            if (!empty($_POST['cropped_photo'])) {
                $cropped_photo = $_POST['cropped_photo'];
                $photo = "product_" . time() . ".jpg";
                
                $cropped_photo = str_replace('data:image/jpeg;base64,', '', $cropped_photo);
                $cropped_photo = base64_decode($cropped_photo);
                file_put_contents("../products/" . $photo, $cropped_photo);
            } else if (!empty($_FILES['photo']['name'])) {
                $allowedExtensions = ['png', 'jpg', 'gif', 'jpeg'];
                $fileExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $error = 'Invalid photo file type. Allowed types: png, jpg, gif, jpeg.';
                } else {
                    $photo = "product_" . time() . ".$fileExtension";
                    move_uploaded_file($_FILES['photo']['tmp_name'], "../products/" . $photo);
                }
            }
            
            if (!isset($error)) {
                $stm = $_db->prepare('UPDATE product SET name = ?, price = ?, photo = ?, description = ? WHERE id = ?');
                $stm->execute([$name, $price, $photo, $description, $id]);

                temp('info', 'Product updated successfully!');
                redirect();
            }
        }
    }
}



// ----------------------------------------------------------------------------

$_title = '<span style="color: white;">Product | Edit Detail</span>';
include '../_head.php';
?>

<body>
    <link rel="stylesheet" href="../css/productEdit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
</body>

<div class="Edit-card">
<h1>Edit Product</h1>
<form method="post">
    <label>Product Name:</label>
    <input name="name" value="<?= htmlspecialchars($product->name) ?>">
    
    <label>Price (RM):</label>
    <input type="number" name="price" value="<?= htmlspecialchars($product->price) ?>" step="0.01">

    <label>Photo:</label>
    <input type="file" id="photo-input" name="photo" style="display: none;">
    <button type="button" id="edit-photo-button">Edit Photo</button>

    <div id="cropper-container" style="display: none;">
        <img id="cropper-image" style="max-width: 100%;"/>
        <button type="button" id="crop-button">Crop</button>
    </div>

    <img id="preview" src="../products/<?= htmlspecialchars($product->photo) ?>" width="100">
    <input type="hidden" name="cropped_photo" id="cropped-photo">

    <label>Description:</label>
    <input type="text" name="description" value="<?= htmlspecialchars($product->description) ?>">
    
    <button type="submit">Update Product</button>
</form>

<form method="post" onsubmit="return confirm('Are you sure you want to delete this product?');">
    <input type="hidden" name="delete" value="1">
    <button type="submit">Delete Product</button>
</form>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif ?>

<div class="list">
<p>
    <a href="/product/detail.php">List</a>
</p>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
    let cropper;
    const photoInput = document.getElementById('photo-input');
    const editPhotoButton = document.getElementById('edit-photo-button');
    const cropperContainer = document.getElementById('cropper-container');
    const cropperImage = document.getElementById('cropper-image');
    const preview = document.getElementById('preview');
    const croppedPhotoInput = document.getElementById('cropped-photo');

    editPhotoButton.addEventListener('click', function() {
        photoInput.click();
    });

    photoInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                cropperImage.src = e.target.result;
                cropperContainer.style.display = 'block';
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(cropperImage, {
                    aspectRatio: 1,
                    viewMode: 2
                });
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('crop-button').addEventListener('click', function() {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({ width: 200, height: 200 });
            const croppedImage = canvas.toDataURL("image/jpeg");
            preview.src = canvas.toDataURL();
            croppedPhotoInput.value = croppedImage;
        }
    });
</script>

<?php
include '../_foot.php';