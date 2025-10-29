<?php
// This is admin/manage_courses.php
include('../includes/header.php'); // Note the ../

// --- 1. ADMIN SECURITY CHECK ---
if (!isAdmin()) {
    header('Location: ../login.php?message=Access Denied.');
    exit;
}

$message = '';
$error = '';
$edit_course = null;

// --- 2. Fetch Faculty for Dropdown ---
try {
    $faculty_stmt = $pdo->prepare("SELECT user_id, full_name FROM users WHERE role = 'faculty'");
    $faculty_stmt->execute();
    $faculty_list = $faculty_stmt->fetchAll();
} catch (PDOException $e) {
    $faculty_list = [];
    $error = "Failed to fetch faculty list: " . $e->getMessage();
}

// --- 3. Handle POST (Create or Update) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $faculty_id = $_POST['faculty_id'];
    $course_id = $_POST['course_id']; // Hidden field, empty for 'create'
    $action = $_POST['action'];

    // --- File Upload Logic ---
    $cover_image_path = $_POST['existing_image']; // Default to old image
    
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/courses/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = uniqid('course_', true) . '.' . pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target_path)) {
            $cover_image_path = 'uploads/courses/' . $file_name;
        } else {
            $error = "Failed to upload new cover image.";
        }
    }
    // --- End File Upload ---

    if (empty($error)) {
        try {
            if ($action == 'create') {
                $stmt = $pdo->prepare("INSERT INTO courses (title, description, faculty_id, category, cover_image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $faculty_id, $category, $cover_image_path]);
                $message = "Course created successfully!";
            } elseif ($action == 'update' && !empty($course_id)) {
                $stmt = $pdo->prepare("UPDATE courses SET title = ?, description = ?, faculty_id = ?, category = ?, cover_image = ? WHERE course_id = ?");
                $stmt->execute([$title, $description, $faculty_id, $category, $cover_image_path, $course_id]);
                $message = "Course updated successfully!";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// --- 4. Handle GET (Delete or Edit) ---
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Handle Delete
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        try {
            $id = $_GET['id'];
            $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id = ?");
            $stmt->execute([$id]);
            $message = "Course deleted successfully.";
        } catch (PDOException $e) {
            $error = "Could not delete course. It may be linked to enrollments.";
        }
    }

    // Handle Edit (Load data into form)
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        try {
            $id = $_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
            $stmt->execute([$id]);
            $edit_course = $stmt->fetch();
        } catch (PDOException $e) {
            $error = "Could not fetch course for editing.";
        }
    }
}

// --- 5. Fetch All Courses to Display ---
try {
    $stmt = $pdo->query("SELECT c.*, u.full_name AS faculty_name FROM courses c LEFT JOIN users u ON c.faculty_id = u.user_id ORDER BY c.title");
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
    $error = "Could not fetch courses: " . $e->getMessage();
}

?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold mb-8">Admin: Manage Courses</h1>

    <div class="mb-6">
        <a href="index.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Dashboard</a>
        <a href="manage_users.php" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Manage Users</a>
    </div>

    <?php if ($message): ?>
        <div class="bg-green-500/20 text-green-300 p-3 rounded-lg mb-4"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-red-500/20 text-red-300 p-3 rounded-lg mb-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg mb-12">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $edit_course ? 'Edit Course' : 'Create New Course'; ?></h2>
        
        <form action="manage_courses.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo $edit_course ? 'update' : 'create'; ?>">
            <input type="hidden" name="course_id" value="<?php echo $edit_course ? $edit_course['course_id'] : ''; ?>">
            <input type="hidden" name="existing_image" value="<?php echo $edit_course ? $edit_course['cover_image'] : ''; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Course Title</label>
                    <input type="text" name="title" id="title" value="<?php echo $edit_course ? htmlspecialchars($edit_course['title']) : ''; ?>" required class="mt-1 block w-full input-style">
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <input type="text" name="category" id="category" value="<?php echo $edit_course ? htmlspecialchars($edit_course['category']) : ''; ?>" placeholder="e.g., Data Science" required class="mt-1 block w-full input-style">
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" id="description" rows="4" required class="mt-1 block w-full input-style"><?php echo $edit_course ? htmlspecialchars($edit_course['description']) : ''; ?></textarea>
                </div>
                <div>
                    <label for="faculty_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Faculty/Instructor</label>
                    <select name="faculty_id" id="faculty_id" required class="mt-1 block w-full input-style appearance-none">
                        <option value="">Select Instructor</option>
                        <?php foreach ($faculty_list as $faculty): ?>
                            <option value="<?php echo $faculty['user_id']; ?>" <?php echo ($edit_course && $edit_course['faculty_id'] == $faculty['user_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($faculty['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cover Image</label>
                    <input type="file" name="cover_image" id="cover_image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                    <?php if ($edit_course && $edit_course['cover_image']): ?>
                        <p class="text-xs text-gray-500 mt-1">Current: <?php echo $edit_course['cover_image']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-6 text-right">
                <?php if ($edit_course): ?>
                    <a href="manage_courses.php" class="bg-gray-600/50 text-white px-5 py-2 rounded-lg hover:bg-gray-700/50 transition mr-2">Cancel Edit</a>
                <?php endif; ?>
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-600 transition">
                    <?php echo $edit_course ? 'Save Changes' : 'Create Course'; ?>
                </button>
            </div>
        </form>
    </div>

    <h2 class="text-2xl font-semibold mb-4">Existing Courses</h2>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden glassmorphism">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-100 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Title</th>
                        <th class="px-6 py-3 font-semibold">Category</th>
                        <th class="px-6 py-3 font-semibold">Instructor</th>
                        <th class="px-6 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($course['title']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($course['category']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($course['faculty_name'] ? $course['faculty_name'] : 'N/A'); ?></td>
                            <td class="px-6 py-4 space-x-2">
                                <a href="manage_courses.php?action=edit&id=<?php echo $course['course_id']; ?>" class="text-blue-500 hover:text-blue-400"><i class="fas fa-edit"></i> Edit</a>
                                <a href="manage_courses.php?action=delete&id=<?php echo $course['course_id']; ?>" class="text-red-500 hover:text-red-400" onclick="return confirm('Are you sure you want to delete this course?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Simple style for form inputs */
.input-style {
    @apply border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg;
}
</style>

<?php include('../includes/footer.php'); ?>