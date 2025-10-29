<?php
// This is payments.php
include('includes/header.php');

// --- SECURITY: Check if user is logged in ---
if (!isLoggedIn()) {
    header('Location: login.php?message=Please log in to manage payments.');
    exit;
}

$user_id = $_SESSION['user_id'];

// --- Fetch Payment History ---
try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.title 
        FROM payments p
        LEFT JOIN courses c ON p.course_id = c.course_id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $payments = $stmt->fetchAll();
} catch (PDOException $e) {
    $payments = [];
    error_log("Payments Page Error: " . $e->getMessage());
}
?>

<div class="relative bg-gradient-to-r from-purple-800 to-indigo-800 text-white py-24 px-6 text-center">
    
    <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-down">Billing & Subscriptions</h1>
    <p class="text-lg md:text-xl text-blue-200" data-aos="fade-up" data-aos-delay="100">Manage your subscription and view payment history.</p>
</div>

<div class="container mx-auto px-6 py-12">

    <h2 class="text-3xl font-bold text-center mb-10" data-aos="fade-up">Choose Your Plan</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto mb-20">
        
        <div class="glassmorphism bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg text-center" data-aos="fade-up" data-aos-delay="100">
            <h3 class="text-2xl font-semibold mb-4">Guest Access</h3>
            <p class="text-4xl font-bold mb-4">$0<span class="text-lg font-normal">/mo</span></p>
            <ul class="text-left space-y-2 text-gray-600 dark:text-gray-400 mb-6">
                <li><i class="fas fa-check text-green-500"></i> Browse courses</li>
                <li><i class="fas fa-times text-red-500"></i> AI Study Assistant</li>
                <li><i class="fas fa-times text-red-500"></i> Enroll in courses</li>
            </ul>
            <button disabled class="w-full bg-gray-400 text-white px-6 py-3 rounded-lg font-semibold cursor-not-allowed">Current Plan</button>
        </div>

        <div class="glassmorphism bg-white dark:bg-gray-800 p-8 rounded-lg shadow-2xl text-center border-2 border-blue-500" data-aos="fade-up">
            <h3 class="text-2xl font-semibold mb-4">Student</h3>
            <p class="text-4xl font-bold mb-4">$15<span class="text-lg font-normal">/mo</span></p>
            <ul class="text-left space-y-2 text-gray-600 dark:text-gray-400 mb-6">
                <li><i class="fas fa-check text-green-500"></i> Browse courses</li>
                <li><i class="fas fa-check text-green-500"></i> AI Study Assistant</li>
                <li><i class="fas fa-check text-green-500"></i> Enroll in all courses</li>
                <li><i class="fas fa-check text-green-500"></i> Generate PDF receipts</li>
            </ul>
            <button class="w-full bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">Get Started</button>
            <p class="text-xs text-gray-500 mt-2">Stripe & Mpesa supported</p>
        </div>

        <div class="glassmorphism bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg text-center" data-aos="fade-up" data-aos-delay="200">
            <h3 class="text-2xl font-semibold mb-4">Faculty</h3>
            <p class="text-4xl font-bold mb-4">$35<span class="text-lg font-normal">/mo</span></p>
            <ul class="text-left space-y-2 text-gray-600 dark:text-gray-400 mb-6">
                <li><i class="fas fa-check text-green-500"></i> All student features</li>
                <li><i class="fas fa-check text-green-500"></i> Upload courses</li>
                <li><i class="fas fa-check text-green-500"></i> Manage research hub</li>
            </ul>
            <button class="w-full bg-purple-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-600 transition">Choose Plan</button>
        </div>
    </div>
    
    <h2 class="text-3xl font-bold mb-6">Payment History</h2>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden glassmorphism">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-100 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Date</th>
                        <th class="px-6 py-3 font-semibold">Amount</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="px-6 py-3 font-semibold">Description</th>
                        <th class="px-6 py-3 font-semibold">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No payment history found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td class="px-6 py-4"><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></td>
                                <td class="px-6 py-4">$<?php echo htmlspecialchars($payment['amount']); ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($payment['status'] == 'completed'): ?>
                                        <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-300 text-xs font-bold">Completed</span>
                                    <?php elseif ($payment['status'] == 'pending'): ?>
                                        <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-300 text-xs font-bold">Pending</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full bg-red-500/20 text-red-300 text-xs font-bold">Failed</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4"><?php echo $payment['title'] ? 'Enrollment: ' . htmlspecialchars($payment['title']) : 'Subscription'; ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($payment['status'] == 'completed'): ?>
                                        <a href="generate_receipt.php?id=<?php echo $payment['payment_id']; ?>" target="_blank" class="text-blue-500 hover:underline"><i class="fas fa-file-pdf"></i> Download</a>
                                    <?php else: ?>
                                        <span class="text-gray-500">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>