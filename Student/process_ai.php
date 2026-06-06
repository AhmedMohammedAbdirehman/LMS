<?php
session_start();
require_once '../config/db.php';

// Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only care about the custom query from the user
    $user_query = $_POST['query'] ?? '';
    $course_id = (int)($_POST['course_id'] ?? 0);

    // --- 1. FETCH CONTEXT ---
    $context_text = "";
    $stmt = $conn->prepare("SELECT title, file_path_or_link FROM lessons WHERE course_id = ? AND content_type = 'text' LIMIT 5");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $context_text .= "Topic: " . $row['title'] . "\nDetails: " . $row['file_path_or_link'] . "\n\n";
    }

    // --- 2. THE "TRAINED" SYSTEM INSTRUCTION ---
    $system_instruction = "You are the Techiftiin Institute AI Assistant, founded by Ahmed Mo. (MSc). ";
    $system_instruction .= "Expertise: MERN Stack, Pyhton Programming and Cybersecurity. Answer the student based on this lesson context:\n\n" . $context_text;

    // --- 3. API CALL (STRICTLY CUSTOM) ---
    $api_key = GEMINI_API_KEY; // Using the secure constant from db.php
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite-preview:generateContent?key=" . $api_key;

    $data = [
        "system_instruction" => ["parts" => [["text" => $system_instruction]]],
        "contents" => [
            ["role" => "user", "parts" => [["text" => $user_query]]]
        ],
        "generationConfig" => ["temperature" => 0.7]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    
    $response = curl_exec($ch);
    $response_data = json_decode($response, true);
    curl_close($ch);

    // --- 4. FORMAT OUTPUT ---
    if (isset($response_data['candidates'][0]['content']['parts'][0]['text'])) {
        $ai_text = $response_data['candidates'][0]['content']['parts'][0]['text'];
        
        // Simple Markdown-to-HTML conversion
        $html_output = str_replace(['**', '###', '* '], ['<b>', '<h4 style="color:#1a0b45;">', '• '], $ai_text);
        if (strpos($html_output, '```') !== false) {
            $html_output = str_replace('```', '<pre style="background:#2d2d2d; color:#ccc; padding:10px; border-radius:5px;">', $html_output);
        }
        echo nl2br($html_output);
    } else {
        echo "I'm sorry, I couldn't process that. Please ask another technical question.";
    }
}
?>