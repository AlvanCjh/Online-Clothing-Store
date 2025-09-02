<?php
include '../_base.php';

// ----------------------------------------------------------------------------

if (!isset($_SESSION['user_id'])) {
    die("Session error: User not logged in");
}

$user_id = $_SESSION['user_id'];

if (!isset($_db)) {
    die("Database connection error");
}

$stm = $_db->prepare("SELECT name, email, photo FROM user WHERE id = ?");
$stm->execute([$user_id]);
$user = $stm->fetch();

if (!$user) {
    die("User not found");
}

$name = $user->name;
$email = $user->email;
$photo = $user->photo;
$password = $comfirm_password = "";
$_err = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $photo = $user->photo;
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);


if ($name == '') {
    $_err['name'] = 'Name is required';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_err['email'] = 'Invalid email format';
}

if (!empty($password) || !empty($confirm_password)) {
    if ($password !== $confirm_password) {
        $_err['password'] = 'Passwords do not match'; 
    } else if (strlen($password) < 6) {
        $_err['password'] = 'Password must be at least 6 characters';
    } else {
        $password_hashed = sha1($password);
    }
}

if (!empty($_POST['cropped_photo'])){
    $cropped_photo = $_POST['cropped_photo'];
    $photo = "profile_" . time() . ".jpg"; 

    $cropped_photo = str_replace('data:image/jpeg;base64,', '', $cropped_photo);
    $cropped_photo = base64_decode($cropped_photo);
    file_put_contents("../photos/" . $photo, $cropped_photo);
} else if (!empty($_FILES['photo']['name'])) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        $_err['photo'] = 'Invalid file type (jpg, jpeg, png, gif only)';
    } else {
        $photo = "profile_" . time() . ".$ext";
        move_uploaded_file($_FILES['photo']['tmp_name'], "../photos/" . $photo); 
    }
} else {
    $photo = $user->photo;
}

if (empty($_err)) {
    if (!empty($password_hashed)) {
        $stm = $_db->prepare("UPDATE user SET name = ?, email = ?, photo = ?, password = ? WHERE ID = ?");
        $stm->execute([$name, $email, $photo, $password_hashed, $user_id]);
    } else {
        $stm = $_db->prepare("UPDATE user SET name = ?, email = ?, photo = ? WHERE id = ?");
        $stm->execute([$name, $email, $photo, $user_id]);
    }

    $_SESSION['photo'] = $photo;

    temp('info', 'Profile updated successfully!');
}
}

// ----------------------------------------------------------------------------

$_title = '';
include '../_head.php';
?>

<head>
    <link rel="stylesheet" href="../css/profileEdit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
</head>

<body>
    
    <div class="profile-card">
           <h1>Edit Profile</h1>
           <form method="post" enctype="multipart/form-data">
           
           <div class="input-box">
           <label>Name:</label>
           <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
           <?= $_err['email'] ?? '' ?>
           </div>

           <div class="input-box">
           <label>Email:</label>
           <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
           <?= $_err['email'] ?? '' ?>
           </div>

           <label>Profile Picture:</label>
           <input type="file" id="photo-input" name="photo" style="display: none;">
           <button class="button1" type="button" id="edit-photo-button">Edit Photo</button>

           <!-- Cropping Area -->
           <div id="cropper-container" style="display: none;">
             <img id="cropper-image" style="max-width: 100%;"/>
             <button type="button" id="crop-button">Crop</button>
            </div>

            <img id="preview" src="<?= isset($photo) ? '../photos/' . htmlspecialchars($photo) : '../photos/default.jpg' ?>" width="100">
           <input type="hidden" name="cropped_photo" id="cropped-photo">
           <?= $_err['photo'] ?? '' ?>


           <div class="input-box">
           <label>New Password (Leave blank to keep current password):</label>
           <input type="password" name="password">
           </div>

           <div class="input-box">
           <label>Confirm Password:</label>
           <input type="password" name="confirm_password">
           <div class="error">
           <?= $_err['password'] ?? '' ?>
           </div>
           </div>
           
           <button type="submit">Update</button>
           
  </div>
  </form>

<!-- Cropper js -->
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


</body>

<?php
include '../_foot.php';