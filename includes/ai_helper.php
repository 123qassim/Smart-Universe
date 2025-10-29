<?php
// This file is included by other files that need AI functionality.
// Ensure config.php is included before this.

/**
 * Calls the OpenAI Chat Completion API.
 *
 * @param string $prompt The user's prompt.
 * @param string $system_message The system role (e.g., "You are a helpful study assistant.").
 * @param array $history (Optional) The past conversation history.
 * @return string The AI's response text.
 */
function getOpenAIChatResponse($prompt, $system_message = "You are a helpful university-level study assistant.", $history = []) {
    
    $api_key = OPENAI_API_KEY;
    $url = 'https://api.openai.com/v1/chat/completions';

    $messages = [];
    $messages[] = ["role" => "system", "content" => $system_message];

    // Add history if provided
    foreach ($history as $msg) {
        $messages[] = $msg;
    }
    
    // Add the new user prompt
    $messages[] = ["role" => "user", "content" => $prompt];

    $data = [
        'model' => 'gpt-3.5-turbo', // Or gpt-4
        'messages' => $messages,
        'temperature' => 0.7,
        'max_tokens' => 1500,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    ]);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return "cURL Error: " . $err;
    }

    $result = json_decode($response, true);

    if (isset($result['choices'][0]['message']['content'])) {
        return $result['choices'][0]['message']['content'];
    } else {
        // Log the full error for debugging
        error_log("OpenAI API Error: " . $response);
        return "Error: Could not get a valid response from AI.";
    }
}
?>