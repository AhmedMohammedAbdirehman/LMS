<?php
session_start();
require_once '../config/db.php';

// Security Check: If not logged in or not an admin, kick them out
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch counts for the dashboard cards
$user_counts = $conn->query("SELECT role, COUNT(*) as total FROM users GROUP BY role");
$stats = ['admin' => 0, 'teacher' => 0, 'student' => 0];

while ($row = $user_counts->fetch_assoc()) {
    $stats[$row['role']] = $row['total'];
}

$course_count = $conn->query("SELECT COUNT(*) as total FROM courses")->fetch_assoc()['total'];



// NEW: Fetch pending certificates count
$cert_count_query = $conn->query("SELECT COUNT(*) as pending FROM certificates WHERE status='pending'");
$pending_certs = ($cert_count_query) ? $cert_count_query->fetch_assoc()['pending'] : 0;


// NEW: Fetch unread messages count
$message_count_query = $conn->query("SELECT COUNT(*) as unread FROM contact_messages WHERE status='unread'");
$unread_messages = ($message_count_query) ? $message_count_query->fetch_assoc()['unread'] : 0;



// --- 1. Gender Distribution ---
$gender_query = "SELECT gender, COUNT(*) as count FROM users WHERE role = 'student' GROUP BY gender";
$gender_res = $conn->query($gender_query);

if (!$gender_res) {
    // This will tell you EXACTLY what is wrong with the SQL
    die("Gender Query Failed: " . $conn->error); 
}

$genders = []; $gender_counts = [];
while($row = $gender_res->fetch_assoc()) {
    $genders[] = $row['gender'];
    $gender_counts[] = $row['count'];
}

// --- 2. Students per Course ---
// Check if your table is actually named 'enrollments' or if you use a different linking method
$course_query = "SELECT c.title as course_name, COUNT(u.id) as student_count 
                 FROM courses c 
                 LEFT JOIN users u ON FIND_IN_SET(c.id, u.course_id) 
                 WHERE u.role = 'student'
                 GROUP BY c.id";

$course_res = $conn->query($course_query);

if (!$course_res) {
    die("Course Query Failed: " . $conn->error);
}

$course_names = []; $course_students = [];
while($row = $course_res->fetch_assoc()) {
    $course_names[] = $row['course_name'];
    $course_students[] = $row['student_count'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Admin Dashboard - LMS</title>
    

    <style>
   :root {
    --primary: #1a0b45;
    --accent: #2ecc71;
    --bg-light: #f4f7f6;
    --accent-green: #2ecc71;
}

body { 
    margin: 0; 
    font-family: 'Inter', 'Segoe UI', sans-serif; 
    background: var(--bg-light); 
    display: flex; 
    min-height: 100vh;
}

/* Sidebar Upgrade */
.sidebar { 
    width: 260px; 
    background: var(--primary); 
    color: white; 
    padding: 25px 0; 
    transition: 0.3s;
    flex-shrink: 0;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 1000;
}

.sidebar h2 { padding: 0 25px; margin-bottom: 30px; font-size: 1.4rem; color: var(--accent); }

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

/* Main Content area */
.main-content { 
    flex-grow: 1; 
    overflow-x: hidden; 
    /* ADD THESE */
    margin-left: 260px; /* Matches sidebar width */
    padding-top: 70px;  /* Matches header height */
    min-height: 100vh;
}


/* Responsive Grid for Cards */
.dashboard-container { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
    gap: 20px; 
    padding: 30px; 
}

.card { 
    background: white; 
    padding: 25px; 
    border-radius: 15px; 
    box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
    text-align: center;
    border-bottom: 4px solid transparent;
    transition: 0.3s;
}

.card:hover { transform: translateY(-5px); border-color: var(--accent); }
.card h3 { margin: 0; color: #7f8c8d; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
.card p { font-size: 2.2rem; font-weight: 800; margin: 10px 0; color: var(--primary); }

/* MOBILE RESPONSIVE FIXES */
@media (max-width: 768px) {
    /* This hides the sidebar off-screen to the left */
    .sidebar { 
        /* position: fixed;  */
        left: -260px; 
        /* height: 100vh; 
        z-index: 1000;
        box-shadow: 10px 0 15px rgba(0,0,0,0.1); */
    }
    /* This slides it in when we click the menu button */
    .sidebar.active { 
        left: 0; 
    }

    /* This shows the 'Hamburger' icon */
    .mobile-toggle { 
        display: block !important; 
       font-size: 1.8rem; 
       cursor: pointer;
        color: var(--accent-green);
       
        
    }

    /* Hide search and text to save space on small screens */
    .search-bar, .user-text { 
        display: none; 
    }

    /* Make cards stack 1-by-1 */
.dashboard-container { 
        grid-template-columns: 1fr !important; 
        /* The first value (60px) should match your .top-bar height */
        padding: 20px 15px !important; 
    }
.top-bar {
     
        left: 0;
   
    }
    .main-content { 
        margin-left: 0; /* Remove the desktop margin */
        padding-top: 60px; /* Match your mobile header height */
    }

    /* Adjust the profile container for mobile */
    .user-profile {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Since you hide .user-text, ensure the avatar is visible */
    .avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: 2px solid var(--accent);
    }

}

/* Sidebar Header Styling */

.logo-circle {
    background: #2ecc71;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.brand-name { display: block; font-weight: 700; color: white; }
.brand-sub { display: block; font-size: 0.65rem; color: #2ecc71; text-transform: uppercase; }

/* Top Bar Styling */

.search-bar {
    background: #f8fafc;
    padding: 8px 15px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    width: 250px;
}
.search-bar input { border: none; background: transparent; outline: none; font-size: 0.9rem; }
.header-right { display: flex; align-items: center; gap: 20px; }
.user-profile { display: flex; align-items: center; gap: 10px; }
.avatar { width: 35px; height: 35px; border-radius: 50%; }
.user-text { text-align: right; line-height: 1.2; }
.user-text strong { display: block; font-size: 0.85rem; color: #1a0b45; }
.user-text span { font-size: 0.7rem; color: #7f8c8d; }



/* Sidebar Header Styling */
.sidebar-header {
    display: flex;
    align-items: center;
    padding: 0 25px;
    gap: 12px;
    margin-bottom: 30px;
}
.logo-circle {
    background: #2ecc71;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.brand-name { display: block; font-weight: 700; color: white; }
.brand-sub { display: block; font-size: 0.65rem; color: #2ecc71; text-transform: uppercase; }

/* Top Bar Styling */
.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    background: #251b4d;
    border-bottom: 2px solid gold;
    position: fixed;
    top: 0;
    /* left: 260px; Anchored after the sidebar/ */
    right: 0;
    z-index: 900;
    height: 70px; /* Set a specific height */
    box-sizing: border-box;
    width:100%
 
 
}
.search-bar {
    background: #8e889f;
    padding: 8px 15px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    width: 250px;
}
.search-bar input { border: none; background: transparent; outline: none; font-size: 0.9rem; }
.header-right { display: flex; align-items: center; gap: 20px; }
.user-profile { display: flex; align-items: center; gap: 10px; }
.avatar { width: 35px; height: 35px; border-radius: 50%; }
.user-text { text-align: right; line-height: 1.2; }
.user-text strong { display: block; font-size: 0.85rem; color: #1a0b45; }
.user-text span { font-size: 0.7rem; color: #7f8c8d; }
@media (max-width: 480px) {
    .header-title {
        font-size: 0.9rem;
        /* or display: none; if it still overlaps */
    }
}
    </style>
</head>



<body style="margin: 0;">


<div class="sidebar">
 <div class="sidebar-header">
    <div class="logo-circle">TI</div>
    <div class="logo-text">
        <span class="brand-name">Tech Iftiin</span>
        <span class="brand-sub" data-lang="portal_title">Admin Portal</span>
 </div>
</div>
        <nav>
            <a href="dashboard.php" class="active"><i class="fas fa-th-large"></i> <span data-lang="dashboard" >Dashboard</span></a>
            <a href="manage_users.php"><i class="fas fa-users"></i> <span data-lang="manage_users">Manage Users</span></a>
            <a href="admin_certificates.php">
                <i class="fas fa-certificate"></i> <span data-lang="certificates">Certificates</span>
                <?php if($pending_certs > 0): ?>
                    <span style="background: #f1c40f; color: #1a0b45; padding: 2px 6px; border-radius: 10px; font-size: 0.6rem; margin-left: auto; font-weight: bold;"><?php echo $pending_certs; ?></span>
                <?php endif; ?>
            </a>
            <a href="courses.php"><i class="fas fa-graduation-cap"></i> <span data-lang="manage_courses">Manage Courses</span></a>
            <a href="admin_logs.php"><i class="fas fa-history"></i> <span data-lang="activity_logs">Activity Logs</span></a>
            <a href="admin_messages.php">
                <i class="fas fa-envelope"></i> <span data-lang="messages">Messages</span>
                <?php if($unread_messages > 0): ?>
                    <span style="background: #e74c3c; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.6rem; margin-left: auto;"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </a>

            <a href="settings.php"><i class="fa-solid fa-gear"></i> <span data-lang="setting">Setting</span></a>

          <hr style="border: 0; border-top: 2px solid rgba(255,100,100,0.5); margin: 20px 25px;">
            <a href="../auth/logout.php" style="color: #ff7675; margin-top: 20px;"><i class="fas fa-sign-out-alt"></i> <span data-lang="logout">Logout</span></a>
        </nav>
    </div>

    <div class="main-content">

    <header class="top-bar">
        <div class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>

        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search...">
        </div>
        
        <div class="user-profile">
            <div class="user-text">
                <strong style="color:gold;"><?php echo $_SESSION['user_name']; ?></strong>
                <span data-lang="admin_role">Administrator</span>
            </div>
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=2ecc71&color=fff" alt="User" class="avatar">
        </div>
    </header>



        <div class="dashboard-container">
            <div class="card">
                <i class="fas fa-chalkboard-teacher" style="color: #3498db; font-size: 1.5rem;"></i>
                <h3 data-lang="teachers">Teachers</h3>
                <p><?php echo $stats['teacher']; ?></p>
            </div>
            <div class="card">
                <i class="fas fa-user-graduate" style="color: #9b59b6; font-size: 1.5rem;"></i>
                <h3 data-lang="students">Students</h3>
                <p><?php echo $stats['student']; ?></p>
            </div>
            <div class="card">
                <i class="fas fa-book-open" style="color: #f1c40f; font-size: 1.5rem;"></i>
                <h3 data-lang="courses">Courses</h3>
                <p><?php echo $course_count; ?></p>
            </div>

            <div class="card" onclick="location.href='admin_certificates.php'" style="cursor: pointer;">
                <i class="fas fa-file-signature" style="color: #e67e22; font-size: 1.5rem;"></i>
                <h3 data-lang="pending_certs">Pending Certs</h3>
                <p style="color: #e67e22;"><?php echo $pending_certs; ?></p>
            </div>
            <div class="card">
                <i class="fas fa-comment-dots" style="color: #2ecc71; font-size: 1.5rem;"></i>
                <h3 data-lang="new_inquiries">New Inquiries</h3>
                <p style="color: #2ecc71;"><?php echo $unread_messages; ?></p>
            </div>
        </div>

        <div style="padding: 0 30px;">
            <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h4 style="margin: 0; color: var(--primary);" data-lang="quick_action">Quick Action</h4>
                    <p style="margin: 5px 0 0; font-size: 0.85rem; color: #7f8c8d;" data-lang="add_member_desc">Add  members to the Tech Iftiin family.</p>
                </div>
                <a href="add_user.php" style="background: var(--accent); color: white; text-decoration: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; transition: 0.3s;" data-lang="register_member">
                    + Register Member
                </a>
            </div>
        </div>
  





<!-- CHARTS SECTION: Positioned at the bottom of the page -->
<div class="analytics-footer" style="margin-top: 40px; clear: both;">
    
    <h2 data-lang="system_analytics" style="color: #1a0b45; font-size: 1.2rem; margin-bottom: 20px; border-left: 5px solid #1a0b45; padding-left: 10px;">
        System Analytics
    </h2>

    <!-- Gender Distribution Card (Full Width) -->
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 4px solid #3498db; margin-bottom: 25px;">
        <h3 style="margin-top:0; font-size: 1rem; color: #1a0b45;">
            <i class="fas fa-venus-mars" style="margin-right: 8px;"></i> 
            <span data-lang="gender_distribution">Student Gender Distribution</span>
        </h3>
        <div style="height: 300px; position: relative;">
            <canvas id="genderChart"></canvas>
        </div>
    </div>

    <!-- Course Enrollment Card (Full Width) -->
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 4px solid #2ecc71; margin-bottom: 25px;">
        <h3 style="margin-top:0; font-size: 1rem; color: #1a0b45;">
            <i class="fas fa-chart-bar" style="margin-right: 8px;"></i> 
            <span data-lang="course_enrollment">Enrollment by Course</span>
        </h3>
        <div style="height: 350px; position: relative;">
            <canvas id="courseChart"></canvas>
        </div>
    </div>
    
</div>





  </div>








    <script>
        function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('active');
}

// Close sidebar if clicking outside of it on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.querySelector('.sidebar');
    const toggle = document.querySelector('.mobile-toggle');
    
    if (window.innerWidth <= 768) {
        if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    }
});







// Gender Chart - Doughnut style for a modern look
new Chart(document.getElementById('genderChart'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($genders); ?>,
        datasets: [{
            data: <?php echo json_encode($gender_counts); ?>,
            backgroundColor: ['#3498db', '#e74c3c', '#95a5a6'],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right', // Legend on the right uses the wide space better
                labels: { boxWidth: 20, padding: 20 }
            }
        },
        cutout: '70%' // Makes the doughnut thinner
    }
});

// Course Bar Chart - Wide format
new Chart(document.getElementById('courseChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($course_names); ?>,
        datasets: [{
            label: 'Students Enrolled',
            data: <?php echo json_encode($course_students); ?>,
            backgroundColor: 'rgba(46, 204, 113, 0.7)',
            borderColor: '#2ecc71',
            borderWidth: 2,
            borderRadius: 8,
            barPercentage: 0.4 // Keeps the bars from getting too "fat" on wide screens
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: { 
                beginAtZero: true, 
                ticks: { stepSize: 1, color: '#7f8c8d' },
                grid: { color: '#f1f1f1' }
            },
            x: { 
                grid: { display: false },
                ticks: { color: '#7f8c8d' }
            }
        },
        plugins: {
            legend: { display: false }
        }
    }
});
    </script>












<!-- <script src="../lang.js"></script> -->
 <!-- <script src="/lms_tech/lang.js"></script> -->
  <script src="http://localhost/lms_tech/lang.js"></script>

</body>
</html>