<?php
session_start();
require_once 'config/db.php';
require_once __DIR__ . '/global_file.php';

// 1. Redirect if already logged in
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: Admin/dashboard.php");
    } elseif ($_SESSION['role'] == 'manager') {
        header("Location: Manager/dashboard.php"); // Added this line
    } elseif ($_SESSION['role'] == 'teacher') {
        header("Location: Teacher/dashboard.php");
    } else {
        header("Location: Student/dashboard.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Secure lookup using Prepared Statements
    $stmt = $conn->prepare("SELECT id, name, role, password, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($user['status'] == 0) {
            $error = ($user['role'] == 'student') ? "Your account is pending approval." : "This account is disabled.";
        } else {
            // 2. VERIFY HASHED PASSWORD (The most important change)
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                logActivity($conn, $user['id'], 'LOGIN', 'User logged into the system');

               // Role-based Redirects
                $redirects = [
                    'admin'   => 'Admin/dashboard.php', 
                    'manager' => 'Manager/dashboard.php', // Added Manager redirect
                    'teacher' => 'Teacher/dashboard.php', 
                    'student' => 'Student/dashboard.php'
                ];
                header("Location: " . ($redirects[$user['role']] ?? 'Student/dashboard.php'));
                exit();
            } else {
                $error = "Invalid password.";
            }
        }
    } else {
        $error = "No user found with that email.";
    }
    $stmt->close();
}

include 'includes/header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
   
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="Join Tech Iftiin for expert-led courses in Full Stack Dev, AI, and Cyber Security. Build real-world projects and launch your career.">

    <title>Tech Iftiin | Leading AI & Tech Institute in Djibouti</title>
<link rel="shortcut icon" type="image/png" href="images/newLogo.png?v=3">




<style>
    .home-hero {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 90vh;
        text-align: center;
        background: linear-gradient(rgba(26, 11, 69, 0.8), rgba(26, 11, 69, 0.8)), url('assets/hero-bg.jpg'); /* Add a nice tech bg image if you have one */
        background-size: cover;
        background-position: center;
        color: white;
        padding: 20px;
    }

    .hero-text h1 { font-size: 3.5rem; margin-bottom: 10px; color: #2ecc71; }
    .hero-text p { font-size: 1.2rem; margin-bottom: 30px; opacity: 0.9; max-width: 600px; }

    /* Overlay / Modal Styling */
    .auth-overlay {
        display: none; /* Hidden by default */
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(5px);
        z-index: 2000;
        justify-content: center;
        align-items: center;
    }

    .auth-card {
        background: white;
        padding: 40px;
        border-radius: 15px;
        width: 100%;
        max-width: 400px;
        position: relative;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .close-btn {
        position: absolute; top: 15px; right: 20px;
        font-size: 1.5rem; cursor: pointer; color: #7f8c8d;
    }

    /* Form Styles */
    .auth-card h2 { color: #1a0b45; margin-bottom: 20px; }
    .input-group { text-align: left; margin-bottom: 15px; }
    .input-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #2c3e50; font-size: 0.85rem; }
    .input-group input { 
        width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box; 
    }
    .btn-submit { 
        width: 100%; padding: 14px; background: #1a0b45; color: white; 
        border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px;
    }
    .btn-submit:hover { background: #2ecc71; }



    /* Hero Section Layout */
.hero-section {
    padding: 80px 5% 100px 5%;
    background: #ffffff;
    min-height: 70vh;
    display: flex;
    align-items: center;
}

.hero-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 50px;
    max-width: 1300px;
    margin: 0 auto;
}

/* Left Content Styles */
.hero-content { flex: 1; text-align: left; }

.badge {
    background: rgba(46, 204, 113, 0.1);
    color: var(--accent-green);
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: bold;
    text-transform: uppercase;
}

.hero-content h1 {
    font-size: 3.5rem;
    color: var(--primary-bg);
    line-height: 1.1;
    margin: 20px 0;
}

.hero-content .highlight { color: var(--accent-green); }

.hero-content p {
    font-size: 1.1rem;
    color: #555;
    margin-bottom: 35px;
    line-height: 1.6;
}

/* Button & Stat Styles */
.hero-btns { display: flex; gap: 15px; align-items: center; }

.btn-primary-hero {
    background: var(--accent-green);
    color: white;
    padding: 15px 35px;
    border-radius: 8px;
    border: none;
    font-weight: bold;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.3s;
}

.hero-stats {
    display: flex; gap: 40px; margin-top: 50px;
    border-top: 1px solid #eee; padding-top: 30px;
}

/* Right Side Image Styles */
.hero-image { flex: 1; position: relative; }

.image-wrapper img {
    width: 100%;
    border-radius: 20px;
    box-shadow: 20px 20px 60px rgba(0,0,0,0.1);
}

.experience-tag {
    position: absolute; bottom: 30px; left: -20px;
    background: white; padding: 15px 20px;
    border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    display: flex; align-items: center; gap: 12px;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .hero-container { flex-direction: column-reverse; text-align: center; }
    .hero-content h1 { font-size: 2.5rem; }
    .hero-btns { justify-content: center; }
    .hero-stats { justify-content: center; }
}
.welcome-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-left: 5px solid #2ecc71;
    padding: 15px 25px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 15px;
    z-index: 10000;
    animation: slideInRight 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.toast-content { display: flex; align-items: center; gap: 15px; }
.toast-content i { font-size: 1.5rem; color: #2ecc71; }
.toast-content strong { display: block; color: #1a0b45; }
.toast-content p { margin: 0; font-size: 0.85rem; color: #7f8c8d; }

@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* 1. Lock the entire page */
body {
    -webkit-user-select: none;
    -ms-user-select: none;
    user-select: none;
    -webkit-touch-callout: none;
}

/* 2. Unlock the form fields (Crucial for UX) */
input, textarea, [contenteditable="true"] {
    -webkit-user-select: text;
    -ms-user-select: text;
    user-select: text;
}

</style>
</head>
<body>




<?php if(isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
    <div id="welcomeToast" class="welcome-toast">
        <div class="toast-content">
            <i class="fas fa-rocket"></i>
            <div>
                <strong data-lang="toast_success">Account Created!</strong>
                <p data-lang="toast_msg">Please login with your email and password.</p>
            </div>
        </div>
        <button onclick="document.getElementById('welcomeToast').remove()">&times;</button>
    </div>
<?php endif; ?>


<section class="hero-section">
    <div class="hero-container">
        <div class="hero-content">
            <span class="badge" data-lang="hero_badge">Djibouti's Tech Leader</span>
                <h1>
                    <span data-lang="hero_title_part1">Shape Your Future with </span>
                    <span class="highlight" data-lang="hero_highlight">Artificial Intelligence</span>
                </h1>  
                <p data-lang="hero_desc">Welcome to Tech Iftiin. We provide specialized training in AI and Information Technology to drive digital transformation in Djibouti and beyond.</p>
            
            <div class="hero-btns">
                <button onclick="showAuth('register')" class="btn-primary-hero" data-lang="hero_btn_start">Get Started Now</button>
                <a href="#courses" data-lang="hero_btn_courses" style="text-decoration:none; color:var(--primary-bg); font-weight:bold; margin-left:20px;">Explore Courses</a>
            </div>

            <div class="hero-stats">
                <div class="stat">
                    <strong>500+</strong>
                    <span data-lang="stat_students" style="color:#7f8c8d; font-size:0.9rem;">Students</span>
                </div>
                <div class="stat">
                    <strong>15+</strong>
                    <span data-lang="stat_tutors" style="color:#7f8c8d; font-size:0.9rem;">Expert Tutors</span>
                </div>
            </div>
        </div>

        <div class="hero-image">
            <div class="image-wrapper">
                <!-- <img src="https://images.unsplash.com/photo-1523961131990-5ea7c61b2107?q=80&w=1000&auto=format&fit=crop" alt="AI Technology"> -->
                <img src="images/two.jfif" alt="">
                <div class="experience-tag">
                    <i class="fas fa-certificate" style="color:#f39c12; font-size:1.5rem;"></i>
                    <span data-lang="cert_tag" style="font-weight:bold; color:#1a0b45;">Certified Programs</span>
                </div>
            </div>
        </div>
    </div>
</section>





<section class="about-section" id="about">
    <div class="about-container">
        <div class="about-image">
            <img src="images/one.jfif" alt="Students at Tech Iftiin">
            <div class="floating-info">
                <h3>5+</h3>
                <p data-lang="about_years">Years of Excellence</p>
            </div>
        </div>

        <div class="about-content">
            <span class="section-badge" data-lang="about_badge">About Tech Iftiin</span>
           <h2>
                <span data-lang="about_title_part1">Leading the Digital Transformation in </span>
                <span class="highlight" data-lang="about_highlight">Djibouti</span>
            </h2>
            <p data-lang="about_desc1">At Tech Iftiin, we believe that technology is the key to the future. Our institute is dedicated to empowering the next generation of Djiboutian innovators through world-class training in Artificial Intelligence and Software Engineering.</p>
            
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-microchip"></i>
                    <div>
                        <h4 data-lang="feat_ai_title">AI-Driven Curriculum</h4>
                        <p data-lang="feat_ai_desc">Our courses are designed around the latest industry trends in AI.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fas fa-user-tie"></i>
                    <div>
                        <h4 data-lang="feat_mentor_title">Expert Mentors</h4>
                        <p data-lang="feat_mentor_desc">Learn from professionals with years of real-world experience.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fas fa-laptop-code"></i>
                    <div>
                        <h4 data-lang="feat_proj_title">Practical Projects</h4>
                        <p data-lang="feat_proj_desc">Don't just learn theory—build actual software applications.</p>
                    </div>
                </div>
            </div>

           <div id="moreAboutText" style="display: none; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                <p style="font-style: italic; color: #444;" data-lang="about_vision">
                    Founded with the vision of bridging the digital divide, Tech Iftiin has grown into Djibouti's leading center for technical vocational training. 
                </p>
                <p>
                  <span data-lang="about_facility">Our facility provides state-of-the-art labs where students engage in:</span>  
                    <ol style="color:#27ae60">
                        <li data-lang="list_ai">Artificial Intelligence AI</li>
                        <li data-lang="list_web">Full-stack Web Development</li>
                        <li data-lang="list_prompt">Prompt Engineering</li>
                        <li data-lang="list_cyber">Cybersecurity Threat Analysis</li>
                        <li data-lang="list_video">Video Editing</li>
                        <li data-lang="list_mobile">Mobile App Creation with Flutter</li>
                        <li data-lang="list_marketing">Digital Marketing</li>
                        <li data-lang="list_python">Python Programming</li>
                    </ol>
<span data-lang="about_foster">We don't just teach code; we foster a community of problem-solvers ready to tackle the challenges of the 21st century.</span>                
           </p>
            </div>

            <button onclick="toggleAboutText()" id="aboutBtn" class="btn-primary" style="margin-top: 20px;" data-lang="about_btn_more">Learn More About Us</button>
        </div>
    </div>
</section>


<style>
    /* About Us Section Styles */
.about-section {
    padding: 100px 5%;
    background: #fdfdfd;
}

.about-container {
    display: flex;
    align-items: center;
    gap: 60px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Image Styling */
.about-image {
    flex: 1;
    position: relative;
}

.about-image img {
    width: 100%;
    border-radius: 20px;
    box-shadow: 15px 15px 40px rgba(0,0,0,0.08);
}

.floating-info {
    position: absolute;
    bottom: -20px;
    right: -20px;
    background: var(--accent-green);
    color: white;
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(46, 204, 113, 0.3);
}

.floating-info h3 { font-size: 2rem; margin: 0; }
.floating-info p { font-size: 0.9rem; margin: 0; }

/* Content Styling */
.about-content { flex: 1; }

.section-badge {
    color: var(--accent-orange);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-size: 0.85rem;
    display: block;
    margin-bottom: 10px;
}

.about-content h2 {
    font-size: 2.5rem;
    color: var(--primary-bg);
    margin-bottom: 20px;
    line-height: 1.2;
}

.about-content .highlight { color: var(--accent-green); }

.about-content p {
    color: #666;
    line-height: 1.7;
    margin-bottom: 30px;
}

/* Features List */
.features-grid {
    display: grid;
    gap: 20px;
}

.feature-item {
    display: flex;
    gap: 15px;
    align-items: flex-start;
}

.feature-item i {
    background: rgba(46, 204, 113, 0.1);
    color: var(--accent-green);
    padding: 12px;
    border-radius: 10px;
    font-size: 1.2rem;
}

.feature-item h4 {
    margin: 0 0 5px 0;
    color: var(--primary-bg);
}

.feature-item p {
    margin: 0;
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 992px) {
    .about-container { flex-direction: column; text-align: center; }
    .feature-item { text-align: left; }
    .floating-info { display: none; }
}

#moreAboutText {
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Ensure the primary button stays consistent with your branding */
.btn-primary {
    background: var(--accent-green);
    color: white;
    padding: 12px 30px;
    border-radius: 8px;
    border: none;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}
.btn-primary:hover {
    background: #27ae60;
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
}
</style>










<section class="courses-section" id="courses">
    <div class="section-header">
        <span class="section-badge" data-lang="course_badge">Our Programs</span>
        <h2 data-lang="course_title">Explore Our <span class="highlight" data-lang="course_highlight">Expert-Led</span> Courses</h2>
        <p data-lang="course_subtitle">Master the most in-demand skills in the digital age with our hands-on training programs.</p>
    </div>

    <div class="courses-grid">
        <div class="course-card">
            <div class="course-img">
                <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?q=80&w=1000" alt="Artificial Intelligence">
            </div>
            <div class="course-info">
                <div class="course-meta">
                    <span><i class="far fa-clock"></i> <span data-lang="dur_4m">4 Months</span></span>
                    <span class="course-price">$400</span>
                </div>
                <h3 data-lang="course_ai_name">Artificial Intelligence (AI)</h3>
                <p data-lang="course_ai_desc">Dive deep into Neural Networks, Deep Learning, and Computer Vision. Build intelligent systems that can see, hear, and think.</p>

                <div class="prereq-container">
                <div class="prereq-header" onclick="togglePrereq(this)">
                    <span><i class="fas fa-info-circle"></i> <span data-lang="pre_label">Prerequisite</span></span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </div>
                <div class="prereq-content">
                    <p data-lang="pre_ai">To join this course, you must have completed <strong>Python Programming</strong> or equivalent coding experience.</p>
                </div>
            </div>
                <button onclick="showAuth('register')" class="btn-enroll" data-lang="btn_enroll">Enroll Now</button>
            </div>
        </div>

        <div class="course-card">
            <div class="course-img">
                <img src="images/program1.avif" alt="MERN Stack">
            </div>
            <div class="course-info">
                <div class="course-meta">
                    <span><i class="far fa-clock"></i> <span data-lang="dur_4m">4 Months</span></span>
                    <span class="course-price">$300</span>
                </div>
                <h3 data-lang="course_mern_name">Full Stack Web (MERN)</h3>
                <p data-lang="course_mern_desc">Master MySQl, MongoDB, Express, React, and Node.js. Become a professional developer capable of building complex web applications.</p>
                <div class="prereq-container">
                <div class="prereq-header" onclick="togglePrereq(this)">
                    <span><i class="fas fa-info-circle"></i> <span data-lang="pre_label">Prerequisite</span></span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </div>
                 <div class="prereq-content">
                        <p data-lang="pre_mern">Understanding of <strong>Basic Computer Skill</strong> is highly recommended.</p>
                    </div>
            </div>
                <button onclick="showAuth('register')" class="btn-enroll" data-lang="btn_enroll">Enroll Now</button>
            </div>
        </div>

        <div class="course-card">
            <div class="course-img">
                <img src="images/AI3.avif" alt="Prompt Engineering">
            </div>
            <div class="course-info">
                 <div class="course-meta">
                    <span><i class="far fa-clock"></i> <span data-lang="dur_1m">1 Month</span></span>
                    <span class="course-price">$100</span>
                </div>
                <h3 data-lang="course_prompt_name">Prompt Engineering</h3>
                <p data-lang="course_prompt_desc">Learn the art of communicating with LLMs like GPT-4. Optimize workflows and boost productivity using advanced AI prompting.</p>
                <div class="prereq-container">
                <div class="prereq-header" onclick="togglePrereq(this)">
                    <span><i class="fas fa-info-circle"></i> <span data-lang="pre_label">Prerequisite</span></span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </div>
                <div class="prereq-content">
                    <p data-lang="pre_prompt">No prior coding experience required. Open to all beginners.</p>
                </div>
            </div>
                <button onclick="showAuth('register')" class="btn-enroll" data-lang="btn_enroll">Enroll Now</button>
            </div>
        </div>

        <div class="course-card">
            <div class="course-img">
                <img src="images/AI2.avif" alt="NLP">
            </div>
            <div class="course-info">
               <div class="course-meta">
                    <span><i class="far fa-clock"></i> <span data-lang="dur_2m">2 Months</span></span>
                    <span class="course-price">$200</span>
                </div>
                <h3 data-lang="course_nlp_name">Natural Language Processing</h3>
                <p data-lang="course_nlp_desc">Understand how machines process human language. Build chatbots, sentiment analyzers, and translation tools.</p>
                <div class="prereq-container">
                <div class="prereq-header" onclick="togglePrereq(this)">
                    <span><i class="fas fa-info-circle"></i> <span data-lang="pre_label">Prerequisite</span></span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </div>
                <div class="prereq-content">
                    <p data-lang="pre_nlp">Completion of <strong>Python Programming</strong> and basic Probability concepts.</p>
                </div>
            </div>
                <button onclick="showAuth('register')" class="btn-enroll" data-lang="btn_enroll">Enroll Now</button>
            </div>
        </div>

        <div class="course-card">
            <div class="course-img">
                <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?q=80&w=1000" alt="Mobile App Dev">
            </div>
            <div class="course-info">
                <div class="course-meta">
                    <span><i class="far fa-clock"></i> <span data-lang="dur_4m">4 Months</span></span>
                    <span class="course-price">$300</span>
                </div>
                <h3 data-lang="course_mobile_name">Mobile App Development</h3>
                <p data-lang="course_mobile_desc">Create stunning cross-platform apps using Flutter and React Native. Deploy your apps to both Android and iOS stores.</p>
            <div class="prereq-container">
                <div class="prereq-header" onclick="togglePrereq(this)">
                    <span><i class="fas fa-info-circle"></i> <span data-lang="pre_label">Prerequisite</span></span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </div>
                <div class="prereq-content">
                    <p data-lang="pre_mobile">Familiarity with Object-Oriented Programming (OOP) logic.</p>
                </div>
            </div>
                
                <button onclick="showAuth('register')" class="btn-enroll" data-lang="btn_enroll">Enroll Now</button>
            </div>
        </div>

        <div class="course-card">
            <div class="course-img">
                <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=1000" alt="Cybersecurity">
            </div>
            <div class="course-info">
                <div class="course-meta">
                    <span><i class="far fa-clock"></i> <span data-lang="dur_3m">3 Months</span></span>
                    <span class="course-price">$300</span>
                </div>
                <h3 data-lang="course_cyber_name">Cyber Security</h3>
                <p data-lang="course_cyber_desc">Protect systems from digital attacks. Learn ethical hacking, network security, and risk management strategies.</p>

                <div class="prereq-container">
                <div class="prereq-header" onclick="togglePrereq(this)">
                    <span><i class="fas fa-info-circle"></i> <span data-lang="pre_label">Prerequisite</span></span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </div>
                <div class="prereq-content">
                    <p data-lang="pre_cyber">Basic knowledge of Computer Networking and Linux is helpful.</p>
                </div>
            </div>
                <button onclick="showAuth('register')" class="btn-enroll" data-lang="btn_enroll">Enroll Now</button>
            </div>
        </div>

        <div class="course-card">
            <div class="course-img">
                <img src="images/program3.avif" alt="Python Programming">
            </div>
            <div class="course-info">
                <div class="course-meta">
                    <span><i class="far fa-clock"></i> <span data-lang="dur_2m">2 Months</span></span>
                    <span class="course-price">$200</span>
                </div>
                <h3 data-lang="course_python_name">Python Programming</h3>
                <p data-lang="course_python_desc">Master the world's most popular language. From basic syntax to automation and data analysis. The perfect starting point.</p>

                <div class="prereq-container">
                <div class="prereq-header" onclick="togglePrereq(this)">
                    <span><i class="fas fa-info-circle"></i> <span data-lang="pre_label">Prerequisite</span></span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </div>
                <div class="prereq-content">
                    <p data-lang="pre_none">No prerequisites. Designed for absolute beginners.</p>
                </div>
            </div>
                <button onclick="showAuth('register')" class="btn-enroll" data-lang="btn_enroll">Enroll Now</button>
            </div>
        </div>

        <div class="course-card">
            <div class="course-img">
                <img src="https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?q=80&w=1000" alt="Video Editing">
            </div>
            <div class="course-info">
                <div class="course-meta">
                    <span><i class="far fa-clock"></i> <span data-lang="dur_2m">2 Months</span></span>
                    <span class="course-price">$300</span>
                </div>
                <h3 data-lang="course_video_name">Professional Video Editing</h3>
                <p data-lang="course_video_desc">Master Adobe Premiere Pro and After Effects. Learn cinematic storytelling, color grading, and advanced motion graphics.</p>

                <div class="prereq-container">
                <div class="prereq-header" onclick="togglePrereq(this)">
                    <span><i class="fas fa-info-circle"></i> <span data-lang="pre_label">Prerequisite</span></span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </div>
                <div class="prereq-content">
              <p data-lang="pre_video">A computer with at least 8GB RAM is recommended for software performance.</p>
                </div>
            </div>
                <button onclick="showAuth('register')" class="btn-enroll" data-lang="btn_enroll">Enroll Now</button>
            </div>
        </div>

        <div class="course-card">
            <div class="course-img">
                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1000" alt="Digital Marketing">
            </div>
            <div class="course-info">
                <div class="course-meta">
                    <span><i class="far fa-clock"></i> <span data-lang="dur_2m">2 Months</span></span>
                    <span class="course-price">$300</span>
                </div>
             <h3 data-lang="course_marketing_name">Digital Marketing & SEO</h3>
                <p data-lang="course_marketing_desc">Grow businesses with social media strategies, Google Ads, and SEO. Learn to analyze data and create high-converting campaigns.</p>          <div class="prereq-container">
                <div class="prereq-header" onclick="togglePrereq(this)">
                    <span><i class="fas fa-info-circle"></i> <span data-lang="pre_label">Prerequisite</span></span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </div>
                <div class="prereq-content">
               <p data-lang="pre_marketing">Basic understanding of social media platforms (Facebook, Instagram, etc.).</p>
                </div>
            </div>
               
                <button onclick="showAuth('register')" class="btn-enroll" data-lang="btn_enroll">Enroll Now</button>
            </div>
        </div>
    </div>
</section>

<style>
    .prereq-container {
        margin: 15px 0;
        border-radius: 8px;
        background: #f8f9fa;
        overflow: hidden;
        border: 1px solid #eee;
    }
    .prereq-header {
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        color: #500b30;
    }
    .prereq-header:hover { background: #f0f0f0; }
    .prereq-content {
        max-height: 0;
        padding: 0 15px;
        transition: all 0.3s ease-out;
        font-size: 0.85rem;
        color: #666;
    }
    .prereq-container.active .prereq-content {
        max-height: 100px; /* Adjust as needed */
        padding: 10px 15px;
    }
    .arrow-icon { transition: transform 0.3s; }
    .prereq-container.active .arrow-icon { transform: rotate(180deg); }
</style>

<script>
    function togglePrereq(element) {
        const container = element.parentElement;
        container.classList.toggle('active');
        
        // Optional: Change text if you want "Read Less" instead of just an arrow
        // const arrow = element.querySelector('.arrow-icon');
    }
</script>

<style>
    /* Course Section Container */
    .courses-section { 
        padding: 60px 5%; 
        background: #f8f9fa; 
    }

    .section-header { 
        text-align: center; 
        max-width: 700px; 
        margin: 0 auto 50px; 
    }

    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Individual Card Layout - FIXED for equal height */
    .course-card {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: 0.3s all ease;
        border: 1px solid #eee;
        display: flex;
        flex-direction: column;
        height: 100%; /* All cards take full height of the row */
    }

    .course-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border-color: var(--accent-green);
    }

    /* Image Styling */
    .course-img { 
        position: relative; 
        height: 220px; 
        overflow: hidden; 
    }
    
    .course-img img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
        transition: 0.5s; 
    }
    
    .course-card:hover .course-img img { 
        transform: scale(1.1); 
    }

    /* Course Info Container - FIXED to push button to bottom */
    .course-info { 
        padding: 25px; 
        flex-grow: 1; /* Takes up remaining space */
        display: flex;
        flex-direction: column;
    }

    /* Meta Info: Duration & Price */
    .course-meta {
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        margin-bottom: 15px;
    }

    .course-meta span:first-child {
        color: #7f8c8d;
        font-weight: 500;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .course-price {
        background: rgba(243, 156, 18, 0.1); /* Soft Orange */
        color: var(--accent-orange);
        padding: 5px 12px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 1rem;
    }

    /* Text Styling */
    .course-info h3 { 
        color: var(--primary-bg); 
        margin: 0 0 12px 0; 
        font-size: 1.35rem; 
    }

    .course-info p { 
        color: #666; 
        font-size: 0.95rem; 
        line-height: 1.6; 
        margin-bottom: 25px; 
        flex-grow: 1; /* Pushes the button to the bottom if text is short */
    }

    /* Button Styling */
    .btn-enroll {
        width: 100%; 
        padding: 14px; 
        background: transparent;
        border: 2px solid var(--accent-green); 
        color: var(--accent-green);
        border-radius: 8px; 
        font-weight: bold; 
        cursor: pointer; 
        transition: 0.3s;
        text-align: center;
        text-decoration: none;
    }

    .course-card:hover .btn-enroll {
        background: var(--accent-green); 
        color: white;
    }
</style>







<section class="services-section" id="services">
    <div class="section-header">
        <span class="section-badge" data-lang="service_badge">Expert Solutions</span>
        <h2 data-lang="service_title">Professional <span class="highlight" data-lang="service_highlight">Tech Services</span></h2>
        <p data-lang="service_subtitle">Beyond training, we provide end-to-end digital solutions to help your business thrive in the modern era.</p>
    </div>

    <div class="services-grid">
        <div class="service-box">
            <div class="service-icon">
                <i class="fas fa-code-branch"></i>
            </div>
            <h3 data-lang="serv_dev_name">Custom Software Development</h3>
            <p data-lang="serv_dev_desc">From POS systems to complex ERP platforms, we build scalable software tailored to your specific business needs in Djibouti.</p>
           <ul class="service-list">
                <li><i class="fas fa-check"></i> <span data-lang="serv_dev_list1">Web Applications</span></li>
                <li><i class="fas fa-check"></i> <span data-lang="serv_dev_list2">Mobile App Solutions</span></li>
            </ul>
        </div>

        <div class="service-box">
            <div class="service-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h3 data-lang="serv_train_name">Corporate Tech Training</h3>
            <p data-lang="serv_train_desc">Empower your team with on-site or remote workshops in Cybersecurity, AI tools, and Digital Productivity.</p>
              <ul class="service-list">
                <li><i class="fas fa-check"></i> <span data-lang="serv_train_list1">Staff Upskilling</span></li>
                <li><i class="fas fa-check"></i> <span data-lang="serv_train_list2">Digital Transformation</span></li>
            </ul>
        </div>

        <div class="service-box">
            <div class="service-icon">
                <i class="fas fa-shield-virus"></i>
            </div>
            <h3 data-lang="serv_sec_name">Security Consulting</h3>
            <p data-lang="serv_sec_desc">We analyze your digital infrastructure to identify vulnerabilities and implement robust security protocols to protect your data.</p>
            <ul class="service-list">
                <li><i class="fas fa-check"></i> <span data-lang="serv_sec_list1">Threat Assessment</span></li>
                <li><i class="fas fa-check"></i> <span data-lang="serv_sec_list2">Data Encryption</span></li>
            </ul>
        </div>

        <div class="service-box">
            <div class="service-icon">
                <i class="fas fa-vial"></i>
            </div>
          <h3 data-lang="serv_ui_name">UI/UX & Branding</h3>
            <p data-lang="serv_ui_desc">Creating intuitive and beautiful digital experiences that resonate with your users and strengthen your brand identity.</p>
            <ul class="service-list">
                <li><i class="fas fa-check"></i> <span data-lang="serv_ui_list1">Interactive Prototypes</span></li>
                <li><i class="fas fa-check"></i> <span data-lang="serv_ui_list2">Modern Rebranding</span></li>
            </ul>
        </div>
    </div>
</section>






<style>
    /* Services Section Styling */
.services-section {
    padding: 100px 5%;
    background: var(--primary-bg); /* Dark background to contrast with white course section */
    color: white;
}

.services-section .section-header h2, 
.services-section .section-header p {
    color: white;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.service-box {
    background: rgba(255, 255, 255, 0.05);
    padding: 40px;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: 0.4s;
}

.service-box:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-10px);
    border-color: var(--accent-green);
}

.service-icon {
    width: 60px;
    height: 60px;
    background: var(--accent-green);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 25px;
}

.service-icon i {
    font-size: 1.8rem;
    color: var(--primary-bg);
}

.service-box h3 {
    font-size: 1.4rem;
    margin-bottom: 15px;
    color: var(--accent-green);
}

.service-box p {
    font-size: 0.95rem;
    color: #bdc3c7;
    line-height: 1.6;
    margin-bottom: 20px;
}

.service-list {
    list-style: none;
    padding: 0;
}

.service-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.9rem;
    margin-bottom: 8px;
    color: white;
}

.service-list li i {
    color: var(--accent-orange);
    font-size: 0.8rem;
}
</style>






<section class="faq-section" id="faq">
    <div class="section-header">
        <span class="section-badge" data-lang="faq_badge">Common Questions</span>
        <h2 data-lang="faq_title">Frequently Asked <span class="highlight" data-lang="faq_highlight">Questions</span></h2>
        <p data-lang="faq_subtitle">Everything you need to know about our admissions, courses, and certifications.</p>
    </div>

    <div class="faq-container">
        <!-- Requirements -->
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <h3 data-lang="faq_q1">What are the requirements to join the AI or MERN courses?</h3>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p data-lang="faq_a1">For most courses, basic computer literacy is enough. For advanced tracks like AI and NLP, a basic understanding of mathematics and logic is helpful. We provide all the necessary software and tools during the training.</p>
            </div>
        </div>

        <!-- Certificate -->
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <h3 data-lang="faq_q2">Will I receive a certificate after completing the course?</h3>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p data-lang="faq_a2">Yes! Upon successful completion of the course and your final project, you will receive a professional certificate from Tech Iftiin Institute, which is recognized by top employers in Djibouti.</p>
            </div>
        </div>

        <!-- Installments -->
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <h3 data-lang="faq_q3">Can I pay the tuition fees in installments?</h3>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p data-lang="faq_a3">Absolutely. We offer flexible payment plans to make our courses accessible. You can choose to pay monthly or in two installments throughout the duration of your program.</p>
            </div>
        </div>

        <!-- Job Placement -->
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <h3 data-lang="faq_q4">Do you offer job placement assistance?</h3>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p data-lang="faq_a4">Yes. We have a dedicated "Job Connecting Platform" specifically for our students. We help you build your LinkedIn profile, prepare your CV, and connect you with local tech companies in Djibouti.</p>
            </div>
        </div>
    </div>
</section>






<style>
    /* FAQ Section Styling */
.faq-section {
    padding: 100px 5%;
    background: #fdfdfd;
}

.faq-container {
    max-width: 800px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.faq-item {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 12px;
    overflow: hidden;
    transition: 0.3s;
}

.faq-item:hover {
    border-color: var(--accent-green);
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.faq-question {
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    background: #fff;
    transition: 0.3s;
}

.faq-question h3 {
    font-size: 1.1rem;
    color: var(--primary-bg);
    margin: 0;
}

.faq-question i {
    color: var(--accent-green);
    transition: 0.4s;
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease-out, padding 0.3s ease;
    background: #fcfcfc;
    padding: 0 25px;
}

.faq-answer p {
    color: #666;
    line-height: 1.6;
    padding-bottom: 20px;
    margin: 0;
}

/* Active State Styles */
.faq-item.active {
    border-color: var(--accent-green);
}

.faq-item.active .faq-question {
    background: rgba(46, 204, 113, 0.03);
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}

.faq-item.active .faq-answer {
    max-height: 200px; /* Adjust if the answer is longer */
    padding-top: 10px;
}
</style>






<section class="stats-section">
    <div class="stats-container">
        <div class="stat-item">
            <h3 class="counter" data-target="500">250</h3>
            <p data-lang="stat_students">Students Trained</p>
        </div>
        <div class="stat-item">
            <h3 class="counter" data-target="15">7</h3>
            <p data-lang="stat_instructors">Expert Instructors</p>
        </div>
        <div class="stat-item">
            <h3 class="counter" data-target="120">70</h3>
            <p data-lang="stat_projects">Projects Completed</p>
        </div>
        <div class="stat-item">
            <h3 class="counter" data-target="95">90</h3>
            <p data-lang="stat_hiring">Hiring Rate %</p>
        </div>
    </div>
</section>

<section class="portfolio-section" id="portfolio">
    <div class="section-header">
        <span class="section-badge" data-lang="port_badge">Our Impact</span>
        <h2 data-lang="port_title">What Our <span class="highlight" data-lang="port_highlight">Experts & Students Build</span></h2>
        <p data-lang="port_subtitle">Real-world applications and platforms powering businesses in Djibouti.</p>
    </div>

    <div class="portfolio-grid">
        <div class="project-card">
            <div class="project-img">
                <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=1000" alt="Restaurant Website">
                <div class="project-overlay">
                    <span class="project-cat" data-lang="cat_design">Web Design & Branding</span>
                </div>
            </div>
            <div class="project-info">
                <h4 data-lang="proj_urban_name">Urban Beach Restaurant</h4>
                <p data-lang="proj_urban_desc">Professional promotional website with digital menus and integrated booking features.</p>
            </div>
        </div>

        <div class="project-card">
            <div class="project-img">
                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?q=80&w=1000" alt="Restaurant POS">
                <div class="project-overlay">
                    <span class="project-cat" data-lang="cat_mern">MERN Stack / POS</span>
                </div>
            </div>
            <div class="project-info">
                <h4 data-lang="proj_pos_name">Advanced Restaurant POS</h4>
                <p data-lang="proj_pos_desc">A role-based Point of Sale system with real-time sales tracking and cashier dashboards.</p>
            </div>
        </div>

        <div class="project-card">
            <div class="project-img">
                <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1000" alt="Stock Management">
                <div class="project-overlay">
                    <span class="project-cat" data-lang="cat_logic">Inventory Logic</span>
                </div>
            </div>
            <div class="project-info">
                <h4 data-lang="proj_stock_name">Smart Stock Manager</h4>
                <p data-lang="proj_stock_desc">Enterprise-level inventory control with automated low-stock alerts and supplier management.</p>
            </div>
        </div>



        <div class="project-card">
            <div class="project-img">
                <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?q=80&w=1000" alt="Job Platform">
                <div class="project-overlay">
                    <span class="project-cat" data-lang="cat_social">Social Impact</span>
                </div>
            </div>
            <div class="project-info">
                <h4 data-lang="proj_job_name">Djibouti Job Connect</h4>
                <p data-lang="proj_job_desc">A multi-actor platform connecting local youth with employment opportunities across the country.</p>
            </div>
        </div>

  <div class="project-card">
    <div class="project-img">
        <img src="https://images.pexels.com/photos/1595483/pexels-photo-1595483.jpeg" alt="Bicycle Competition">
        <div class="project-overlay">
            <span class="project-cat" data-lang="cat_event">Event Platform</span>
        </div>
    </div>
    <div class="project-info">
        <h4 data-lang="proj_tour_name">Tour de Djibouti Platform</h4>
        <p data-lang="proj_tour_desc">Integrated digital platform for race registration, rider tracking, and sponsor management.</p>
    </div>
</div>

 <div class="project-card">
    <div class="project-img">
        <img src="https://images.pexels.com/photos/2199293/pexels-photo-2199293.jpeg" alt="Fleet Tracking">
        <div class="project-overlay">
            <span class="project-cat" data-lang="cat_logistics">Logistics Tech</span>
        </div>
    </div>
    <div class="project-info">
        <h4 data-lang="proj_truck_name">TruckTrack System</h4>
        <p data-lang="proj_truck_desc">Fleet tracking software with driver documentation and real-time approval workflows.</p>
    </div>
  </div>
    </div>
</section>





<style>
    /* Stats Bar Styling */
.stats-section {
    background: var(--primary-bg);
    padding: 60px 5%;
    margin-bottom: 50px;
}
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
    gap: 30px;
}
.stat-item h3 {
    color: var(--accent-green);
    font-size: 2.5rem;
    margin-bottom: 5px;
}
.stat-item p {
    color: #bdc3c7;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 1px;
}

/* Project Showcase Styling */
.portfolio-section { padding: 80px 5%; background: #fcfcfc; }
.portfolio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}
.project-card {
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}
.project-img { position: relative; height: 200px; }
.project-img img { width: 100%; height: 100%; object-fit: cover; }
.project-overlay {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(46, 204, 113, 0.8);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: 0.3s;
}
/* .project-card:hover .project-overlay { opacity: 1; } */
.project-cat { color: white; font-weight: bold; border: 1px solid white; padding: 5px 15px; border-radius: 5px; }
.project-info { padding: 20px; }
.project-info h4 { color: var(--primary-bg); margin-bottom: 10px; }
.project-info p { color: #666; font-size: 0.9rem; line-height: 1.5; }
</style>











<section class="choice-section" id="why-us">
    <div class="section-header">
        <span class="section-badge" data-lang="choice_badge">Our Impact</span>
        <h2 data-lang="choice_title">Why Students & Clients <span class="highlight" data-lang="choice_highlight">Choose Us</span></h2>
        <p data-lang="choice_subtitle">Learn from industry experts and join a community of successful graduates leading the tech scene in Djibouti.</p>
    </div>

    <div class="choice-container">
        <div class="people-grid">
            <div class="person-card ceo">
                <div class="person-img">
                    <img src="https://ui-avatars.com/api/?name=Ahmed+Bache&background=2c3e50&color=fff&size=128" alt="Ahmed Bache">
                    <span class="role-tag" data-lang="role_ceo">Founder & CEO</span>
                </div>
                <div class="person-info">
                    <h3>Ahmed Bache</h3>
                    <p class="designation" data-lang="desc_ceo">Visionary leader driving tech innovation at Tech Iftiin.</p>
                </div>
            </div>
            <div class="person-card instructor">
                <div class="person-img">
                    <img src="https://ui-avatars.com/api/?name=Ahmed+Mohammed&background=2ecc71&color=fff&size=128" alt="Ahmed Mohammed">
                    <span class="role-tag" data-lang="role_lead">Lead Instructor</span>
                </div>
                <div class="person-info">
                    <h3>Ahmed Mohammed</h3>
                    <p class="designation" data-lang="desc_ahmed_m">Full Stack Web Development & Cyber Security</p>
                    <div class="social-links">
                        <i class="fab fa-linkedin"></i>
                        <i class="fab fa-github"></i>
                    </div>
                </div>
            </div>

            <div class="person-card instructor">
                <div class="person-img">
                    <img src="https://ui-avatars.com/api/?name=Elias+Ibrahim&background=f39c12&color=fff&size=128" alt="Elias Ibrahim">
                    <span class="role-tag" data-lang="role_lead">Lead Instructor</span>
                </div>
                <div class="person-info">
                    <h3>Elias Ibrahim</h3>
                    <p class="designation" data-lang="desc_elias">Artificial Intelligence & Prompt Engineering</p>
                    <div class="social-links">
                        <i class="fab fa-linkedin"></i>
                    </div>
                </div>
            </div>



<div class="person-card instructor">
    <div class="person-img">
        <img src="https://ui-avatars.com/api/?name=Hassan+Idriss&background=9b59b6&color=fff&size=128" alt="Hassan Idriss">
        <span class="role-tag" data-lang="role_lead">Lead Instructor</span>
    </div>
    <div class="person-info">
        <h3>Hassan Idriss</h3>
        <p class="designation" data-lang="desc_hassan">Python Programming & Data Science</p>
        <div class="social-links">
            <i class="fab fa-linkedin"></i>
            <i class="fab fa-github"></i>
        </div>
    </div>
</div>

        </div>

        <div class="graduates-divider">
            <span data-lang="success_label">Success Stories (Our Graduates)</span>
        </div>

        <div class="testimonials-grid">
            <div class="testimonial-box">
                <i class="fas fa-quote-left"></i>
                <p data-lang="test_ali">"Tech Iftiin changed my career. The hands-on training in AI and Full Stack gave me the confidence to build real-world applications."</p>
                <div class="grad-info">
                    <strong>Ali Mohamed</strong>
                    <span data-lang="grad_tag">Full Stack & AI Graduate</span>
                </div>
            </div>

            <div class="testimonial-box">
                <i class="fas fa-quote-left"></i>
                <p data-lang="test_bilan">"The mentorship here is unmatched. Learning Python and MERN stack under experts helped me secure a great position in the industry."</p>
                <div class="grad-info">
                    <strong>Bilan Mohamed</strong>
                    <span data-lang="grad_tag">Full Stack & AI Graduate</span>
                </div>
            </div>
            <div class="testimonial-box">
    <i class="fas fa-quote-left"></i>
    <p data-lang="test_omar">"The MERN stack program gave me the exact skills local companies are looking for. I am now working on enterprise-level web solutions."</p>
    <div class="grad-info">
        <strong>Omar Aden</strong>
        <span data-lang="grad_tag">Full Stack & AI Graduate</span>
    </div>
</div>



<div class="testimonial-box current">
    <i class="fas fa-quote-left"></i>
    <p data-lang="test_khalid">"Learning Prompt Engineering and NLP at Tech Iftiin is opening my eyes to the power of AI. It's the best tech investment I've made."</p>
    <div class="grad-info">
        <strong>Khalid Ibrahim</strong>
        <span class="status-tag" data-lang="status_current">Current Student - AI & NLP</span>
    </div>
</div>
        </div>
    </div>
</section>





<style>
    /* Why Choose Us Section */
    .status-tag {
    font-size: 0.8rem;
    background: rgba(46, 204, 113, 0.2);
    color: #2ecc71;
    padding: 2px 8px;
    border-radius: 4px;
    margin-top: 5px;
    display: inline-block;
}

.testimonial-box.current {
    border-left: 4px solid var(--accent-orange);
}
.choice-section {
    padding: 100px 5%;
    background: #fff;
}

.people-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.person-card {
    background: #fdfdfd;
    border-radius: 20px;
    padding: 30px;
    text-align: center;
    border: 1px solid #eee;
    transition: 0.3s;
}

.person-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.05);
    border-color: var(--accent-green);
}

.person-img {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 20px;
}

.person-img img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.role-tag {
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--accent-green);
    color: white;
    font-size: 0.7rem;
    padding: 4px 12px;
    border-radius: 50px;
    white-space: nowrap;
    font-weight: bold;
}

.person-info h3 { color: var(--primary-bg); margin-bottom: 5px; }
.designation { color: #7f8c8d; font-size: 0.9rem; line-height: 1.4; }

/* Testimonials Styling */
.graduates-divider {
    text-align: center;
    margin-bottom: 40px;
    position: relative;
}
.graduates-divider::before {
    content: ""; position: absolute; top: 50%; left: 0; width: 100%; height: 1px; background: #eee; z-index: 1;
}
.graduates-divider span {
    position: relative; z-index: 2; background: #fff; padding: 0 20px; color: var(--accent-orange); font-weight: bold; text-transform: uppercase; letter-spacing: 1px;
}

.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.testimonial-box {
    background: var(--primary-bg);
    color: white;
    padding: 40px;
    border-radius: 20px;
    position: relative;
}

.testimonial-box i {
    font-size: 2rem;
    color: var(--accent-green);
    opacity: 0.3;
    margin-bottom: 15px;
}

.testimonial-box p {
    font-style: italic;
    line-height: 1.6;
    margin-bottom: 20px;
}

.grad-info strong { display: block; color: var(--accent-green); font-size: 1.1rem; }
.grad-info span { font-size: 0.85rem; color: #bdc3c7; }
</style>










<style>
    .alert-success {
    background-color: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>





<section class="contact-section" id="contact">
    <div class="section-header">
        <span class="section-badge" data-lang="cont_badge">>Get In Touch</span>
        <h2 data-lang="cont_title">Visit Us or <span class="highlight" data-lang="cont_highlight">Reach Out</span></h2>
        <p data-lang="cont_subtitle">Have questions about our programs or services? Our team is ready to assist you.</p>
    </div>

    <div class="contact-container">
        <div class="contact-form-box">
            <h3 data-lang="cont_form_title">Send us a Message</h3>
            <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> 
        <span data-lang="cont_success">Thank you! Your message has been sent to Tech Iftiin. We will contact you soon.</span>
            </div>
        <?php endif; ?>
            <form action="contact_process.php" method="POST" class="main-contact-form">
            <div class="input-group">
            <input type="text" name="name" data-lang-placeholder="ph_name" placeholder="Your Name" required>
            <input type="email" name="email" data-lang-placeholder="ph_email" placeholder="Your Email" required>
            </div>
            <input type="text" name="subject" data-lang-placeholder="ph_subject" placeholder="Subject" required>
            <textarea name="message" rows="5" data-lang-placeholder="ph_msg" placeholder="Your Message" required></textarea>

            <button type="submit" name="submit_contact" class="btn-primary" data-lang="btn_send">Send Message</button>
            </form>
        </div>

        <div class="contact-info-box">
            <div class="info-details">
                <div class="info-item">
                    <i class="fas fa-location-dot"></i>
                    <div>
                        <h4 data-lang="btn_send">Location</h4>
                        <p>Bld de Gaulle,at the junction of Route Siesta and Bld de Gaulle, Building, 238</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h4 data-lang="label_call">Call Us</h4>
                        <p>+253 77 26 13 42</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4 data-lang="label_email">Email</h4>
                        <p>info@techiftiin.com</p>
                    </div>
                </div>
            </div>

  <div class="map-wrapper">
    <iframe 
        width="100%" 
        height="250" 
        style="border:0; border-radius: 15px;" 
        loading="lazy" 
        allowfullscreen 
        referrerpolicy="no-referrer-when-downgrade"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3906.941655079361!2d43.150035074151744!3d11.588098788613437!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x162301b8d01f6873%3A0xc1829a664491193d!2sTECH%20IFTIIN%20INSTITUTE%20OF%20AI%20%26%20TECHNOLOGY!5e0!3m2!1sen!2sdj!4v1713800000000!5m2!1sen!2sdj">
    </iframe>
</div>
    </div>
    </div>
</section>








<style>
    /* Contact Section Styling */
.contact-section {
    padding: 100px 5%;
    background: #fff;
}

.contact-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Form Styling */
.contact-form-box {
    background: #fdfdfd;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.05);
}

.contact-form-box h3 {
    margin-bottom: 25px;
    color: var(--primary-bg);
}

.main-contact-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.input-group {
    display: flex;
    gap: 15px;
}

.main-contact-form input, 
.main-contact-form textarea {
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 8px;
    outline: none;
    font-size: 0.95rem;
}

.main-contact-form input:focus, 
.main-contact-form textarea:focus {
    border-color: var(--accent-green);
}

/* Info Box & Map Styling */
.contact-info-box {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.info-item {
    display: flex;
    gap: 15px;
    margin-bottom: 10px;
}

.info-item i {
    background: rgba(46, 204, 113, 0.1);
    color: var(--accent-green);
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 1.1rem;
}

.info-item h4 {
    margin: 0;
    color: var(--primary-bg);
    font-size: 1.1rem;
}

.info-item p {
    margin: 3px 0 0;
    color: #666;
}

.map-wrapper {
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border-radius: 15px;
    overflow: hidden;
}

/* Responsive */
@media (max-width: 992px) {
    .contact-container {
        grid-template-columns: 1fr;
    }
    .input-group {
        flex-direction: column;
    }
}
</style>
















<div class="auth-overlay" id="authOverlay">
    <div class="auth-card" id="authCard">
        <span class="close-btn" onclick="closeAuth()">&times;</span>
        
        <div id="loginForm">
            <div style="text-align: center; margin-bottom: 15px;">
        <i class="fas fa-user-shield" style="font-size: 3rem; color: #2ecc71; background: #f4f7f6; padding: 15px; border-radius: 50%;"></i>
    </div>

    <h2 style="text-align:center; color:#1a0b45; margin-bottom: 5px;" data-lang="auth_title">Portal Login</h2>
    <p style="text-align:center; color:#7f8c8d; font-size:0.85rem; margin-bottom:20px;" data-lang="auth_subtitle">Welcome back to Tech Iftiin</p>
           
    <?php if($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="index.php" method="POST">
                <div class="input-group">
                    <label data-lang="label_email_addr">Email Address</label>
                    <input type="email" name="email" required  data-lang-placeholder="ph_email_portal" placeholder="name@example.com">
                </div>
                <div class="input-group">
                    <label data-lang="label_password">Password</label>
                    <input type="password" name="password" data-lang="label_password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-submit" data-lang="btn_access">Access Dashboard</button>
            </form>
            <p style="margin-top:20px; font-size:0.9rem; color:#7f8c8d;">
                <span data-lang="new_student">New student?</span>
                 <a href="javascript:void(0)" onclick="showAuth('register')" style="color:#2ecc71; font-weight:bold; text-decoration:none;" data-lang="create_acc">Create an account</a>
            </p>
        </div>

        <div id="registerContainer" style="display:none; height: 500px;">
            <iframe id="registerIframe" src="" style="width:100%; height:100%; border:none; border-radius:10px;"></iframe>
        </div>
    </div>
</div>


<style>
    /* Modern Blur Overlay */
.auth-overlay {
    background: rgba(26, 11, 69, 0.85); /* Navy with Transparency */
    backdrop-filter: blur(8px); /* The "Glass" effect */
}

/* Polished Card */
.auth-card {
    border-radius: 20px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    padding: 40px;
    position: relative;
    border: 1px solid rgba(255,255,255,0.1);
    transition: max-width 0.4s ease; /* Smooth transition when resizing for registration */
}

/* Icon-in-Input Styling */
.input-wrapper {
    position: relative;
    margin-bottom: 15px;
}

.input-wrapper i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #bdc3c7;
    font-size: 1rem;
}

.input-wrapper input {
    width: 100%;
    padding: 14px 15px 14px 45px; /* Leave space for the icon */
    border: 2px solid #f0f3f5;
    border-radius: 12px;
    box-sizing: border-box;
    font-size: 0.95rem;
    transition: 0.3s;
}

.input-wrapper input:focus {
    border-color: #2ecc71;
    background: #fff;
    outline: none;
    box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1);
}

/* Error Message Polish */
.error-msg {
    background: #fff5f5;
    color: #e74c3c;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 0.85rem;
    border-left: 4px solid #e74c3c;
}
</style>



<script>
    const overlay = document.getElementById('authOverlay');
    const loginForm = document.getElementById('loginForm');
    const registerContainer = document.getElementById('registerContainer');
    const registerIframe = document.getElementById('registerIframe');
    const authCard = document.getElementById('authCard');

function showAuth(type) {
    overlay.style.display = 'flex';
    
    // Reset opacity for transition
    loginForm.style.opacity = "0";
    registerContainer.style.opacity = "0";

    if(type === 'login') {
        authCard.style.maxWidth = "400px";
        loginForm.style.display = 'block';
        registerContainer.style.display = 'none';
        setTimeout(() => loginForm.style.opacity = "1", 50); // Small delay for CSS transition
    } else {
        authCard.style.maxWidth = "600px"; // Slightly wider for your new 2-column register grid
        loginForm.style.display = 'none';
        registerContainer.style.display = 'block';
        
        if(!registerIframe.src.endsWith("auth/register.php")) {
            registerIframe.src = "auth/register.php";
        }
        setTimeout(() => registerContainer.style.opacity = "1", 50);
    }
}

    function closeAuth() {
        overlay.style.display = 'none';
    }

    // Close modal if clicking outside the card
    window.onclick = function(event) {
        if (event.target == overlay) closeAuth();
    }



function toggleAboutText() {
    const moreText = document.getElementById("moreAboutText");
    const btn = document.getElementById("aboutBtn");
    const currentLang = localStorage.getItem('preferredLang') || 'en';

    if (moreText.style.display === "none") {
        moreText.style.display = "block";
        btn.textContent = (currentLang === 'en') ? "Show Less" : "Afficher moins";
    } else {
        moreText.style.display = "none";
        btn.textContent = (currentLang === 'en') ? "Learn More About Us" : "En savoir plus sur nous";
    }
}


function toggleFaq(element) {
    const item = element.parentElement;
    
    // Close other open FAQ items (optional - remove if you want multiple open)
    document.querySelectorAll('.faq-item').forEach(otherItem => {
        if (otherItem !== item) {
            otherItem.classList.remove('active');
        }
    });

    // Toggle the clicked item
    item.classList.toggle('active');
}



// Place this at the bottom of your page
// document.addEventListener('contextmenu', function(e) {
//     e.preventDefault();
// }, false);


</script>
<?php 
// Show login automatically if there is an error
if($error) {
    echo "<script>showAuth('login');</script>";
}
include 'includes/footer.php'; 
?>





<script src="/lms_tech/lang.js"></script>

</body>
</html>