<?php
// 1. Start session and include database first!
session_start();
require_once '../config/db.php';

// 2. Security: Ensure only logged-in admins can see this
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Tech Iftiin Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
    /* Global Styles */
    * { box-sizing: border-box; }
    .admin-dashboard { 
        display: grid; 
        grid-template-columns: 250px 1fr; 
        min-height: 100vh; 
        background: #f4f7f6; 
        font-family: 'Segoe UI', sans-serif; 
    }
    
    .admin-sidebar { background: #1a0b45; color: white; padding: 30px 20px; }
    .admin-sidebar h3 { margin-bottom: 30px; color: #2ecc71; font-size: 1.2rem; }
    .admin-sidebar nav a { 
        display: block; color: #bdc3c7; text-decoration: none; 
        padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1); 
        transition: 0.3s; font-size: 0.95rem;
    }
    .admin-sidebar nav a:hover, .admin-sidebar nav a.active { color: #2ecc71; padding-left: 10px; }
    
    .message-center { padding: 40px; max-width: 1200px; }
    .inbox-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .inbox-header h2 { font-size: 1.5rem; margin: 0; }
    
    .stats-mini span { background: #2ecc71; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; }
    
    .message-card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-left: 5px solid #eee; transition: 0.3s; }
    .message-card.unread-border { border-left-color: #2ecc71; }
    .message-card h4 { margin: 10px 0; font-size: 1.1rem; color: #2c3e50; }
    .message-card p { font-size: 0.9rem; color: #7f8c8d; line-height: 1.4; }
    
    .msg-meta { display: flex; justify-content: space-between; font-size: 0.8rem; color: #95a5a6; margin-bottom: 5px; }
    .msg-actions { margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap; }
    
    .btn-open, .btn-reply { 
        padding: 8px 16px; border-radius: 6px; text-decoration: none; 
        font-size: 0.8rem; font-weight: 600; transition: 0.2s;
    }
    .btn-open { background: #1a0b45; color: white; border: none; cursor: pointer; }
    .btn-reply { border: 1px solid #1a0b45; color: #1a0b45; }

    /* Modal Styles */
    .modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center;
    }
    .modal-content {
        background: white; width: 600px; max-width: 95%; border-radius: 15px;
        padding: 25px; animation: slideDown 0.3s ease; position: relative;
    }
    .message-text { font-size: 0.95rem; padding: 15px 0; line-height: 1.6; color: #34495e; }

    /* --- MOBILE ADJUSTMENTS (The Fix) --- */
    @media (max-width: 768px) {
        .admin-dashboard {
            grid-template-columns: 1fr; /* Stack sidebar and content */
        }
        
        .admin-sidebar {
            padding: 15px;
            text-align: center;
        }

        .admin-sidebar h3 { margin-bottom: 15px; }

        .admin-sidebar nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .admin-sidebar nav a {
            border: none;
            font-size: 0.8rem;
            padding: 5px 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }

        .message-center {
            padding: 20px 15px;
        }

        .inbox-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .inbox-header h2 { font-size: 1.2rem; }

        .message-card h4 { font-size: 1rem; }
        .message-card p { font-size: 0.85rem; }

        .modal-content {
            padding: 15px;
            width: 90%;
        }
        
        .modal-header h3 { font-size: 1.1rem; }
        .sender-info p { font-size: 0.8rem; }
    }
</style>
</head>
<body>

<div class="admin-dashboard">
    <aside class="admin-sidebar">
        <h3>Tech Iftiin Panel</h3>
        <nav>
            <a href="dashboard.php"><i class="fas fa-chart-line"></i> <span data-lang="dashboard">Dashboard</span></a>
            <a href="admin_messages.php" class="active"><i class="fas fa-envelope"></i> <span data-lang="messages">Messages</span></a>
            <a href="manage_users.php"><i class="fas fa-users"></i> <span data-lang="manage_users">Manage Users</span></a>
            <a href="courses.php"><i class="fas fa-book"></i> <span data-lang="manage_courses">Courses</span></a>
            <a href="../auth/logout.php" style="color: #e74c3c; margin-top: 20px;"><i class="fas fa-sign-out-alt"></i> <span data-lang="logout">Logout</span></a>
        </nav>
    </aside>

    <main class="message-center">
        <div class="inbox-header">
            <h2 data-lang="student_inquiries">Student Inquiries</h2>
            <div class="stats-mini">
                <?php
                // Now $conn will work because we required it at the top
                $res = $conn->query("SELECT COUNT(*) as total FROM contact_messages WHERE status='unread'");
                $count = $res->fetch_assoc();
                echo "<span>" . ($count['total'] ?? 0) . " New Messages</span>";
                ?>
            </div>
        </div>

        <div class="message-list">
            <?php
            $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $status_class = ($row['status'] == 'unread') ? 'unread-border' : '';
                    echo "
                <div class='message-card $status_class' data-id='{$row['id']}'>
                    <div class='msg-meta'>
                        <strong>" . htmlspecialchars($row['name']) . "</strong>
                        <span>" . date('M d, H:i', strtotime($row['created_at'])) . "</span>
                    </div>
                    <h4>" . htmlspecialchars($row['subject']) . "</h4>
                    <p>" . htmlspecialchars(substr($row['message'], 0, 120)) . "...</p>
                    <div class='msg-actions'>
                        <button type='button' onclick='openMessage({$row['id']})' class='btn-open' style='border:none; cursor:pointer;'>
                            View Details
                        </button>
                        <a href='mailto:" . htmlspecialchars($row['email']) . "' class='btn-reply'>Reply via Email</a>
                    </div>
                </div>";
                }
            } else {
                echo "<p style='color: #7f8c8d;' data-lang='no_messages'>No messages found.</p>";
            }
            ?>
        </div>
    </main>
</div>



<div id="messageModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalSubject" data-lang="message_detail">Message Detail</h3>
            <span class="close-modal" onclick="closeMessage()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="sender-info">
                <p><strong data-lang="label_from">From:</strong> <span id="modalName"></span> (<span id="modalEmail"></span>)</p>
                <p><strong data-lang="label_date">Date:</strong> <span id="modalDate"></span></p>
            </div>
            <hr>
            <div id="modalBody" class="message-text"></div>
        </div>
        <div class="modal-footer">
            <a href="#" id="modalReplyBtn" class="btn-open" data-lang="btn_reply_email">Reply via Email</a>
            <button onclick="closeMessage()" class="btn-reply" data-lang="btn_close">Close</button>
        </div>
    </div>
</div>


<script>
    function openMessage(msgId) {
    // 1. Fetch message data from the server
    fetch(`get_message_details.php?id=${msgId}`)
    .then(response => response.json())
    .then(data => {
        // 2. Fill the modal with data
        document.getElementById('modalSubject').innerText = data.subject;
        document.getElementById('modalName').innerText = data.name;
        document.getElementById('modalEmail').innerText = data.email;
        document.getElementById('modalDate').innerText = data.created_at;
        document.getElementById('modalBody').innerHTML = data.message.replace(/\n/g, '<br>');
        document.getElementById('modalReplyBtn').href = `mailto:${data.email}?subject=Re: ${data.subject}`;

        // 3. Show the modal
        document.getElementById('messageModal').style.display = 'flex';
        
        // 4. Silently update the card UI to show it's read
        const card = document.querySelector(`[data-id="${msgId}"]`);
        if(card) card.classList.remove('unread-border');
    });
}

function closeMessage() {
    document.getElementById('messageModal').style.display = 'none';
}
</script>






  <script src="http://localhost/lms_tech/lang.js"></script>


</body>
</html>