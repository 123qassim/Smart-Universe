<?php
// This is register.php
include('includes/config.php');

$errors = [];
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and retrieve data
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // 2. Validation
    if (empty($full_name) || empty($username) || empty($email) || empty($password) || empty($role)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if (!in_array($role, ['student', 'faculty'])) {
        $errors[] = "Invalid role selected.";
    }

    // 3. Check for existing user (if no validation errors)
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            $existing_user = $stmt->fetch();

            if ($existing_user) {
                if ($existing_user['email'] === $email) {
                    $errors[] = "An account with this email already exists.";
                }
                if ($existing_user['username'] === $username) {
                    $errors[] = "This username is already taken.";
                }
            } else {
                // 4. Create new user
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                
                $insert_stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password_hash, role) VALUES (?, ?, ?, ?, ?)");
                $insert_stmt->execute([$full_name, $username, $email, $password_hash, $role]);
                
                // --- NEW ---
                // Grant the "Community Member" achievement
                $new_user_id = $pdo->lastInsertId();
                grant_achievement($pdo, $new_user_id, 'Community Member');
                // --- END NEW ---
                
                $success = "Registration successful! You can now log in.";
                // Redirect to login page after a short delay
                header("refresh:3;url=login.php");
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Include the header
include('includes/header.php');
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);">
    <div class="max-w-md w-full space-y-8">
        
        <div class="glassmorphism p-8 rounded-2xl shadow-2xl space-y-6" data-aos="fade-up">
            
            <h2 class="text-center text-3xl font-bold text-white">
                Join the Smart Uni-Verse
            </h2>
            <p class="text-center text-gray-300">Create your account to begin.</p>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-500/30 text-red-100 p-3 rounded-lg">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-500/30 text-green-100 p-3 rounded-lg">
                    <p><?php echo $success; ?></p>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="register.php" method="POST">
                
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-300">Full Name</label>
                    <input id="full_name" name="full_name" type="text" required class="mt-1 block w-full px-4 py-3 rounded-lg bg-white/10 text-white border-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                    <input id="username" name="username" type="text" required class="mt-1 block w-full px-4 py-3 rounded-lg bg-white/10 text-white border-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required class="mt-1 block w-full px-4 py-3 rounded-lg bg-white/10 text-white border-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required class="mt-1 block w-full px-4 py-3 rounded-lg bg-white/10 text-white border-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-300">Confirm Password</label>
                    <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required class="mt-1 block w-full px-4 py-3 rounded-lg bg-white/10 text-white border-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-300">I am a...</label>
                    <select id="role" name="role" required class="mt-1 block w-full px-4 py-3 rounded-lg bg-white/10 text-white border-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                        <option value="student" class="text-gray-900">Student</option>
                        <option value="faculty" class="text-gray-900">Faculty / Instructor</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-300">
                        Create Account
                    </button>
                </div>
            </form>
            
            <p class="text-center text-sm text-gray-300">
                Already have an account?
                <a href="login.php" class="font-medium text-blue-400 hover:text-blue-300">
                    Sign in
                </a>
            </p>
        </div>
        
    </div>
</div>

<?php include('includes/footer.php'); ?>