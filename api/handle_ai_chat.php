<?php
// This is the backend endpoint for the AI chat
include('../includes/config.php');
include('../includes/ai_helper.php');

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

header('Content-Type: application/json');

if (!isset($data['prompt']) || empty($data['prompt'])) {
    echo json_encode(['error' => 'No prompt provided.']);
    exit;
}

$prompt = $data['prompt'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // 0 for guest

// --- Optional: Get Chat History ---
// For a production app, you'd fetch the last 5-10 messages for this user
// $history = ...
$history = []; // Keeping it simple for now

// Call the AI function
$ai_response = getOpenAIChatResponse($prompt, "You are a helpful university-level study assistant.", $history);

// Save to database (for logged-in users)
if ($user_id > 0) {
    try {
        $stmt = $pdo->prepare("INSERT INTO ai_chat_history (user_id, message, response) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $prompt, $ai_response]);
    } catch (PDOException $e) {
        // Log error, but don't stop the user
        error_log("DB Error: " . $e->getMessage());
    }
}

// Send response back to the frontend
echo json_encode(['response' => $ai_response]);
?>