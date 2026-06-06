<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle Status Toggle (Approve/Disable)
if (isset($_GET['toggle_id']) && isset($_GET['current_status'])) {
    $id = intval($_GET['toggle_id']);
    $new_status = $_GET['current_status'] == 1 ? 0 : 1;
    $status_label = $new_status == 1 ? 'Enabled/Approved' : 'Disabled';

    // 1. Fetch user name for the log
    $user_query = $conn->query("SELECT name FROM users WHERE id = '$id'");
    $user_data = $user_query->fetch_assoc();
    $target_name = $user_data['name'] ?? 'Unknown User';

    // 2. Update Status
    $conn->query("UPDATE users SET status = '$new_status' WHERE id = '$id'");

    // 3. Track in Logs
    $admin_id = $_SESSION['user_id'];
    $details = "Admin changed status of  ($target_name) to $status_label";
    $conn->query("INSERT INTO activity_logs (user_id, action, details) VALUES ('$admin_id', 'STATUS_CHANGE', '$details')");

    header("Location: manage_users.php?msg=User status updated to $status_label");
    exit();
}

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);

    // 1. Fetch name before deleting
    $user_query = $conn->query("SELECT name FROM users WHERE id = '$id'");
    $user_data = $user_query->fetch_assoc();
    $target_name = $user_data['name'] ?? 'Unknown User';

    // 2. Delete User
    $conn->query("DELETE FROM users WHERE id = '$id'");

    // 3. Track in Logs
    $admin_id = $_SESSION['user_id'];
    $details = "Admin permanently deleted student: $target_name";
    $conn->query("INSERT INTO activity_logs (user_id, action, details) VALUES ('$admin_id', 'DELETE_USER', '$details')");

    header("Location: manage_users.php?msg=User Deleted");
    exit();
}


// --- Search & Pagination Logic ---
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build the search condition
$search_condition = "";
if (!empty($search)) {
    $search_condition = " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}

// Get total student count (Filtered by search)
$total_query = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'student' $search_condition");
$total_students = $total_query->fetch_assoc()['total'];
$total_pages = ceil($total_students / $limit);

// Fetch Students (Filtered by search)
$student_result = $conn->query("SELECT id, name, email, phone, gender, role, status FROM users 
    WHERE role = 'student' $search_condition 
    ORDER BY id DESC LIMIT $limit OFFSET $offset");

// Keep your existing Staff query
$staff_result = $conn->query("SELECT id, name, email, phone,gender, role, status FROM users 
    WHERE role IN ('admin', 'teacher') AND id != '" . $_SESSION['user_id'] . "'");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Manage Users - LMS</title>
  
    <style>
  /* Table Module Styling */
:root { --primary: #1a0b45; --accent: #2ecc71; --bg-light: #f4f7f6; }

body { margin: 0; display: flex; background: var(--bg-light); font-family: 'Inter', sans-serif; }

/* The Sidebar needs to stay fixed for the UI to feel 'Good' */
.sidebar { width: 260px; background: var(--primary); min-height: 100vh; flex-shrink: 0; color: white; }

.main-content { flex-grow: 1; min-width: 0; }

.table-container { padding: 30px; }

/* Each table gets its own white 'Card' */
.table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    overflow-x: auto; /* Fixes mobile overflow */
}

table { width: 100%; border-collapse: collapse; min-width: 600px; }
th { background: #f8fafc; padding: 15px 20px; text-align: left; color: #64748b; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid #edf2f7; }
td { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #1e293b; }

.role-badge { padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase; }
.admin { background: #fee2e2; color: #991b1b; }
.teacher { background: #e0e7ff; color: #3730a3; }

.status-pill { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.enabled { background: #dcfce7; color: #166534; }
.disabled { background: #f3f4f6; color: #4b5563; }

@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        left: -260px; /* Hidden by default */
        top: 0;
        bottom: 0;
        width: 260px;
        z-index: 9999; /* Sits on top of everything */
        transition: 0.3s ease-in-out;
    }

    /* This is what the JavaScript toggles */
    .sidebar.active {
        left: 0;
    }

    .mobile-toggle {
        display: block !important;
        cursor: pointer;
    }
}

tr:hover { background: #f8fafc; }

/* Status & Action UI */
.status-pill { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
.enabled { background: #def7ec; color: #03543f; }
.disabled { background: #fde2e2; color: #9b1c1c; }

.btn-edit { color: #3498db; margin-right: 15px; }
.btn-delete { color: #e74c3c; }

/* Pagination Styling */
.pagination { display: flex; gap: 5px; padding: 20px; justify-content: center; background: white; }
.pagination a { 
    padding: 8px 16px; border-radius: 8px; border: 1px solid #edf2f7; 
    text-decoration: none; color: #1a0b45; font-weight: 600; transition: 0.3s;
}
.pagination a.active { background: #2ecc71; color: white; border-color: #2ecc71; }
.pagination a:hover:not(.active) { background: #f1f5f9; }

/* Top Bar Base Styling */
.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    background: #251b4d;
    border-bottom: 2px solid gold;
 
    
}

.mobile-toggle, .mobile-home { display: none; }

.mobile-home a {
    color: var(--primary);
    font-size: 1.2rem;
    text-decoration: none;
}
    .page-title h2 {
        font-size: 1.5rem;
     
         color: var(--accent);
    }

/* Mobile Specific Adjustments */
@media (max-width: 768px) {
    .top-bar {
        padding: 20px 20px;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .mobile-toggle, .mobile-home { 
        display: flex; 
        align-items: center;
        justify-content: center;
        /* width: 40px; */
        height: 30px;
        cursor: pointer;
        font-size: 1.5rem;
         color: var(--accent);
    }

    .page-title h2 {
        font-size: 1rem;
        margin: 0;
         color: var(--accent);
    }
    .mobile-home a{
         color: var(--accent);
    }
}

/* Pro Sidebar Styling */
.sidebar {
    width: 260px;
    background: #1a0b45; /* Deep Navy */
    min-height: 100vh;
    color: #ffffff;
    display: flex;
    flex-direction: column;
    transition: 0.3s ease;
}

.sidebar-header {
    padding: 30px 25px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.logo-circle {
    background: #2ecc71;
    color: white;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(46, 204, 113, 0.3);
}

.brand-name { font-weight: 700; font-size: 1.1rem; display: block; }
.brand-sub { font-size: 0.65rem; color: #2ecc71; text-transform: uppercase; letter-spacing: 1px; }

.menu-label {
    padding: 0 25px;
    font-size: 0.7rem;
    text-transform: uppercase;
    color: #636e72;
    letter-spacing: 1.5px;
    margin-bottom: 15px;
}

.sidebar-menu nav a {
    display: flex;
    align-items: center;
    padding: 14px 25px;
    color: #a4b0be;
    text-decoration: none;
    font-size: 0.95rem;
    transition: all 0.3s;
    border-left: 4px solid transparent;
}

.sidebar-menu nav a i {
    width: 25px;
    font-size: 1.1rem;
}

/* The Active State - Makes it feel professional */
.sidebar-menu nav a:hover, 
.sidebar-menu nav a.active {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.05);
    border-left: 4px solid #2ecc71;
}

.sidebar-menu nav a.active i {
    color: #2ecc71;
}

.msg-count {
    margin-left: auto;
    background: #e74c3c;
    color: white;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.7rem;
}

.logout-link:hover {
    background: rgba(231, 76, 60, 0.1) !important;
    color: #e74c3c !important;
    border-left: 4px solid #e74c3c !important;
}
.sidebar a { 
    display: flex; 
    align-items: center; 
    color: #bdc3c7; 
    text-decoration: none; 
    padding: 15px 25px; 
    transition: 0.3s;
    font-size: 0.95rem;
}

.sidebar a i { margin-right: 12px; width: 20px; }

.sidebar a:hover, .sidebar a.active { 
    background: rgba(255,255,255,0.05); 
    color: var(--accent); 
    border-left: 4px solid var(--accent); 
}
    </style>
</head>

<body>


<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo-circle">TI</div>
        <span class="brand-name">Tech Iftiin</span>
    </div>
    <nav>
     <a href="dashboard.php" style="color: #2ecc71; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 10px;">
        <i class="fas fa-chevron-left"></i> <span data-lang="back_to_dashboard">Back to Dashboard</span>
    </a>
        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 10px 0;">
        <a href="manage_users.php" class="active">
            <i class="fas fa-users"></i> <span data-lang="manage_users">Manage Users</span>   </a>
            <a href="courses.php"><i class="fas fa-graduation-cap"></i> <span data-lang="manage_courses">Manage Courses</span></a>
            <a href="admin_logs.php"><i class="fas fa-history"></i> <span data-lang="activity_logs">Activity Logs</span></a>
    

            <a href="settings.php"><i class="fa-solid fa-gear"></i> <span data-lang="setting">Setting</span></a>
               <hr>
            <a href="../auth/logout.php"><i class="fas fa-sign-out-alt" style="color:red;"></i> <span data-lang="logout">Logout</span></a>
      
        </nav>
</div>

<div class="main-content">
<header class="top-bar">
    <div class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>

    <div class="page-title">
        <h2 data-lang="user_management">User Management</h2>
    </div>
    
    <div class="mobile-home">
        <a href="dashboard.php"><i class="fas fa-home"></i></a>
    </div>
</header>

        <div class="table-container">
            
            <h3 style="font-size: 1rem; color: #64748b; margin-bottom: 15px;" data-lang="staff_members">Staff Members</h3>
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                        
                        <th data-lang="th_name">Name</th>
                        <th data-lang="th_email">Email</th>
                        <th data-lang="th_phone">Phone</th>
                        <th data-lang="th_gender">Gender</th>
                        <th data-lang="th_role">Role</th>
                        <th data-lang="th_status">Status</th>
                        <th data-lang="th_actions">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $staff_result->fetch_assoc()): ?>
                        <tr>
                           
                            <td><strong><?php echo $user['name']; ?></strong></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td><?php echo $user['gender']; ?></td>
                            <td><span class="role-badge <?php echo $user['role']; ?>"><?php echo $user['role']; ?></span></td>
                            <td>
                                <!-- <span class="status-pill <?php echo $user['status'] == 1 ? 'enabled' : 'disabled'; ?>"><?php echo $user['status'] == 1 ? 'Active' : 'Inactive'; ?></span> -->
                                 <span class="status-pill <?php echo $user['status'] == 1 ? 'enabled' : 'disabled'; ?>" data-lang="<?php echo $user['status'] == 1 ? 'active_status' : 'inactive_status'; ?>">
                                <?php echo $user['status'] == 1 ? 'Active' : 'Inactive'; ?>
                            </span>
                            </td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" style="color: #3498db; text-decoration: none; margin-right: 10px;" data-lang="btn_edit">Edit</a>
                                <a href="manage_users.php?toggle_id=<?php echo $user['id']; ?>&current_status=<?php echo $user['status']; ?>" style="color: #64748b; text-decoration: none;" data-lang="btn_toggle">Toggle</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <h3 style="font-size: 1rem; color: #64748b; margin-bottom: 15px; margin-top: 40px;" data-lang="enrolled_students">Enrolled Students</h3>
            <div class="table-card">

 <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px; margin-bottom: 15px;">
    <h3 style="font-size: 1rem; color: #64748b; margin: 0;">Enrolled Students</h3>
    
    <!-- Search Form -->
    <form action="manage_users.php" method="GET" style="display: flex; gap: 10px;">
        <div style="position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                   data-lang="search_placeholder" placeholder="Search name or email..." 
                   style="padding: 8px 12px 8px 35px; border-radius: 8px; border: 1px solid #ddd; outline: none; width: 250px;">
        </div>
        <button type="submit" style="background: #1a0b45; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;" data-lang="btn_search">
            Search
        </button>
        <?php if(!empty($search)): ?>
            <a href="manage_users.php" style="background: #f1f5f9; color: #64748b; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 0.8rem; border: 1px solid #ddd;">Clear</a>
        <?php endif; ?>
    </form>
</div>
                <table>
                    <thead>
                        <tr>
                            <th data-lang="th_name">Name</th>
                            <th data-lang="th_email">Email</th>
                            <th data-lang="th_phone">Phone</th>
                            <th data-lang="th_gender">Gender</th>
                            <th data-lang="th_status">Status</th>
                            <th data-lang="th_actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($student = $student_result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $student['name']; ?></strong></td>
                            <td><?php echo $student['email']; ?></td>
                            <td><?php echo $student['phone']; ?></td>
                            <td><?php echo $student['gender']; ?></td>
                            <td>
                        <span class="status-pill <?php echo $student['status'] == 1 ? 'enabled' : 'disabled'; ?>" data-lang="<?php echo $student['status'] == 1 ? 'active_status' : 'inactive_status'; ?>">
                        <?php echo $student['status'] == 1 ? 'Active' : 'Inactive'; ?>
                            </span>
                            </td>
                            <td>
                                <?php if($student['status'] == 0): ?>
                                    <a href="manage_users.php?toggle_id=<?php echo $student['id']; ?>&current_status=0" 
                                    style="background: #27ae60; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: bold;" data-lang="btn_approve">
                                    <i class="fas fa-check"></i> Approve
                                    </a>
                                <?php else: ?>
                                    <a href="manage_users.php?toggle_id=<?php echo $student['id']; ?>&current_status=1" 
                                    style="color: #e74c3c; font-weight: 600; text-decoration: none; font-size: 0.85rem;">
                                    Disable
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <!-- Added &search= to the URL -->
                    <a href="manage_users.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                    class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            </div>
        </div>
    </div>








<script>
function toggleSidebar() {
    // Select the sidebar element
    const sidebar = document.querySelector('.sidebar');
    
    // Add or remove the 'active' class
    sidebar.classList.toggle('active');
    
    console.log("Sidebar toggled!"); // Check your browser console (F12) to see if this runs
}

// Close sidebar if user clicks on the main content while it's open
document.addEventListener('click', function(event) {
    const sidebar = document.querySelector('.sidebar');
    const toggle = document.querySelector('.mobile-toggle');
    
    if (window.innerWidth <= 768) {
        // If the click is NOT on the sidebar and NOT on the button, close it
        if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    }
});
</script>




  <script src="http://localhost/lms_tech/lang.js"></script>



</body>

</html>