<?php
session_start();
require_once '../config/db.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$message = "";
// --- 1. HANDLE APPROVAL OR REJECTION ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $cert_id = $_POST['cert_id'];
    $action = $_POST['status']; // 'approved' or 'rejected'
    $currentYear = date("Y");

    if ($action === 'approved') {
        // 1. Generate the 4-digit serial (e.g., techiftiin-0001-2026)
        $pattern = "techiftiin-%-" . $currentYear;
        $num_query = "SELECT certificate_serial FROM certificates 
                      WHERE certificate_serial LIKE ? 
                      ORDER BY certificate_serial DESC LIMIT 1";
        
        $stmt_num = $conn->prepare($num_query);
        $stmt_num->bind_param("s", $pattern);
        $stmt_num->execute();
        $res_num = $stmt_num->get_result();
        
        $next_num = 1;
        if ($res_num->num_rows > 0) {
            $last_serial = $res_num->fetch_assoc()['certificate_serial'];
            $parts = explode('-', $last_serial);
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $next_num = intval($parts[1]) + 1;
            }
        }

        $serial = "techiftiin-" . str_pad($next_num, 4, '0', STR_PAD_LEFT) . "-" . $currentYear;

        // 2. Update status to 'approved' and save the serial
        $update = $conn->prepare("UPDATE certificates SET status = 'approved', certificate_serial = ?, issue_date = NOW() WHERE id = ?");
        $update->bind_param("si", $serial, $cert_id);
    } else {
        // 3. Handle Rejection
        $update = $conn->prepare("UPDATE certificates SET status = 'rejected' WHERE id = ?");
        $update->bind_param("i", $cert_id);
    }
    
    if ($update->execute()) {
        $message = "approved successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}



// --- 2. PAGINATION SETTINGS ---
$limit = 8; // Requests per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM certificates WHERE status IN ('pending', 'approved')";
$total_results = $conn->query($count_query)->fetch_assoc()['total'];
$total_pages = ceil($total_results / $limit);

// Updated Fetch Query with LIMIT/OFFSET
$query = "SELECT c.id, c.course_name, c.status, c.issue_date, c.certificate_serial,
          u_std.name as student_name, u_tea.name as teacher_name 
          FROM certificates c
          JOIN users u_std ON c.student_id = u_std.id
          JOIN users u_tea ON c.teacher_id = u_tea.id
          WHERE c.status IN ('pending', 'approved')
          ORDER BY FIELD(c.status, 'pending', 'approved'), c.issue_date DESC
          LIMIT $limit OFFSET $offset";

$pending_list = $conn->query($query);

if (!$pending_list) {
    die("Query Failed: " . $conn->error);
}



$unread_messages = $conn->query("SELECT COUNT(*) as unread FROM contact_messages WHERE status='unread'")->fetch_assoc()['unread'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Manage Certificates - Admin</title>
    <style>
        :root { --primary: #1a0b45; --accent: #2ecc71; --bg-light: #f4f7f6; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg-light); display: flex; overflow-x: hidden; }
        
        /* Sidebar Styling */
        .sidebar { width: 260px; background: var(--primary); color: white; min-height: 100vh; flex-shrink: 0; transition: all 0.3s ease; z-index: 1000; }
        .sidebar-header { display: flex; align-items: center; padding: 25px; gap: 12px; }
        .logo-circle { background: var(--accent); color: white; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .sidebar a { display: flex; align-items: center; color: #bdc3c7; text-decoration: none; padding: 15px 25px; transition: 0.3s; white-space: nowrap; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.05); color: var(--accent); border-left: 4px solid var(--accent); }
        .sidebar a i { margin-right: 12px; width: 20px; }

        /* Main Content */
        .main-content { flex-grow: 1; min-width: 0; }
        .top-bar { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #edf2f7; }
        .content-container { padding: 30px; }
        
        .table-card { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: none; }

        /* RESPONSIVE HAMBURGER LOGIC */
        #mobile-toggle { display: none; background: none; border: none; color: var(--primary); font-size: 1.5rem; cursor: pointer; }

        @media (max-width: 692px) {
            #mobile-toggle { display: block; }
            .sidebar { position: fixed; left: -260px; height: 100%; }
            .sidebar.active { left: 0; }
            .content-container { padding: 15px; }
            .top-bar { padding: 15px; }
            .user-info-text { display: none; } /* Hide name on small screens to save space */
        }

        /* Overlay for mobile */
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; }
        .sidebar-overlay.active { display: block; }

        .pagination .page-link {
    color: var(--primary);
    border: 1px solid #edf2f7;
    padding: 8px 14px;
}
.pagination .page-item.active .page-link {
    background-color: var(--primary);
    border-color: var(--primary);
    color: white;
}
.pagination .page-link:focus {
    box-shadow: none;
}
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-circle">TI</div>
            <div>
                <span style="display:block; font-weight:700;">Tech Iftiin</span>
                <span style="display:block; font-size:0.65rem; color:var(--accent); text-transform:uppercase;" data-lang="portal_title" >Admin Portal</span>
            </div>
        </div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-th-large"></i> <span data-lang="dashboard">Dashboard</span></a>
            <a href="manage_users.php"><i class="fas fa-users"></i> <span data-lang="manage_users">Manage Users</span></a>
            <a href="admin_certificates.php" class="active"><i class="fas fa-certificate"></i> <span data-lang="certificates">Certificates</span></a>
            <a href="courses.php"><i class="fas fa-graduation-cap"></i> <span data-lang="manage_courses">Manage Courses</span></a>
            <a href="admin_messages.php">
                <i class="fas fa-envelope"></i> <span data-lang="messages">Messages</span>
                <?php if($unread_messages > 0): ?>
                    <span class="badge bg-danger ms-auto" style="font-size: 0.6rem;"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </a>
            <hr style="border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 25px;">
            <a href="../auth/logout.php" style="color: #ff7675;"><i class="fas fa-sign-out-alt"></i> <span data-lang="logout">Logout</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="top-bar">
            <div class="d-flex align-items-center gap-3">
                <button id="mobile-toggle"><i class="fas fa-bars"></i></button>
                <h5 class="mb-0 d-none d-md-block" style="color: var(--primary); font-weight: 700;" data-lang="cert_approvals">Certificate Approvals</h5>
            </div>
            
            <div class="user-profile d-flex align-items-center gap-3">
                <div class="text-end user-info-text">
                    <strong class="d-block" style="font-size: 0.85rem;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                    <span style="font-size: 0.7rem; color: #7f8c8d;" data-lang="role_admin">Administrator</span>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=2ecc71&color=fff" width="35" class="rounded-circle">
            </div>
        </header>


        <div class="content-container">
            <?php if($message): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="card table-card">

                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0" style="color: var(--primary);" data-lang="incoming_requests">Incoming Requests</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" data-lang="th_student">Student</th>
                                    <th data-lang="th_module">Module</th>
                                    <th class="d-none d-sm-table-cell" data-lang="th_instructor">Instructor</th>
                                    <th class="text-center" data-lang="th_actions">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($pending_list->num_rows > 0): ?>
                                <?php while($row = $pending_list->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div style="font-weight:700;"><?php echo htmlspecialchars($row['student_name']); ?></div>
                                            <small class="text-muted d-md-none"><?php echo date('M d', strtotime($row['issue_date'])); ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['course_name']); ?></span></td>
                                        <td class="d-none d-sm-table-cell"><?php echo htmlspecialchars($row['teacher_name']); ?></td>
           <td class="text-center">
    <?php if ($row['status'] === 'approved'): ?>
        <!-- Show Approved Sign + View Icon -->
        <div class="d-flex align-items-center justify-content-center gap-2">
            <span class="badge bg-success px-3 py-2">
                <i class="fas fa-check-circle"></i> 
                <span data-lang="status_approved">Approved</span>
            </span>
            <a href="view_certificate.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    <?php else: ?>
        <!-- Show Approval Buttons + Preview Icon -->
        <div class="d-flex gap-1 justify-content-center align-items-center">
            <!-- Added Preview Icon for Pending students -->
            <a href="view_certificate.php?id=<?php echo $row['id']; ?>&preview=true" target="_blank" class="btn btn-sm btn-info text-white">
                <i class="fas fa-eye"></i>
            </a>

            <form method="POST" class="d-inline">
                <input type="hidden" name="cert_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="status" value="approved">
                <button type="submit" name="update_status" class="btn btn-sm btn-success" data-lang="btn_approve">Approve</button>
            </form>
            
            <form method="POST" class="d-inline">
                <input type="hidden" name="cert_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="status" value="rejected">
                <button type="submit" name="update_status" class="btn btn-sm btn-outline-danger" data-lang="btn_reject">Reject</button>
            </form>
        </div>
    <?php endif; ?>
</td>
                                                              
                        </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted" data-lang="no_requests">No pending requests.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>


                    </div> <!-- end table-responsive -->
                </div> <!-- end card-body -->

                <!-- PAGINATION FOOTER -->
                <?php if ($total_pages > 1): ?>
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                         <span data-lang="showing_text">Showing</span> <?php echo ($offset + 1); ?> 
                        <span data-lang="to_text">to</span> <?php echo min($offset + $limit, $total_results); ?> 
                        <span data-lang="of_text">of</span> <?php echo $total_results; ?> 
                        <span data-lang="requests_text">requests</span>
                        </div>
                        <nav aria-label="Certificate navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <!-- Previous -->
                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" tabindex="-1">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>

                                <!-- Simple Page Numbers (Optional, but helpful) -->
                                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Next -->
                                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php endif; ?>
            </div> <!-- end card table-card -->
                </div>
            </div>



        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const toggleBtn = document.getElementById('mobile-toggle');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>









  <script src="http://localhost/lms_tech/lang.js"></script>

</body>
</html>