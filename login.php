<?php
// This is login.php
include('includes/config.php');

$errors = [];

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validation
    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    }

    if (empty($errors)) {
        try {
            // Fetch user by email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Verify user and password
            if ($user && password_verify($password, $user['password_hash'])) {
                // Password is correct!
                
                // Regenerate session ID for security
                session_regenerate_id(true); 
                
                // Store user data in session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                // Redirect to the appropriate dashboard
                if ($user['role'] === 'admin') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
                
            } else {
                // Invalid credentials
                $errors[] = "Invalid email or password.";
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
                Welcome Back
            </h2>
            <p class="text-center text-gray-300">Sign in to your account.</p>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-500/30 text-red-100 p-3 rounded-lg">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="login.php" method="POST">
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required class="mt-1 block w-full px-4 py-3 rounded-lg bg-white/10 text-white border-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="mt-1 block w-full px-4 py-3 rounded-lg bg-white/10 text-white border-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-blue-500 bg-white/20 border-white/30 rounded focus:ring-blue-400">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-300">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-blue-400 hover:text-blue-300">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-300">
                        Sign In
                    </button>
                </div>
            </form>
            
            <p class="text-center text-sm text-gray-300">
                Don't have an account?
                <a href="register.php" class="font-medium text-blue-400 hover:text-blue-300">
                    Sign up
                </a>
            </p>
        </div>
        
    </div>
</div>

<?php include('includes/footer.php'); ?>