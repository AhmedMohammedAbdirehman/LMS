<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
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
    <title>Manage Courses - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
/* 1. Reset & Layout */
body { font-family: 'Inter', sans-serif; margin: 0; background: #f4f7f6; }
.main-content { flex: 1; padding: 30px; min-width: 0; }

/* 2. Fix the "Double Div" Form Issue */
.course-form {
    background: white !important;
    padding: 25px !important;
    border-radius: 15px !important;
    margin-bottom: 30px !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important;
}

/* If you have a course-form inside a course-form, strip the inner one */
.course-form .course-form {
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
    background: transparent !important;
}

.course-form h3 { margin-top: 0; color: #1a0b45; font-size: 1.1rem; margin-bottom: 20px; }

/* 3. Form Input Styling */
.course-form form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: flex-end;
}

.course-form div { flex: 1; min-width: 200px; }

.course-form label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 8px;
}

.course-form input, .course-form select {
    width: 100% !important;
    padding: 12px !important; /* Larger touch area */
    border: 1px solid #333 !important;
    border-radius: 8px !important;
    background: #fff !important;
    box-sizing: border-box !important;
}

/* 4. The Grid & Cards */
.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
}

/* Paste this at the bottom of your existing <style> tag */
.alert-success {
    background: #ecfdf5 !important;
    color: #065f46 !important;
    padding: 15px 20px !important;
    border-radius: 12px !important;
    border: 1px solid #a7f3d0 !important;
    margin-bottom: 20px !important;
    font-weight: 600 !important;
    display: flex;
    align-items: center;
    box-shadow: 0 4px 6px rgba(0,0,0,0.02) !important;
}

.alert-error {
    background: #fef2f2 !important;
    color: #991b1b !important;
    padding: 15px 20px !important;
    border-radius: 12px !important;
    border: 1px solid #fecaca !important;
    margin-bottom: 20px !important;
    font-weight: 600 !important;
}
.teacher-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border-top: 6px solid #2ecc71; /* Brand Green */
    display: flex;
    flex-direction: column;
}

.stats-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}
.btn-login {
       background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%) !important;
    color: white !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3) !important;
    box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3) !important;
}

.btn-login:hover {
    background: #4338ca !important;
    transform: translateY(-2px);
}

/* 5. Mobile Responsiveness */
@media (max-width: 768px) {
    .main-content { padding: 15px; }
    .admin-grid { grid-template-columns: 1fr; }
    .course-form form { flex-direction: column; align-items: stretch; }
    .course-form div { width: 100%; }
    .btn-login {
        width: 100% !important;
        display: block !important;
        margin-top: 10px !important;
        padding: 15px !important; /* Larger for easier tapping on phones */
       background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%) !important;
    color: white !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3) !important;
    }
}
/* FIX: Ensure the Sidebar can actually move when JS triggers it */
#contentDrawer {
    position: fixed !important;
    top: 0 !important;
    height: 100% !important;
    background: white !important;
    z-index: 2000 !important;
    transition: 0.4s ease-in-out !important;
    box-shadow: -10px 0 30px rgba(0,0,0,0.2) !important;
    display: block !important;
    right: -500px !important; 
    width: 450px !important;

    /* --- ADD THESE THREE LINES --- */
    overflow-y: auto !important; /* This enables vertical scrolling */
    max-height: 100vh !important; /* Ensures it doesn't go past the screen height */
    padding: 20px !important;    /* Gives content some breathing room */
}

/* This class will be toggled by JS to show the drawer */
#contentDrawer.show-drawer {
    right: 0 !important;
}

#overlay.show-overlay {
    display: block !important;
}


/* Delete Button Styling */
.btn-delete {
    background: #fff5f5 !important;
    color: #e53e3e !important;
    border: 1px solid #feb2b2 !important;
    padding: 6px 12px !important;
    border-radius: 8px !important;
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-delete:hover {
    background: #e53e3e !important;
    color: white !important;
    border-color: #e53e3e !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(229, 62, 62, 0.2);
}

/* Ensure the button is full width on mobile if needed */
@media (max-width: 768px) {
    .stats-row {
        flex-direction: column;
        gap: 10px;
        align-items: stretch !important;
    }
    .btn-delete {
        justify-content: center;
    }
}
    </style>
</head>
<body style="display: flex; background: #f4f7f6; margin:0;">

    <div class="main-content" style="flex: 1; min-width: 0;">
        <h2 data-lang="manage_courses_title">Manage Global Courses</h2>
        <a href="dashboard.php">← <span data-lang="btn_back_dashboard">Back to Dashboard</span></a>
        <?php echo $msg; ?>

        <!-- <div class="course-form"> -->
<div class="course-form">
    <h3 data-lang="add_new_course">Add New Course</h3>
    <form method="POST" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label data-lang="label_course_title">Course Title</label>
            <input type="text" name="title" required style="width: 100%; padding: 8px;">
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label data-lang="label_description">Description</label>
            <input type="text" name="description" style="width: 100%; padding: 8px;">
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label data-lang="label_assign_teacher">Assign Teacher</label>
            <select name="teacher_id" style="width: 100%; padding: 8px;">
                <option value="" data-lang="option_unassigned">-- Leave Unassigned --</option>
                <?php while($t = $teachers_res->fetch_assoc()): ?>
                    <option value="<?php echo $t['id']; ?>"><?php echo $t['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="add_course" class="btn-login" style="padding: 10px 20px;" data-lang="btn_add_course">Add Course</button>
    </form>
</div>

<div class="admin-grid">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="teacher-card">
                <span style="font-size: 0.7rem; color: #95a5a6; text-transform: uppercase;" data-lang="label_course_type">Course</span>
                <h3 style="margin: 5px 0; color: #2c3e50;"><?php echo $row['title']; ?></h3>
                <p style="font-size: 0.9rem; color: #7f8c8d; height: 40px; overflow: hidden;">
                    <?php echo $row['description']; ?>
                </p>
                
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 15px;">
                    <div style="width: 35px; height: 35px; background: #34495e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                        <?php echo strtoupper(substr($row['teacher_name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-size: 0.9rem; font-weight: bold;">
                            <?php echo $row['teacher_name'] ? $row['teacher_name'] : '<span class="unassigned" data-lang="status_unassigned">Unassigned</span>'; ?>
                        </div>
                        <a href="assign_teacher.php?course_id=<?php echo $row['id']; ?>" style="font-size: 0.75rem; color: #3498db; text-decoration: none;" data-lang="link_change_teacher">Change Teacher</a>
                    </div>
                </div>

                <div class="stats-row">
                    <button class="file-count" onclick="openSidebar('<?php echo $row['id']; ?>', '<?php echo addslashes($row['title']); ?>')">
                        📂 <?php echo $row['material_count']; ?> <span data-lang="label_materials">Materials</span>
                    </button>
                <small style="color: <?php echo ($row['material_count'] > 0) ? '#27ae60' : '#e74c3c'; ?>; font-weight: bold;">
                <?php echo ($row['material_count'] > 0) ? '<span data-lang="status_active">● Active</span>' : '<span data-lang="status_empty">○ Empty</span>'; ?>
                </small>

<button type="button" class="btn-delete" onclick="confirmDelete('<?php echo $row['id']; ?>', '<?php echo addslashes($row['title']); ?>')">
    🗑️ <span data-lang="btn_delete">Delete</span>
</button>
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
    <div style="background:white; margin:15% auto; padding:30px; border-radius:16px; width:90%; max-width:400px; text-align:center; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
        <div style="color: #e53e3e; font-size: 3rem; margin-bottom: 15px;">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <h2 style="margin:0; color: #1a0b45;" data-lang="delete_confirm_title">Are you sure?</h2>
        <p style="color: #64748b; line-height: 1.5; margin: 15px 0 25px 0;">
          <span data-lang="delete_warning_text">This will permanently delete the course</span> <strong id="deleteCourseName"></strong> <span data-lang="delete_warning_subtext">and all associated materials. This action cannot be undone.</span>
        </p>
        <div style="display: flex; gap: 10px;">
            <button onclick="closeDeleteModal()" style="flex:1; padding:12px; border:1px solid #e2e8f0; border-radius:8px; background:white; color:#64748b; cursor:pointer; font-weight:600;" data-lang="btn_cancel">Cancel</button>
            <a id="confirmDeleteBtn" href="#" style="flex:1; padding:12px; border:none; border-radius:8px; background:#e53e3e; color:white; text-decoration:none; font-weight:600; font-size: 0.9rem;" data-lang="btn_confirm_delete">Yes, Delete It</a>
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

    // Set the course name in the text
    nameSpan.innerText = '"' + courseTitle + '"';
    
    // Set the actual URL for the delete action
    deleteBtn.href = "delete_course.php?id=" + courseId;

    // Show the modal
    modal.style.display = 'block';
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
</script>






<script src="/lms_tech/lang.js"></script>

</body>
</html>