<?php
session_start();
require_once '../config/db.php';
require_once '../global_file.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password']; 
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // This captures if they are a 'teacher' or 'manager'
    $gender = $_POST['gender'];

    if ($password !== $confirm_password) {
        $msg = "<div class='alert-error'>❌ Passwords do not match!</div>";
    } 
    else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $msg = "<div class='alert-error'>❌ Password must be at least 8 characters and include uppercase, lowercase, number, and special character.</div>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // 1. Prepare the statement
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role, gender, status) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("ssssss", $name, $email, $phone, $hashed_password, $role, $gender);
        
        // 2. Execute ONLY ONCE
        if ($stmt->execute()) {
            // 3. Log the activity correctly
            // Use $name (what the user typed) instead of $new_user_name (which doesn't exist)
            // Use ucfirst($role) to make the log look clean (e.g., 'Teacher' instead of 'teacher')
            $admin_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;
            $details = "Admin added a new " . ucfirst($role) . ": " . $name;
            
            logActivity($conn, $admin_id, 'INSERT', $details);

            $msg = "<div class='alert-success'>✅ User '$name' <span data-lang='user_success'>User Created!</span></div>";
        } else {
            $msg = "<div class='alert-error'>❌ Error: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Tech Iftiin Admin</title>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-dark: #1a0b45;
            --accent-green: #2ecc71;
        }

        body { font-family: 'Inter', sans-serif; margin: 0; background: #f4f7f6; color: var(--primary-dark); display: flex; }

        /* --- SIDEBAR STYLES --- */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary-dark);
            height: 100vh;
            color: white;
            position: fixed;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header { padding: 25px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h2 { font-size: 1.5rem; margin: 0; color: white; }
        .sidebar-header span { color: var(--accent-green); }

        .sidebar nav { padding: 20px 0; }
        .sidebar nav a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
            font-size: 0.95rem;
        }

        .sidebar nav a i { margin-right: 15px; width: 20px; font-size: 1.1rem; }
        .sidebar nav a:hover, .sidebar nav a.active {
            background: rgba(255,255,255,0.05);
            color: var(--accent-green);
            border-left: 4px solid var(--accent-green);
        }

        /* --- MAIN CONTENT AREA --- */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            width: 100%;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Mobile Header with Hamburger */
        .mobile-header {
            display: none;
            background: var(--primary-dark);
            color: white;
            padding: 15px 20px;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1001;
        }

        .hamburger { font-size: 1.8rem; cursor: pointer; color: var(--accent-green); }

        .main-container { max-width: 850px; margin: 40px auto; padding: 0 30px; }
        
        /* Form Styling */
        .user-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-top: 6px solid var(--accent-green); }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; }
        
        .input-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 8px; }
        .input-group input, .input-group select { 
            width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 10px; 
            background: #f8fafc; font-size: 1rem; box-sizing: border-box; transition: 0.3s;
        }
        .input-group input:focus { border-color: var(--accent-green); outline: none; background: #fff; }

        .btn-submit { 
            grid-column: span 2; background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); 
            color: white; border: none; padding: 15px; border-radius: 10px; font-weight: 700; 
            cursor: pointer; margin-top: 15px; font-size: 1rem;
        }

        .back-nav { margin-bottom: 20px; display: inline-flex; align-items: center; text-decoration: none; color: var(--accent-green); font-weight: 600; }

        /* --- RESPONSIVE DESIGN --- */
        @media (max-width: 992px) {
            .sidebar { left: -100%; }
            .sidebar.active { left: 0; }
            .main-wrapper { margin-left: 0; }
            .mobile-header { display: flex; border-bottom:2px solid gold; }
            .form-grid { grid-template-columns: 1fr; }
            .btn-submit { grid-column: span 1; }
            .input-group[style*="grid-column: span 2"] { grid-column: span 1 !important; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>Tech <span>Iftiin</span></h2>
        </div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-th-large"></i> <span data-lang="dashboard">Dashboard</span></a>
            <a href="manage_users.php" class="active"><i class="fas fa-users"></i> <span data-lang="manage_users">Manage Users</span></a>
            <a href="admin_certificates.php">
                <i class="fas fa-certificate"></i> <span data-lang="certificates">Certificates</span>
                <?php if(isset($pending_certs) && $pending_certs > 0): ?>
                    <span style="background: #f1c40f; color: #1a0b45; padding: 2px 6px; border-radius: 10px; font-size: 0.6rem; margin-left: auto; font-weight: bold;"><?php echo $pending_certs; ?></span>
                <?php endif; ?>
            </a>
            <a href="courses.php"><i class="fas fa-graduation-cap"></i> <span data-lang="manage_courses">Manage Courses</span></a>
            <a href="admin_logs.php"><i class="fas fa-history"></i> <span data-lang="activity_logs">Activity Logs</span></a>
            <a href="admin_messages.php">
                <i class="fas fa-envelope"></i> <span data-lang="messages">Messages</span>
                <?php if(isset($unread_messages) && $unread_messages > 0): ?>
                    <span style="background: #e74c3c; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.6rem; margin-left: auto;"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </a>
            <a href="settings.php"><i class="fa-solid fa-gear"></i> <span data-lang="setting">Settings</span></a>
            
            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 25px;">
            <a href="../auth/logout.php" style="color: #ff7675;"><i class="fas fa-sign-out-alt"></i> <span data-lang="logout">Logout</span></a>
        </nav>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Mobile Navigation Bar -->
        <div class="mobile-header">
            <span data-lang="portal_title">Admin Panel</span>
            <div class="hamburger" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </div>
        </div>

        <div class="main-container">
            <a href="manage_users.php" class="back-nav"><i class="fas fa-arrow-left"></i> &nbsp; <span data-lang="back_to_list">Back to User List</span></a>
            
            <div class="header-section" style="margin-bottom: 25px;">
                <h2 style="margin-bottom: 5px;" data-lang="create_new_user">Create New User</h2>
                <p style="font-size:12px; color: #64748b; margin: 0;">
                    <i class="fas fa-info-circle" style="color: #8b5cf6;"></i> 
             <span data-lang="password_requirement">Password must include Uppercase, Lowercase, Number, and Special Character (> 8 chars).</span>                </p>
            </div>

            <?php if(isset($msg)) echo $msg; ?>

            <div class="user-card">
                <form action="add_user.php" method="POST">
                    <div class="form-grid">
                        
                        <div class="input-group">
                            <label data-lang="full_name">Full Name</label>
                            <input type="text" name="name" data-lang="enter_name_placeholder" placeholder="Enter Your Name" required>
                        </div>

                        <div class="input-group">
                            <label data-lang="email_address">Email Address</label>
                            <input type="email" name="email" placeholder="name@example.com" placeholder="name@example.com" required>
                        </div>

                        <div class="input-group">
                            <label data-lang="phone_number">Phone Number</label>
                            <input type="text" name="phone" placeholder="+253 77 -- -- --" required>
                        </div>

                        <div class="input-group">
                            <label data-lang="system_role">System Role</label>
                            <select name="role" required>
                                <option value="teacher" data-lang="role_teacher">Teacher (Instructor)</option>
                                <option value="manager" data-lang="role_manager">Manager (Coordinator)</option>
                                <option value="admin" data-lang="role_admin">Administrator</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label data-lang="password">Password</label>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>

                        <div class="input-group">
                            <label data-lang="repeat_password">Repeat Password</label>
                            <input type="password" name="confirm_password" placeholder="••••••••" required>
                        </div>

                        <div class="input-group" style="grid-column: span 2;">
                            <label data-lang="gender">Gender</label>
                            <select name="gender" required>
                                <option value="" disabled selected data-lang="choose_gender">Choose Gender</option>
                                <option value="Male" data-lang="male">Male</option>
                                <option value="Female" data-lang="female">Female</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-submit" data-lang="create_account_btn">Create User Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const hamburger = document.querySelector('.hamburger');
            if (window.innerWidth <= 992 && 
                !sidebar.contains(event.target) && 
                !hamburger.contains(event.target) && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>









  <script src="http://localhost/lms_tech/lang.js"></script>


</body>
</html>