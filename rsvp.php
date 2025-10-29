<?php
// This is rsvp.php
include('includes/config.php');

// 1. Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php?message=You must be logged in to RSVP.');
    exit;
}

// 2. Check if it's a POST request and event_id is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_id'])) {
    
    $user_id = $_SESSION['user_id'];
    $event_id = $_POST['event_id'];

    try {
        // 3. Check if already RSVP'd (to prevent double-submission)
        $stmt = $pdo->prepare("SELECT * FROM event_rsvps WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$user_id, $event_id]);
        
        if ($stmt->rowCount() > 0) {
            header('Location: events.php?message=You have already RSVP\'d to this event.');
            exit;
        }

        // 4. Not RSVP'd, so add them
        $insert_stmt = $pdo->prepare("INSERT INTO event_rsvps (user_id, event_id) VALUES (?, ?)");
        $insert_stmt->execute([$user_id, $event_id]);

        // --- NEW ---
        // Grant the 'First Event' achievement
        grant_achievement($pdo, $user_id, 'First Event');
        // --- END NEW ---

        // 5. Redirect back with success
        header('Location: events.php?message=RSVP successful! We saved you a spot.');
        exit;

    } catch (PDOException $e) {
        // Handle database errors
        error_log("RSVP Error: " . $e->getMessage());
        header('Location: events.php?message=An error occurred. Please try again.');
        exit;
    }

} else {
    // Not a POST request, redirect
    header('Location: events.php');
    exit;
}
?>