<?php
// This is admin/analytics.php
include('../includes/header.php'); // Note the ../

// --- 1. ADMIN SECURITY CHECK ---
if (!isAdmin()) {
    header('Location: ../login.php?message=Access Denied.');
    exit;
}

// --- 2. Fetch Detailed Analytics Data ---
$error = '';
$signup_data = ['labels' => [], 'data' => []];
$revenue_data = ['labels' => [], 'data' => []];
$course_popularity = ['labels' => [], 'data' => []];

try {
    // --- Chart 1: User Signups (Last 7 Days) ---
    // This query groups users by the date they were created
    $stmt = $pdo->query("
        SELECT DATE(created_at) AS signup_date, COUNT(*) AS user_count
        FROM users
        WHERE created_at >= CURDATE() - INTERVAL 7 DAY
        GROUP BY signup_date
        ORDER BY signup_date ASC
    ");
    $signups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data for Chart.js
    foreach ($signups as $row) {
        $signup_data['labels'][] = date('M d', strtotime($row['signup_date']));
        $signup_data['data'][] = $row['user_count'];
    }

    // --- Chart 2: Revenue (Last 7 Days) ---
    $stmt = $pdo->query("
        SELECT DATE(created_at) AS payment_date, SUM(amount) AS daily_revenue
        FROM payments
        WHERE status = 'completed' AND created_at >= CURDATE() - INTERVAL 7 DAY
        GROUP BY payment_date
        ORDER BY payment_date ASC
    ");
    $revenue = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($revenue as $row) {
        $revenue_data['labels'][] = date('M d', strtotime($row['payment_date']));
        $revenue_data['data'][] = $row['daily_revenue'];
    }

    // --- Chart 3: Course Popularity (Top 5 Courses) ---
    $stmt = $pdo->query("
        SELECT c.title, COUNT(e.enrollment_id) AS enrollment_count
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        GROUP BY c.course_id
        ORDER BY enrollment_count DESC
        LIMIT 5
    ");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($courses as $row) {
        $course_popularity['labels'][] = $row['title'];
        $course_popularity['data'][] = $row['enrollment_count'];
    }

} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}

?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-3xl font-bold mb-8">Admin: Detailed Analytics</h1>

    <div class="mb-6">
        <a href="index.php" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Dashboard</a>
        <a href="manage_users.php" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Manage Users</a>
        <a href="manage_courses.php" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Manage Courses</a>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-500/20 text-red-300 p-3 rounded-lg mb-4"><?php echo $error; ?></div>
    <?php endif; ?>
    
    

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
        <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">New User Signups (Last 7 Days)</h2>
            <canvas id="userSignupChart"></canvas>
        </div>
        
        <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">Revenue (Last 7 Days)</h2>
            <canvas id="revenueChart"></canvas>
        </div>
        
        <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">Top 5 Courses by Enrollment</h2>
            <canvas id="coursePopularityChart" style="max-height: 400px;"></canvas>
        </div>
        
        <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">User Role Distribution</h2>
            <canvas id="userRolesChart" style="max-height: 400px;"></canvas>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // Chart 1: User Signups (Line)
    const ctx1 = document.getElementById('userSignupChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($signup_data['labels']); ?>,
            datasets: [{
                label: 'New Users',
                data: <?php echo json_encode($signup_data['data']); ?>,
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true }
    });

    // Chart 2: Revenue (Bar)
    const ctx2 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($revenue_data['labels']); ?>,
            datasets: [{
                label: 'Daily Revenue ($)',
                data: <?php echo json_encode($revenue_data['data']); ?>,
                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 1
            }]
        },
        options: { responsive: true }
    });

    // Chart 3: Course Popularity (Doughnut)
    const ctx3 = document.getElementById('coursePopularityChart').getContext('2d');
    new Chart(ctx3, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($course_popularity['labels']); ?>,
            datasets: [{
                label: 'Enrollments',
                data: <?php echo json_encode($course_popularity['data']); ?>,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(234, 179, 8, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                hoverOffset: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
    
    // Chart 4: User Roles (Pie) - This query is simple, so we do it in JS
    const ctx4 = document.getElementById('userRolesChart').getContext('2d');
    new Chart(ctx4, {
        type: 'pie',
        data: {
            labels: ['Students', 'Faculty', 'Admins'],
            datasets: [{
                label: 'User Roles',
                // We fetch these counts from PHP elements we got earlier
                data: [
                    <?php echo $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn(); ?>,
                    <?php echo $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'faculty'")->fetchColumn(); ?>,
                    <?php echo $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn(); ?>
                ],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                hoverOffset: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});
</script>

<?php include('../includes/footer.php'); ?>