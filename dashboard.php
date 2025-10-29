<?php
// This is dashboard.php
include('includes/header.php');

// --- SECURITY: Check if user is logged in ---
if (!isLoggedIn()) {
    header('Location: login.php?message=Please log in to access your dashboard.');
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// --- Fetch Dashboard Data ---

// 1. Fetch Enrolled Courses
try {
    $stmt = $pdo->prepare("
        SELECT c.title, c.cover_image, e.progress 
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        WHERE e.user_id = ?
        ORDER BY e.enrolled_at DESC
        LIMIT 6
    ");
    $stmt->execute([$user_id]);
    $enrolled_courses = $stmt->fetchAll();
} catch (PDOException $e) {
    $enrolled_courses = [];
    error_log("Dashboard Error (Courses): " . $e->getMessage());
}

// 2. Fetch Payment Status (Last 3)
try {
    $stmt = $pdo->prepare("
        SELECT amount, status, created_at 
        FROM payments
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 3
    ");
    $stmt->execute([$user_id]);
    $payments = $stmt->fetchAll();
} catch (PDOException $e) {
    $payments = [];
    error_log("Dashboard Error (Payments): " . $e->getMessage());
}

?>

<div class="container mx-auto px-6 py-12">
    <div class="mb-10" data-aos="fade-down">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white">Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-purple-600"><?php echo htmlspecialchars($full_name); ?>!</span></h1>
        <p class="text-lg text-gray-500 dark:text-gray-400 mt-2">Here's your academic snapshot. Let's get learning.</p>
    </div>

    

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
        <div class="lg:col-span-2 space-y-8">
        
            <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg" data-aos="fade-up">
                <h2 class="text-2xl font-semibold mb-4">My Courses</h2>
                
                <?php if (empty($enrolled_courses)): ?>
                    <p class="text-gray-500 dark:text-gray-400">You are not enrolled in any courses yet.</p>
                    <a href="courses.php" class="mt-4 inline-block bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 transition">Explore Courses</a>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($enrolled_courses as $course): ?>
                            <div class="bg-gray-100 dark:bg-gray-700/50 p-4 rounded-lg flex items-center space-x-4">
                                <img src="<?php echo htmlspecialchars($course['cover_image'] ? $course['cover_image'] : 'https://via.placeholder.com/150'); ?>" alt="Course" class="w-16 h-16 rounded-lg object-cover">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-lg"><?php echo htmlspecialchars($course['title']); ?></h4>
                                    <div class="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-2.5 mt-2">
                                        <div class="bg-blue-500 h-2.5 rounded-full" style="width: <?php echo $course['progress']; ?>%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo $course['progress']; ?>% Complete</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="100">
                <h2 class="text-2xl font-semibold mb-4">AI Scheduler</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Let our AI suggest a study plan based on your deadlines.</p>
                <div class="p-4 bg-gray-100 dark:bg-gray-700/50 rounded-lg">
                    <p class="font-semibold">Today's Focus:</p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 mt-2">
                        <li><span class="font-semibold text-blue-500">18:00 - 19:00:</span> Review 'Intro to Quantum' notes.</li>
                        <li><span class="font-semibold text-blue-500">19:00 - 19:30:</span> Generate AI quiz on 'Data Structures'.</li>
                    </ul>
                    </div>
                <button class="mt-4 bg-purple-500 text-white px-5 py-2 rounded-lg hover:bg-purple-600 transition">
                    <i class="fas fa-magic"></i> Generate New Plan
                </button>
            </div>

        </div>
        
        <div class="space-y-8">
            
            <?php
                // Fetch user's points and 3 recent badges
                $points_stmt = $pdo->prepare("SELECT points FROM users WHERE user_id = ?");
                $points_stmt->execute([$user_id]);
                $user_points = $points_stmt->fetchColumn();
                
                $badges_stmt = $pdo->prepare("
                    SELECT a.icon, a.name 
                    FROM user_achievements ua
                    JOIN achievements a ON ua.achievement_id = a.achievement_id
                    WHERE ua.user_id = ?
                    ORDER BY ua.date_earned DESC
                    LIMIT 3
                ");
                $badges_stmt->execute([$user_id]);
                $recent_badges = $badges_stmt->fetchAll();
            ?>
            <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="100">
                <h2 class="text-2xl font-semibold mb-4">My Progress</h2>
                <div class="text-center mb-4">
                    <p class="text-lg text-gray-500 dark:text-gray-400">Total Points</p>
                    <p class="text-5xl font-bold text-yellow-500"><?php echo $user_points; ?></p>
                </div>
                <h4 class="font-semibold mb-2 text-center">Recent Badges</h4>
                <div class="flex justify-center space-x-4">
                    <?php if (empty($recent_badges)): ?>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Start learning to earn badges!</p>
                    <?php else: ?>
                        <?php foreach ($recent_badges as $badge): ?>
                            <i class="<?php echo htmlspecialchars($badge['icon']); ?> text-3xl text-yellow-500" title="<?php echo htmlspecialchars($badge['name']); ?>"></i>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <a href="achievements.php" class="mt-4 w-full block text-center bg-gray-600/50 text-white px-5 py-2 rounded-lg hover:bg-gray-700/50 transition">
                    View All Achievements
                </a>
            </div>
            <?php
                $recent_threads = $pdo->query("
                    SELECT t.thread_id, t.title 
                    FROM forum_threads t
                    ORDER BY t.created_at DESC 
                    LIMIT 3
                ")->fetchAll();
            ?>
            <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="150">
                <h2 class="text-2xl font-semibold mb-4">Recent Discussions</h2>
                <ul class="space-y-3">
                    <?php if (empty($recent_threads)): ?>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No discussions yet.</p>
                    <?php else: ?>
                        <?php foreach ($recent_threads as $thread): ?>
                            <li class="border-b border-gray-200 dark:border-gray-700 pb-2 last:border-b-0">
                                <a href="thread.php?id=<?php echo $thread['thread_id']; ?>" class="text-blue-500 hover:underline">
                                    <?php echo htmlspecialchars($thread['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <a href="community.php" class="mt-4 w-full block text-center bg-gray-600/50 text-white px-5 py-2 rounded-lg hover:bg-gray-700/50 transition">
                    View All Discussions
                </a>
            </div>
            <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="200">
                <h2 class="text-2xl font-semibold mb-4">Smart Assistant</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Have a quick question? Ask your AI tutor.</p>
                <a href="ai_assistant.php" class="w-full block text-center bg-blue-500 text-white px-5 py-3 rounded-lg hover:bg-blue-600 transition">
                    <i class="fas fa-robot"></i> Open AI Tutor
                </a>
            </div>
            
            <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="250">
                <h2 class="text-2xl font-semibold mb-4">Payment History</h2>
                <ul class="space-y-3">
                    <?php if (empty($payments)): ?>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No recent payments found.</p>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <li class="flex justify-between items-center text-sm">
                                <div>
                                    <p class="font-semibold">$<?php echo htmlspecialchars($payment['amount']); ?></p>
                                    <p class="text-xs text-gray-400"><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></p>
                                </div>
                                <?php if ($payment['status'] == 'completed'): ?>
                                    <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-300 text-xs font-bold">Completed</span>
                                <?php elseif ($payment['status'] == 'pending'): ?>
                                    <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-300 text-xs font-bold">Pending</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 rounded-full bg-red-500/20 text-red-300 text-xs font-bold">Failed</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <a href="payments.php" class="mt-4 w-full block text-center bg-gray-600/50 text-white px-5 py-2 rounded-lg hover:bg-gray-700/50 transition">
                    View All Payments
                </a>
            </div>

        </div>
    
    </div>

</div>

<?php include('includes/footer.php'); ?>