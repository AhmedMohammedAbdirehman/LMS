

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Techiftiin AI and Technology</title>
    <style>
        /* Footer Styling */
.main-footer {
    background: #0f0529; /* Slightly darker than services for depth */
    color: white;
    padding: 80px 5% 30px;
    margin-top: 0; /* Flows naturally from contact section */
}

.footer-container {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1.5fr;
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding-bottom: 50px;
}

.footer-logo { font-size: 1.5rem; margin-bottom: 20px; color: #fff; }

.footer-brand p { color: #bdc3c7; font-size: 0.95rem; line-height: 1.6; margin-bottom: 25px; }

.footer-socials { display: flex; gap: 15px; }

.footer-socials a {
    width: 40px; height: 40px; background: rgba(255,255,255,0.05);
    display: flex; align-items: center; justify-content: center;
    border-radius: 50%; color: #fff; transition: 0.3s; text-decoration: none;
}

.footer-socials a:hover { background: var(--accent-green); transform: translateY(-3px); }

.footer-links h4, .footer-contact h4 { color: var(--accent-green); margin-bottom: 25px; font-size: 1.1rem; }

.footer-links ul { list-style: none; padding: 0; }

.footer-links ul li { margin-bottom: 12px; }

.footer-links ul li a { color: #bdc3c7; text-decoration: none; font-size: 0.9rem; transition: 0.3s; }

.footer-links ul li a:hover { color: var(--accent-green); padding-left: 5px; }

.footer-contact p { color: #bdc3c7; font-size: 0.9rem; margin-bottom: 15px; display: flex; gap: 10px; }

.footer-contact i { color: var(--accent-green); margin-top: 3px; }

.footer-bottom { text-align: center; padding-top: 30px; color: #7f8c8d; font-size: 0.85rem; }

/* Floating WhatsApp Button */
.whatsapp-float {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #25d366;
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    z-index: 999;
    transition: 0.3s;
    text-decoration: none;
}

.whatsapp-float:hover {
    transform: scale(1.1) rotate(10deg);
    background: #20ba5a;
}

.whatsapp-float .tooltip {
    position: absolute;
    right: 75px;
    background: #333;
    color: white;
    padding: 5px 15px;
    border-radius: 5px;
    font-size: 0.8rem;
    opacity: 0;
    transition: 0.3s;
    pointer-events: none;
    white-space: nowrap;
}

.whatsapp-float:hover .tooltip { opacity: 1; }

/* Responsive Footer */
@media (max-width: 992px) {
    .footer-container { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 576px) {
    .footer-container { grid-template-columns: 1fr; }
}

/* Site Directory Section */
.site-directory {
    background: #0a041a; /* Darker navy to separate from the previous section */
    padding: 80px 5%;
    color: #fff;
    border-top: 1px solid rgba(255,255,255,0.05);
}

.directory-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1.2fr 1fr 1fr 1.2fr;
    gap: 40px;
}

.directory-col h4 {
    color: #fff;
    font-size: 1.1rem;
    margin-bottom: 25px;
    position: relative;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.brand-col p {
    color: #bdc3c7;
    font-size: 0.9rem;
    line-height: 1.8;
    margin-bottom: 20px;
}

.directory-list {
    list-style: none;
    padding: 0;
}

.directory-list li {
    color: #bdc3c7;
    font-size: 0.9rem;
    margin-bottom: 12px;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.directory-list li:hover {
    color: var(--accent-green);
}

/* Badges Style from Image */
.badge {
    font-size: 0.65rem;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: bold;
    margin-left: 10px;
}

.badge.popular {
    background: rgba(46, 204, 113, 0.2);
    color: #2ecc71;
    border: 1px solid #2ecc71;
}

.badge.new {
    background: rgba(52, 152, 219, 0.2);
    color: #3498db;
    border: 1px solid #3498db;
}

.contact-list li {
    justify-content: flex-start;
    gap: 12px;
}

.contact-list i {
    color: var(--accent-orange);
    width: 20px;
}

.mt-20 { margin-top: 30px; }

/* Responsive Grid */
@media (max-width: 992px) {
    .directory-container {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 576px) {
    .directory-container {
        grid-template-columns: 1fr;
    }
}

.support-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(26, 11, 69, 0.9); /* Tech Iftiin Dark Blue with alpha */
    backdrop-filter: blur(8px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.support-card {
    background: #fff;
    width: 90%;
    max-width: 800px;
    max-height: 85vh;
    border-radius: 20px;
    position: relative;
    padding: 40px;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

.support-header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #f4f7f6;
    padding-bottom: 20px;
}

.support-header i {
    font-size: 3rem;
    color: #2ecc71; /* Your brand green */
    margin-bottom: 15px;
}

.support-body h5 {
    color: #1a0b45;
    margin: 20px 0 10px;
    font-size: 1.1rem;
}

.support-body p {
    color: #7f8c8d;
    line-height: 1.6;
    margin-bottom: 15px;
}

.close-support {
    position: absolute;
    top: 20px; right: 20px;
    font-size: 2rem;
    cursor: pointer;
    color: #bdc3c7;
    transition: 0.3s;
}

.close-support:hover { color: #e74c3c; }

.policy-item {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f2f1;
}

.policy-item:last-child {
    border-bottom: none;
}

.policy-item h5 {
    color: #2ecc71; /* Use your green for section numbers/titles */
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 1.05rem;
}

.policy-item p {
    font-size: 0.95rem;
    color: #5d6d6e;
    line-height: 1.7;
}
    </style>
</head>
<body>





<section class="site-directory">
    <div class="directory-container">
        <div class="directory-col brand-col">
            <h2 class="footer-logo">Tech <span class="highlight">Iftiin</span></h2>
            <p data-lang="dir_about_p1">Tech Iftiin Institute is the leading specialized tech bootcamp in Djibouti. We are committed to providing cutting-edge AI and software education to empower the next generation of digital leaders.</p>
            <p data-lang="dir_about_p2">Join our transformative courses and unlock your potential in the exciting world of technology.</p>
        </div>

        <div class="directory-col">
            <h4 data-lang="dir_h_courses">Course Categories</h4>
           <ul class="directory-list">
                <li><span data-lang="cat_mern">Full Stack (MERN)</span> <span class="badge popular" data-lang="badge_pop">POPULAR</span></li>
                <li><span data-lang="cat_ai">Artificial Intelligence</span> <span class="badge popular" data-lang="badge_pop">POPULAR</span></li>
                <li><span data-lang="cat_cyber">Cyber Security</span> <span class="badge new" data-lang="badge_new">NEW</span></li>
                <li><span data-lang="cat_py">Python Programming</span> <span class="badge popular" data-lang="badge_pop">POPULAR</span></li>
                <li><span data-lang="cat_mobile">Mobile App Dev</span> <span class="badge new" data-lang="badge_new">NEW</span></li>
                <li><span data-lang="cat_marketing">Digital Marketing</span> <span class="badge popular" data-lang="badge_pop">POPULAR</span></li>
                <li><span data-lang="cat_prompt">Prompt Engineering</span> <span class="badge new" data-lang="badge_new">NEW</span></li>
                <li><span data-lang="cat_video">Video Editing</span> <span class="badge new" data-lang="badge_new">NEW</span></li>
            </ul>
        </div>

        <div class="directory-col">
          <h4 data-lang="dir_h_services">Our Services</h4>
            <ul class="directory-list">
                <li data-lang="serv_soft">Software Development</li>
                <li data-lang="serv_web">Web Development</li>
                <li data-lang="serv_mob">Mobile App Development</li>
                <li data-lang="serv_audit">Cybersecurity Audits</li>
                <li data-lang="serv_cons">IT Consultations</li>
                <li data-lang="serv_corp">Corporate Training</li>
                <li data-lang="serv_uiux">UI/UX Design</li>
                <li data-lang="serv_brand">Digital Branding</li>
            </ul>
        </div>

        <div class="directory-col contact-col">
          <h4 data-lang="dir_h_contact">Contact Us</h4>
            <ul class="directory-list contact-list">
                <li><i class="fas fa-envelope"></i> info@techiftiin.com</li>
                <li><i class="fas fa-phone"></i> +253 77 03 54 85</li>
                <li><i class="fas fa-phone"></i> +253 77 26 13 42</li>
                <li><i class="fas fa-location-dot"></i> <span>Bld de Gaulle,at the junction of Route Siesta and Bld de Gaulle, Building, 238</span></li>
            </ul>
          <h4 class="mt-20" data-lang="dir_h_company">Company</h4>
            <ul class="directory-list">
                <li data-lang="link_about">About Us</li>
                <li data-lang="link_admissions">Admissions</li>
                <li data-lang="link_scholarships">Scholarships</li>
                <li data-lang="link_terms">Terms of Service</li>
            </ul>        </div>
    </div>
</section>




<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <h4 class="footer-logo">Tech <span class="highlight">Iftiin</span></h4>
            <p data-lang="footer_tagline">Djibouti's premier institute for Artificial Intelligence and cutting-edge technology. Shaping the innovators of tomorrow.</p>
            <div class="footer-socials">
                <a href="https://www.linkedin.com/company/tech-iftiin-institute-of-ai-and-technology" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                <a href="https://www.instagram.com/tech.iftiiin/" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://www.facebook.com/profile.php?id=100064930471229" target="_blank"><i class="fab fa-facebook-f"></i></a>
            </div>
        </div>

        <div class="footer-links">
        <h4 data-lang="footer_h_links">Quick Links</h4>
            <ul>
                <li><a href="#about" data-lang="link_about">About Us</a></li>
                <li><a href="#courses" data-lang="link_courses">Our Courses</a></li>
                <li><a href="#services" data-lang="link_services">Services</a></li>
                <li><a href="#faq" data-lang="link_faq">FAQs</a></li>
            </ul>
        </div>

        <div class="footer-links">
        <h4 data-lang="footer_h_support">Support</h4>
            <ul>
                <li><a href="javascript:void(0)" onclick="openSupport('privacy')" data-lang="link_privacy">Privacy Policy</a></li>
            <li><a href="javascript:void(0)" onclick="openSupport('terms')" data-lang="link_terms">Terms of Service</a></li>
                <li><a href="#contact" data-lang="link_student_support">Student Support</a></li>
            </ul>
        </div>

        <div class="footer-contact">
         <h4 data-lang="dir_h_contact">Get in Touch</h4>
            <p><i class="fas fa-phone"></i> +253 77 03 54 85</p>
            <p><i class="fas fa-envelope"></i> info@techiftiin.com</p>
            <p><i class="fas fa-map-marker-alt"></i> <span>Bld de Gaulle,at the junction of Route Siesta and Bld de Gaulle, Building, 238</span></p>
        </div>
    </div>
    
<div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <span data-lang="footer_rights">Tech Iftiin Institute of AI & Technology. All rights reserved.</span></p>
    </div>
</footer>

<a href="https://wa.me/25377261342" class="whatsapp-float" target="_blank" aria-label="Chat on WhatsApp">
    <i class="fab fa-whatsapp"></i>
    <span class="tooltip" data-lang="whatsapp_tooltip">Chat with us</span>
</a>



<!-- Privacy & Terms Modal -->
<div class="support-overlay" id="supportModal">
    <div class="support-card">
        <span class="close-support" onclick="closeSupport()">&times;</span>
        
        <div class="support-content" id="policyContent">
            <!-- Header -->
            <div class="support-header">
                <i class="fas fa-file-contract"></i>
                <h2 id="supportTitle" data-lang="support_default_title">Legal Information</h2>
            </div>

            <!-- Scrollable Body -->
            <div class="support-body">
                <div id="dynamicSupportText">
                    <!-- Content injected by JS -->
                </div>
            </div>

            <!-- Footer for Modal -->
            <div class="support-footer">
                <button class="btn-primary" onclick="closeSupport()" data-lang="btn_close">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
function openSupport(type) {
    // Get current language or default to English
    const currentLang = localStorage.getItem('preferredLang') || 'en';
    
    const modal = document.getElementById('supportModal');
    const titleElement = document.getElementById('supportTitle');
    const bodyElement = document.getElementById('dynamicSupportText');

    // Verify elements exist before trying to update them
    if (!modal || !titleElement || !bodyElement) {
        console.error("Support Modal elements not found in the DOM.");
        return;
    }

    // Add this inside the openSupport(type) function
    if(type === 'support') {
        titleElement.innerHTML = translations[currentLang]['title_support'];
        bodyElement.innerHTML = translations[currentLang]['support_content'];
    }

    if (type === 'privacy') {
        titleElement.innerHTML = translations[currentLang]['title_privacy'];
        bodyElement.innerHTML = translations[currentLang]['privacy_content'];
    } else if (type === 'terms') {
        titleElement.innerHTML = translations[currentLang]['title_terms'];
        bodyElement.innerHTML = translations[currentLang]['terms_content'];
    }

    // Show the modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeSupport() {
    const modal = document.getElementById('supportModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
}

// Close modal if user clicks outside the white card
window.onclick = function(event) {
    const modal = document.getElementById('supportModal');
    if (event.target == modal) {
        closeSupport();
    }
}
</script>


<script src="/lms_tech/lang.js"></script>

    
</body>
</html>




