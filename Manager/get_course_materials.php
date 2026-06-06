<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    die("Unauthorized access.");
}

if (!isset($_GET['course_id'])) {
    die("<p style='color:red; text-align:center;'>Error: No course ID provided.</p>");
}

$course_id = mysqli_real_escape_string($conn, $_GET['course_id']);

$sql = "SELECT id, title, file_path_or_link, content_type, part_number 
        FROM lessons 
        WHERE course_id = '$course_id' 
        ORDER BY part_number ASC";

$files = $conn->query($sql);

if ($files->num_rows > 0) {
    echo "<div style='display: grid; gap: 12px;'>";
    while ($f = $files->fetch_assoc()) {
        $lesson_id = $f['id'];
        $title = htmlspecialchars($f['title']);
        $content = $f['file_path_or_link']; 
        $type = strtolower($f['content_type']);
        $part = htmlspecialchars($f['part_number']);

        echo "<div style='padding: 15px; border: 1px solid #eee; border-radius: 10px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.03);'>";
        
        echo "<div style='display: flex; justify-content: space-between; align-items: center;'>
                <div style='display: flex; align-items: center; gap: 12px;'>
                    <span style='font-size: 1.4rem;'>" . ($type == 'video' ? '🎥' : ($type == 'text' ? '📝' : '📄')) . "</span>
                    <div>
                        <div style='font-size: 0.7rem; color: #3498db; font-weight: bold;'>PART $part</div>
                        <strong style='font-size: 0.95rem; color: #2c3e50;'>$title</strong>
                    </div>
                </div>";

        // Logic for Buttons
        if ($type === 'text') {
            // Toggle button for text
            echo "<button onclick='toggleText($lesson_id)' id='btn-$lesson_id' 
                     style='background: #9b59b6; color: white; padding: 7px 14px; border: none; border-radius: 6px; font-size: 0.75rem; font-weight: bold; cursor: pointer;'>
                     View Text
                  </button>";
        } else {
            // Open link for files/videos
            echo "<a href='../" . htmlspecialchars($content) . "' target='_blank' 
                     style='background: #3498db; color: white; padding: 7px 14px; text-decoration: none; border-radius: 6px; font-size: 0.75rem; font-weight: bold;'>
                     Open File
                  </a>";
        }
        
        echo "</div>";

        // The Hidden Text Section
        if ($type === 'text') {
            echo "<div id='text-$lesson_id' style='display: none; margin-top: 12px; padding: 12px; background: #f9f9f9; border-top: 1px solid #eee; border-radius: 0 0 6px 6px; font-size: 0.85rem; color: #444; line-height: 1.5;'>
                    <strong style='display:block; margin-bottom: 5px; color: #7f8c8d;'>Content:</strong>" 
                    . nl2br(htmlspecialchars($content)) . 
                  "</div>";
        }

        echo "</div>";
    }
    echo "</div>";

    // Small Javascript for the toggle functionality
    ?>
    <script>
    function toggleText(id) {
        var textDiv = document.getElementById('text-' + id);
        var btn = document.getElementById('btn-' + id);
        if (textDiv.style.display === "none") {
            textDiv.style.display = "block";
            btn.innerText = "Hide Text";
            btn.style.background = "#34495e";
        } else {
            textDiv.style.display = "none";
            btn.innerText = "View Text";
            btn.style.background = "#9b59b6";
        }
    }
    </script>
    <?php
} else {
    echo "<div style='text-align: center; padding: 60px 20px; color: #b2bec3;'><h3>No Materials Found</h3></div>";
}
?>