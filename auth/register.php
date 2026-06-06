<?php
// Since this loads in an iframe, we need to establish the connection and session here
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/../global_file.php';
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$msg = "";
$courses_res = $conn->query("SELECT id, title FROM courses");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $password = $_POST['password']; 
    $confirm_password = $_POST['confirm_password']; // Capture the repeat field


    // 1. Properly handle the array of IDs
    if (isset($_POST['course_ids']) && is_array($_POST['course_ids'])) {
        $clean_ids = array_map(function($id) use ($conn) {
            return mysqli_real_escape_string($conn, $id);
        }, $_POST['course_ids']);
        $course_ids_string = implode(',', $clean_ids);
    } else {
        $course_ids_string = '';
    }
    
    $role = 'student';
    $status = 0; // Students start as "Pending"

    $checkEmail = $conn->query("SELECT id FROM users WHERE email = '$email'");
    
    if ($checkEmail->num_rows > 0) {
        $msg = "<div class='error-msg' style='color:red; text-align:center; grid-column: span 2;'>Email already registered!</div>";
    } elseif (empty($course_ids_string)) {
        $msg = "<div class='error-msg' style='color:red; text-align:center; grid-column: span 2;'>Please select at least one course!</div>";
    } 
// 1. Check if Email exists
    if ($checkEmail->num_rows > 0) {
        $msg = "<div class='error-msg' style='color:red; text-align:center; grid-column: span 2;'>Email already registered!</div>";
    } 
    // 2. Check if Courses are selected
    elseif (empty($course_ids_string)) {
        $msg = "<div class='error-msg' style='color:red; text-align:center; grid-column: span 2;'>Please select at least one course!</div>";
    } 
    // 3. Check if Passwords match
    elseif ($password !== $confirm_password) {
        $msg = "<div class='error-msg' style='color:red; text-align:center; grid-column: span 2;'>Passwords do not match!</div>";
    }
    // 4. Check Password Strength
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $msg = "<div class='error-msg' style='color:red; text-align:center; grid-column: span 2;'>Password must be 8+ characters with uppercase, lowercase, number, and special char!</div>";
    } 
    // 5. If everything is fine, proceed to Insert
    else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, gender, password, role, course_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", $name, $email, $phone, $gender, $hashed_password, $role, $course_ids_string, $status);
        if ($stmt->execute()) {
            // Success Message and Iframe Script
            echo "
            <div style='text-align: center; padding: 40px; font-family: sans-serif;'>
                <div style='font-size: 5rem; color: #2ecc71; margin-bottom: 20px;'>
                    <i class='fas fa-check-circle'></i>
                </div>
                <h2 style='color: #1a0b45; margin-bottom: 10px;'>Registration Successful!</h2>
                <p style='color: #7f8c8d; line-height: 1.6;'>
                    Your account is pending admin approval.<br>
                    Redirecting to portal...
                </p>
                <div style='margin-top: 30px;' class='loader'></div>
            </div>
            <style>.loader { border: 4px solid #f3f3f3; border-top: 4px solid #2ecc71; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; display: inline-block; } @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>
            <script>setTimeout(function() { window.top.location.href = '../index.php?registered=success'; }, 2000);</script>";
            exit();
        } else {
            $msg = "<div class='error-msg'>Error: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    body { 
    font-family: 'Inter', 'Segoe UI', sans-serif; 
    padding: 20px; background: #fff; color: #2c3e50;
}
h2 { color: #1a0b45; text-align: center; margin-bottom: 25px; font-weight: 800; }

/* The Grid Logic */
form {
    display: grid;
    grid-template-columns: 1fr 1fr; 
    gap: 15px;
}
.full-width { grid-column: span 2; } /* Fields that need full width */

.input-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #1a0b45; font-size: 0.85rem; }
.input-group input, .input-group select { 
    width: 100%; padding: 12px; border: 2px solid #edf2f7; border-radius: 10px; 
    box-sizing: border-box; background: #f8fafc; transition: 0.3s;
}
.input-group input:focus { border-color: #2ecc71; outline: none; background: #fff; }

.btn-submit { 
    grid-column: span 2; padding: 16px; background: #2ecc71; color: white; 
    border: none; border-radius: 10px; font-weight: 700; cursor: pointer;
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
}

/* Mobile Responsive */
@media (max-width: 480px) {
    form { grid-template-columns: 1fr; }
    .full-width, .btn-submit { grid-column: span 1; }
}
.icon-container {
    text-align: center;
    margin-bottom: 10px;
}

.icon-container i {
    font-size: 3rem;
    color: #2ecc71; /* Your Accent Green */
    background: #f4f7f6;
    padding: 20px;
    border-radius: 50%;
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.2);
}

.form-footer {
    grid-column: span 2; /* Ensures it stretches across your grid */
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #edf2f7;
}

.form-footer p {
    font-size: 0.9rem;
    color: #7f8c8d;
    margin: 0;
}

.form-footer a {
    color: #1a0b45; /* Your Navy Theme */
    font-weight: 700;
    text-decoration: none;
    margin-left: 5px;
    transition: color 0.3s ease;
}

.form-footer a:hover {
    color: #2ecc71; /* Switches to your Accent Green on hover */
    text-decoration: underline;
}

/* Responsive fix for mobile */
@media (max-width: 480px) {
    .form-footer { grid-column: span 1; }
}

/* Modern Course Checkbox Grid */
.courses-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    background: #f8fafc;
    padding: 15px;
    border-radius: 10px;
    border: 2px solid #edf2f7;
}

.course-option {
    display: flex;
    align-items: center;
    gap: 10px;
    background: white;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: 0.2s;
}

.course-option:hover {
    border-color: #2ecc71;
}

.course-option input[type="checkbox"] {
    width: 18px !important;
    height: 18px !important;
    cursor: pointer;
    accent-color: #2ecc71; /* Matches your green theme */
}

.course-option span {
    font-size: 0.85rem;
    font-weight: 500;
    color: #1a0b45;
}

@media (max-width: 480px) {
    .courses-container { grid-template-columns: 1fr; }
}
    </style>
</head>
<body>

<div id="registerFormFields">
    <div class="icon-container">
    <i class="fas fa-user-graduate"></i>
</div>
    <h2 data-lang="reg_title">Student Registration</h2>
    <?php echo $msg; ?>
    
    <form action="" method="POST">
        <div class="input-group full-width">
            <label data-lang="label_name">Full Name</label>
            <input type="text" name="name" placeholder="your name" data-placeholder="placeholder_name" required>
        </div>
        
        <div class="input-group">
            <label data-lang="label_email">Email Address</label>
            <input type="email" name="email" placeholder="email@example.com" data-placeholder="placeholder_email" required>
        </div>

        <div class="input-group">
            <label data-lang="label_phone">Phone Number</label>
            <input type="text" name="phone" placeholder="+253 ..." required>
        </div>
        
        <div class="input-group" style="flex: 1;">
                <label data-lang="label_gender">Gender</label>
                <select name="gender" required>
                    <option value="" data-lang="opt_select">Select</option>
                    <option value="Male" data-lang="opt_male">Male</option>
                    <option value="Female" data-lang="opt_female">Female</option>
                </select>
            </div>
            
<div class="input-group full-width">
    <label><i class="fas fa-book-open"></i> <span data-lang="label_courses">Select Your Courses (Multiple Allowed)</span></label>
    <div class="courses-container">
        <?php while($course = $courses_res->fetch_assoc()): ?>
            <label class="course-option">
                <input type="checkbox" name="course_ids[]" value="<?php echo $course['id']; ?>">
                <span><?php echo $course['title']; ?></span>
            </label>
        <?php endwhile; ?>
    </div>
</div>

<div class="input-group">
    <label data-lang="label_pass">Create Password</label>
    <input type="password" name="password" placeholder="••••••••" required>
</div>

<div class="input-group">
    <label data-lang="label_confirm">Repeat Password</label>
    <input type="password" name="confirm_password" placeholder="••••••••" required>
</div>

<div class="input-group full-width" style="margin-top: -10px; margin-bottom: 10px;">
    <small style="color: #7f8c8d; font-size: 0.75rem;">
        <i class="fas fa-info-circle"></i> <span data-lang="pass_hint">Use 8+ characters with uppercase, numbers, and symbols (@$!%).</span>
    </small>
</div>
        
        <button type="submit" class="btn-submit" data-lang="btn_register_now">Register Now</button>
        
<div class="form-footer">
    <p><span data-lang="footer_text">Already have an account?</span>
        <a href="javascript:void(0)" onclick="window.top.showAuth('login')" data-lang="footer_link">Login here</a>
    </p>
</div>
    </form>
</div>


<script src="/lms_tech/lang.js"></script>
</body>
</html>