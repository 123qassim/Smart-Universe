<?php
// This is admin/manage_events.php
include('../includes/header.php'); // Note the ../

// --- 1. ADMIN SECURITY CHECK ---
if (!isAdmin()) {
    header('Location: ../login.php?message=Access Denied.');
    exit;
}

$message = '';
$error = '';
$edit_event = null;

// --- 2. Handle POST (Create or Update) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $location = trim($_POST['location']);
    $event_id = $_POST['event_id']; // Hidden field
    $action = $_POST['action'];

    try {
        if ($action == 'create') {
            $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, location) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $event_date, $location]);
            $message = "Event created successfully!";
        } elseif ($action == 'update' && !empty($event_id)) {
            $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, location = ? WHERE event_id = ?");
            $stmt->execute([$title, $description, $event_date, $location, $event_id]);
            $message = "Event updated successfully!";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// --- 3. Handle GET (Delete or Edit) ---
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Handle Delete
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        try {
            $id = $_GET['id'];
            $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
            $stmt->execute([$id]);
            $message = "Event deleted successfully.";
        } catch (PDOException $e) {
            $error = "Could not delete event. It may be linked to RSVPs.";
        }
    }

    // Handle Edit (Load data into form)
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        try {
            $id = $_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
            $stmt->execute([$id]);
            $edit_event = $stmt->fetch();
            // Format datetime-local for the input
            if($edit_event) {
                $edit_event['event_date'] = date('Y-m-d\TH:i', strtotime($edit_event['event_date']));
            }
        } catch (PDOException $e) {
            $error = "Could not fetch event for editing.";
        }
    }
}

// --- 4. Fetch All Events to Display ---
try {
    $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $events = [];
    $error = "Could not fetch events: " . $e->getMessage();
}

?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold mb-8">Admin: Manage Events</h1>

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

    <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg mb-12">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $edit_event ? 'Edit Event' : 'Create New Event'; ?></h2>
        
        <form action="manage_events.php" method="POST">
            <input type="hidden" name="action" value="<?php echo $edit_event ? 'update' : 'create'; ?>">
            <input type="hidden" name="event_id" value="<?php echo $edit_event ? $edit_event['event_id'] : ''; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Title</label>
                    <input type="text" name="title" id="title" value="<?php echo $edit_event ? htmlspecialchars($edit_event['title']) : ''; ?>" required class="mt-1 block w-full input-style">
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                    <input type="text" name="location" id="location" value="<?php echo $edit_event ? htmlspecialchars($edit_event['location']) : 'Virtual'; ?>" required class="mt-1 block w-full input-style">
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" id="description" rows="4" required class="mt-1 block w-full input-style"><?php echo $edit_event ? htmlspecialchars($edit_event['description']) : ''; ?></textarea>
                </div>
                <div>
                    <label for="event_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Date & Time</label>
                    <input type="datetime-local" name="event_date" id="event_date" value="<?php echo $edit_event ? $edit_event['event_date'] : ''; ?>" required class="mt-1 block w-full input-style">
                </div>
            </div>
            <div class="mt-6 text-right">
                <?php if ($edit_event): ?>
                    <a href="manage_events.php" class="bg-gray-600/50 text-white px-5 py-2 rounded-lg hover:bg-gray-700/50 transition mr-2">Cancel Edit</a>
                <?php endif; ?>
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-600 transition">
                    <?php echo $edit_event ? 'Save Changes' : 'Create Event'; ?>
                </button>
            </div>
        </form>
    </div>

    <h2 class="text-2xl font-semibold mb-4">Existing Events</h2>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden glassmorphism">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-100 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Title</th>
                        <th class="px-6 py-3 font-semibold">Date & Time</th>
                        <th class="px-6 py-3 font-semibold">Location</th>
                        <th class="px-6 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($event['title']); ?></td>
                            <td class="px-6 py-4"><?php echo date('M d, Y @ h:i A', strtotime($event['event_date'])); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($event['location']); ?></td>
                            <td class="px-6 py-4 space-x-2">
                                <a href="manage_events.php?action=edit&id=<?php echo $event['event_id']; ?>" class="text-blue-500 hover:text-blue-400"><i class="fas fa-edit"></i> Edit</a>
                                <a href="manage_events.php?action=delete&id=<?php echo $event['event_id']; ?>" class="text-red-500 hover:text-red-400" onclick="return confirm('Are you sure you want to delete this event?')"><i class="fas fa-trash"></i> Delete</a>
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
/* Fix for dark mode date picker */
.dark .input-style {
    color-scheme: dark;
}
</style>

<?php include('../includes/footer.php'); ?>