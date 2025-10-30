<?php require_once('config.php'); ?>
<!DOCTYPE html>
<html lang="en" class="<?php echo isset($_COOKIE['theme']) ? $_COOKIE['theme'] : ''; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Uni-Verse 2.0</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    
    <script>
        // Tailwind dark mode config
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    // ... your custom theme config
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

<nav class="sticky top-0 z-50 bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg shadow-md">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-400 dark:to-purple-500">
            🧠 Smart Uni-Verse
        </a>
        
        <div class="hidden md:flex space-x-6 items-center">
            <a href="index.php" class="hover:text-blue-500">Home</a>
            
            <a href="campus.php" class="hover:text-blue-500 font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">
                <i class="fas fa-vr-cardboard"></i> Virtual Campus
            </a>
            
            <a href="courses.php" class="hover:text-blue-500">Courses</a>
            <a href="ai_assistant.php" class="hover:text-blue-500">AI Tutor</a>
            <a href="research.php" class="hover:text-blue-500">Research</a>
            <a href="events.php" class="hover:text-blue-500">Events</a>
            <a href="achievements.php" class="hover:text-blue-500">Achievements</a>
            <a href="community.php" class="hover:text-blue-500">Community</a>
            <a href="contact.php" class="hover:text-blue-500">Contact</a>
            
            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php" class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600">Dashboard</a>
            <?php else: ?>
                <a href="login.php" class="hover:text-blue-500">Login</a>
                <a href="register.php" class="bg-purple-500 text-white px-4 py-2 rounded-full hover:bg-purple-600">Join Now</a>
            <?php endif; ?>
            
            <button id="theme-toggle" class="text-xl">
                <i class="fas fa-sun"></i>
                <i class="fas fa-moon hidden"></i>
            </button>
        </div>
        </div>
</nav>