const translations = {
    en: {
        // header part 
        nav_home: "Home",
        nav_about: "About",
        nav_courses: "Courses",
        nav_services: "Services",
        nav_contact: "Contact",
        btn_login: "Login",
        btn_register: "Register",

        // register part 
        reg_title: "Student Registration",
        label_name: "Full Name",
        placeholder_name: "your name",
        label_email: "Email Address",
        placeholder_email: "email@example.com",
        label_phone: "Phone Number",
        label_gender: "Gender",
        opt_select: "Select",
        opt_male: "Male",
        opt_female: "Female",
        label_courses: "Select Your Courses (Multiple Allowed)",
        label_pass: "Create Password",
        label_confirm: "Repeat Password",
        pass_hint: "Use 8+ characters with uppercase, numbers, and symbols (@$!%).",
        btn_register_now: "Register Now",
        footer_text: "Already have an account?",
        footer_link: "Login here",

        // hero sect part 

        toast_success: "Account Created!",
        toast_msg: "Please login with your email and password.",
        hero_badge: "Djibouti's Tech Leader",
       hero_title_part1: "Shape Your Future with ",
        hero_highlight: "Artificial Intelligence",
        hero_desc: "Welcome to Tech Iftiin. We provide specialized training in AI and Information Technology to drive digital transformation in Djibouti and beyond.",
        hero_btn_start: "Get Started Now",
        hero_btn_courses: "Explore Courses",
        stat_students: "Students",
        stat_tutors: "Expert Tutors",
        cert_tag: "Certified Programs",


        // about sect 

        about_years: "Years of Excellence",
        about_badge: "About Tech Iftiin",
        about_title_part1: "Leading the Digital Transformation in ",
        about_highlight: "Djibouti",
        about_desc1: "At Tech Iftiin, we believe that technology is the key to the future. Our institute is dedicated to empowering the next generation of Djiboutian innovators through world-class training in Artificial Intelligence and Software Engineering.",
        feat_ai_title: "AI-Driven Curriculum",
        feat_ai_desc: "Our courses are designed around the latest industry trends in AI.",
        feat_mentor_title: "Expert Mentors",
        feat_mentor_desc: "Learn from professionals with years of real-world experience.",
        feat_proj_title: "Practical Projects",
        feat_proj_desc: "Don't just learn theory—build actual software applications.",
        about_vision: "Founded with the vision of bridging the digital divide, Tech Iftiin has grown into Djibouti's leading center for technical vocational training.",
        about_facility: "Our facility provides state-of-the-art labs where students engage in:",
        list_ai: "Artificial Intelligence AI",
        list_web: "Full-stack Web Development",
        list_prompt: "Prompt Engineering",
        list_cyber: "Cybersecurity Threat Analysis",
        list_video: "Video Editing",
        list_mobile: "Mobile App Creation with Flutter",
        list_marketing: "Digital Marketing",
        list_python: "Python Programming",
        about_foster: "We don't just teach code; we foster a community of problem-solvers ready to tackle the challenges of the 21st century.",
        about_btn_more: "Learn More About Us",


        // course sect 

        // Headers
        course_badge: "Our Programs",
        course_title: "Explore Our ",
        course_highlight: "Expert-Led",
        course_subtitle: "Master the most in-demand skills in the digital age with our hands-on training programs.",
        btn_enroll: "Enroll Now",
        pre_label: "Prerequisite",
        // Durations
        dur_1m: "1 Month", dur_2m: "2 Months", dur_3m: "3 Months", dur_4m: "4 Months",
        // Course Specifics
        course_ai_name: "Artificial Intelligence (AI)",
        course_ai_desc: "Dive deep into Neural Networks, Deep Learning, and Computer Vision. Build intelligent systems that can see, hear, and think.",
        pre_ai: "To join this course, you must have completed Python Programming or equivalent coding experience.",
        course_mern_name: "Full Stack Web (MERN)",
        course_mern_desc: "Master MySQL, MongoDB, Express, React, and Node.js. Become a professional developer capable of building complex web applications.",
        pre_mern: "Understanding of Basic Computer Skill is highly recommended.",
        course_prompt_name: "Prompt Engineering",
        course_prompt_desc: "Learn the art of communicating with LLMs like GPT-4. Optimize workflows and boost productivity using advanced AI prompting.",
        pre_prompt: "No prior coding experience required. Open to all beginners.",
        course_nlp_name: "Natural Language Processing",
        course_nlp_desc: "Understand how machines process human language. Build chatbots, sentiment analyzers, and translation tools.",
        pre_nlp: "Completion of Python Programming and basic Probability concepts.",
        course_python_name: "Python Programming",
        course_python_desc: "Master the world's most popular language. From basic syntax to automation and data analysis. The perfect starting point.",
        pre_none: "No prerequisites. Designed for absolute beginners.",
        course_mobile_name: "Mobile App Development",
        course_mobile_desc: "Create stunning cross-platform apps using Flutter and React Native. Deploy your apps to both Android and iOS stores.",
        pre_mobile: "Familiarity with Object-Oriented Programming (OOP) logic.",
        
        course_cyber_name: "Cyber Security",
        course_cyber_desc: "Protect systems from digital attacks. Learn ethical hacking, network security, and risk management strategies.",
        pre_cyber: "Basic knowledge of Computer Networking and Linux is helpful.",
        course_video_name: "Professional Video Editing",
        course_video_desc: "Master industry-standard tools like Adobe Premiere Pro and DaVinci Resolve. Learn storytelling, color grading, and motion graphics.",
        pre_video: "A computer with at least 8GB RAM is recommended for software performance.",
        
        course_marketing_name: "Digital Marketing & SEO",
        course_marketing_desc: "Grow businesses with social media strategies, Google Ads, and SEO. Learn to analyze data and create high-converting campaigns.",
        pre_marketing: "Basic understanding of social media platforms (Facebook, Instagram, etc.).",



        // service sect 

        service_badge: "Expert Solutions",
        service_title: "Professional ",
        service_highlight: "Tech Services",
        service_subtitle: "Beyond training, we provide end-to-end digital solutions to help your business thrive in the modern era.",
        
        // Software Dev
        serv_dev_name: "Custom Software Development",
        serv_dev_desc: "From POS systems to complex ERP platforms, we build scalable software tailored to your specific business needs.",
        serv_dev_list1: "Web Applications",
        serv_dev_list2: "Mobile App Solutions",
        
        // Training
        serv_train_name: "Corporate Tech Training",
        serv_train_desc: "Empower your team with on-site or remote workshops in Cybersecurity, AI tools, and Digital Productivity.",
        serv_train_list1: "Staff Upskilling",
        serv_train_list2: "Digital Transformation",
        
        // Security
        serv_sec_name: "Security Consulting",
        serv_sec_desc: "We analyze your digital infrastructure to identify vulnerabilities and implement robust security protocols to protect your data.",
        serv_sec_list1: "Threat Assessment",
        serv_sec_list2: "Data Encryption",
        
        // UI/UX
        serv_ui_name: "UI/UX & Branding",
        serv_ui_desc: "Creating intuitive and beautiful digital experiences that resonate with your users and strengthen your brand identity.",
        serv_ui_list1: "Interactive Prototypes",
        serv_ui_list2: "Modern Rebranding",



        // FAQ part 

        faq_badge: "Common Questions",
        faq_title: "Frequently Asked ",
        faq_highlight: "Questions",
        faq_subtitle: "Everything you need to know about our admissions, courses, and certifications.",
        
        faq_q1: "What are the requirements to join the AI or MERN courses?",
        faq_a1: "For most courses, basic computer literacy is enough. For advanced tracks like AI and NLP, a basic understanding of mathematics and logic is helpful. We provide all the necessary software and tools during the training.",
        
        faq_q2: "Will I receive a certificate after completing the course?",
        faq_a2: "Yes! Upon successful completion of the course and your final project, you will receive a professional certificate from Tech Iftiin Institute, which is recognized by top employers in Djibouti.",
        
        faq_q3: "Can I pay the tuition fees in installments?",
        faq_a3: "Absolutely. We offer flexible payment plans to make our courses accessible. You can choose to pay monthly or in two installments throughout the duration of your program.",
        
        faq_q4: "Do you offer job placement assistance?",
        faq_a4: "Yes. We have a dedicated 'Job Connecting Platform' specifically for our students. We help you build your LinkedIn profile, prepare your CV, and connect you with local tech companies in Djibouti.",


        // service sect 
        stat_students: "Students Trained",
        stat_instructors: "Expert Instructors",
        stat_projects: "Projects Completed",
        stat_hiring: "Hiring Rate %",
        
        port_badge: "Our Impact",
        port_title: "What Our ",
        port_highlight: "Experts & Students Build",
        port_subtitle: "Real-world applications and platforms powering businesses in Djibouti.",
        
        cat_design: "Web Design & Branding",
        cat_mern: "MERN Stack / POS",
        cat_logic: "Inventory Logic",
        cat_social: "Social Impact",
        cat_event: "Event Platform",
        cat_logistics: "Logistics Tech",
        
        proj_urban_name: "Urban Beach Restaurant",
        proj_urban_desc: "Professional promotional website with digital menus and integrated booking features.",
        proj_pos_name: "Advanced Restaurant POS",
        proj_pos_desc: "A role-based Point of Sale system with real-time sales tracking and cashier dashboards.",
        proj_stock_name: "Smart Stock Manager",
        proj_stock_desc: "Enterprise-level inventory control with automated low-stock alerts and supplier management.",
        proj_job_name: "Djibouti Job Connect",
        proj_job_desc: "A multi-actor platform connecting local youth with employment opportunities across the country.",
        proj_tour_name: "Tour de Djibouti Platform",
        proj_tour_desc: "Integrated digital platform for race registration, rider tracking, and sponsor management.",
        proj_truck_name: "TruckTrack System",
        proj_truck_desc: "Fleet tracking software with driver documentation and real-time approval workflows.",



        // Whey Us sec 
        choice_badge: "Our Impact",
        choice_title: "Why Students & Clients ",
        choice_highlight: "Choose Us",
        choice_subtitle: "Learn from industry experts and join a community of successful graduates leading the tech scene in Djibouti.",
        
        role_ceo: "Founder & CEO",
        role_lead: "Lead Instructor",
        desc_ceo: "Visionary leader driving tech innovation at Tech Iftiin.",
        desc_ahmed_m: "Full Stack Web Development & Cyber Security",
        desc_elias: "Artificial Intelligence & Prompt Engineering",
        desc_hassan: "Python Programming & Data Science",
        
        success_label: "Success Stories (Our Graduates)",
        grad_tag: "Full Stack & AI Graduate",
        status_current: "Current Student - AI & NLP",
        
        test_ali: '"Tech Iftiin changed my career. The hands-on training in AI and Full Stack gave me the confidence to build real-world applications."',
        test_bilan: '"The mentorship here is unmatched. Learning Python and MERN stack under experts helped me secure a great position in the industry."',
        test_omar: '"The MERN stack program gave me the exact skills local companies are looking for. I am now working on enterprise-level web solutions."',
        test_khalid: '"Learning Prompt Engineering and NLP at Tech Iftiin is opening my eyes to the power of AI. It\'s the best tech investment I\'ve made."',


        // contact sec 
        cont_badge: "Get In Touch",
        cont_title: "Visit Us or ",
        cont_highlight: "Reach Out",
        cont_subtitle: "Have questions about our programs or services? Our team is ready to assist you.",
        cont_form_title: "Send us a Message",
        cont_success: "Thank you! Your message has been sent to Tech Iftiin. We will contact you soon.",
        btn_send: "Send Message",
        
        // Labels
        label_loc: "Location",
        label_call: "Call Us",
        label_email: "Email",
        
        // Placeholders
        ph_name: "Your Name",
        ph_email: "Your Email",
        ph_subject: "Subject",
        ph_msg: "Your Message",




        // login part 
        auth_title: "Portal Login",
        auth_subtitle: "Welcome back to Tech Iftiin",
        label_email_addr: "Email Address",
        label_password: "Password",
        ph_email_portal: "name@techiftiin.com",
        ph_password: "••••••••",
        btn_access: "Access Dashboard",
        new_student: "New student?",
        create_acc: "Create an account",



        // footer sec 

        dir_about_p1: "Tech Iftiin Institute is the leading specialized tech bootcamp in Djibouti. We are committed to providing cutting-edge AI and software education to empower the next generation of digital leaders.",
        dir_about_p2: "Join our transformative courses and unlock your potential in the exciting world of technology.",
        
        dir_h_courses: "Course Categories",
        cat_mern: "Full Stack (MERN)",
        cat_ai: "Artificial Intelligence",
        cat_cyber: "Cyber Security",
        cat_py: "Python Programming",
        cat_mobile: "Mobile App Dev",
        cat_marketing: "Digital Marketing",
        cat_prompt: "Prompt Engineering",
        cat_video: "Video Editing",
        badge_pop: "POPULAR",
        badge_new: "NEW",

        dir_h_services: "Our Services",
        serv_soft: "Software Development",
        serv_web: "Web Development",
        serv_mob: "Mobile App Development",
        serv_audit: "Cybersecurity Audits",
        serv_cons: "IT Consultations",
        serv_corp: "Corporate Training",
        serv_uiux: "UI/UX Design",
        serv_brand: "Digital Branding",

        dir_h_contact: "Contact Us",
        dir_loc: "Blvd du General de Gaulle, Djibouti",
        dir_h_company: "Company",
        link_about: "About Us",
        link_admissions: "Admissions",
        link_scholarships: "Scholarships",
        link_terms: "Terms of Service",
        link_privacy: "Privacy Policy",
        link_courses: "Our Courses",
        link_services: "Services",
        link_faq: "FAQs",
        link_student_support: "Student Support",

        footer_tagline: "Djibouti's premier institute for Artificial Intelligence and cutting-edge technology. Shaping the innovators of tomorrow.",
        footer_h_links: "Quick Links",
        footer_h_support: "Support",
        footer_rights: "Tech Iftiin Institute of AI & Technology. All rights reserved.",
        whatsapp_tooltip: "Chat with us",




        // privay sec 
        btn_close: "Close",
        title_privacy: "Privacy Policy",
        title_terms: "Terms of Service",
        
        // Privacy Content
       privacy_content: `
            <div class="policy-item">
                <h5>1. Information Collection</h5>
                <p>We collect personal identification information (Name, email address, phone number) when you register for our courses or fill out a contact form.</p>
            </div>
            <div class="policy-item">
                <h5>2. How We Use Your Data</h5>
                <p>Your data allows us to manage your student account, process your certifications, and send you important updates about your classes and the Djiboutian tech market.</p>
            </div>
            <div class="policy-item">
                <h5>3. Data Security</h5>
                <p>We implement a variety of security measures to maintain the safety of your personal information. Your data is never sold or traded to third parties.</p>
            </div>
            <div class="policy-item">
                <h5>4. Cookies</h5>
                <p>Our website uses cookies to enhance your experience and analyze site traffic to improve our teaching tools.</p>
            </div>
        `,
        
        // Terms Content
     terms_content: `
            <div class="policy-item">
                <h5>1. Admission & Enrollment</h5>
                <p>Admission to Tech Iftiin is based on merit and seat availability. Enrollment is confirmed only after the registration fee is processed.</p>
            </div>
            <div class="policy-item">
                <h5>2. Attendance Policy</h5>
                <p>To receive a certification, students must maintain at least an 85% attendance rate throughout the bootcamp duration.</p>
            </div>
            <div class="policy-item">
                <h5>3. Intellectual Property</h5>
                <p>While students own the code they write during projects, the curriculum, recorded lectures, and course materials remain the exclusive property of Tech Iftiin.</p>
            </div>
            <div class="policy-item">
                <h5>4. Refund Policy</h5>
                <p>Withdrawal requests must be submitted in writing. Refunds are subject to the institute's administrative timeline as stated in your enrollment agreement.</p>
            </div>
            <div class="policy-item">
                <h5>5. Code of Conduct</h5>
                <p>Tech Iftiin maintains a zero-tolerance policy for harassment, academic dishonesty, or disruptive behavior in both physical and digital classrooms.</p>
            </div>
        `,

        // admin dashboard 

        "portal_title": "Admin Portal",
        "dashboard": "Dashboard",
        "manage_users": "Manage Users",
        "certificates": "Certificates",
        "manage_courses": "Manage Courses",
        "activity_logs": "Activity Logs",
        "messages": "Messages",
        "setting": "Setting",
        "logout": "Logout",
        "search_placeholder": "Search...",
        "admin_role": "Administrator",

       // Dashboard Cards
        "teachers": "Teachers",
        "students": "Students",
        "courses": "Courses",
        "pending_certs": "Pending Certs",
        "new_inquiries": "New Inquiries",

        // Quick Action Section
        "quick_action": "Quick Action",
        "add_member_desc": "Add members to the Tech Iftiin family.",
        "register_member": "+ Register Member",
        "system_analytics": "System Analytics",
        "gender_distribution": "Student Gender Distribution",
        "course_enrollment": "Enrollment by Course",
        "male": "Male",
        "female": "Female",
        "total_students": "Total Students",

        "manage_global_courses": "Manage Global Courses",
        "add_new_course": "Add New Course",
        "course_title": "Course Title",
        "description": "Description",
        "assign_teacher": "Assign Teacher",
        "title_placeholder": "e.g. Full Stack",
        "desc_placeholder": "Overview e.g. Popular",
        "unassigned_option": "-- Leave Unassigned --",
        "create_course": "Create Course",

        // --- Course Cards ---
        "course_module": "Course Module",
        "unassigned": "Unassigned",
        "change_teacher": "Change Teacher",
        "materials": "Materials",
        "welcome": "Welcome",
        // --- Delete Modal ---
        "are_you_sure": "Are you sure?",
        "delete_warning": "This will permanently delete the course and all associated materials. This action cannot be undone.",
        "cancel": "Cancel",
        "yes_delete": "Yes, Delete It",

        // --- Add User Page ---
        "back_to_list": "Back to User List",
        "create_new_user": "Create New User",
        "password_requirement": "Password must include Uppercase, Lowercase, Number, and Special Character (> 8 chars).",
        "full_name": "Full Name",
        "enter_name_placeholder": "Enter Your Name",
        "email_address": "Email Address",
        "phone_number": "Phone Number",
        "system_role": "System Role",
        "role_teacher": "Teacher (Instructor)",
        "role_manager": "Manager (Coordinator)",
        "role_admin": "Administrator",
        "password": "Password",
        "repeat_password": "Repeat Password",
        "gender": "Gender",
        "choose_gender": "Choose Gender",
        "male": "Male",
        "female": "Female",
        "create_account_btn": "Create User Account",
        "user_success": "User account created successfully!",
        "back_to_dashboard": "Back to Dashboard",
        "user_management": "User Management",
        "staff_members": "Staff Members",
        "enrolled_students": "Enrolled Students",
        "search_placeholder": "Search name or email...",
        "btn_search": "Search",
        "th_name": "Name",
        "th_email": "Email",
        "th_phone": "Phone",
        "th_gender": "Gender",
        "th_role": "Role",
        "th_status": "Status",
        "th_actions": "Actions",
        "male": "Male",
        "female": "Female",
        "role_admin": "Admin",
        "role_teacher": "Teacher",
        "role_manager": "Manager",
        "active_status": "Active",
        "inactive_status": "Inactive",
        "btn_edit": "Edit",
        "btn_toggle": "Toggle",
        "btn_approve": "Approve",
        "btn_disable": "Disable",
       "system_logs": "System Logs",
        "filter_date": "Filter by Date",
        "filter_role": "Filter by Role",
        "all_roles": "All Roles",
        "btn_reset": "Reset",
        "th_user_role": "User & Role",
        "th_action": "Action",
        "th_details": "Details",
        "th_time": "Time",
        "no_logs": "No activity logs found for this search.",
        "role_student": "Student",
        "role_na": "N/A",
        "cert_approvals": "Certificate Approvals",
        "incoming_requests": "Incoming Requests",
        "th_student": "Student",
        "th_module": "Module",
        "th_instructor": "Instructor",
        "th_actions": "Action",
        "status_approved": "Approved",
        "btn_approve": "Approve",
        "btn_reject": "Reject",
        "no_requests": "No pending requests.",
        "showing_text": "Showing",
        "to_text": "to",
        "of_text": "of",
        "requests_text": "requests",
        "account_settings": "Account Settings",
        "password_label": "New Password (leave blank to keep current)",
        "btn_update_profile": "Update Profile",
        "profile_success": "Profile updated successfully!",
        "student_inquiries": "Student Inquiries",
        "new_messages_count": "New Messages",
        "btn_view_details": "View Details",
        "btn_reply_email": "Reply via Email",
        "no_messages": "No messages found.",
        "message_detail": "Message Detail",
        "label_from": "From:",
        "label_date": "Date:",
        "btn_close": "Close",

        "teacher_portal": "Teacher Portal",
        "submissions": "Submissions",
        "submissions_desc": "Review assignment answers.",
        "report_cards": "Report Cards",
        "report_cards_desc": "Generate student cards.",
        "btn_generate": "Generate",
        "my_assigned_courses": "My Assigned Courses",
        "curriculum": "Curriculum",
        "btn_manage_lessons": "Manage Lessons & Materials",
        "assessment": "Assessment",
        "assessment_desc": "View registered students, track attendance, and update final grades.",
        "btn_grade_students": "Grade Students",
        "class_list": "Class List",
        "class_list_desc": "View all enrolled students and their contact info.",
        "btn_view_student_list": "View Student List",
        "daily_log": "Daily Log",
        "daily_log_desc": "Record daily student presence and track attendance history.",
        "btn_take_attendance": "Take Attendance",
        "btn_export_pdf": "Export PDF Report",
        "no_courses_assigned": "No courses assigned yet.",
        "contact_admin_desc": "Please contact the Admin to get started.",
        "settings_subtitle": "Update your login credentials.",
        "btn_save_changes": "Save Changes",
        "btn_manage_small": "Manage",
        "no_students_registered": "No students registered.",
        "student_submissions_title": "Student Submissions",
        "student_submissions_desc": "Review and download the latest assignment answers from your students.",
        "btn_view_all_answers": "View All Answers",
        "label_course": "Course:",
        "label_instructor": "Instructor:",
        "th_student_name": "Student Name",
        "th_gender": "Gender",
        "th_status": "Status",
        "gender_male": "Male",
        "gender_female": "Female",
        "status_present": "Present",
        "status_absent": "Absent",
        "status_late": "Late",
        "report_generated_on": "Report Generated on:",
        "instructor_signature": "Instructor Signature",
        "admin_stamp": "Administration Stamp",

     "back_to_teacher_dashboard": "Back to Teacher Dashboard",
        "search_placeholder": "Search by name or course...",
        "total_students_label": "TOTAL STUDENTS",
        "grade_c_above_label": "GRADE C & ABOVE",
        "active_courses_label": "ACTIVE COURSES",
        "high_achievers_title": "High Achievers (Grade C & Above)",
        "high_achievers_desc": "Review and send eligible students for certification.",
        "select_course": "Select Course",
        "btn_preview_list": "Preview List",
        "btn_send_admin": "Send to Admin",
        "achievement_cards_title": "Student Achievement Cards",
        "achievement_cards_desc": "Generate and download official reports for students.",
        "th_student_details": "Student Details",
        "th_course_module": "Course Module",
        "th_action": "Action",
        "btn_view_report": "View Report",
        "no_students_found": "No enrolled students found for your courses.",

        "review_submissions_title": "Review Submissions",
        "back_to_dashboard": "Back to Dashboard",
        "total_label": "Total:",
        "submissions_count_label": "Submissions",
        "th_student_name": "Student Name",
        "th_assignment": "Assignment",
        "th_submitted_date": "Submitted Date",
        "th_action": "Action",
        "btn_view_pdf": "View PDF",
        "graded_label": "Graded",
        "btn_edit": "Edit",
        "btn_add_grade": "Add Grade",
        "no_submissions_found": "No submissions found.",


        "class_grade_report": "Class Grade Report",
        "label_course": "Course:",
        "generated_on": "Generated on:",
        "th_student_name": "Student Name",
        "th_email": "Email",
        "th_phone": "Phone",
        "th_total_score": "Total Score",
        "th_percentage": "Percentage",

        "manage_content_title": "Manage Course Content",
        "label_category": "Category",
        "opt_material": "Lesson Material",
        "opt_assignment": "Assignment",
        "opt_quiz": "Quiz",
        "opt_exam": "Mid/Final Exam",
        "label_content_type": "Content Type",
        "opt_pdf": "PDF Document",
        "opt_video": "Video (Link)",
        "opt_text": "Text Instructions",
        "label_part_number": "Part Number",
        "placeholder_part": "e.g. 1",
        "label_material_title": "Material Title",
        "placeholder_title": "e.g. Introduction to AI",
        "label_upload_file": "Upload File (PDF/ZIP) Max: 10MB",
        "label_paste_link": "Paste Link (URL)",
        "placeholder_url": "https://...",
        "btn_save_content": "Save Content",
        "section_1_title": "Section 1: Learning Materials",
        "section_2_title": "Section 2: Assignments & Exams",
        "part_label": "Part",
        "btn_view_text": "View Text",
        "btn_view_file": "View File",
        "no_materials": "No materials added yet.",
        "no_assessments": "No assignments or exams added yet.",


        "alert_success_bold": "Success!",
        "alert_success_msg": "The student's grade has been recorded and tracked successfully.",
        "grading_portal_title": "Grading Portal",
        "back_to_submissions": "Back to Submissions",
        "nav_dashboard": "Dashboard",
        "student_id_label": "Student ID:",
        "label_as_name": "ASSESSMENT NAME",
        "placeholder_as_name": "e.g. Mid Exam",
        "label_weight": "TOTAL WEIGHT",
        "placeholder_weight": "e.g. 20",
        "label_score": "SCORE",
        "placeholder_score": "e.g. 18",
        "btn_save_grade": "Confirm & Save Grade",

        "btn_print_report": "PRINT OFFICIAL REPORT CARD",
        "institute_motto": "Empowering the Future through Innovation",
        "label_student_caps": "STUDENT:",
        "label_course_caps": "COURSE:",
        "th_desc": "Assessment Description",
        "th_weight": "Weight (%)",
        "th_score": "Score Obtained",
        "cumulative_total": "CUMULATIVE TOTAL",
        "final_percentage": "Final Percentage",
        "overall_grade": "Overall Grade",
        "no_grades_recorded": "No Grades Recorded",
        "sig_instructor": "INSTRUCTOR SIGNATURE",
        "sig_registrar": "ACADEMIC REGISTRAR",
        "disclaimer_start": "This document is an official academic record of TechIftiin Institute. Issued on",


        "take_attendance_title": "Take Attendance",
        "label_select_date": "Select Date:",
        "th_gender": "Gender",
        "th_status": "Status",
        "opt_take_attendance": "-- take attendance --",
        "opt_present": "✅ Present",
        "opt_absent": "❌ Absent",
        "opt_late": "🕒 Late",
        "btn_submit_attendance": "Submit Attendance",
        "msg_submission_closed": "Submission Closed for Today",
        "chart_absenteeism_title": "High Absenteeism Rate (%)",
        "attendance_history_title": "Attendance History",
        "btn_filter": "Filter",
        "btn_clear": "Clear",
        "th_date": "Date",
        "btn_download_pdf": "Download PDF",
        "no_records_found": "No records found.",



        "label_registered_courses": "Your Registered Courses:",
        "portal_title": "My Learning Portal",
        "welcome_back": "Welcome back,",
        "btn_student_ai": "Student AI",
        "nav_logout": "Logout",
        "cert_label": "Certificate",
        "status_label": "Status:",
        "cert_none": "No Certificate Found",
        "btn_download_cert": "Download Certificate",
        "cert_pending": "Pending Approval",
        "cert_instruction": "Certificate will appear here after course completion",
        "academic_progress_title": "Academic Progress",
        "cumulative_grade_label": "Current Cumulative Grade",
        "th_assessment": "Assessment",
        "th_weight": "Weight %",
        "th_score_obtained": "Score Obtained",
        "total_raw_score": "TOTAL RAW SCORE",
        "no_grades_posted": "No grades posted yet.",
        "course_materials_title": "Course Materials",
        "tasks_exams_title": "Tasks & Exams",
        "btn_read_text": "Read Text",
        "btn_open_pdf": "Open PDF",
        "btn_submit_work": "Submit Work",
        "status_submitted": "Submitted",

     "cumulative_grade_label": "Current Cumulative Grade",
     "attendance_summary_title": "Attendance Summary",
        "label_total_rate": "Total Rate",
        "label_days_present": "Days Present",
        "logs_title": "Logs",
        "btn_clear": "Clear",
        "course_materials_title": "Course Materials",
        "btn_read_text": "Read Text",
        "btn_open_pdf": "Open PDF",
        "modal_content_details": "Content Details",
        "btn_close": "Close",
        "settings_title": "Account Settings",
        "settings_subtitle": "Update your email or set a secure new password.",
        "label_email": "Email Address",
        "label_new_password": "New Password",
        "placeholder_password_blank": "Leave blank to keep current",
        "crit_upper": "✖ Uppercase & Lowercase",
        "crit_number": "✖ At least one number",
        "crit_special": "✖ Special character (@$!%*#?&)",
        "label_confirm_password": "Confirm New Password",
        "placeholder_repeat_password": "Repeat new password",
        "btn_save_changes": "Save Changes",

       "submit_title": "Submit Work",
        "btn_back_dashboard": "Back to Dashboard",
        "label_upload_solution": "Upload your Solution (PDF Only)",
        "btn_upload_submit": "Upload & Submit PDF",


        "cert_presentation_text": "This Certificate is Proudly Presented to",
        "cert_completion_text": "For successfully completing the professional requirements for the course",
        "label_verification_id": "VERIFICATION ID:",
        "stamp_instruction": "Affix Official<br>Institute Stamp<br>Here",
        "label_academic_director": "Academic Director",
        "assign_teacher_title": "Assign Teacher",
        "label_selected_course": "Selected Course",
        "label_select_instructor": "Select an Instructor",
        "option_choose_list": "-- Choose from List --",
        "btn_confirm_assignment": "Confirm Assignment",
        "link_go_back": "Nevermind, go back",

        "manage_courses_title": "Manage Global Courses",
        "add_new_course": "Add New Course",
        "label_course_title": "Course Title",
        "label_description": "Description",
        "label_assign_teacher": "Assign Teacher",
        "option_unassigned": "-- Leave Unassigned --",
        "btn_add_course": "Add Course",
        "label_course_type": "Course",
        "status_unassigned": "Unassigned",
        "link_change_teacher": "Change Teacher",
        "label_materials": "Materials",
        "status_active": "● Active",
        "status_empty": "○ Empty",
        "btn_delete": "Delete",
        "delete_confirm_title": "Are you sure?",
        "delete_warning_text": "This will permanently delete the course",
        "delete_warning_subtext": "and all associated materials. This action cannot be undone.",
        "btn_cancel": "Cancel",
        "btn_confirm_delete": "Yes, Delete It",
    

        "portal_manager_title": "Manager Portal",
        "nav_dashboard": "Dashboard",
        "nav_users": "Users List",
        "nav_manage_courses": "Manage Courses",
        "nav_reports": "System Reports",
        "nav_settings": "Settings",
        "nav_logout": "Logout",
        "placeholder_search": "Search...",
        "role_coordinator": "Coordinator",
        "stat_teachers": "Teachers",
        "stat_students": "Students",
        "stat_courses": "Courses",
        "analytics_title": "System Analytics",
        "chart_gender_title": "Student Gender Distribution",
        "chart_enrollment_title": "Enrollment by Course",

        "btn_generate_pdf": "Generate PDF Report",
        "report_main_title": "System Analytics Report",
        "report_attendance_title": "Course Attendance Rates",
        "th_course_title": "COURSE TITLE",
        "th_engagement": "ENGAGEMENT",
        "report_at_risk_title": "At-Risk Students",
        "report_at_risk_sub": "Top 10 students with highest absences.",
        "label_absences": "Absences",
     
        "account_settings_title": "Account Settings",
        "label_full_name": "Full Name",
        "label_email_address": "Email Address",
        "label_new_password": "New Password (leave blank to keep current)",
        "placeholder_password": "********",
        "btn_update_profile": "Update Profile",
        "btn_back_dashboard": "Back to Dashboard",

        "user_management_title": "User Management",
        "tab_students": "Students",
        "tab_teachers": "Teachers",
        "th_name": "Name",
        "th_email": "Email",
        "th_phone": "Phone",
        "th_gender": "Gender",
        "th_status": "Status",
        "gender_male": "Male",
        "gender_female": "Female",
        "status_active_label": "Active",
        "status_pending_label": "Pending",
        "no_users_found": "No users found for this category:",
        "th_ip":"IP Address"











    },
    fr: {
        // header part 
        nav_home: "Accueil",
        nav_about: "À Propos",
        nav_courses: "Cours",
        nav_services: "Services",
        nav_contact: "Contact",
        btn_login: "Connexion",
        btn_register: "S'inscrire",

        // register part 
        reg_title: "Inscription de l'Étudiant",
        label_name: "Nom Complet",
        placeholder_name: "votre nom",
        label_email: "Adresse Email",
        placeholder_email: "email@exemple.com",
        label_phone: "Numéro de Téléphone",
        label_gender: "Genre",
        opt_select: "Sélectionner",
        opt_male: "Homme",
        opt_female: "Femme",
        label_courses: "Choisir vos cours (plusieurs autorisés)",
        label_pass: "Créer un mot de passe",
        label_confirm: "Répéter le mot de passe",
        pass_hint: "Utilisez 8+ caractères avec majuscules, chiffres et symboles (@$!%).",
        btn_register_now: "S'inscrire Maintenant",
        footer_text: "Vous avez déjà un compte ?",
        footer_link: "Connectez-vous ici",

        // hero sect part 
        toast_success: "Compte Créé !",
        toast_msg: "Veuillez vous connecter avec votre email et votre mot de passe.",
        hero_title_part1: "Façonnez votre avenir avec ",
        hero_highlight: "l'Intelligence Artificielle",
        hero_highlight: "l'Intelligence Artificielle",
        hero_desc: "Bienvenue chez Tech Iftiin. Nous proposons une formation spécialisée en IA et en informatique pour stimuler la transformation numérique à Djibouti et au-delà.",
        hero_btn_start: "Commencez Maintenant",
        hero_btn_courses: "Explorer les Cours",
        stat_students: "Étudiants",
        stat_tutors: "Tuteurs Experts",
        cert_tag: "Programmes Certifiés",

        // about secti 

        about_years: "Ans d'Excellence",
        about_badge: "À Propos de Tech Iftiin",
        about_title_part1: "Leader de la transformation numérique à ",
        about_highlight: "Djibouti",
        about_desc1: "Chez Tech Iftiin, nous croyons que la technologie est la clé de l'avenir. Notre institut est dédié à l'autonomisation de la prochaine génération d'innovateurs djiboutiens grâce à une formation de classe mondiale en intelligence artificielle et en génie logiciel.",
        feat_ai_title: "Curriculum Axé sur l'IA",
        feat_ai_desc: "Nos cours sont conçus autour des dernières tendances de l'industrie en IA.",
        feat_mentor_title: "Mentors Experts",
        feat_mentor_desc: "Apprenez auprès de professionnels ayant des années d'expérience réelle.",
        feat_proj_title: "Projets Pratiques",
        feat_proj_desc: "Ne vous contentez pas d'apprendre la théorie, créez de véritables applications logicielles.",
        about_vision: "Fondé avec la vision de combler le fossé numérique, Tech Iftiin est devenu le principal centre de formation professionnelle technique de Djibouti.",
        about_facility: "Notre établissement propose des laboratoires de pointe où les étudiants s'engagent dans :",
        list_ai: "Intelligence Artificielle IA",
        list_web: "Développement Web Full-stack",
        list_prompt: "Ingénierie de Prompt",
        list_cyber: "Analyse des menaces de cybersécurité",
        list_video: "Montage Vidéo",
        list_mobile: "Création d'applications mobiles avec Flutter",
        list_marketing: "Marketing Numérique",
        list_python: "Programmation Python",
        about_foster: "Nous n'enseignons pas seulement le code ; nous encourageons une communauté de résolveurs de problèmes prêts à relever les défis du 21e siècle.",
        about_btn_more: "En savoir plus sur nous",


        // course sect 

        // Headers
        course_badge: "Nos Programmes",
        course_title: "Explorez nos cours ",
        course_highlight: "dirigés par des experts",
        course_subtitle: "Maîtrisez les compétences les plus demandées à l'ère numérique grâce à nos programmes de formation pratique.",
        btn_enroll: "S'inscrire maintenant",
        pre_label: "Prérequis",
        // Durations
        dur_1m: "1 Mois", dur_2m: "2 Mois", dur_3m: "3 Mois", dur_4m: "4 Mois",
        // Course Specifics
        course_ai_name: "Intelligence Artificielle (IA)",
        course_ai_desc: "Plongez dans les réseaux neuronaux, l'apprentissage profond et la vision par ordinateur. Créez des systèmes intelligents.",
        pre_ai: "Pour rejoindre ce cours, vous devez avoir terminé la programmation Python ou une expérience équivalente.",
        course_mern_name: "Web Full Stack (MERN)",
        course_mern_desc: "Maîtrisez MySQL, MongoDB, Express, React et Node.js. Devenez un développeur professionnel capable de créer des applications complexes.",
        pre_mern: "Une compréhension des compétences informatiques de base est fortement recommandée.",
        course_prompt_name: "Ingénierie de Prompt",
        course_prompt_desc: "Apprenez l'art de communiquer avec des LLM comme GPT-4. Optimisez les flux de travail et boostez la productivité.",
        pre_prompt: "Aucune expérience préalable en codage n'est requise. Ouvert à tous les débutants.",
        course_nlp_name: "Traitement du langage naturel (NLP)",
        course_nlp_desc: "Comprenez comment les machines traitent le langage humain. Créez des chatbots et des outils de traduction.",
        pre_nlp: "Achèvement de la programmation Python et des concepts de base en probabilités.",
        course_python_name: "Programmation Python",
        course_python_desc: "Maîtrisez le langage le plus populaire au monde. De la syntaxe de base à l'automatisation. Le point de départ idéal.",
        pre_none: "Aucun prérequis. Conçu pour les débutants absolus.",
        course_mobile_name: "Développement d'Applications Mobiles",
        course_mobile_desc: "Créez de superbes applications multiplateformes avec Flutter et React Native. Déployez vos applications sur Android et iOS.",
        pre_mobile: "Connaissance de la logique de programmation orientée objet (POO).",
        
        course_cyber_name: "Cybersécurité",
        course_cyber_desc: "Protégez les systèmes contre les attaques numériques. Apprenez le piratage éthique, la sécurité réseau et la gestion des risques.",
        pre_cyber: "Une connaissance de base des réseaux informatiques et de Linux est utile.",
        course_video_name: "Montage Vidéo Professionnel",
        course_video_desc: "Maîtrisez les outils standards de l'industrie comme Adobe Premiere Pro et DaVinci Resolve. Apprenez le storytelling et l'étalonnage.",
        pre_video: "Un ordinateur avec au moins 8 Go de RAM est recommandé pour les performances du logiciel.",
        
        course_marketing_name: "Marketing Digital & SEO",
        course_marketing_desc: "Développez des entreprises avec des stratégies de médias sociaux, Google Ads et SEO. Apprenez à analyser les données.",
        pre_marketing: "Compréhension de base des plateformes de médias sociaux (Facebook, Instagram, etc.).",




    //  service sect 

    service_badge: "Solutions d'Experts",
        service_title: "Services ",
        service_highlight: "Technologiques Professionnels",
        service_subtitle: "Au-delà de la formation, nous fournissons des solutions numériques de bout en bout pour aider votre entreprise à prospérer.",
        
        // Software Dev
        serv_dev_name: "Développement de Logiciels sur Mesure",
        serv_dev_desc: "Des systèmes POS aux plateformes ERP complexes, nous créons des logiciels évolutifs adaptés à vos besoins.",
        serv_dev_list1: "Applications Web",
        serv_dev_list2: "Solutions d'Applications Mobiles",
        
        // Training
        serv_train_name: "Formation Technologique en Entreprise",
        serv_train_desc: "Renforcez votre équipe avec des ateliers sur site ou à distance en cybersécurité, outils d'IA et productivité numérique.",
        serv_train_list1: "Montée en Compétences du Personnel",
        serv_train_list2: "Transformation Numérique",
        
        // Security
        serv_sec_name: "Conseil en Sécurité",
        serv_sec_desc: "Nous analysons votre infrastructure pour identifier les vulnérabilités et mettre en œuvre des protocoles de sécurité robustes.",
        serv_sec_list1: "Évaluation des Menaces",
        serv_sec_list2: "Chiffrement des Données",
        
        // UI/UX
        serv_ui_name: "UI/UX & Branding",
        serv_ui_desc: "Créer des expériences numériques intuitives et esthétiques qui résonnent avec vos utilisateurs et renforcent votre image de marque.",
        serv_ui_list1: "Prototypes Interactifs",
        serv_ui_list2: "Rebranding Moderne",



        // FAQ part 

        faq_badge: "Questions Fréquentes",
        faq_title: "Questions ",
        faq_highlight: "Fréquemment Posées",
        faq_subtitle: "Tout ce que vous devez savoir sur nos admissions, nos cours et nos certifications.",
        
        faq_q1: "Quelles sont les conditions pour rejoindre les cours d'IA ou MERN ?",
        faq_a1: "Pour la plupart des cours, une culture informatique de base suffit. Pour les parcours avancés comme l'IA et le NLP, une compréhension de base des mathématiques et de la logique est utile.",
        
        faq_q2: "Est-ce que je recevrai un certificat après avoir terminé le cours ?",
        faq_a2: "Oui ! Après avoir réussi le cours et votre projet final, vous recevrez un certificat professionnel de Tech Iftiin Institute, reconnu par les meilleurs employeurs de Djibouti.",
        
        faq_q3: "Puis-je payer les frais de scolarité par versements ?",
        faq_a3: "Absolument. Nous proposons des plans de paiement flexibles. Vous pouvez choisir de payer mensuellement ou en deux versements tout au long de votre programme.",
        
        faq_q4: "Offrez-vous une aide au placement professionnel ?",
        faq_a4: "Oui. Nous disposons d'une plateforme dédiée 'Job Connecting Platform' pour nos étudiants. Nous vous aidons à construire votre profil LinkedIn, préparer votre CV et vous connecter aux entreprises locales.",

        // service sect 
        stat_students: "Étudiants Formés",
        stat_instructors: "Instructeurs Experts",
        stat_projects: "Projets Réalisés",
        stat_hiring: "Taux d'Embauche %",
        
        port_badge: "Notre Impact",
        port_title: "Ce que nos ",
        port_highlight: "Experts et Étudiants Construisent",
        port_subtitle: "Des applications et plateformes réelles au service des entreprises à Djibouti.",
        
        cat_design: "Web Design & Image de Marque",
        cat_mern: "MERN Stack / POS",
        cat_logic: "Logique d'Inventaire",
        cat_social: "Impact Social",
        cat_event: "Plateforme d'Événement",
        cat_logistics: "Tech Logistique",
        
        proj_urban_name: "Urban Beach Restaurant",
        proj_urban_desc: "Site promotionnel professionnel avec menus numériques et réservation intégrée.",
        proj_pos_name: "POS Restaurant Avancé",
        proj_pos_desc: "Système de point de vente basé sur les rôles avec suivi des ventes en temps réel.",
        proj_stock_name: "Gestionnaire de Stock Intelligent",
        proj_stock_desc: "Contrôle d'inventaire avec alertes automatiques de stock bas et gestion des fournisseurs.",
        proj_job_name: "Djibouti Job Connect",
        proj_job_desc: "Une plateforme connectant la jeunesse locale avec des opportunités d'emploi dans tout le pays.",
        proj_tour_name: "Plateforme Tour de Djibouti",
        proj_tour_desc: "Plateforme numérique intégrée pour l'inscription à la course et le suivi des coureurs.",
        proj_truck_name: "Système TruckTrack",
        proj_truck_desc: "Logiciel de suivi de flotte avec documentation des chauffeurs et flux d'approbation.",



    // Whey Us sec 
    choice_badge: "Notre Impact",
        choice_title: "Pourquoi étudiants et clients ",
        choice_highlight: "nous choisissent",
        choice_subtitle: "Apprenez auprès d'experts de l'industrie et rejoignez une communauté de diplômés qui dirigent la scène tech à Djibouti.",
        
        role_ceo: "Fondateur et PDG",
        role_lead: "Instructeur Principal",
        desc_ceo: "Leader visionnaire propulsant l'innovation technologique chez Tech Iftiin.",
        desc_ahmed_m: "Développement Web Full Stack et Cybersécurité",
        desc_elias: "Intelligence Artificielle et Ingénierie de Prompt",
        desc_hassan: "Programmation Python et Science des Données",
        
        success_label: "Histoires de Réussite (Nos Diplômés)",
        grad_tag: "Diplômé Full Stack et IA",
        status_current: "Étudiant Actuel - IA et NLP",
        
        test_ali: '"Tech Iftiin a changé ma carrière. La formation pratique en IA et Full Stack m\'a donné la confiance nécessaire pour créer des applications réelles."',
        test_bilan: '"Le mentorat ici est inégalé. Apprendre Python et le MERN stack avec des experts m\'a aidé à décrocher un excellent poste dans l\'industrie."',
        test_omar: '"Le programme MERN stack m\'a donné les compétences exactes que les entreprises locales recherchent. Je travaille maintenant sur des solutions web d\'entreprise."',
        test_khalid: '"Apprendre l\'ingénierie de prompt et le NLP chez Tech Iftiin m\'ouvre les yeux sur la puissance de l\'IA. C\'est le meilleur investissement tech que j\'ai fait."',

        // contact sec 
        cont_badge: "Contactez-nous",
        cont_title: "Visitez-nous ou ",
        cont_highlight: "Contactez-nous",
        cont_subtitle: "Vous avez des questions sur nos programmes ou services ? Notre équipe est prête à vous aider.",
        cont_form_title: "Envoyez-nous un message",
        cont_success: "Merci ! Votre message a été envoyé à Tech Iftiin. Nous vous contacterons bientôt.",
        btn_send: "Envoyer le message",
        
        // Labels
        label_loc: "Emplacement",
        label_call: "Appelez-nous",
        label_email: "E-mail",
        
        // Placeholders
        ph_name: "Votre Nom",
        ph_email: "Votre E-mail",
        ph_subject: "Sujet",
        ph_msg: "Votre Message",



        // login part 
        auth_title: "Connexion au Portail",
        auth_subtitle: "Bon retour chez Tech Iftiin",
        label_email_addr: "Adresse E-mail",
        label_password: "Mot de passe",
        ph_email_portal: "nom@example.com",
        ph_password: "••••••••",
        btn_access: "Accéder au Tableau de Bord",
        new_student: "Nouvel étudiant ?",
        create_acc: "Créer un compte",

        // footer sect 


        dir_about_p1: "Tech Iftiin Institute est le principal bootcamp technologique spécialisé à Djibouti. Nous nous engageons à fournir une éducation de pointe en IA et en logiciels pour autonomiser la prochaine génération de leaders numériques.",
        dir_about_p2: "Rejoignez nos cours transformateurs et libérez votre potentiel dans le monde passionnant de la technologie.",
        
        dir_h_courses: "Catégories de Cours",
        cat_mern: "Full Stack (MERN)",
        cat_ai: "Intelligence Artificielle",
        cat_cyber: "Cybersécurité",
        cat_py: "Programmation Python",
        cat_mobile: "Dév App Mobile",
        cat_marketing: "Marketing Numérique",
        cat_prompt: "Ingénierie de Prompt",
        cat_video: "Montage Vidéo",
        badge_pop: "POPULAIRE",
        badge_new: "NOUVEAU",

        dir_h_services: "Nos Services",
        serv_soft: "Développement de Logiciels",
        serv_web: "Développement Web",
        serv_mob: "Développement d'Apps Mobiles",
        serv_audit: "Audits de Cybersécurité",
        serv_cons: "Consultations IT",
        serv_corp: "Formation en Entreprise",
        serv_uiux: "Design UI/UX",
        serv_brand: "Branding Numérique",

        dir_h_contact: "Contactez-nous",
        dir_loc: "Blvd du Général de Gaulle, Djibouti",
        dir_h_company: "Entreprise",
        link_about: "À Propos",
        link_admissions: "Admissions",
        link_scholarships: "Bourses",
        link_terms: "Conditions d'Utilisation",
        link_privacy: "Politique de Confidentialité",
        link_courses: "Nos Cours",
        link_services: "Services",
        link_faq: "FAQ",
        link_student_support: "Support Étudiant",

        footer_tagline: "Le premier institut de Djibouti pour l'Intelligence Artificielle et les technologies de pointe. Façonner les innovateurs de demain.",
        footer_h_links: "Liens Rapides",
        footer_h_support: "Support",
        footer_rights: "Tech Iftiin Institute of AI & Technology. Tous droits réservés.",
        whatsapp_tooltip: "Discutez avec nous",


        // privacy sec 

     title_privacy: "Politique de Confidentialité",
        title_terms: "Conditions d'Utilisation",

        privacy_content: `
            <div class="policy-item">
                <h5>1. Collecte d'informations</h5>
                <p>Nous recueillons des informations d'identification personnelle (nom, adresse e-mail, numéro de téléphone) lorsque vous vous inscrivez à nos cours.</p>
            </div>
            <div class="policy-item">
                <h5>2. Utilisation de vos données</h5>
                <p>Vos données nous permettent de gérer votre compte étudiant, de traiter vos certifications et de vous envoyer des mises à jour importantes.</p>
            </div>
            <div class="policy-item">
                <h5>3. Sécurité des données</h5>
                <p>Nous mettons en œuvre diverses mesures de sécurité pour préserver la sécurité de vos informations personnelles.</p>
            </div>
        `,
        
        terms_content: `
            <div class="policy-item">
                <h5>1. Admission et Inscription</h5>
                <p>L'admission à Tech Iftiin est basée sur le mérite et la disponibilité des places. L'inscription est confirmée après le paiement des frais.</p>
            </div>
            <div class="policy-item">
                <h5>2. Politique de Présence</h5>
                <p>Pour recevoir une certification, les étudiants doivent maintenir un taux de présence d'au moins 85%.</p>
            </div>
            <div class="policy-item">
                <h5>3. Propriété Intellectuelle</h5>
                <p>Le programme, les cours enregistrés et le matériel pédagogique restent la propriété exclusive de Tech Iftiin.</p>
            </div>
            <div class="policy-item">
                <h5>4. Code de Conduite</h5>
                <p>Tech Iftiin maintient une politique de tolérance zéro pour le harcèlement ou la malhonnêteté académique.</p>
            </div>
        `,


        // admin dashboard part 
        "portal_title": "Portail Administrateur",
        "dashboard": "Tableau de Bord",
        "manage_users": "Gérer les Utilisateurs",
        "certificates": "Certificats",
        "manage_courses": "Gérer les Cours",
        "activity_logs": "Journaux d'Activité",
        "messages": "Messages",
        "setting": "Paramètres",
        "logout": "Déconnexion",
        "search_placeholder": "Rechercher...",
        "admin_role": "Administrateur",
        // Dashboard Cards
        "teachers": "Enseignants",
        "students": "Étudiants",
        "courses": "Cours",
        "pending_certs": "Certificats en Attente",
        "new_inquiries": "Nouvelles Demandes",

        // Quick Action Section
        "quick_action": "Action Rapide",
        "add_member_desc": "Ajoutez des membres à la famille Tech Iftiin.",
        "register_member": "+ Inscrire un Membre",
        "system_analytics": "Analyses du Système",
        "gender_distribution": "Répartition des Étudiants par Sexe",
        "course_enrollment": "Inscriptions par Cours",
        "male": "Homme",
        "female": "Femme",
        "total_students": "Total d'Étudiants",

       // --- Course Management Page ---
        "manage_global_courses": "Gérer les Cours Mondiaux",
        "add_new_course": "Ajouter un Nouveau Cours",
        "course_title": "Titre du Cours",
        "description": "Description",
        "assign_teacher": "Affecter un Enseignant",
        "title_placeholder": "ex. Full Stack",
        "desc_placeholder": "Aperçu ex. Populaire",
        "unassigned_option": "-- Laisser non assigné --",
        "create_course": "Créer un Cours",

        // --- Course Cards ---
        "course_module": "Module de Cours",
        "unassigned": "Non assigné",
        "change_teacher": "Changer d'Enseignant",
        "materials": "Matériaux",
        "welcome": "Bienvenue",
        // --- Delete Modal ---
        "are_you_sure": "Ma hubtaa?",
        "delete_warning": "Tani waxay si joogto ah u tirtiri doontaa koorsada iyo dhammaan agabka la xiriira. Falkaan dib looma celin karo.",
        "cancel": "Iska daa",
        "yes_delete": "Haa, tirtir",

        // --- Add User Page ---
        "back_to_list": "Retour à la liste",
        "create_new_user": "Créer un Nouvel Utilisateur",
        "password_requirement": "Le mot de passe doit inclure une majuscule, une minuscule, un chiffre et un caractère spécial (> 8 car.).",
        "full_name": "Nom Complet",
        "enter_name_placeholder": "Entrez votre nom",
        "email_address": "Adresse E-mail",
        "phone_number": "Numéro de Téléphone",
        "system_role": "Rôle du Système",
        "role_teacher": "Enseignant (Instructeur)",
        "role_manager": "Gestionnaire (Coordinateur)",
        "role_admin": "Administrateur",
        "password": "Mot de passe",
        "repeat_password": "Répéter le mot de passe",
        "gender": "Genre",
        "choose_gender": "Choisir le genre",
        "male": "Homme",
        "female": "Femme",
        "create_account_btn": "Créer le compte utilisateur",
        "user_success": "Compte utilisateur créé avec succès !",
        "back_to_dashboard": "Retour au Tableau de Bord",
        "user_management": "Gestion des Utilisateurs",
        "staff_members": "Membres du Personnel",
        "enrolled_students": "Étudiants Inscrits",
        "search_placeholder": "Rechercher nom ou email...",
        "btn_search": "Rechercher",
        "th_name": "Nom",
        "th_email": "Email",
        "th_phone": "Téléphone",
        "th_gender": "Genre",
        "th_role": "Rôle",
        "th_status": "Statut",
        "th_actions": "Actions",
        "male": "Homme",
        "female": "Femme",
        "role_admin": "Admin",
        "role_teacher": "Enseignant",
        "role_manager": "Gestionnaire",
        "active_status": "Actif",
        "inactive_status": "Inactif",
        "btn_edit": "Modifier",
        "btn_toggle": "Basculer",
        "btn_approve": "Approuver",
        "btn_disable": "Désactiver",
        "system_logs": "Journaux du Système",
        "filter_date": "Filtrer par date",
        "filter_role": "Filtrer par rôle",
        "all_roles": "Tous les rôles",
        "btn_reset": "Réinitialiser",
        "th_user_role": "Utilisateur et Rôle",
        "th_action": "Action",
        "th_details": "Détails",
        "th_time": "Heure",
        "no_logs": "Aucun journal d'activité trouvé pour cette recherche.",
        "role_student": "Étudiant",
        "role_na": "N/A",
        "cert_approvals": "Approbations de Certificats",
        "incoming_requests": "Demandes Entrantes",
        "th_student": "Étudiant",
        "th_module": "Module",
        "th_instructor": "Instructeur",
        "th_actions": "Action",
        "status_approved": "Approuvé",
        "btn_approve": "Approuver",
        "btn_reject": "Rejeter",
        "no_requests": "Aucune demande en attente.",
        "showing_text": "Affichage de",
        "to_text": "à",
        "of_text": "sur",
        "requests_text": "demandes",
        "account_settings": "Paramètres du compte",
        "password_label": "Nouveau mot de passe (laisser vide pour conserver l'actuel)",
        "btn_update_profile": "Mettre à jour le profil",
        "profile_success": "Profil mis à jour avec succès !",
        
        "student_inquiries": "Demandes des étudiants",
        "new_messages_count": "Nouveaux messages",
        "btn_view_details": "Voir les détails",
        "btn_reply_email": "Répondre par e-mail",
        "no_messages": "Aucun message trouvé.",
        "message_detail": "Détails du message",
        "label_from": "De :",
        "label_date": "Date :",
        "btn_close": "Fermer",

        "teacher_portal": "Portail des Enseignants",
        "submissions": "Soumissions",
        "submissions_desc": "Examiner les réponses aux devoirs.",
        "report_cards": "Bulletins de Notes",
        "report_cards_desc": "Générer des cartes d'étudiant.",
        "btn_generate": "Générer",
        "my_assigned_courses": "Mes Cours Assignés",
        "curriculum": "Programme",
        "btn_manage_lessons": "Gérer les leçons et supports",
        "assessment": "Évaluation",
        "assessment_desc": "Voir les étudiants, suivre l'assiduité et les notes finales.",
        "btn_grade_students": "Noter les Étudiants",
        "class_list": "Liste de Classe",
        "class_list_desc": "Voir tous les étudiants inscrits et leurs contacts.",
        "btn_view_student_list": "Voir la liste des étudiants",
        "daily_log": "Journal Quotidien",
        "daily_log_desc": "Enregistrer la présence et suivre l'historique.",
        "btn_take_attendance": "Prendre l'appel",
        "btn_export_pdf": "Exporter le rapport PDF",
        "no_courses_assigned": "Aucun cours assigné pour le moment.",
        "contact_admin_desc": "Veuillez contacter l'administrateur pour commencer.",
        "settings_subtitle": "Mettez à jour vos identifiants de connexion.",
        "btn_save_changes": "Sauvegarder",
        "btn_manage_small": "Gérer",
        "no_students_registered": "Aucun étudiant inscrit.",
        "student_submissions_title": "Soumissions des étudiants",
        "student_submissions_desc": "Examinez et téléchargez les dernières réponses aux devoirs de vos étudiants.",
        "btn_view_all_answers": "Voir toutes les réponses",

        "label_course": "Cours :",
        "label_instructor": "Instructeur :",
        "th_student_name": "Nom de l'étudiant",
        "th_gender": "Genre",
        "th_status": "Statut",
        "gender_male": "Homme",
        "gender_female": "Femme",
        "status_present": "Présent",
        "status_absent": "Absent",
        "status_late": "En retard",
        "report_generated_on": "Rapport généré le :",
        "instructor_signature": "Signature de l'instructeur",
        "admin_stamp": "Timbre de l'administration",

       "back_to_teacher_dashboard": "Retour au tableau de bord",
        "search_placeholder": "Rechercher par nom ou cours...",
        "total_students_label": "TOTAL DES ÉTUDIANTS",
        "grade_c_above_label": "GRADE C ET PLUS",
        "active_courses_label": "COURS ACTIFS",
        "high_achievers_title": "Hauts Potentiels (Grade C et plus)",
        "high_achievers_desc": "Examiner et envoyer les étudiants éligibles pour la certification.",
        "select_course": "Sélectionner un cours",
        "btn_preview_list": "Aperçu de la liste",
        "btn_send_admin": "Envoyer à l'admin",
        "achievement_cards_title": "Cartes de réussite des étudiants",
        "achievement_cards_desc": "Générer et télécharger les rapports officiels.",
        "th_student_details": "Détails de l'étudiant",
        "th_course_module": "Module de cours",
        "th_action": "Action",
        "btn_view_report": "Voir le rapport",
        "no_students_found": "Aucun étudiant inscrit trouvé.",


        "review_submissions_title": "Examiner les soumissions",
        "back_to_dashboard": "Retour au tableau de bord",
        "total_label": "Total :",
        "submissions_count_label": "Soumissions",
        "th_student_name": "Nom de l'étudiant",
        "th_assignment": "Devoir",
        "th_submitted_date": "Date de soumission",
        "th_action": "Action",
        "btn_view_pdf": "Voir le PDF",
        "graded_label": "Noté",
        "btn_edit": "Modifier",
        "btn_add_grade": "Ajouter une note",
        "no_submissions_found": "Aucune soumission trouvée.",


        "class_grade_report": "Rapport de Notes de la Classe",
        "label_course": "Cours :",
        "generated_on": "Généré le :",
        "th_student_name": "Nom de l'étudiant",
        "th_email": "Email",
        "th_phone": "Téléphone",
        "th_total_score": "Score Total",
        "th_percentage": "Pourcentage",

        "manage_content_title": "Gérer le contenu du cours",
        "label_category": "Catégorie",
        "opt_material": "Support de cours",
        "opt_assignment": "Devoir",
        "opt_quiz": "Quiz",
        "opt_exam": "Examen Partiel/Final",
        "label_content_type": "Type de contenu",
        "opt_pdf": "Document PDF",
        "opt_video": "Vidéo (Lien)",
        "opt_text": "Instructions textuelles",
        "label_part_number": "Numéro de partie",
        "placeholder_part": "ex. 1",
        "label_material_title": "Titre du support",
        "placeholder_title": "ex. Introduction à l'IA",
        "label_upload_file": "Charger un fichier (PDF/ZIP) Max : 10Mo",
        "label_paste_link": "Coller le lien (URL)",
        "placeholder_url": "https://...",
        "btn_save_content": "Enregistrer le contenu",
        "section_1_title": "Section 1 : Supports d'apprentissage",
        "section_2_title": "Section 2 : Devoirs et Examens",
        "part_label": "Partie",
        "btn_view_text": "Voir le texte",
        "btn_view_file": "Voir le fichier",
        "no_materials": "Aucun matériel ajouté pour le moment.",
        "no_assessments": "Aucun devoir ou examen ajouté.",



        "alert_success_bold": "Succès !",
        "alert_success_msg": "La note de l'élève a été enregistrée et suivie avec succès.",
        "grading_portal_title": "Portail de Notation",
        "back_to_submissions": "Retour aux soumissions",
        "nav_dashboard": "Tableau de bord",
        "student_id_label": "ID Étudiant :",
        "label_as_name": "NOM DE L'ÉVALUATION",
        "placeholder_as_name": "ex. Examen de mi-parcours",
        "label_weight": "COEFFICIENT TOTAL",
        "placeholder_weight": "ex. 20",
        "label_score": "NOTE",
        "placeholder_score": "ex. 18",
        "btn_save_grade": "Confirmer et enregistrer la note",




        "btn_print_report": "IMPRIMER LE BULLETIN OFFICIEL",
        "institute_motto": "Autonomiser l'avenir grâce à l'innovation",
        "label_student_caps": "ÉTUDIANT :",
        "label_course_caps": "COURS :",
        "th_desc": "Description de l'évaluation",
        "th_weight": "Coefficient (%)",
        "th_score": "Note obtenue",
        "cumulative_total": "TOTAL CUMULÉ",
        "final_percentage": "Pourcentage final",
        "overall_grade": "Note globale",
        "no_grades_recorded": "Aucune note enregistrée",
        "sig_instructor": "SIGNATURE DE L'INSTRUCTEUR",
        "sig_registrar": "REGISTRAIRE ACADÉMIQUE",
        "disclaimer_start": "Ce document est un relevé académique officiel de l'Institut TechIftiin. Délivré le",


        "take_attendance_title": "Prendre les présences",
        "label_select_date": "Sélectionner la date :",
        "th_gender": "Genre",
        "th_status": "Statut",
        "opt_take_attendance": "-- noter la présence --",
        "opt_present": "✅ Présent",
        "opt_absent": "❌ Absent",
        "opt_late": "🕒 En retard",
        "btn_submit_attendance": "Soumettre les présences",
        "msg_submission_closed": "Soumission fermée pour aujourd'hui",
        "chart_absenteeism_title": "Taux d'absentéisme élevé (%)",
        "attendance_history_title": "Historique des présences",
        "btn_filter": "Filtrer",
        "btn_clear": "Effacer",
        "th_date": "Date",
        "btn_download_pdf": "Télécharger le PDF",
        "no_records_found": "Aucun enregistrement trouvé.",

        "label_registered_courses": "Vos cours inscrits :",
        "portal_title": "Mon portail d'apprentissage",
        "welcome_back": "Bon retour,",
        "btn_student_ai": "IA Étudiante",
        "nav_logout": "Déconnexion",
        "cert_label": "Certificat",
        "status_label": "Statut :",
        "cert_none": "Aucun certificat trouvé",
        "btn_download_cert": "Télécharger le certificat",
        "cert_pending": "En attente d'approbation",
        "cert_instruction": "Le certificat apparaîtra ici après la fin du cours",
        "academic_progress_title": "Progrès Académique",
        "cumulative_grade_label": "Note globale actuelle",
        "th_assessment": "Évaluation",
        "th_weight": "Coefficient %",
        "th_score_obtained": "Note obtenue",
        "total_raw_score": "SCORE BRUT TOTAL",
        "no_grades_posted": "Aucune note publiée pour le moment.",
        "course_materials_title": "Supports de cours",
        "tasks_exams_title": "Tâches et Examens",
        "btn_read_text": "Lire le texte",
        "btn_open_pdf": "Ouvrir le PDF",
        "btn_submit_work": "Soumettre le travail",
        "status_submitted": "Soumis",
        "cumulative_grade_label": "Note globale actuelle",

        "attendance_summary_title": "Résumé des présences",
        "label_total_rate": "Taux total",
        "label_days_present": "Jours présents",
        "logs_title": "Journaux",
        "btn_clear": "Effacer",
        "course_materials_title": "Supports de cours",
        "btn_read_text": "Lire le texte",
        "btn_open_pdf": "Ouvrir le PDF",
        "modal_content_details": "Détails du contenu",
        "btn_close": "Fermer",
        "settings_title": "Paramètres du compte",
        "settings_subtitle": "Mettez à jour votre e-mail ou définissez un nouveau mot de passe sécurisé.",
        "label_email": "Adresse e-mail",
        "label_new_password": "Nouveau mot de passe",
        "placeholder_password_blank": "Laissez vide pour conserver l'actuel",
        "crit_upper": "✖ Majuscules et minuscules",
        "crit_number": "✖ Au moins un chiffre",
        "crit_special": "✖ Caractère spécial (@$!%*#?&)",
        "label_confirm_password": "Confirmer le nouveau mot de passe",
        "placeholder_repeat_password": "Répétez le nouveau mot de passe",
        "btn_save_changes": "Enregistrer les modifications",
        

        "submit_title": "Soumettre le travail",
        "btn_back_dashboard": "Retour au tableau de bord",
        "label_upload_solution": "Téléchargez votre solution (PDF uniquement)",
        "btn_upload_submit": "Télécharger et soumettre le PDF",


        "cert_presentation_text": "Ce certificat est fièrement présenté à",
        "cert_completion_text": "Pour avoir complété avec succès les exigences professionnelles du cours",
        "label_verification_id": "ID DE VÉRIFICATION :",
        "stamp_instruction": "Apposer le cachet<br>officiel de l'institut<br>ici",
        "label_academic_director": "Directeur Académique",

        "assign_teacher_title": "Assigner un enseignant",
        "label_selected_course": "Cours sélectionné",
        "label_select_instructor": "Sélectionnez un instructeur",
        "option_choose_list": "-- Choisir dans la liste --",
        "btn_confirm_assignment": "Confirmer l'affectation",
        "link_go_back": "Tant pis, retournez",

      "manage_courses_title": "Gérer les cours globaux",
        "add_new_course": "Ajouter un nouveau cours",
        "label_course_title": "Titre du cours",
        "label_description": "Description",
        "label_assign_teacher": "Assigner un enseignant",
        "option_unassigned": "-- Laisser non assigné --",
        "btn_add_course": "Ajouter le cours",
        "label_course_type": "Cours",
        "status_unassigned": "Non assigné",
        "link_change_teacher": "Changer d'enseignant",
        "label_materials": "Supports",
        "status_active": "● Actif",
        "status_empty": "○ Vide",
        "btn_delete": "Supprimer",
        "delete_confirm_title": "Êtes-vous sûr ?",
        "delete_warning_text": "Ceci supprimera définitivement le cours",
        "delete_warning_subtext": "et tous les supports associés. Cette action est irréversible.",
        "btn_cancel": "Annuler",
        "btn_confirm_delete": "Oui, supprimez-le",

        "portal_manager_title": "Portail Gestionnaire",
        "nav_dashboard": "Tableau de bord",
        "nav_users": "Liste des utilisateurs",
        "nav_manage_courses": "Gérer les cours",
        "nav_reports": "Rapports système",
        "nav_settings": "Paramètres",
        "nav_logout": "Se déconnecter",
        "placeholder_search": "Rechercher...",
        "role_coordinator": "Coordinateur",
        "stat_teachers": "Enseignants",
        "stat_students": "Étudiants",
        "stat_courses": "Cours",
        "analytics_title": "Analyses du système",
        "chart_gender_title": "Répartition des étudiants par sexe",
        "chart_enrollment_title": "Inscriptions par cours",
       
        "btn_generate_pdf": "Générer un rapport PDF",
        "report_main_title": "Rapport d'analyse du système",
        "report_attendance_title": "Taux de présence aux cours",
        "th_course_title": "TITRE DU COURS",
        "th_engagement": "ENGAGEMENT",
        "report_at_risk_title": "Étudiants à risque",
        "report_at_risk_sub": "Les 10 étudiants ayant le plus d'absences.",
        "label_absences": "Absences",

        "account_settings_title": "Paramètres du compte",
        "label_full_name": "Nom complet",
        "label_email_address": "Adresse e-mail",
        "label_new_password": "Nouveau mot de passe (laisser vide pour conserver l'actuel)",
        "placeholder_password": "********",
        "btn_update_profile": "Mettre à jour le profil",
        "btn_back_dashboard": "Retour au tableau de bord",

        "user_management_title": "Gestion des utilisateurs",
        "tab_students": "Étudiants",
        "tab_teachers": "Enseignants",
        "th_name": "Nom",
        "th_email": "E-mail",
        "th_phone": "Téléphone",
        "th_gender": "Genre",
        "th_status": "Statut",
        "gender_male": "Homme",
        "gender_female": "Femme",
        "status_active_label": "Actif",
        "status_pending_label": "En attente",
        "no_users_found": "Aucun utilisateur trouvé pour cette catégorie :",
        "th_ip":"Adresse IP"








    }
};











window.changeLanguage = function(lang) {
    console.log("--- Language Change Started ---");
    console.log("Target Language: " + lang);

    if (!translations[lang]) {
        console.error("CRITICAL: Language '" + lang + "' does not exist in the translations object!");
        return;
    }

    localStorage.setItem('preferredLang', lang);

    const elements = document.querySelectorAll('[data-lang]');
    console.log("Found " + elements.length + " elements with data-lang attribute.");

    elements.forEach((el, index) => {
        const key = el.getAttribute('data-lang');
        if (translations[lang][key]) {
            if (el.tagName === 'INPUT') {
                el.placeholder = translations[lang][key];
            } else {
                el.textContent = translations[lang][key];
            }
        } else {
            console.warn("Missing Key: The key '" + key + "' was not found in the '" + lang + "' dictionary.");
        }
    });
    console.log("--- Language Change Finished ---");
};

// Auto-init on load
document.addEventListener('DOMContentLoaded', () => {
    const savedLang = localStorage.getItem('preferredLang') || 'en';
    window.changeLanguage(savedLang);
});