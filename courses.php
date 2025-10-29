<?php
// This is courses.php
include('includes/header.php');

// --- Handle Filtering/Search ---
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$params = [];
$sql = "SELECT c.*, u.full_name AS faculty_name 
        FROM courses c 
        LEFT JOIN users u ON c.faculty_id = u.user_id 
        WHERE 1=1";

if (!empty($search_term)) {
    $sql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
    $params[] = "%$search_term%";
    $params[] = "%$search_term%";
}

if (!empty($category)) {
    $sql .= " AND c.category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY c.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
    error_log("Courses Page Error: " . $e->getMessage());
}

// Fetch categories for filter dropdown
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM courses WHERE category IS NOT NULL");
$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);

?>

<div classclass="relative bg-gradient-to-r from-blue-900 to-purple-800 text-white py-24 px-6 text-center">
    
    <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-down">University Course Catalog</h1>
    <p class="text-lg md:text-xl text-blue-200" data-aos="fade-up" data-aos-delay="100">Find your next challenge and expand your knowledge.</p>
</div>

<div class="container mx-auto px-6 py-12">

    <div class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md glassmorphism" data-aos="fade-up">
        <form action="courses.php" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search Courses</label>
                <input type="text" name="search" id="search" placeholder="e.g., 'Quantum Physics'" value="<?php echo htmlspecialchars($search_term); ?>" class="mt-1 block w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filter by Category</label>
                <select name="category" id="category" class="mt-1 block w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category == $cat) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="md:mt-6">
                <button type="submit" class="w-full bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 transition">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (empty($courses)): ?>
            <p class="text-gray-500 dark:text-gray-400 md:col-span-3 text-center">No courses found matching your criteria. Try a different search.</p>
        <?php else: ?>
            <?php foreach ($courses as $course): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden glassmorphism flex flex-col" data-aos="zoom-in-up">
                    <img src="<?php echo htmlspecialchars($course['cover_image'] ? $course['cover_image'] : 'https://images.unsplash.com/photo-1543269865-cbf427c98b4a?w=600'); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="w-full h-48 object-cover">
                    
                    <div class="p-6 flex-grow">
                        <span class="inline-block bg-purple-500/20 text-purple-400 text-xs font-semibold px-3 py-1 rounded-full mb-2">
                            <?php echo htmlspecialchars($course['category']); ?>
                        </span>
                        <h3 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                            By <?php echo htmlspecialchars($course['faculty_name'] ? $course['faculty_name'] : 'Smart Uni-Verse'); ?>
                        </p>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                            <?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...
                        </p>
                    </div>
                    
                    <div class="p-6 pt-0">
                        <form action="enroll.php" method="POST">
                            <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                            <button type="submit" class="w-full bg-blue-500 text-white px-5 py-3 rounded-lg font-semibold hover:bg-blue-600 transition duration-300">
                                Enroll Now
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>