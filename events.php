<?php
// This is events.php
include('includes/header.php');

$user_id = isLoggedIn() ? $_SESSION['user_id'] : 0;

// --- Fetch All Events and check if current user has RSVP'd ---
try {
    // This query fetches all events and adds a 'user_rsvpd' column (1 if yes, 0 if no)
    $stmt = $pdo->prepare("
        SELECT 
            e.*, 
            (CASE WHEN r.rsvp_id IS NOT NULL THEN 1 ELSE 0 END) AS user_rsvpd
        FROM events e
        LEFT JOIN event_rsvps r ON e.event_id = r.event_id AND r.user_id = ?
        ORDER BY e.event_date ASC
    ");
    $stmt->execute([$user_id]);
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $events = [];
    error_log("Events Page Error: " . $e->getMessage());
}
?>

<div class="relative bg-gradient-to-r from-green-800 to-cyan-700 text-white py-24 px-6 text-center">
    
    <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-down">University Events</h1>
    <p class="text-lg md:text-xl text-blue-200" data-aos="fade-up" data-aos-delay="100">Join virtual conferences, hackathons, and guest lectures.</p>
</div>

<div class="container mx-auto px-6 py-12">

    <?php if (isset($_GET['message'])): ?>
        <div class="bg-green-500/20 text-green-300 p-3 rounded-lg mb-6 text-center">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <div class="space-y-8">
        <?php if (empty($events)): ?>
            <p class="text-gray-500 dark:text-gray-400 text-center">No upcoming events scheduled. Please check back soon.</p>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg flex flex-col md:flex-row items-center" data-aos="fade-up">
                    
                    <div class="flex-shrink-0 text-center bg-blue-500 text-white rounded-lg p-4 w-24 mb-4 md:mb-0 md:mr-6">
                        <span class="block text-2xl font-bold"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                        <span class="block text-lg font-semibold"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                        <span class="block text-sm"><?php echo date('Y', strtotime($event['event_date'])); ?></span>
                    </div>
                    
                    <div class="flex-grow text-center md:text-left">
                        <span class="inline-block bg-green-500/20 text-green-400 text-xs font-semibold px-3 py-1 rounded-full mb-2">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?>
                        </span>
                        <h3 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            <?php echo htmlspecialchars($event['description']); ?>
                        </p>
                    </div>
                    
                    <div class="flex-shrink-0 mt-4 md:mt-0 md:ml-6">
                        <?php if ($event['user_rsvpd']): ?>
                            <button disabled class="w-full md:w-auto bg-green-600 text-white px-6 py-3 rounded-lg font-semibold">
                                <i class="fas fa-check"></i> You're Going!
                            </button>
                        <?php else: ?>
                            <form action="rsvp.php" method="POST">
                                <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                <button type="submit" class="w-full md:w-auto bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">
                                    RSVP Now
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>