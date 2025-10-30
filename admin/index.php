<?php include('includes/header.php'); ?>

<div class="relative h-screen flex items-center justify-center overflow-hidden" style="background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);">
    <div class="absolute inset-0 z-0" id="parallax-bg"></div>
    
    <div class="text-center z-10 p-6">
        <h1 class="hero-title text-5xl md:text-7xl font-bold text-white mb-4" data-aos="fade-down">
            Smart Uni-Verse 2.0
        </h1>
        <p class="hero-subtitle text-2xl text-gray-200 mb-8" data-aos="fade-up" data-aos-delay="200">
            Empowering <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">Smarter Learning</span>
        </p>
        <div data-aos="fade-up" data-aos-delay="400">
            <a href="register.php" class="bg-blue-500 text-white px-6 py-3 rounded-full text-lg font-semibold hover:bg-blue-600 transition duration-300 mx-2">
                Join Now
            </a>
            <a href="courses.php" class="bg-gray-700/50 text-white px-6 py-3 rounded-full text-lg font-semibold backdrop-blur-sm hover:bg-gray-600/50 transition duration-300 mx-2">
                Explore Courses
            </a>
        </div>
    </div>
</div>

<div class="container mx-auto -mt-16 z-20 relative px-6" data-aos="fade-up" data-aos-delay="500">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md p-6 rounded-lg shadow-xl text-center glassmorphism">
            <i class="fas fa-robot text-4xl text-blue-500 mb-4"></i>
            <h3 class="text-2xl font-semibold mb-2">Ask AI Tutor</h3>
            <p class="mb-4">Get instant help with complex topics, generate quizzes, and more.</p>
            <a href="ai_assistant.php" class="text-blue-500 font-bold hover:underline">Start Chatting &rarr;</a>
        </div>
        
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md p-6 rounded-lg shadow-xl text-center glassmorphism">
            <i class="fas fa-laptop-code text-4xl text-purple-500 mb-4"></i>
            <h3 class="text-2xl font-semibold mb-2">Explore Courses</h3>
            <p class="mb-4">Browse our catalog of cutting-edge courses taught by industry experts.</p>
            <a href="courses.php" class="text-purple-500 font-bold hover:underline">Find Your Course &rarr;</a>
        </div>
        
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md p-6 rounded-lg shadow-xl text-center glassmorphism">
            <i class="fas fa-calendar-alt text-4xl text-green-500 mb-4"></i>
            <h3 class="text-2xl font-semibold mb-2">Virtual Events</h3>
            <p class="mb-4">Join hackathons, guest lectures, and virtual meetups.</p>
            <a href="events.php" class="text-green-500 font-bold hover:underline">See Schedule &rarr;</a>
        </div>
    </div>
</div>

<div class="container mx-auto text-center py-20">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
        <div data-aos="zoom-in">
            <h4 class="text-5xl font-bold text-blue-500" data-counter="12000">12,000+</h4>
            <p class="text-lg text-gray-600 dark:text-gray-400">Students Enrolled</p>
        </div>
        <div data-aos="zoom-in" data-aos-delay="100">
            <h4 class="text-5xl font-bold text-purple-500" data-counter="500">500+</h4>
            <p class="text-lg text-gray-600 dark:text-gray-400">Courses</p>
        </div>
        <div data-aos="zoom-in" data-aos-delay="200">
            <h4 class="text-5xl font-bold text-green-500" data-counter="1500">1,500+</h4>
            <p class="text-lg text-gray-600 dark:text-gray-400">Projects</p>
        </div>
        <div data-aos="zoom-in" data-aos-delay="300">
            <h4 class="text-5xl font-bold text-yellow-500" data-counter="300">300+</h4>
            <p class="text-lg text-gray-600 dark:text-gray-400">Events Hosted</p>
        </div>
    </div>
</div>

<div class="container mx-auto px-6 py-16 text-center">
    <h2 class="text-3xl font-bold mb-8" data-aos="fade-up">Experience Our Digital Campus</h2>
    <div class="aspect-w-16 aspect-h-9 rounded-lg shadow-xl overflow-hidden" data-aos="zoom-in">
        <iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>

<?php include('includes/footer.php'); ?>