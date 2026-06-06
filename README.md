# TechIftiin LMS - Learning Management System v1.0.0

An omni-channel, responsive Learning Management System (LMS) designed for the educational sector in Djibouti. Built with a 4-tier Role-Based Access Control (RBAC) model, the platform ensures data isolation, operational security, and a customized experience for administrators, managers, instructors, and students.

## 🚀 Key Features

### 1. Security Architecture
* **Advanced One-Way Cryptography:** Utilizes industry-standard Bcrypt hashing protocols to securely transform credentials into mathematically non-reversible, fixed-length hashes. Plain-text passwords are never stored in the database.
* **Multi-Factor Password Entropy:** Enforces a strict minimum threshold of 8 characters for all accounts, requiring a mandatory mix of uppercase, lowercase, numbers, and special symbols.
* **Secure File Governance:** Prevents Remote Code Execution (RCE) by validating file integrity and restricting uploads strictly to `.pdf` and `.zip` extensions with a 10MB maximum threshold.
* **Granular Activity Forensics:** Maintains a complete audit trail tracking precise timestamps, actions, user roles, and Device IP Addresses to identify unauthorized access points.

### 2. Pedagogical Features & Localization
* **Real-Time Attendance & KPI Tracking:** Instructors can log daily presence for specific courses directly from their dashboard. Attendance data dynamically reflects on student profiles as a core engagement Key Performance Indicator (KPI).
* **Automated Evaluation & Report Generation:** A unified environment for grading assignments and exams capable of exporting print-ready, high-fidelity academic report cards with a single click.
* **Verifiable Digital Certification:** Automatically issues completion certificates embedded with unique, verifiable barcodes, allowing third-party authenticity checks against the secure database.
* **Bilingual Localization Engine:** Full native UI and notification support for both English and French, customized to comply with regional linguistic needs in Djibouti.

### 3. Compliance & Disaster Recovery
* **Data Redundancy:** Configured with automated daily incremental database backups and weekly full-system snapshots stored offsite via encrypted environments.
* **Legal Transparency:** Explicit links to the Privacy Policy and Terms of Service delivered directly within smooth, interactive pop-up modals for fluid user awareness.

---

## 🔑 Permission Matrix (RBAC)

The system isolates operations seamlessly across 4 distinct user roles:

| Role | Core Responsibilities & Capabilities |
| :--- | :--- |
| **Administrator (Root)** | Staff lifecycle management (Teacher/Manager accounts), final student registration approvals, global forensic audit log access with IP tracking, curriculum definitions, and certificate approvals. |
| **Academic Manager** | Access to student demographic and KPI trend analytics, quality control monitoring of absenteeism, checking teacher registries, and structural course resource allocations. |
| **Instructor** | Curriculum content and assignment delivery, real-time grading rosters, live attendance registry tracking, profile management, and initiating certificate validation requests to the Admin. |
| **Student** | Self-service registration, interactive dashboard with immediate access to learning assets, live personal KPI/progress tracking, and direct downloads of barcode-verified certificates. |

---

## 🛠️ Tech Stack & Requirements

* **Backend:** PHP 8.x (utilizing secure `password_verify()` matching and data sanitation layers).
* **Database:** MySQL (Structured relational schema with foreign key constraints or operational cascading checks).
* **Frontend:** HTML5, Modern CSS Frameworks (Bootstrap), JavaScript (ES6) for cross-platform responsive layouts across Mobile, Tablet, and Desktop displays.

---

## 📦 Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git](https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git)
