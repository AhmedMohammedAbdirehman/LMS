<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$msg = "";

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'deleted') {
        $msg = "<div class='alert-success'>🗑️ Course deleted successfully!</div>";
    } elseif ($_GET['msg'] == 'error') {
        $msg = "<div class='alert-error'>⚠️ Error: Could not delete course.</div>";
    }
}

// 1. Handle Adding New Course
if (isset($_POST['add_course'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $t_id = $_POST['teacher_id']; 

    $t_id_val = ($t_id == "") ? "NULL" : "'$t_id'";

    $sql = "INSERT INTO courses (title, description, teacher_id) VALUES ('$title', '$desc', $t_id_val)";
    if ($conn->query($sql)) {
        $msg = "<p style='color: green;'>New Course Added successfully!</p>";
    } else {
        $msg = "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }

    
}

// 2. Fetch all teachers for the dropdown (DO THIS HERE)
$teachers_res = $conn->query("SELECT id, name FROM users WHERE role = 'teacher' AND status = 1");

// 3. Fetch all courses with teacher names AND material counts
$result = $conn->query("SELECT 
                            c.*, 
                            u.name as teacher_name,
                            (SELECT COUNT(*) FROM lessons WHERE lessons.course_id = c.id) as material_count
                        FROM courses c
                        LEFT JOIN users u ON c.teacher_id = u.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Manage Courses - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>

/* 1. Global & Variables */
:root { 
    --primary: #1a0b45; 
    --accent: #2ecc71; 
    --bg: #f4f7f6; 
}

body { 
    font-family: 'Inter', sans-serif; 
    margin: 0; 
    background: var(--bg); 
    display: flex; /* Kept flex for desktop layout */
}

/* --- UPDATED HEADER FOR LARGE DEVICES --- */
.top-header {
    position: fixed; 
    top: 0; 
    left: 0; 
    right: 0; 
    height: 60px;
    /* Option A: Pure white with a border for a modern 'SaaS' look */
    background: #352a5d; 
    border-bottom: 2px solid gold;
    color: var(--primary); /* Dark text for the white background */
    
    display: flex; 
    align-items: center;
    justify-content: space-between; 
    padding: 0 20px; 
    z-index: 4000;
}

/* Ensure header text is readable against white background */
.header-title, .welcome-text {
   color: var(--accent) ;
    font-weight: 500;
    letter-spacing: 0.5px;
}

/* Hamburger button color needs to change to be visible on white */
.hamburger {
    color: var(--accent) !important;
}

/* --- LARGE DEVICE SPECIFIC TWEAK --- */
@media (min-width: 993px) {
    .top-header {
        /* If you prefer a darker header but different from sidebar, try this: */
        /* background: #241261; */ 
        
        /* Adding a slight shadow helps it 'float' above the sidebar */
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding-left: 280px; /* Aligns header content with main content area */
    }
}

/* --- MOBILE/TABLET HEADER REVERT --- */
@media (max-width: 992px) {
    .top-header {
        background: var(--primary); /* Keep it dark on mobile to match brand */
        /* color: white; */
        padding-left: 20px;
    }
    .header-title, .welcome-text, .hamburger {
        color: var(--accent);
    }
}

.hamburger {
    display: none; /* Hidden on Desktop */
    background: none;
    border: none;
    color: var(--accent);
    font-size: 1.5rem;
    cursor: pointer;
}

/* 3. Sidebar Fixes */
.sidebar {
    width: 260px; 
    background: var(--primary); 
    color: white;
    height: 100vh; 
    position: fixed; 
    top: 0; 
    left: 0;
    padding-top: 70px; /* Space for the top-header */
    transition: 0.3s ease; 
    z-index: 3500;
}

.sidebar-header {
    padding: 20px;
    font-size: 1.1rem;
    font-weight: bold;
    color: var(--accent);
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.sidebar-menu {
    list-style: none;
    padding: 10px 0;
    margin: 0;
}

.sidebar-menu li a {
    padding: 15px 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    transition: 0.3s;
    font-size: 0.95rem;
}

/* Hover & Active States */
.sidebar-menu li a:hover, 
.sidebar-menu li a.active {
    background: rgba(255,255,255,0.1);
    color: var(--accent); /* Brand Green */
}

.sidebar-menu li a i {
    width: 20px; /* Keeps icons aligned */
    text-align: center;
}
.main-content { 
    margin-left: 260px; /* Offset for sidebar */
    margin-top: 60px;  /* Offset for header */
    padding: 30px; 
    width: calc(100% - 260px);
    transition: 0.3s;
}

/* 4. Responsive Grid */
.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
    padding: 10px 0;
}

/* 5. THE FIX: Responsivity & Hamburger visibility */
@media (max-width: 992px) {
    .hamburger {
        display: block !important; /* Show hamburger on tablet/phone */
    }

    .sidebar {
        left: -260px; /* Hide sidebar by default */
    }

    .sidebar.active {
        left: 0; /* Slide in when toggled */
    }

    .main-content {
        margin-left: 0;
        width: 100%;
        padding: 20px;
    }

    /* Hide text on very small screens */
    @media (max-width: 480px) {
        .header-title { display: none; }
    }
}

/* Form Styling */
.course-form { 
    background: white; 
    padding: 20px; 
    border-radius: 12px; 
    margin-bottom: 30px; 
    box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
}

.course-form form { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 15px; 
    align-items: flex-end; 
}
/* Container Grid */
.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
    padding: 10px 0;
}

/* Graceful Teacher Card */
.teacher-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid #f0f2f5;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    display: flex;
    flex-direction: column;
    border-top: 5px solid var(--accent);
}

.teacher-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: var(--accent-color);
}

.course-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: var(--accent-color);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
    display: block;
}

.course-title {
    font-size: 1.25rem;
    color: #1e293b;
    margin: 0 0 12px 0;
    line-height: 1.4;
}

.course-desc {
    font-size: 0.9rem;
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 20px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 45px;
}

/* Avatar and Teacher Info */
.teacher-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-top: 15px;
    border-top: 1px solid #f1f5f9;
}

.avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    color: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
}

.teacher-details b {
    display: block;
    font-size: 0.95rem;
    color: #334155;
}

.change-link {
    font-size: 0.75rem;
    color: var(--accent-color);
    text-decoration: none;
    font-weight: 500;
}
.change-link i{
  
    color: red;
   
}

/* Stats and Actions */
.stats-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    gap: 10px;
}

.file-count {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    color: #475569;
    cursor: pointer;
    transition: 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.file-count:hover {
    background: #e2e8f0;
}

.btn-delete-icon {
    color: #94a3b8;
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    transition: 0.2s;
}

.btn-delete-icon:hover {
    color: #ef4444;
}

.status-dot {
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Alert/Card styles remained the same but ensured they don't break flex */
/* .teacher-card { background: white; padding: 20px; border-radius: 16px; border-top: 5px solid var(--accent); } */
#sidebarOverlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 3000; }
#sidebarOverlay.active { display: block; }

/* Content Drawer (Slide out) */
#contentDrawer {
    position: fixed;
    right: -100%;
    top: 0;
    width: 400px;
    height: 100vh;
    background: white;
    z-index: 5000;
    transition: 0.4s;
    padding: 20px;
    overflow-y: auto;
    box-shadow: -5px 0 15px rgba(0,0,0,0.1);
}
#contentDrawer.show-drawer { right: 0; }
.welcome-text{
    color:var(--accent);
}

/* Container Styling */
.course-form-container {
    background: #ffffff;
    padding: 25px;
    border-radius: 16px;
    margin-bottom: 35px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    border: 1px solid #edf2f7;
}

.form-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
}

.form-icon {
    background: rgba(46, 204, 113, 0.1);
    color: var(--accent);
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.form-header h3 {
    margin: 0;
    font-size: 1.2rem;
    color: var(--primary);
    font-weight: 700;
}

/* Modern Input Layout */
.modern-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    align-items: end;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.input-group label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.input-group input, 
.input-group select {
    padding: 12px 15px;
    border: 2px solid #f1f5f9;
    border-radius: 10px;
    font-size: 0.95rem;
    color: #334155;
    background: #f8fafc;
    transition: all 0.3s ease;
}

/* Focus Effects */
.input-group input:focus, 
.input-group select:focus {
    outline: none;
    border-color: var(--accent);
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1);
}

/* Submit Button */
.btn-submit {
    background: var(--accent);
    color: white;
    padding: 13px 25px;
    border-radius: 10px;
    border: none;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    height: 48px; /* Matches input height */
}

.btn-submit:hover {
    background: #27ae60;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
}

/* Mobile Tweak */
@media (max-width: 768px) {
    .modern-form {
        grid-template-columns: 1fr;
    }
    .btn-submit {
        width: 100%;
    }
}

    </style>
</head>
<body style="display: flex; background: #f4f7f6; margin:0;">
<!-- HEADER: Always first -->

<header class="top-header">

    <div style="display: flex; align-items: center; gap: 15px;">
        <!-- The Hamburger Button -->
        <button class="hamburger" onclick="toggleNav()">
            <i class="fas fa-bars"></i>
        </button>
        <!-- Combined classes here -->
        <span class="header-title admin"; styel="font-weight: bold;" data-lang="portal_title">Admin Panel</span>
    </div>
    <div class="welcome-text">
        <span data-lang="welcome">Welcome</span>, <?php echo $_SESSION['role']; ?>
    </div>
</header>

    <div id="sidebarOverlay" onclick="toggleNav()"></div>

<!-- SIDEBAR: Fixed to the left -->
    <div class="sidebar" id="navSidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-house" ></i> <span data-lang="dashboard">Dashboard</span></a></li>
            <li><a href="manage_users.php"><i class="fas fa-users"></i> <span data-lang="manage_users">Manage Users</span></a></li>
            <li><a href="manage_courses.php" class="active"><i class="fas fa-book"></i> <span data-lang="manage_courses">Manage Courses</span></a></li>
             <li> <a href="admin_logs.php"><i class="fas fa-history"></i> <span data-lang="activity_logs">Activity Logs</span></a> </li>

            <li>  <a href="settings.php"><i class="fa-solid fa-gear"></i> <span data-lang="setting">Setting</span></a> </li>

            <hr>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt" style="color:red;"></i> <span data-lang="logout">Logout</span></a></li>
        </ul>
    </div>

<div class="main-content">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin:0;" data-lang="manage_global_courses">Manage Global Courses</h2>
            <!-- <div style="font-size: 0.9rem; color: #64748b;">
                Welcome, <?php echo $_SESSION['role']; ?>
            </div> -->
        </div>
        <!-- <a href="dashboard.php">← Back to Dashboard</a> -->
        <?php echo $msg; ?>

        <!-- <div class="course-form"> -->
<div class="course-form-container">
    <div class="form-header">
        <div class="form-icon"><i class="fas fa-plus-circle"></i></div>
        <h3 data-lang="add_new_course">Add New Course</h3>
    </div>
    
    <form method="POST" class="modern-form">
        <div class="input-group">
            <label><i class="fas fa-heading"></i> <span data-lang="course_title">Course Title</span></label>
            <input type="text" name="title" data-lang="title_placeholder" placeholder="e.g. Full Stack" required>
        </div>

        <div class="input-group">
            <label><i class="fas fa-align-left"></i> <span data-lang="description">Description</span></label>
            <input type="text" name="description" data-lang="desc_placeholder" placeholder="Overview e.g. Popular ">
        </div>

        <div class="input-group">
            <label><i class="fas fa-user-tie"></i> <span data-lang="assign_teacher">Assign Teacher</span></label>
            <select name="teacher_id">
                <option value="" data-lang="unassigned_option">-- Leave Unassigned --</option>
                <?php while($t = $teachers_res->fetch_assoc()): ?>
                    <option value="<?php echo $t['id']; ?>"><?php echo $t['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" name="add_course" class="btn-submit">
            <i class="fas fa-save"></i><span data-lang="create_course">Create Course</span>
        </button>
    </form>
</div>



<div class="admin-grid">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="teacher-card">
                <span class="course-label" data-lang="course_module">Course Module</span>
                <h3 class="course-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                <p class="course-desc"><?php echo htmlspecialchars($row['description']); ?></p>
                
                <div class="teacher-info">
                    <div class="avatar">
                        <?php echo strtoupper(substr($row['teacher_name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div class="teacher-details">
                        <b><?php echo $row['teacher_name'] ? htmlspecialchars($row['teacher_name']) : '<span class="unassigned" data-lang="unassigned">Unassigned</span>'; ?></b>
                        <a href="assign_teacher.php?course_id=<?php echo $row['id']; ?>" class="change-link">
                            <i class="fas fa-user-edit"></i> <span data-lang="change_teacher">Change Teacher</span>
                        </a>
                    </div>
                </div>

                <div class="stats-row">
                    <button class="file-count" onclick="openSidebar('<?php echo $row['id']; ?>', '<?php echo addslashes($row['title']); ?>')">
                        <i class="far fa-folder-open"></i> <?php echo $row['material_count']; ?> <span data-lang="materials">Materials</span>
                    </button>

                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span class="status-dot" style="color: <?php echo ($row['material_count'] > 0) ? '#10b981' : '#f43f5e'; ?>;">
                            <i class="fas fa-circle" style="font-size: 8px;"></i> 
                            <?php echo ($row['material_count'] > 0) ? 'Active' : 'Empty'; ?>
                        </span>

                        <button type="button" class="btn-delete-icon" onclick="confirmDelete('<?php echo $row['id']; ?>', '<?php echo addslashes($row['title']); ?>')" title="Delete Course">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>



<div id="overlay" onclick="closeSidebar()"></div>





<div id="contentDrawer">
    <div style="position: sticky; top: -20px; background: white; padding: 10px 0; border-bottom: 1px solid #eee; z-index: 10; margin-bottom: 15px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 id="drawerTitle" style="margin:0; font-size: 1.2rem;">Course Content</h2>
            <button onclick="closeSidebar()" style="background:none; border:none; font-size: 2rem; cursor:pointer; color: #7f8c8d;">&times;</button>
        </div>
    </div>
    
    <div id="drawerBody"></div>
</div>


    </div>





<div id="deleteModal" style="display:none; position:fixed; z-index:3000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter: blur(3px);">
    <div style="background:white; margin:15% auto; padding:30px; border-radius:16px; width:90%; max-width:400px; text-align:center;">
        <div style="color: #e53e3e; font-size: 3rem; margin-bottom: 15px;">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <h2 style="margin:0; color: #1a0b45;" data-lang="are_you_sure">Are you sure?</h2>
        <p style="color: #64748b; line-height: 1.5; margin: 15px 0 25px 0;">
            <span data-lang="delete_warning">This will permanently delete</span> <strong id="deleteCourseName"></strong>.
        </p>
        <div style="display: flex; gap: 10px;">
            <button onclick="closeDeleteModal()" style="flex:1; padding:12px;" data-lang="cancel">Cancel</button>
            <a id="confirmDeleteBtn" href="#" style="flex:1; padding:12px; background:#e53e3e; color:white; text-decoration:none; border-radius:8px;" data-lang="yes_delete">Yes, Delete It</a>
        </div>
    </div>
</div>


<script>
// Main Sidebar Toggle
function openSidebar(courseId, title) {
    const drawer = document.getElementById('contentDrawer');
    const overlay = document.getElementById('overlay');
    
    document.getElementById('drawerTitle').innerText = title;
    document.getElementById('drawerBody').innerHTML = "<div style='padding:20px;'>Loading materials...</div>";

    // CHANGE: Use classList instead of direct style to bypass !important
    drawer.classList.add('show-drawer');
    overlay.classList.add('show-overlay');

    fetch('get_course_materials.php?course_id=' + courseId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('drawerBody').innerHTML = data;
        });
}

function closeSidebar() {
    const drawer = document.getElementById('contentDrawer');
    const overlay = document.getElementById('overlay');
    
    // CHANGE: Remove the classes to slide it back
    drawer.classList.remove('show-drawer');
    overlay.classList.remove('show-overlay');
}

// THE NEW TOGGLE FOR TEXT (Add this part)
function toggleText(id) {
    const textDiv = document.getElementById('text-' + id);
    const btn = document.getElementById('btn-' + id);
    
    if (textDiv.style.display === "none" || textDiv.style.display === "") {
        textDiv.style.display = "block";
        btn.innerText = "Hide Text";
        btn.style.background = "#34495e";
    } else {
        textDiv.style.display = "none";
        btn.innerText = "View Text";
        btn.style.background = "#9b59b6";
    }
}

function confirmDelete(courseId, courseTitle) {
    const modal = document.getElementById('deleteModal');
    const nameSpan = document.getElementById('deleteCourseName');
    const deleteBtn = document.getElementById('confirmDeleteBtn');

    if (modal && nameSpan && deleteBtn) {
        // 1. Set the name
        nameSpan.innerText = '"' + courseTitle + '"';
        
        // 2. Set the link - Use the full path to avoid folder confusion
        deleteBtn.setAttribute('href', 'delete_course.php?id=' + courseId);

        // 3. Show the modal
        modal.style.display = 'block';
    } else {
        console.error("Modal elements not found!");
    }
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close if user clicks outside the modal box
window.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target == modal) {
        closeDeleteModal();
    }
});


// Toggle Navigation Sidebar for Mobile
function toggleNav() {
    const sidebar = document.getElementById('navSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
}

// Close mobile sidebar if window is resized above mobile breakpoint
window.addEventListener('resize', function() {
    if (window.innerWidth > 992) {
        document.getElementById('navSidebar').classList.remove('active');
        document.getElementById('sidebarOverlay').classList.remove('active');
    }
});

</script>


  <script src="http://localhost/lms_tech/lang.js"></script>


</body>
</html>