<?php
// This is thread.php
include('includes/header.php');

// Security: Must be logged in
if (!isLoggedIn()) {
    header('Location: login.php?message=Please log in to view threads.');
    exit;
}
$user_id = $_SESSION['user_id'];

// Check for ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: community.php');
    exit;
}
$thread_id = $_GET['id'];

$errors = [];
$success = '';

// --- Handle New Reply ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'create_post') {
    $content = trim($_POST['content']);
    
    if (empty($content)) {
        $errors[] = "Reply content cannot be empty.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO forum_posts (thread_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$thread_id, $user_id, $content]);
            $success = "Reply posted successfully!";
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}


// --- Fetch Thread Info & All Posts ---
try {
    // 1. Get Thread Title
    $thread_stmt = $pdo->prepare("SELECT * FROM forum_threads WHERE thread_id = ?");
    $thread_stmt->execute([$thread_id]);
    $thread = $thread_stmt->fetch();

    if (!$thread) {
        header('Location: community.php'); // Thread doesn't exist
        exit;
    }

    // 2. Get all posts in this thread, with author info
    $posts_stmt = $pdo->prepare("
        SELECT 
            p.*, 
            u.full_name, u.username, u.profile_pic, u.role
        FROM forum_posts p
        JOIN users u ON p.user_id = u.user_id
        WHERE p.thread_id = ?
        ORDER BY p.created_at ASC
    ");
    $posts_stmt->execute([$thread_id]);
    $posts = $posts_stmt->fetchAll();

    $original_post = $posts[0]; // The first post is the OP

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container mx-auto px-6 py-12 max-w-4xl">

    <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
        <a href="community.php" class="hover:underline text-blue-500">Community</a> &gt; 
        <span><?php echo htmlspecialchars($thread['title']); ?></span>
    </div>

    <h1 class="text-3xl md:text-4xl font-bold mb-6"><?php echo htmlspecialchars($thread['title']); ?></h1>
    
    <?php if ($success): ?>
        <div class="bg-green-500/20 text-green-300 p-3 rounded-lg mb-6 text-center"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="bg-red-500/20 text-red-300 p-3 rounded-lg mb-6">
            <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="space-y-6">
        <?php foreach ($posts as $index => $post): ?>
            <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg flex space-x-5" id="post-<?php echo $post['post_id']; ?>">
                
                <div class="flex-shrink-0 w-24 text-center">
                    <img src="assets/images/<?php echo htmlspecialchars($post['profile_pic']); ?>" alt="Profile" class="w-16 h-16 rounded-full mx-auto object-cover">
                    <p class="font-semibold mt-2"><?php echo htmlspecialchars($post['full_name']); ?></p>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold
                        <?php echo ($post['role'] == 'admin') ? 'bg-red-500/20 text-red-300' : ''; ?>
                        <?php echo ($post['role'] == 'faculty') ? 'bg-blue-500/20 text-blue-300' : ''; ?>
                        <?php echo ($post['role'] == 'student') ? 'bg-green-500/20 text-green-300' : ''; ?>
                    ">
                        <?php echo ucfirst($post['role']); ?>
                    </span>
                    <?php if ($index == 0): ?>
                        <span class="block text-xs text-blue-500 font-bold mt-1">AUTHOR</span>
                    <?php endif; ?>
                </div>
                
                <div class="flex-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        Posted on <?php echo date('M d, Y @ h:i A', strtotime($post['created_at'])); ?>
                    </p>
                    <div class="prose dark:prose-invert max-w-none">
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg mt-10">
        <h2 class="text-2xl font-semibold mb-4">Reply to this Thread</h2>
        <form action="thread.php?id=<?php echo $thread_id; ?>" method="POST">
            <input type="hidden" name="action" value="create_post">
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Reply</label>
                <textarea name="content" id="content" rows="6" required class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg"></textarea>
            </div>
            <div class="text-right">
                <button type="submit" class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">
                    Post Reply
                </button>
            </div>
        </form>
    </div>
</div>

<?php include('includes/footer.php'); ?>