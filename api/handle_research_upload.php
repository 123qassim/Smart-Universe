<?php
// This is api/handle_research_upload.php (UPGRADED)
include('../includes/config.php');
include('../includes/ai_helper.php');

header('Content-Type: application/json');

// 1. Security & Validation
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['title']) || !isset($_FILES['paper_file'])) {
    echo json_encode(['success' => false, 'message' => 'Title and PDF file are required.']);
    exit;
}
$title = trim($_POST['title']);
$user_id = $_SESSION['user_id'];
$file = $_FILES['paper_file'];

// 2. File Upload Handling
$upload_dir = '../uploads/research/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Validate file
$file_type = $file['type'];
$file_size = $file['size'];
$file_error = $file['error'];
$file_tmp_name = $file['tmp_name'];

if ($file_error !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'File upload error. Code: ' . $file_error]);
    exit;
}
if ($file_type != 'application/pdf') {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF is allowed.']);
    exit;
}
if ($file_size > 10000000) { // 10MB limit
    echo json_encode(['success' => false, 'message' => 'File is too large (Max 10MB).']);
    exit;
}

// Create unique filename and path
$file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$file_name = uniqid('paper_', true) . '.' . $file_ext;
$target_path = $upload_dir . $file_name;
$db_path = 'uploads/research/' . $file_name; // Path to store in DB

if (!move_uploaded_file($file_tmp_name, $target_path)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
    exit;
}

// 3. Text Extraction (Simulated)
// !! In production, you would use a library like smalot/pdfparser !!
// require '../vendor/autoload.php';
// $parser = new \Smalot\PdfParser\Parser();
// $pdf = $parser->parseFile($target_path);
// $text = $pdf->getText();
$text = "Simulated text for: " . $title . ". " . 
        "This paper explores advanced machine learning models and quantum computing. " .
        "The primary focus is on neural network optimization and data structures.";

// --- 4. FIRST AI CALL: Get Summary & Keywords ---
$ai_summary = 'AI summary failed to generate.';
$ai_keywords_string = ''; // We will store keywords as "keyword1, keyword2, keyword3"

try {
    $prompt1 = "You are a research assistant. Read the following text from a research paper.
    Respond in JSON format with two keys: 'summary' and 'keywords'.
    - 'summary': A concise 3-sentence summary.
    - 'keywords': A comma-separated list of the 7-10 most important keywords.
    
    Text: " . substr($text, 0, 4000);

    $response_json = getOpenAIChatResponse($prompt1);
    $response_data = json_decode($response_json, true);

    if ($response_data && isset($response_data['summary']) && isset($response_data['keywords'])) {
        $ai_summary = $response_data['summary'];
        $ai_keywords_string = $response_data['keywords'];
    } else {
        // Fallback for non-JSON response
        $ai_summary = $response_json;
    }

} catch (Exception $e) {
    error_log("AI Summary Error: " . $e->getMessage());
}

// --- 5. Save to Database (First) ---
$new_paper_id = 0;
try {
    $stmt = $pdo->prepare("
        INSERT INTO research_papers (user_id, title, file_path, ai_summary, ai_keywords, is_approved)
        VALUES (?, ?, ?, ?, ?, 0)
    ");
    // We set is_approved to 0, so it waits for admin approval
    $stmt->execute([$user_id, $title, $db_path, $ai_summary, $ai_keywords_string]);
    $new_paper_id = $pdo->lastInsertId();

    // --- NEW: Grant 'First Research' achievement ---
    grant_achievement($pdo, $user_id, 'First Research');

} catch (PDOException $e) {
    error_log("Research DB Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error. Could not save paper.']);
    exit;
}

// --- 6. SECOND AI CALL: Find Related Fields ---
if (empty($ai_keywords_string)) {
    // Can't find collaborators without keywords
    echo json_encode(['success' => true, 'message' => 'Upload successful! AI summary generated.', 'collaborators' => []]);
    exit;
}

$collaborators = [];
try {
    $prompt2 = "Based on the following keywords: [{$ai_keywords_string}], " .
               "list 5 highly related or complementary research fields or topics. " .
               "Return only a comma-separated list. Example: Topic A, Topic B, Topic C";
    
    $related_topics_string = getOpenAIChatResponse($prompt2);
    $related_topics = explode(',', $related_topics_string);

    if (empty($related_topics)) {
        throw new Exception("AI did not return related topics.");
    }

    // --- 7. Database Search for Collaborators ---
    $sql_params = [];
    $sql_where = [];

    foreach ($related_topics as $topic) {
        $topic = trim($topic);
        if (!empty($topic)) {
            $sql_where[] = "ai_keywords LIKE ?";
            $sql_params[] = "%$topic%";
        }
    }

    if (empty($sql_where)) {
        throw new Exception("No valid topics to search.");
    }

    // Find users (not self) who have papers with these keywords
    $search_sql = "
        SELECT DISTINCT u.user_id, u.full_name
        FROM users u
        JOIN research_papers r ON u.user_id = r.user_id
        WHERE r.is_approved = 1 
          AND r.user_id != ? 
          AND (" . implode(' OR ', $sql_where) . ")
        LIMIT 5
    ";
    array_unshift($sql_params, $user_id); // Add user_id to the beginning for the '!=' check
    
    $collab_stmt = $pdo->prepare($search_sql);
    $collab_stmt->execute($sql_params);
    $collaborators = $collab_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Collaborator Finder Error: " . $e->getMessage());
    // Don't fail the whole request, just return no collaborators
}

// --- 8. Final Response ---
echo json_encode([
    'success' => true, 
    'message' => 'Upload successful! Your paper is pending admin approval.',
    'collaborators' => $collaborators
]);
exit;
?>