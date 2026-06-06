<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../global_file.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// --- SEARCH & FILTER LOGIC ---
$where_clauses = [];
$params = [];

if (!empty($_GET['search_date'])) {
    $where_clauses[] = "DATE(l.created_at) = ?";
    $params[] = $_GET['search_date'];
}

if (!empty($_GET['search_role'])) {
    $where_clauses[] = "u.role = ?";
    $params[] = $_GET['search_role'];
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// --- PAGINATION WITH FILTERS ---
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total count based on filters
$count_query = "SELECT COUNT(*) as total FROM activity_logs l LEFT JOIN users u ON l.user_id = u.id $where_sql";
$stmt_count = $conn->prepare($count_query);
if (!empty($params)) {
    $stmt_count->bind_param(str_repeat("s", count($params)), ...$params);
}
$stmt_count->execute();
$total_logs = $stmt_count->get_result()->fetch_assoc()['total'] ?? 0;
$total_pages = max(1, ceil($total_logs / $limit));

// --- FETCH DATA ---
$data_query = "SELECT l.*, u.name as user_name, u.role as user_role 
               FROM activity_logs l 
               LEFT JOIN users u ON l.user_id = u.id 
               $where_sql 
               ORDER BY l.created_at DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($data_query);
$all_params = array_merge($params, [$limit, $offset]);
$types = str_repeat("s", count($params)) . "ii";
$stmt->bind_param($types, ...$all_params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Activity Logs | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-bg: #1a0b45;
            --accent-color: #3498db;
            --body-bg: #f4f7f6;
        }

        * { box-sizing: border-box; } /* Crucial for responsiveness */

body { 
    font-family: 'Inter', sans-serif; 
    background: var(--body-bg); 
    margin: 0; 
    /* Remove display: flex; */
    overflow-x: hidden;
}

        /* --- SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--primary-bg);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 2000;
            transition: transform 0.3s ease;
        }

        .sidebar-header { padding: 20px; text-align: center; border-bottom: 2px solid rgba(255,255,255,0.1); }
        .sidebar-menu { list-style: none; padding: 0; margin: 20px 0; }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left: 4px solid var(--accent-color);
        }

        /* --- MAIN CONTENT --- */
.main-content {
    flex: 1;
    margin-left: var(--sidebar-width);
    padding: 25px; /* Slightly reduced for a tighter feel */
    width: calc(100% - var(--sidebar-width)); /* Explicitly subtract sidebar width */
    min-height: 100vh;
    transition: margin 0.3s ease;
}

   .mobile-header {
    display: none; /* Desktop hidden */
    background: #251b4d; /* Updated color */
    padding: 15px;
    align-items: center;
    justify-content: space-between;
    position: fixed; /* Fix to top */
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000; /* Stay above content */
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

/* Ensure the text inside the header is white */
.mobile-header span {
    color: #2ecc71;
}

 .hamburger { font-size: 1.8rem; cursor: pointer; color: #2ecc71; }

.log-container {
    background: white;
    padding: 20px; /* Reduced from 30px if necessary */
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    max-width: 1400px; /* Optional: Prevents the table from becoming too wide on ultra-wide monitors */
    margin: 0 auto; /* Centers the container if you use max-width */
}

        /* --- RESPONSIVE TABLE --- */
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            border-radius: 8px;
        }

        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { text-align: left; padding: 12px; background: #f8fafc; color: #64748b; font-size: 0.8rem; border-bottom: 2px solid #edf2f7; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 0.85rem; }

        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; }
        .badge-update { background: #e3f2fd; color: #1976d2; }
        .badge-delete { background: #ffebee; color: #c62828; }
        .badge-login { background: #e8f5e9; color: #2e7d32; }

        /* --- OVERLAY --- */
        .overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1500;
        }

        /* --- MOBILE BREAKPOINTS --- */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
          .main-content { 
        margin-left: 0; 
        padding: 15px; 
        padding-top: 80px; /* Increased to clear the fixed header */
        width: 100%; /* Reset width for mobile */
    }
            .mobile-header { display: flex; }
            .overlay.active { display: block; }
        }

        /* Pagination Buttons */
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
        .btn-page { padding: 8px 12px; background: white; border: 1px solid #ccc; text-decoration: none; color: #333; border-radius: 4px; font-size: 0.8rem; }
        .btn-page.disabled { opacity: 0.5; pointer-events: none; }

         
    .search-section {
        background: #f8fafc;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
    }
    .search-group { display: flex; flex-direction: column; gap: 5px; }
    .search-group label { font-size: 0.8rem; font-weight: bold; color: #64748b; }
    .search-input { 
        padding: 8px; 
        border: 1px solid #cbd5e1; 
        border-radius: 5px; 
        font-size: 0.9rem;
    }
    .btn-search {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 5px;
        cursor: pointer;
        height: 38px;
    }
    .btn-reset {
        background: #94a3b8;
        color: white;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 0.9rem;
        height: 38px;
        display: flex;
        align-items: center;
    }
    .sidebar-header h3{
        color:#2ecc71;
    }
    .sidebar-menu li a i{
             padding-right: 10px;
    }
</style>
</head>
<body>


    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header"><h3 data-lang="portal_title">Admin Panel</h3></div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i><span data-lang="dashboard">Dashboard</span></a></li>
            <li><a href="manage_users.php"><i class="fas fa-users"></i> <span data-lang="manage_users">Manage Users</span></a></li>
            <li><a href="courses.php"><i class="fas fa-graduation-cap"></i><span data-lang="manage_courses">Manage Courses</span></a></li>
            <li><a href="admin_logs.php" class="active"><i class="fas fa-history"></i> <span data-lang="activity_logs">Logs</span></a></li>
            <hr>
            <li><a href="../auth/logout.php" style="color: #ff7675;"><i class="fas fa-sign-out-alt"></i> <span data-lang="logout">Logout</span></a></li>
        </ul>
    </div>

  
<div class="main-content">
    <div class="mobile-header">
        <dive class="hamburger" onclick="toggleMenu()">
            <i class="fas fa-bars"></i> 
        </dive>
        <span style="font-weight: bold;" data-lang="activity_logs">Activity Logs</span>
    </div>
    



     <div class="log-container">
            <h2 style="margin-top:0;"><i class="fas fa-history"></i> <span data-lang="system_logs">System Logs</span></h2>



  <div class="table-wrapper">

<form method="GET" class="search-section">
    <div class="search-group">
        <label data-lang="filter_date">Filter by Date</label>
        <input type="date" name="search_date" class="search-input" value="<?php echo $_GET['search_date'] ?? ''; ?>">
    </div>
    <div class="search-group">
        <label data-lang="filter_role">Filter by Role</label>
        <select name="search_role" class="search-input">
            <option value="" data-lang="all_roles">All Roles</option>
            <option value="admin" <?php echo (($_GET['search_role'] ?? '') == 'admin') ? 'selected' : ''; ?> data-lang="role_admin">Admin</option>
            <option value="student" <?php echo (($_GET['search_role'] ?? '') == 'student') ? 'selected' : ''; ?> data-lang="role_student">Student</option>
            <option value="teacher" <?php echo (($_GET['search_role'] ?? '') == 'teacher') ? 'selected' : ''; ?> data-lang="role_teacher">Teacher</option>
        </select>
    </div>
    <button type="submit" class="btn-search"><i class="fas fa-search"></i> <span data-lang="btn_search">Search</span></button>
    <a href="admin_logs.php" class="btn-reset" data-lang="btn_reset">Reset</a>
</form>


                <table>
                    <thead>
                        <tr>
                            <th data-lang="th_user_role">User & Role</th>
                            <th data-lang="th_action">Action</th>
                            <th data-lang="th_details">Details</th>
                            <th data-lang="th_ip">IP Address</th>
                            <th data-lang="th_time">Time</th>
                        </tr>
                    </thead>
<tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): 
            $act = strtolower($row['action']);
            $class = 'badge-update';
            if(strpos($act, 'delete') !== false) $class = 'badge-delete';
            if(strpos($act, 'login') !== false) $class = 'badge-login';
        ?>
        <tr>
            <td>
                <div style="display: flex; flex-direction: column;">
                    <strong style="color: #1e293b;"><?php echo htmlspecialchars($row['user_name'] ?? 'System'); ?></strong>
                    <!-- Tracking the Role here -->
                    <span style="font-size: 0.75rem; color: #64748b; text-transform: capitalize;">
                        <?php echo htmlspecialchars($row['user_role'] ?? 'N/A'); ?>
                    </span>
                </div>
            </td>
            <td><span class="badge <?php echo $class; ?>"><?php echo strtoupper($row['action']); ?></span></td>
            <td style="max-width: 300px; color: #475569;"><?php echo htmlspecialchars($row['details']); ?></td>
            <td style="max-width: 300px; color: #475569;"><?php echo htmlspecialchars($row['ip_address']); ?></td>
            <td style="white-space: nowrap;"><?php echo date('d M, H:i', strtotime($row['created_at'])); ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;" data-lang="no_logs">
                No activity logs found for this search.
            </td>
        </tr>
    <?php endif; ?>
</tbody>
                </table>
            </div>

<?php 
// Create a query string for the search filters
$query_string = http_build_query(array_filter([
    'search_date' => $_GET['search_date'] ?? null,
    'search_role' => $_GET['search_role'] ?? null
]));
$url_params = $query_string ? "&" . $query_string : "";
?>

<div class="pagination">
    <a href="?page=<?php echo max(1, $page - 1) . $url_params; ?>" 
       class="btn-page <?php echo ($page <= 1) ? 'disabled' : ''; ?>">Prev</a>
    
    <span>Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
    
    <a href="?page=<?php echo min($total_pages, $page + 1) . $url_params; ?>" 
       class="btn-page <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">Next</a>
</div>



        </div>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('overlay').classList.toggle('active');
        }
    </script>



  <script src="http://localhost/lms_tech/lang.js"></script>

</body>
</html>