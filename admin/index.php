<?php 
// This is admin/index.php (Updated)
include('../includes/header.php'); 
// Admin access control
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// --- Fetch Dashboard Stat Cards ---
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$course_count = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'completed'")->fetchColumn();
$pending_research = $pdo->query("SELECT COUNT(*) FROM research_papers WHERE is_approved = 0")->fetchColumn();

// --- Fetch AI Insight (Example) ---
$insight = "No new insights.";
try {
    $top_course = $pdo->query("
        SELECT c.title, COUNT(e.enrollment_id) AS enroll_count
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        GROUP BY c.course_id
        ORDER BY enroll_count DESC
        LIMIT 1
    ")->fetch();
    
    if ($top_course) {
        // This is a simple PHP-generated insight.
        // For AI, you would send data to your ai_helper.php
        $insight = "This month's top-performing course is **" . htmlspecialchars($top_course['title']) . "** with " . $top_course['enroll_count'] . " new enrollments.";
    }
} catch (PDOException $e) {
    // ignore
}

?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold mb-2">Admin Dashboard</h1>
    <p class="text-lg text-gray-500 dark:text-gray-400 mb-8">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>. Here's your university overview.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glassmorphism p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h3 class="text-lg text-gray-500 dark:text-gray-400">Total Users</h3>
            <p class="text-3xl font-bold"><?php echo $user_count; ?></p>
        </div>
        <div class="glassmorphism p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h3 class="text-lg text-gray-500 dark:text-gray-400">Total Courses</h3>
            <p class="text-3xl font-bold"><?php echo $course_count; ?></p>
        </div>
        <div class="glassmorphism p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h3 class="text-lg text-gray-500 dark:text-gray-400">Total Revenue</h3>
            <p class="text-3xl font-bold">$<?php echo number_format($total_revenue, 2); ?></p>
        </div>
        <div class="glassmorphism p-6 bg-yellow-500/20 text-yellow-200 rounded-lg shadow-md">
            <h3 class="text-lg text-yellow-100">Pending Research</h3>
            <p class="text-3xl font-bold"><?php echo $pending_research; ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
        <div class="lg:col-span-1 glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="manage_users.php" class="block w-full text-center bg-blue-500/20 text-blue-300 p-3 rounded-lg hover:bg-blue-500/40 transition">
                    <i class="fas fa-users-cog"></i> Manage Users
                </a>
                <a href="manage_courses.php" class="block w-full text-center bg-purple-500/20 text-purple-300 p-3 rounded-lg hover:bg-purple-500/40 transition">
                    <i class="fas fa-book"></i> Manage Courses
                </a>
                <a href="manage_events.php" class="block w-full text-center bg-green-500/20 text-green-300 p-3 rounded-lg hover:bg-green-500/40 transition">
                    <i class="fas fa-calendar-alt"></i> Manage Events
                </a>
                <a href="manage_research.php" class_="block w-full text-center bg-yellow-500/20 text-yellow-300 p-3 rounded-lg hover:bg-yellow-500/40 transition">
                    <i class="fas fa-flask"></i> Manage Research
                </a>
            </div>
        </div>
        
        <div class="lg:col-span-2 glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">AI Insight</h2>
            <div class="p-4 bg-gray-100 dark:bg-gray-700/50 rounded-lg mb-6">
                <p class="text-gray-700 dark:text-gray-200"><?php echo $insight; ?></p>
            </div>
            
            <h2 class="text-2xl font-semibold mb-4">Analytics</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-4">View detailed reports on revenue, user growth, and course engagement.</p>
            <a href="analytics.php" class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">
                <i class="fas fa-chart-line"></i> View Detailed Analytics
            </a>
        </div>
        
    </div>
</div>

<?php include('../includes/footer.php'); ?>