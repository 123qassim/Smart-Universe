<?php
// This is admin/manage_users.php
include('../includes/header.php'); // Note the ../

// --- 1. ADMIN SECURITY CHECK ---
if (!isAdmin()) {
    header('Location: ../login.php?message=Access Denied.');
    exit;
}

$message = '';
$error = '';

// --- 2. Handle Actions (POST for Update, GET for Delete) ---

// Handle POST for Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update') {
    $user_id = $_POST['user_id'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (!in_array($role, ['student', 'faculty', 'admin']) || empty($full_name) || empty($email)) {
        $error = "Invalid data provided.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $role, $user_id]);
            $message = "User (ID: $user_id) updated successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle GET for Delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Safety check: Don't let an admin delete themselves
    if ($user_id == $_SESSION['user_id']) {
        $error = "You cannot delete your own account.";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ? AND role != 'admin'");
            $stmt->execute([$user_id]);
            
            if ($stmt->rowCount() > 0) {
                $message = "User (ID: $user_id) deleted successfully.";
            } else {
                $error = "Could not delete user. (User might be an Admin or not exist).";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}


// --- 3. Fetch All Users ---
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
    $error = "Could not fetch users: " . $e->getMessage();
}

?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold mb-8">Admin: Manage Users</h1>
    
    <div class="mb-6">
        <a href="index.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Dashboard</a>
        <a href="manage_courses.php" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Manage Courses</a>
    </div>
    
    <?php if ($message): ?>
        <div class="bg-green-500/20 text-green-300 p-3 rounded-lg mb-4"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-red-500/20 text-red-300 p-3 rounded-lg mb-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden glassmorphism">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-100 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 font-semibold">ID</th>
                        <th class="px-6 py-3 font-semibold">Full Name</th>
                        <th class="px-6 py-3 font-semibold">Email</th>
                        <th class="px-6 py-3 font-semibold">Role</th>
                        <th class="px-6 py-3 font-semibold">Joined</th>
                        <th class="px-6 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="px-6 py-4"><?php echo $user['user_id']; ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    <?php echo ($user['role'] == 'admin') ? 'bg-red-500/20 text-red-300' : ''; ?>
                                    <?php echo ($user['role'] == 'faculty') ? 'bg-blue-500/20 text-blue-300' : ''; ?>
                                    <?php echo ($user['role'] == 'student') ? 'bg-green-500/20 text-green-300' : ''; ?>
                                ">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td class="px-6 py-4 space-x-2">
                                <button class="edit-btn text-blue-500 hover:text-blue-400"
                                    data-id="<?php echo $user['user_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($user['full_name']); ?>"
                                    data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                    data-role="<?php echo $user['role']; ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="manage_users.php?action=delete&id=<?php echo $user['user_id']; ?>" 
                                   class="text-red-500 hover:text-red-400" 
                                   onclick="return confirm('Are you sure you want to delete this user? This cannot be undone.')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="edit-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center hidden z-50">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md" data-aos="zoom-in">
        <h2 class="text-2xl font-bold mb-6">Edit User</h2>
        <form action="manage_users.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="user_id" id="modal-user-id">
            
            <div class="mb-4">
                <label for="modal-full-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                <input type="text" name="full_name" id="modal-full-name" required class="mt-1 block w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label for="modal-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="email" id="modal-email" required class="mt-1 block w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-6">
                <label for="modal-role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                <select name="role" id="modal-role" required class="mt-1 block w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-4">
                <button type="button" id="modal-close-btn" class="bg-gray-600/50 text-white px-5 py-2 rounded-lg hover:bg-gray-700/50 transition">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const editModal = document.getElementById('edit-modal');
    const closeModalBtn = document.getElementById('modal-close-btn');
    const editButtons = document.querySelectorAll('.edit-btn');

    // Form fields
    const modalId = document.getElementById('modal-user-id');
    const modalName = document.getElementById('modal-full-name');
    const modalEmail = document.getElementById('modal-email');
    const modalRole = document.getElementById('modal-role');

    // Open modal
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Get data from button
            const id = button.dataset.id;
            const name = button.dataset.name;
            const email = button.dataset.email;
            const role = button.dataset.role;

            // Populate form
            modalId.value = id;
            modalName.value = name;
            modalEmail.value = email;
            modalRole.value = role;
            
            editModal.classList.remove('hidden');
        });
    });

    // Close modal
    closeModalBtn.addEventListener('click', () => {
        editModal.classList.add('hidden');
    });
    
    // Close on outside click
    editModal.addEventListener('click', (e) => {
        if (e.target === editModal) {
            editModal.classList.add('hidden');
        }
    });
});
</script>

<?php include('../includes/footer.php'); ?>