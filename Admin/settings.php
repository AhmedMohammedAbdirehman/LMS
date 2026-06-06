<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
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

    // Only update password if the field isn't empty
    if (!empty($password)) {
        $sql .= ", password='$password'";
    }

    $sql .= " WHERE id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['user_name'] = $name; // Update session name in case it changed
        $msg = "<p style='color: green;'>Profile updated successfully!</p>";
        // Refresh local data
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






    /* Global Styles */
    * { box-sizing: border-box; }
    .admin-dashboard { 
        display: grid; 
        grid-template-columns: 250px 1fr; 
        min-height: 100vh; 
        background: #f4f7f6; 
        font-family: 'Segoe UI', sans-serif; 
    }
    
    .admin-sidebar { background: #1a0b45; color: white; padding: 30px 20px; }
    .admin-sidebar h3 { margin-bottom: 30px; color: #2ecc71; font-size: 1.2rem; }
    .admin-sidebar nav a { 
        display: block; color: #bdc3c7; text-decoration: none; 
        padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1); 
        transition: 0.3s; font-size: 0.95rem;
    }
    .admin-sidebar nav a:hover, .admin-sidebar nav a.active { color: #2ecc71; padding-left: 10px; }
    
    .message-center { padding: 40px; max-width: 1200px; }
    .inbox-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .inbox-header h2 { font-size: 1.5rem; margin: 0; }
    
    .stats-mini span { background: #2ecc71; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; }
    
    .message-card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-left: 5px solid #eee; transition: 0.3s; }
    .message-card.unread-border { border-left-color: #2ecc71; }
    .message-card h4 { margin: 10px 0; font-size: 1.1rem; color: #2c3e50; }
    .message-card p { font-size: 0.9rem; color: #7f8c8d; line-height: 1.4; }
    
    .msg-meta { display: flex; justify-content: space-between; font-size: 0.8rem; color: #95a5a6; margin-bottom: 5px; }
    .msg-actions { margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap; }
    
    .btn-open, .btn-reply { 
        padding: 8px 16px; border-radius: 6px; text-decoration: none; 
        font-size: 0.8rem; font-weight: 600; transition: 0.2s;
    }
    .btn-open { background: #1a0b45; color: white; border: none; cursor: pointer; }
    .btn-reply { border: 1px solid #1a0b45; color: #1a0b45; }

    /* Modal Styles */
    .modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center;
    }
    .modal-content {
        background: white; width: 600px; max-width: 95%; border-radius: 15px;
        padding: 25px; animation: slideDown 0.3s ease; position: relative;
    }
    .message-text { font-size: 0.95rem; padding: 15px 0; line-height: 1.6; color: #34495e; }

    /* --- MOBILE ADJUSTMENTS (The Fix) --- */
    @media (max-width: 768px) {
        .admin-dashboard {
            grid-template-columns: 1fr; /* Stack sidebar and content */
        }
        
        .admin-sidebar {
            padding: 15px;
            text-align: center;
        }

        .admin-sidebar h3 { margin-bottom: 15px; }

        .admin-sidebar nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .admin-sidebar nav a {
            border: none;
            font-size: 0.8rem;
            padding: 5px 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }

        .message-center {
            padding: 20px 15px;
        }

        .inbox-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .inbox-header h2 { font-size: 1.2rem; }

        .message-card h4 { font-size: 1rem; }
        .message-card p { font-size: 0.85rem; }

        .modal-content {
            padding: 15px;
            width: 90%;
        }
        
        .modal-header h3 { font-size: 1.1rem; }
        .sender-info p { font-size: 0.8rem; }
    }

    </style>
</head>
<body style="background: #f4f7f6; font-family: sans-serif;">

<div class="admin-dashboard">
    <aside class="admin-sidebar">
        <h3>Tech Iftiin Panel</h3>
        <nav>
            <a href="dashboard.php"><i class="fas fa-chart-line"></i> <span data-lang="dashboard">Dashboard</span></a>
            <a href="admin_messages.php" class="active"><i class="fas fa-envelope"></i> <span data-lang="messages">Messages</span></a>
            <a href="manage_users.php"><i class="fas fa-users"></i> <span data-lang="manage_users">Manage Users</span></a>
            <a href="courses.php"><i class="fas fa-book"></i> <span data-lang="manage_courses">Courses</span></a>
            <a href="../auth/logout.php" style="color: #e74c3c; margin-top: 20px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <div class="settings-card">
        <h2 data-lang="account_settings">Account Settings</h2>
        <?php echo $msg; ?>
        
        <form action="settings.php" method="POST">
            <div class="input-group">
                <label data-lang="full_name">Full Name</label>
                <input type="text" name="name" value="<?php echo $userData['name']; ?>" required>
            </div>
            
            <div class="input-group">
                <label data-lang="email_address">Email Address</label>
                <input type="email" name="email" value="<?php echo $userData['email']; ?>" required>
            </div>
            
            <div class="input-group">
                <label data-lang="password_label">New Password (leave blank to keep current)</label>
                <input type="password" name="password" placeholder="********">
            </div>
            
            <button type="submit" class="btn-login" data-lang="btn_update_profile">Update Profile</button>
            <br><br>
            <a href="dashboard.php" style="color: #7f8c8d; text-decoration: none;" data-lang="back_to_dashboard">← Back to Dashboard</a>
        </form>
    </div>


</div>


  <script src="http://localhost/lms_tech/lang.js"></script>


</body>
</html>