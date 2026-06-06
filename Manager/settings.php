<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? $_SESSION['id'];
$msg = "";

// 1. Fetch current data to populate the form
$query = $conn->query("SELECT name, email FROM users WHERE id = '$user_id'");
$userData = $query->fetch_assoc();

// 2. Handle Update Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Update name and email
    $sql = "UPDATE users SET name='$name', email='$email'";

    // Prepare log details
    $log_details = "Manager updated profile details (Name: $name, Email: $email)";

    // Only update password if the field isn't empty
    if (!empty($password)) {
        // Correctly hashing the password before saving
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $sql .= ", password='$hashed_password'";
        $log_details .= " and changed their password";
    }

    $sql .= " WHERE id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        // --- LOG THE ACTIVITY ---
        if (function_exists('logActivity')) {
            logActivity($conn, $user_id, 'UPDATE', $log_details);
        }

        $_SESSION['user_name'] = $name; // Update session name in case it changed
        $msg = "<p style='color: green;'>Profile updated successfully!</p>";
        
        // Refresh local data for the form
        $userData['name'] = $name;
        $userData['email'] = $email;
    } else {
        $msg = "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - LMS Admin</title>
    
    <style>
        .settings-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: 40px auto;
        }
        /* Modern CSS for Admin Settings Card */
:root {
    --primary: #1a0b45;    /* Deep Navy */
    --accent: #2ecc71;     /* Success Green */
    --text-main: #2c3e50;
    --text-muted: #7f8c8d;
    --bg-light: #f8fafc;
    --white: #ffffff;
}

.settings-card {
    background: var(--white);
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    max-width: 450px;
    margin: 40px auto;
    text-align: center;
}

/* Profile Icon Styling */
.avatar-preview {
    width: 80px;
    height: 80px;
    background: var(--primary);
    color: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    margin: 0 auto 20px;
    box-shadow: 0 4px 12px rgba(26, 11, 69, 0.2);
}

h2 { 
    margin: 0 0 10px 0; 
    color: var(--primary); 
    font-size: 1.5rem; 
    font-weight: 800;
}

.input-group { 
    text-align: left;
    margin-bottom: 20px; 
}

.input-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--primary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.input-group input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #edf2f7;
    border-radius: 10px;
    box-sizing: border-box;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--bg-light);
}

/* Interactive Focus State */
.input-group input:focus {
    border-color: var(--accent);
    outline: none;
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1);
}

/* Update Button Styling */
.btn-login {
    width: 100%;
    padding: 14px;
    background: var(--accent);
    color: var(--white);
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(46, 204, 113, 0.2);
    margin-top: 10px;
}

.btn-login:hover {
    background: #27ae60;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(46, 204, 113, 0.3);
}

.btn-login:active {
    transform: translateY(0);
}

/* Message Box Styling */
.settings-card p {
    font-size: 0.9rem;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 20px;
}

/* Back Link Styling */
.back-link {
    display: inline-block;
    margin-top: 25px;
    text-decoration: none;
    color: var(--text-muted);
    font-size: 0.9rem;
    font-weight: 500;
    transition: color 0.2s;
}

.back-link:hover {
    color: var(--primary);
}
    </style>
</head>
<body style="background: #f4f7f6; font-family: sans-serif;">

    <div class="settings-card">
        <h2 data-lang="account_settings_title">Account Settings</h2>
        <?php echo $msg; ?>
        
        <form action="settings.php" method="POST">
            <div class="input-group">
                <label data-lang="label_full_name">Full Name</label>
                <input type="text" name="name" value="<?php echo $userData['name']; ?>" required>
            </div>
            
            <div class="input-group">
                <label data-lang="label_email_address">Email Address</label>
                <input type="email" name="email" value="<?php echo $userData['email']; ?>" required>
            </div>
            
            <div class="input-group">
                <label data-lang="label_new_password">New Password (leave blank to keep current)</label>
                <input type="password" name="password" data-lang-placeholder="placeholder_password" placeholder="********">
            </div>
            
            <button type="submit" class="btn-login" data-lang="btn_update_profile">Update Profile</button>
            <br><br>
            <a href="dashboard.php" style="color: #7f8c8d; text-decoration: none;">← <span data-lang="btn_back_dashboard">Back to Dashboard</span></a>
        </form>
    </div>



<script src="/lms_tech/lang.js"></script>


</body>
</html>