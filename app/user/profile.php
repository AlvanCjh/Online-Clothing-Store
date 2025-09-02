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

$stm = $_db->prepare("SELECT name, email, photo, role FROM user WHERE id = ?");
$stm->execute([$user_id]);
$user = $stm->fetch();

if (!$user) {
    die("User not found");
}


// ----------------------------------------------------------------------------

$_title = '';
include '../_head.php';
?>

<head>
    <link rel="stylesheet" href="../css/profile.css">
</head>

<body>
    <div class="welcome">
         <h1>Welcome, <?php echo htmlspecialchars($user->name); ?>!</h1>
    </div>
    <div class="profile-container">
      <div class="profile-card">
          <div class="profile-details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user->name); ?></p>
            <img src="../photos/<?php echo htmlspecialchars($user->photo); ?>" alt="Profile Picture" class="profile-pic">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user->email); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user->role); ?></p>
          </div>

            <a href="edit_profile.php" class="btn">Edit Profile</a>
        </div>
    </div>
</body>

<?php
include '../_foot.php';