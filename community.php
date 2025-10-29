<?php
// This is community.php
include('includes/header.php');

// Security: Must be logged in to view community
if (!isLoggedIn()) {
    header('Location: login.php?message=Please log in to join the community.');
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// --- Handle New Thread Creation (from modal) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'create_thread') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = $_POST['category_id'];

    if (empty($title) || empty($content) || empty($category_id)) {
        $errors[] = "All fields are required to start a thread.";
    } else {
        try {
            // 1. Start a transaction
            $pdo->beginTransaction();

            // 2. Create the thread
            $stmt = $pdo->prepare("INSERT INTO forum_threads (category_id, user_id, title) VALUES (?, ?, ?)");
            $stmt->execute([$category_id, $user_id, $title]);
            $thread_id = $pdo->lastInsertId();

            // 3. Create the first post (the original post)
            $post_stmt = $pdo->prepare("INSERT INTO forum_posts (thread_id, user_id, content) VALUES (?, ?, ?)");
            $post_stmt->execute([$thread_id, $user_id, $content]);

            // 4. Commit the transaction
            $pdo->commit();
            $success = "Thread created successfully! You are being redirected...";
            
            // Redirect to the new thread
            header("refresh:2;url=thread.php?id=" . $thread_id);

        } catch (PDOException $e) {
            $pdo->rollBack(); // Undo changes on error
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}


// --- Fetch Categories and Recent Threads ---
try {
    $cat_stmt = $pdo->query("SELECT * FROM forum_categories");
    $categories = $cat_stmt->fetchAll();

    // Fetch 20 most recent threads with author and post count
    $thread_stmt = $pdo->query("
        SELECT 
            t.thread_id, t.title, t.created_at,
            u.full_name, u.username,
            c.name AS category_name,
            (SELECT COUNT(*) FROM forum_posts p WHERE p.thread_id = t.thread_id) AS post_count
        FROM forum_threads t
        JOIN users u ON t.user_id = u.user_id
        JOIN forum_categories c ON t.category_id = c.category_id
        ORDER BY t.created_at DESC
        LIMIT 20
    ");
    $threads = $thread_stmt->fetchAll();

} catch (PDOException $e) {
    $categories = [];
    $threads = [];
    $errors[] = "Error fetching community data: " . $e->getMessage();
}
?>

<div class="relative bg-gradient-to-r from-indigo-700 to-purple-800 text-white py-24 px-6 text-center">
    
    <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-down">Community Hub</h1>
    <p class="text-lg md:text-xl text-blue-200" data-aos="fade-up" data-aos-delay="100">Connect, share, and learn with your peers.</p>
</div>

<div class="container mx-auto px-6 py-12">

    <?php if ($success): ?>
        <div class="bg-green-500/20 text-green-300 p-3 rounded-lg mb-6 text-center"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="bg-red-500/20 text-red-300 p-3 rounded-lg mb-6">
            <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    
        <div class="lg:col-span-1 space-y-6">
            <button id="new-thread-btn" class="w-full bg-blue-500 text-white px-6 py-4 rounded-lg font-semibold hover:bg-blue-600 transition text-lg">
                <i class="fas fa-plus"></i> Start a New Thread
            </button>
            
            <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg sticky top-24">
                <h2 class="text-2xl font-semibold mb-4">Categories</h2>
                <ul class="space-y-3">
                    <?php foreach ($categories as $cat): ?>
                        <li class="border-b border-gray-200 dark:border-gray-700 pb-2">
                            <a href="#" class="flex items-center space-x-3 hover:text-blue-500">
                                <i class="<?php echo htmlspecialchars($cat['icon']); ?> w-6 text-blue-500"></i>
                                <span><?php echo htmlspecialchars($cat['name']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="lg:col-span-3 space-y-4">
            <h2 class="text-3xl font-bold">Recent Activity</h2>
            <?php if (empty($threads)): ?>
                <p class="text-gray-500 dark:text-gray-400">No threads found. Be the first to start one!</p>
            <?php else: ?>
                <?php foreach ($threads as $thread): ?>
                    <div class="glassmorphism bg-white dark:bg-gray-800 p-5 rounded-lg shadow-md flex items-center space-x-4">
                        <div class="text-center w-16">
                            <p class="text-2xl font-bold"><?php echo $thread['post_count'] - 1; ?></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">replies</p>
                        </div>
                        <div class="flex-1">
                            <span class="inline-block bg-purple-500/20 text-purple-400 text-xs font-semibold px-3 py-1 rounded-full mb-2">
                                <?php echo htmlspecialchars($thread['category_name']); ?>
                            </span>
                            <a href="thread.php?id=<?php echo $thread['thread_id']; ?>" class="block text-xl font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                <?php echo htmlspecialchars($thread['title']); ?>
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Started by <?php echo htmlspecialchars($thread['full_name']); ?> (@<?php echo htmlspecialchars($thread['username']); ?>) on <?php echo date('M d, Y', strtotime($thread['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="new-thread-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-2xl" data-aos="zoom-in">
        <h2 class="text-2xl font-bold mb-6">Start a New Discussion</h2>
        <form action="community.php" method="POST">
            <input type="hidden" name="action" value="create_thread">
            
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                <input type="text" name="title" id="title" required class="mt-1 block w-full input-style">
            </div>
            
            <div class="mb-4">
                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                <select name="category_id" id="category_id" required class="mt-1 block w-full input-style appearance-none">
                    <option value="">Select a category...</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Post</label>
                <textarea name="content" id="content" rows="6" required placeholder="Write your question or discussion topic here..." class="mt-1 block w-full input-style"></textarea>
            </div>
            
            <div class="flex justify-end space-x-4">
                <button type="button" id="modal-close-btn" class="bg-gray-600/50 text-white px-5 py-2 rounded-lg hover:bg-gray-700/50 transition">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 transition">
                    Post Thread
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.input-style {
    @apply border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('new-thread-modal');
    const openBtn = document.getElementById('new-thread-btn');
    const closeBtn = document.getElementById('modal-close-btn');

    openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
    closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
    });
});
</script>

<?php include('includes/footer.php'); ?>