<?php
// This is includes/footer.php
?>

<footer class="bg-gray-800 dark:bg-gray-900 text-gray-400 mt-20">
    <div class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold text-white mb-4">🧠 Smart Uni-Verse</h3>
                <p class="text-sm">Empowering smarter learning through a futuristic digital ecosystem. Join the revolution of education.</p>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="about.php" class="hover:text-white">About Us</a></li>
                    <li><a href="courses.php" class="hover:text-white">Courses</a></li>
                    <li><a href="events.php" class="hover:text-white">Events</a></li>
                    <li><a href="contact.php" class="hover:text-white">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Legal</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-white">Cookie Policy</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Connect</h4>
                <div class="flex space-x-4 text-xl">
                    <a href="#" class="hover:text-white"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="hover:text-white"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-white"><i class="fab fa-linkedin"></i></a>
                    <a href="#" class="hover:text-white"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
        
        <hr class="border-gray-700 my-8">
        
        <div class="text-center text-sm">
            &copy; <?php echo date('Y'); ?> Smart Uni-Verse 2.0. All rights reserved.
        </div>
    </div>
</footer>

<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // This MUST be at the bottom, after the library is loaded
    AOS.init();
</script>

</body>
</html>