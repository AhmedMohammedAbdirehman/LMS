<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get the user ID from the URL
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$id = $_GET['id'];
$user_res = $conn->query("SELECT * FROM users WHERE id = '$id'");
$user = $user_res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = $_POST['role'];
    $new_password = $_POST['password'];

    if (!empty($new_password)) {
        // Only update password if a new one is typed
        // Validate strength (optional but recommended)
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
            $error = "New password does not meet security criteria.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $sql = "UPDATE users SET name='$name', email='$email', phone='$phone', password='$hashed_password', role='$role' WHERE id = '$id'";
        }
    } else {
        // Do NOT update password field if it is empty
        $sql = "UPDATE users SET name='$name', email='$email', phone='$phone', role='$role' WHERE id = '$id'";
    }

    if (!isset($error)) {
        if ($conn->query($sql) === TRUE) {
            header("Location: manage_users.php?msg=User Updated Successfully");
            exit();
        } else {
            $error = "Error updating record: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
    :root {
        --admin-primary: #2c3e50;
        --accent: #3498db;
        --success: #27ae60;
        --bg: #f8fafc;
        --border: #e2e8f0;
    }

    body {
        background-color: var(--bg) !important;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
        padding: 20px; /* Space for mobile edges */
        box-sizing: border-box;
    }

    /* Target the container from your HTML */
    .login-container {
        background: white !important;
        width: 100% !important;
        max-width: 500px !important; /* Perfect size for admin forms */
        padding: 40px !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
        border: 1px solid var(--border) !important;
    }

    h2 {
        color: var(--admin-primary);
        font-size: 1.5rem;
        margin-top: 0;
        margin-bottom: 25px;
        text-align: center;
        font-weight: 700;
    }

    /* Input Group Styling */
    .input-group {
        margin-bottom: 20px !important;
        width: 100% !important;
    }

    .input-group label {
        display: block !important;
        margin-bottom: 8px !important;
        font-weight: 600 !important;
        color: #64748b !important;
        font-size: 0.9rem !important;
    }

    .input-group input, 
    .input-group select {
        width: 100% !important;
        padding: 12px !important;
        border: 1.5px solid var(--border) !important;
        border-radius: 8px !important;
        font-size: 1rem !important;
        color: #1e293b !important;
        transition: all 0.2s ease !important;
        box-sizing: border-box !important; /* Critical for full width */
    }

    .input-group input:focus, 
    .input-group select:focus {
        border-color: var(--accent) !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1) !important;
    }

    /* The Submit Button */
    .btn-login {
        width: 100% !important;
        padding: 14px !important;
        background: var(--admin-primary) !important;
        color: white !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 1rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: background 0.2s !important;
        margin-top: 10px !important;
    }

    .btn-login:hover {
        background: #1a252f !important;
    }

    /* Back Link Styling */
    a[href="manage_users.php"] {
        display: block;
        text-align: center;
        margin-top: 15px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: color 0.2s;
    }

    a[href="manage_users.php"]:hover {
        color: var(--admin-primary) !important;
        text-decoration: underline !important;
    }

    /* Mobile Responsive Fixes */
    @media (max-width: 480px) {
        .login-container {
            padding: 25px 20px !important;
        }
        
        h2 {
            font-size: 1.25rem;
        }
    }

    /* The Edit User Title with Icon */
    h2 {
        color: var(--admin-primary);
        font-size: 1.5rem;
        margin-top: 0;
        margin-bottom: 30px;
        text-align: center;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    /* Injecting the Icon via CSS */
    h2::before {
        content: '';
        display: inline-block;
        width: 28px;
        height: 28px;
        background-color: var(--accent);
        /* This creates a modern 'User Edit' icon using a mask */
        -webkit-mask: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>') no-repeat center;
        mask: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>') no-repeat center;
    }

    /* Bonus: Add icons to the input labels for better UI tracking */
    .input-group label {
        display: flex !important;
        align-items: center;
        gap: 8px;
    }

    /* Optional: Small circle indicators for the role selector */
    select[name="role"] {
        border-left: 5px solid var(--accent) !important;
    }
</style>
</head>
<body>
    <div class="login-container">
        <form method="POST">
            <h2>Edit User Details</h2>
            
            <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>

            <div class="input-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo $user['phone']; ?>" required>
            </div>

         <div class="input-group">
            <label>Password (Leave blank to keep current)</label>
            <input type="password" name="password" placeholder="Enter new password ">
        </div>

            <div class="input-group">
                <label>Role</label>
                <select name="role" style="width: 100%; padding: 10px; border-radius: 4px;">
                    <option value="manager" <?php if($user['role']=='manager') echo 'selected'; ?>>Manager</option>
                    <option value="teacher" <?php if($user['role']=='teacher') echo 'selected'; ?>>Teacher</option>
                    <!-- <option value="student" <?php if($user['role']=='student') echo 'selected'; ?>>Student</option> -->
                </select>
            </div>

            <button type="submit" class="btn-login">Save Changes</button>
            <br><br>
            <a href="manage_users.php" style="text-decoration:none; color:#7f8c8d;">Cancel and Go Back</a>
        </form>
    </div>
</body>
</html>