<?php
// This is enroll.php
include('includes/config.php');

// 1. Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php?message=You must be logged in to enroll.');
    exit;
}

// 2. Check if it's a POST request and course_id is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['course_id'])) {
    
    $user_id = $_SESSION['user_id'];
    $course_id = $_POST['course_id'];

    try {
        // 3. Check if already enrolled
        $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);
        
        if ($stmt->rowCount() > 0) {
            // Already enrolled, just redirect to dashboard
            header('Location: dashboard.php?message=You are already enrolled in this course.');
            exit;
        }

        // 4. Not enrolled, so enroll them
        $insert_stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, progress) VALUES (?, ?, 0)");
        $insert_stmt->execute([$user_id, $course_id]);

        // --- NEW ---
        // Grant the 'First Enrollment' achievement
        // The grant_achievement function will automatically check if they already have it.
        grant_achievement($pdo, $user_id, 'First Enrollment');
        // --- END NEW ---

        // 5. Redirect to dashboard with success
        header('Location: dashboard.php?message=Enrollment successful!');
        exit;

    } catch (PDOException $e) {
        // Handle database errors
        error_log("Enrollment Error: " . $e->getMessage());
        header('Location: courses.php?message=An error occurred. Please try again.');
        exit;
    }

} else {
    // Not a POST request, redirect to courses
    header('Location: courses.php');
    exit;
}
?>