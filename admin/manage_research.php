<?php
// This is admin/manage_research.php
include('../includes/header.php'); // Note the ../

// --- 1. ADMIN SECURITY CHECK ---
if (!isAdmin()) {
    header('Location: ../login.php?message=Access Denied.');
    exit;
}

$message = '';
$error = '';

// --- 2. Handle GET Actions (Approve, Unapprove, Delete) ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $paper_id = $_GET['id'];
    
    try {
        if ($_GET['action'] == 'approve') {
            $stmt = $pdo->prepare("UPDATE research_papers SET is_approved = 1 WHERE paper_id = ?");
            $stmt->execute([$paper_id]);
            $message = "Paper (ID: $paper_id) has been approved and is now public.";
        
        } elseif ($_GET['action'] == 'unapprove') {
            $stmt = $pdo->prepare("UPDATE research_papers SET is_approved = 0 WHERE paper_id = ?");
            $stmt->execute([$paper_id]);
            $message = "Paper (ID: $paper_id) has been un-approved and is now hidden.";
        
        } elseif ($_GET['action'] == 'delete') {
            // First, get the file path to delete the file
            $stmt = $pdo->prepare("SELECT file_path FROM research_papers WHERE paper_id = ?");
            $stmt->execute([$paper_id]);
            $paper = $stmt->fetch();
            
            if ($paper) {
                $file_to_delete = '../' . $paper['file_path']; // Go up one dir
                if (file_exists($file_to_delete)) {
                    unlink($file_to_delete); // Delete the actual file
                }
            }
            
            // Now, delete the database record
            $delete_stmt = $pdo->prepare("DELETE FROM research_papers WHERE paper_id = ?");
            $delete_stmt->execute([$paper_id]);
            $message = "Paper (ID: $paper_id) and its file have been permanently deleted.";
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

// --- 3. Fetch All Research Papers ---
try {
    $stmt = $pdo->prepare("
        SELECT r.*, u.full_name 
        FROM research_papers r
        JOIN users u ON r.user_id = u.user_id
        ORDER BY r.uploaded_at DESC
    ");
    $stmt->execute();
    $papers = $stmt->fetchAll();
} catch (PDOException $e) {
    $papers = [];
    $error = "Could not fetch research papers: " . $e->getMessage();
}

?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold mb-8">Admin: Manage Research</h1>

    <div class="mb-6">
        <a href="index.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Dashboard</a>
        <a href="manage_users.php" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Manage Users</a>
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
                        <th class="px-6 py-3 font-semibold">Title</th>
                        <th class="px-6 py-3 font-semibold">Author</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="px-6 py-3 font-semibold">Uploaded</th>
                        <th class="px-6 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($papers)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No research papers submitted.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($papers as $paper): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <?php echo htmlspecialchars($paper['title']); ?>
                                    <a href="../<?php echo htmlspecialchars($paper['file_path']); ?>" target="_blank" class="text-blue-500 ml-2"><i class="fas fa-file-pdf"></i></a>
                                </td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($paper['full_name']); ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($paper['is_approved']): ?>
                                        <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-300 text-xs font-bold">Approved</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-300 text-xs font-bold">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?php echo date('M d, Y', strtotime($paper['uploaded_at'])); ?></td>
                                <td class="px-6 py-4 space-x-2 whitespace-nowrap">
                                    <?php if ($paper['is_approved']): ?>
                                        <a href="manage_research.php?action=unapprove&id=<?php echo $paper['paper_id']; ?>" class="text-yellow-500 hover:text-yellow-400"><i class="fas fa-times-circle"></i> Unapprove</a>
                                    <?php else: ?>
                                        <a href="manage_research.php?action=approve&id=<?php echo $paper['paper_id']; ?>" class="text-green-500 hover:text-green-400"><i class="fas fa-check-circle"></i> Approve</a>
                                    <?php endif; ?>
                                    
                                    <a href="manage_research.php?action=delete&id=<?php echo $paper['paper_id']; ?>" class="text-red-500 hover:text-red-400" onclick="return confirm('Are you sure you want to delete this paper? This will also delete the file.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>