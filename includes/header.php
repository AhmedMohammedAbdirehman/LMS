<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Iftiin - AI & Technology Institute</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    

    <style>
    :root {
        --primary-bg: #0a1931;
        --accent-orange: #f39c12;
        --accent-green: #2ecc71;
        --text-white: #ffffff;
        --nav-height: 80px;
    }

    body { margin: 0; font-family: 'Segoe UI', Tahoma, sans-serif; background: #f4f7f6; overflow-x: hidden; }

    /* Navbar Styling */
    nav {
        background: var(--primary-bg);
        height: var(--nav-height);
        padding: 0 5%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 2px solid #14284b;
    }

    .logo-container { display: flex; align-items: center; height: 100%; }
    .logo-box {
        height: 100%;
        display: flex;
        align-items: center;
        padding: 0 15px;
        background: rgba(255,255,255,0.05);
        border-left: 4px solid var(--accent-green);
    }
    .logo-box img { height: 50px; border-radius: 4px; }

    .nav-links { display: flex; list-style: none; gap: 30px; margin: 0; padding: 0; align-items: center; }
    .nav-links a { 
        color: var(--text-white); text-decoration: none; font-weight: 600; 
        font-size: 1rem; transition: 0.3s; display: flex; align-items: center; gap: 5px;
    }
    .nav-links a:hover, .nav-links a.active { color: var(--accent-orange); }

    .auth-buttons { display: flex; gap: 12px; }
    .btn-auth { padding: 8px 20px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 0.9rem; transition: 0.3s; }
    .btn-login { border: 1px solid var(--accent-green); color: var(--text-white); }
    .btn-register { background: var(--accent-green); color: var(--primary-bg); }

    /* --- Hero Section - Fixed Padding --- */
    .hero-section {
        padding: 40px 5%; /* Reduced from 80px to 40px */
        background: #ffffff;
        min-height: 60vh;
        display: flex;
        align-items: center;
    }
    .hero-container { display: flex; align-items: center; gap: 40px; max-width: 1200px; margin: 0 auto; }
    .hero-content { flex: 1; }
    .hero-content h1 { font-size: 3rem; color: var(--primary-bg); line-height: 1.2; margin: 15px 0; }
    .hero-image { flex: 1; }
    .hero-image img { width: 100%; border-radius: 15px; box-shadow: 10px 10px 30px rgba(0,0,0,0.1); }

    /* --- Unified Mobile Responsive Logic --- */
    .mobile-toggle { display: none; color: white; font-size: 1.8rem; cursor: pointer; z-index: 1100; }

    @media (max-width: 992px) {
        .mobile-toggle { display: block; }
        .nav-links {
            position: fixed; top: 0; right: -100%; width: 280px; height: 100vh;
            background: var(--primary-bg); flex-direction: column; justify-content: center;
            transition: 0.4s ease; z-index: 1050; box-shadow: -5px 0 15px rgba(0,0,0,0.3);
        }
        .nav-links.active { right: 0; }
        .auth-buttons { display: none; }
        
        .hero-container { flex-direction: column; text-align: center; }
        .hero-content h1 { font-size: 2.2rem; }
    }

    /* Desktop: Hide the mobile version of buttons */
.mobile-auth {
    display: none;
    width: 100%;
    padding: 20px;
}

.mobile-btn-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
    width: 100%;
}

@media (max-width: 900px) {
    /* Hide the horizontal header buttons */
    .desktop-auth {
        display: none;
    }

    /* Show the vertical menu buttons */
    .mobile-auth {
        display: block;
    }

    .nav-links {
        position: fixed;
        top: 0;
        right: -100%;
        width: 280px;
        height: 100vh;
        background: var(--primary-bg);
        flex-direction: column;
        justify-content: flex-start; /* Start from top */
        padding-top: 100px; /* Space for the logo area */
        transition: 0.4s ease;
        z-index: 1050;
    }

    .nav-links.active {
        right: 0;
    }
}
.lang-select {
    background: transparent;
    color: white;
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 4px;
    padding: 10px;
    margin-right: 10px;
    cursor: pointer;
    font-size: 0.85rem;
    outline: none;
    width: 100px;
}

.lang-select option {
    background: #1a0b45; /* Matches your header color */
    color: white;
}
/* Adjustments for Mobile (below 692px or your breakpoint) */
@media (max-width: 692px) {
    .lang-control {
        margin-left: 0;
        position: absolute;
        right: 60px; /* Position it to the left of the hamburger icon */
        top: 20px;
    }

    /* Make sure desktop-auth doesn't hide the lang-select anymore */
    .desktop-auth {
        display: none;
    }
}
</style>
</head>
<body>


<nav>
    <div class="logo-container">
        <div class="logo-box">
            <img src="images/logo.jpg" alt="Logo">
            <div style="margin-left: 10px;">
                <div style="color: white; font-weight: 800; font-size: 1.1rem;">TECH IFTIIN</div>
            </div>
        </div>
    </div>

    <ul class="nav-links" id="navLinks">
        <li><a href="#" class="active" data-lang="nav_home">Home</a></li>
        <li><a href="#about" data-lang="nav_about">About</a></li>
        <li><a href="#courses" data-lang="nav_courses">Courses</a></li>
        <li><a href="#services" data-lang="nav_services">Services</a></li>
        <li><a href="#contact" data-lang="nav_contact">Contact</a></li>
        
        <li class="mobile-auth">
            <div class="mobile-btn-container">
        <select onchange="changeLanguage(this.value)" class="lang-select lang-switcher">
            <option value="en">ENG</option>
            <option value="fr">FREN</option>
        </select>
                <a href="javascript:void(0)" onclick="showAuth('login')" class="btn-auth btn-login" data-lang="btn_login">Login</a>
                <a href="javascript:void(0)" onclick="showAuth('register')" class="btn-auth btn-register" data-lang="btn_register">Register</a>
            </div>
        </li>
    </ul>



    <div class="auth-buttons desktop-auth">
        <!-- Language Switcher (Recommended) -->
        <select onchange="changeLanguage(this.value)" class="lang-select lang-switcher">
            <option value="en">ENG</option>
            <option value="fr">FREN</option>
        </select>
        <a href="javascript:void(0)" onclick="showAuth('login')" class="btn-auth btn-login" data-lang="btn_login">Login</a>
        <a href="javascript:void(0)" onclick="showAuth('register')" class="btn-auth btn-register" data-lang="btn_register">Register</a>
    </div>

    <div class="mobile-toggle" onclick="toggleMenu()">
        <i class="fa-solid fa-bars" id="menuIcon"></i>
    </div>
</nav>

<script>
    function toggleMenu() {
        const navLinks = document.getElementById('navLinks');
        const menuIcon = document.getElementById('menuIcon');
        
        navLinks.classList.toggle('active');
        
        // Switch between hamburger and "X" close icon
        if (navLinks.classList.contains('active')) {
            menuIcon.classList.remove('fa-bars');
            menuIcon.classList.add('fa-xmark');
        } else {
            menuIcon.classList.remove('fa-xmark');
            menuIcon.classList.add('fa-bars');
        }
    }

    // Close menu when a link is clicked
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            document.getElementById('navLinks').classList.remove('active');
            document.getElementById('menuIcon').classList.add('fa-bars');
        });
    });
</script>



<script src="/lms_tech/lang.js"></script>

</body>
</html>