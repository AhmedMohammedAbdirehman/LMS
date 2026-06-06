<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$course_id = $_GET['course_id'];

// Handle Assignment
if (isset($_POST['assign'])) {
    $teacher_id = $_POST['teacher_id'];
    
    // 1. Run the update and save the result to $update
    $update = $conn->query("UPDATE courses SET teacher_id = '$teacher_id' WHERE id = '$course_id'");

    if ($update) {
        // 2. Fetch names for a descriptive log entry
        $t_data = $conn->query("SELECT name FROM users WHERE id = '$teacher_id'")->fetch_assoc();
        $c_data = $conn->query("SELECT title FROM courses WHERE id = '$course_id'")->fetch_assoc();
        
        $teacher_name = $t_data['name'] ?? 'Unknown Teacher';
        $course_name = $c_data['title'] ?? 'Unknown Course';
        
        // 3. Log the activity (using the Admin's session ID)
        $admin_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0; 
        $details = "Admin assigned teacher ($teacher_name) to course ($course_name)";
        
        if (function_exists('logActivity')) {
            logActivity($conn, $admin_id, 'ASSIGN', $details);
        }
    }

    header("Location: courses.php?msg=Teacher Assigned");
    exit();
}

// Fetch details for the form
$course = $conn->query("SELECT title FROM courses WHERE id = '$course_id'")->fetch_assoc();
$teachers = $conn->query("SELECT id, name FROM users WHERE role = 'teacher' AND status = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Teacher | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a0b45;
            --accent: #3498db;
            --success: #2ecc71;
            --bg: #f4f7f6;
        }

        body { 
            font-family: 'Inter', system-ui, sans-serif; 
            background: var(--bg); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0; 
        }

        .assignment-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            background: rgba(52, 152, 219, 0.1);
            color: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 20px;
        }

        h2 { color: var(--primary); margin: 0 0 10px 0; font-size: 1.6rem; }
        
        .course-info {
            background: #f8fafc;
            padding: 12px;
            border-radius: 10px;
            border: 1px dashed #cbd5e1;
            margin-bottom: 25px;
        }

        .course-info label {
            display: block;
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .course-info strong { color: var(--primary); font-size: 1rem; }

        .input-group { text-align: left; margin-bottom: 25px; }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--primary);
        }

        select {
            width: 100%;
            padding: 14px;
            border: 2px solid #edf2f7;
            border-radius: 12px;
            background: #fff;
            font-size: 1rem;
            color: var(--primary);
            cursor: pointer;
            transition: 0.3s;
            appearance: none; /* Custom arrow look */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%231a0b45'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
        }

        select:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
        }

        .btn-assign {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(26, 11, 69, 0.2);
        }

        .btn-assign:hover {
            background: #2c166b;
            transform: translateY(-2px);
        }

        .cancel-link {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .cancel-link:hover { color: var(--primary); }
    </style>
</head>
<body>

    <div class="assignment-card">
        <div class="icon-circle">
            <i class="fas fa-user-tie"></i>
        </div>
        
        <h2>Assign Teacher</h2>
        
        <div class="course-info">
            <label>Selected Course</label>
            <strong><?php echo htmlspecialchars($course['title']); ?></strong>
        </div>
        
        <form method="POST">
            <div class="input-group">
                <label>Select an Instructor</label>
                <select name="teacher_id" required>
                    <option value="">-- Choose from List --</option>
                    <?php while($t = $teachers->fetch_assoc()): ?>
                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <button type="submit" name="assign" class="btn-assign">
                <i class="fas fa-check"></i> Confirm Assignment
            </button>
            
            <a href="courses.php" class="cancel-link">Nevermind, go back</a>
        </form>
    </div>

</body>
</html>