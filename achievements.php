<?php
// This is achievements.php
include('includes/header.php');

// --- 1. Security (Must be logged in) ---
if (!isLoggedIn()) {
    header('Location: login.php?message=Please log in to view your achievements.');
    exit;
}
$user_id = $_SESSION['user_id'];

// --- 2. Fetch All Possible Achievements & User's Progress ---
try {
    // This query gets ALL achievements and joins the user_achievements table
    // 'date_earned' will be NULL if the user hasn't earned it.
    $stmt = $pdo->prepare("
        SELECT 
            a.name, a.description, a.icon, a.points,
            ua.date_earned
        FROM achievements a
        LEFT JOIN user_achievements ua ON a.achievement_id = ua.achievement_id AND ua.user_id = ?
        ORDER BY a.points ASC
    ");
    $stmt->execute([$user_id]);
    $all_achievements = $stmt->fetchAll();

    // --- 3. Fetch Leaderboard (Top 10 Users) ---
    $leader_stmt = $pdo->query("
        SELECT full_name, username, points, profile_pic 
        FROM users 
        ORDER BY points DESC 
        LIMIT 10
    ");
    $leaderboard = $leader_stmt->fetchAll();

    // --- 4. Fetch User's Rank
    // This is a more complex query to find the user's rank
    $rank_query = $pdo->query("SET @rank=0;"); // Initialize rank variable
    $rank_stmt = $pdo->prepare("
        SELECT rank FROM (
            SELECT user_id, @rank:=@rank+1 AS rank 
            FROM users 
            ORDER BY points DESC
        ) ranked_users 
        WHERE user_id = ?
    ");
    $rank_stmt->execute([$user_id]);
    $user_rank = $rank_stmt->fetchColumn();

} catch (PDOException $e) {
    $all_achievements = [];
    $leaderboard = [];
    $user_rank = 'N/A';
    error_log("Achievements Page Error: " . $e->getMessage());
}

?>

<div class="relative bg-gradient-to-r from-yellow-700 to-orange-600 text-white py-24 px-6 text-center">
    
    <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-down">Achievements & Leaderboard</h1>
    <p class="text-lg md:text-xl text-yellow-200" data-aos="fade-up" data-aos-delay="100">Track your progress, earn badges, and see how you stack up!</p>
</div>

<div class="container mx-auto px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
        <div class="lg:col-span-2 space-y-8">
            <h2 class="text-3xl font-bold">Your Badge Collection</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($all_achievements as $badge): ?>
                    <?php $is_earned = !is_null($badge['date_earned']); ?>
                    
                    <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center <?php echo $is_earned ? 'opacity-100' : 'opacity-40'; ?>" data-aos="fade-up">
                        <i class="<?php echo htmlspecialchars($badge['icon']); ?> text-5xl <?php echo $is_earned ? 'text-yellow-500' : 'text-gray-400'; ?> mb-4"></i>
                        <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($badge['name']); ?></h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-2"><?php echo htmlspecialchars($badge['description']); ?></p>
                        <span class="inline-block bg-yellow-500/20 text-yellow-300 text-xs font-bold px-3 py-1 rounded-full">
                            +<?php echo htmlspecialchars($badge['points']); ?> PTS
                        </span>
                        <?php if ($is_earned): ?>
                            <p class="text-xs text-green-500 mt-2">Earned on <?php echo date('M d, Y', strtotime($badge['date_earned'])); ?></p>
                        <?php else: ?>
                            <p class="text-xs text-gray-500 mt-2">Locked</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="space-y-8">
            <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg sticky top-24">
                <h2 class="text-2xl font-semibold mb-4 text-center">Your Rank</h2>
                <div class="text-center mb-6">
                    <p class="text-5xl font-bold text-blue-500">#<?php echo $user_rank; ?></p>
                    <p class="text-lg text-gray-500 dark:text-gray-400">Total Points: <?php echo $pdo->query("SELECT points FROM users WHERE user_id=$user_id")->fetchColumn(); ?></p>
                </div>

                <h2 class="text-2xl font-semibold mb-4">Leaderboard</h2>
                <ul class="space-y-4">
                    <?php foreach ($leaderboard as $index => $player): ?>
                        <li class="flex items-center space-x-4">
                            <span class="text-lg font-bold w-6 text-gray-500 dark:text-gray-400"><?php echo $index + 1; ?>.</span>
                            <img src="assets/images/<?php echo htmlspecialchars($player['profile_pic']); ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover">
                            <div class="flex-1">
                                <p class="font-semibold"><?php echo htmlspecialchars($player['full_name']); ?></p>
                                <p class="text-sm text-gray-400">@<?php echo htmlspecialchars($player['username']); ?></p>
                            </div>
                            <span class="font-bold text-blue-500"><?php echo htmlspecialchars($player['points']); ?> PTS</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    
    </div>
</div>

<?php include('includes/footer.php'); ?>