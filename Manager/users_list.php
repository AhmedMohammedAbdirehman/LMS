<?php
session_start();
require_once '../config/db.php';
// Security: Only Managers allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../index.php");
    exit();
}

$view = isset($_GET['view']) ? $_GET['view'] : 'student';
if (!in_array($view, ['student', 'teacher'])) { $view = 'student'; }

$query = "SELECT id, name, email, phone, gender, status FROM users WHERE role = '$view' ORDER BY name ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager - User Directory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #1a0b45; --accent: #2ecc71; --bg: #f4f7f6; }
        
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; padding: 20px; }
        
        .container { 
            max-width: 1000px; 
            margin: auto; 
            background: white; 
            padding: 25px; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
        }

        .tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; overflow-x: auto; }
        .tab-btn { text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; color: #7f8c8d; white-space: nowrap; transition: 0.3s; }
        .tab-btn.active { background: var(--primary); color: white; }

        /* TABLE RESPONSIVENESS */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { color: var(--primary); background: #f8fafc; text-align: left; padding: 12px; border-bottom: 2px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #eee; }

        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; display: inline-block; }
        .active-status { background: #eafaf1; color: #27ae60; }
        .pending-status { background: #fef9e7; color: #f39c12; }

        /* MOBILE VIEW: Stacked Cards */
        @media (max-width: 668px) {
            body { padding: 10px; }
            .container { padding: 15px; }
            
            /* Hide the table header */
            table thead { display: none; }
            
            table, tbody, tr, td { display: block; width: 100%; }
            
            tr { 
                margin-bottom: 15px; 
                border: 1px solid #eee; 
                border-radius: 10px; 
                padding: 10px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            }

            td { 
                text-align: right; 
                padding: 8px 5px; 
                position: relative; 
                border-bottom: 1px solid #f9f9f9;
            }

            td:last-child { border-bottom: none; }

            /* Add labels before the data */
            td::before {
                content: attr(data-label);
                position: absolute;
                left: 5px;
                font-weight: bold;
                color: var(--primary);
                text-transform: uppercase;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>


<div class="container">
    <div style="margin-bottom: 20px;">
        <a href="dashboard.php" style="text-decoration:none; color:#3498db; font-size:0.9rem;">
            <i class="fas fa-arrow-left"></i> <span data-lang="btn_back_dashboard">Back to Dashboard</span>
        </a>
    </div>

    <h2 style="color: var(--primary);"><i class="fas fa-users-cog"></i> <span data-lang="user_management_title">User Management</span></h2>

    <div class="tabs">
        <a href="users_list.php?view=student" class="tab-btn <?php echo ($view == 'student') ? 'active' : ''; ?>">
            <i class="fas fa-user-graduate"></i> <span data-lang="tab_students">Students</span>
        </a>
        <a href="users_list.php?view=teacher" class="tab-btn <?php echo ($view == 'teacher') ? 'active' : ''; ?>">
            <i class="fas fa-chalkboard-teacher"></i> <span data-lang="tab_teachers">Teachers</span>
        </a>
    </div>

    <table>
        <thead>
            <tr>
             <th data-lang="th_name">Name</th>
                <th data-lang="th_email">Email</th>
                <th data-lang="th_phone">Phone</th>
                <th data-lang="th_gender">Gender</th>
                <th data-lang="th_status">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result && $result->num_rows > 0): ?>
                <?php while($user = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="Name"><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                    <td data-label="Email"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td data-label="Phone"><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td data-label="Gender"><?php echo htmlspecialchars($user['gender']); ?></td>
                    <td data-label="Status">
                        <?php if($user['status'] == 1): ?>
                            <span class="badge active-status" data-lang="status_active_label">Active</span>
                        <?php else: ?>
                            <span class="badge pending-status" data-lang="status_pending_label">Pending</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding:20px;">No <?php echo $view; ?>s found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>



<script src="/lms_tech/lang.js"></script>


</body>
</html>