<?php
// File: pages/profile.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Tự động đăng nhập nếu có remember token
Auth::autoLogin();

// Lấy thông tin user nếu đã đăng nhập
$isLoggedIn = Auth::isLoggedIn();
if (!$isLoggedIn) {
    header('Location: ../login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = '';
$message = '';

// Lấy thông tin chi tiết của user
try {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $email = $user['email'];
    }
} catch (Exception $e) {
    error_log("Error fetching user details: " . $e->getMessage());
    $message = "Error: Could not retrieve user information.";
}

// Cập nhật thông tin user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['username'] ?? $username;
    $newEmail = $_POST['email'] ?? $email;

    // Validate input
    if (empty($newUsername) || empty($newEmail)) {
        $message = "Username and email are required.";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        try {
            $updateStmt = $db->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $updateStmt->execute([$newUsername, $newEmail, $userId]);
            
            // Cập nhật session
            $_SESSION['username'] = $newUsername;
            
            $message = "Profile updated successfully!";
            $username = $newUsername; // Update for display
            $email = $newEmail; // Update for display
        } catch (Exception $e) {
            // Check for duplicate entry
            if ($e->getCode() == 23000) { // Integrity constraint violation
                $message = "Username or email already exists.";
            } else {
                $message = "An error occurred during the update.";
            }
            error_log("Error updating profile: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 2rem;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .profile-container h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }
        .profile-form .form-group {
            margin-bottom: 1.5rem;
        }
        .profile-form label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
        }
        .profile-form input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .profile-form button {
            width: 100%;
            padding: 0.75rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .profile-form button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="profile-container">
        <h1>My Profile</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="profile.php" method="POST" class="profile-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <button type="submit">Update Profile</button>
        </form>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
