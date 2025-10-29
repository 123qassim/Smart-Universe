document.addEventListener('DOMContentLoaded', () => {

    // --- 1. Initialize AOS (Animate on Scroll) ---
    AOS.init({
        duration: 700,
        once: true,
        offset: 50,
    });

    // --- 2. GSAP Hero Animations (Example) ---
    // Animates the hero text on load
    if (document.querySelector('.hero-title')) {
        gsap.from('.hero-title', { 
            duration: 1, 
            y: 50, 
            opacity: 0, 
            ease: 'power3.out',
            delay: 0.2
        });
        gsap.from('.hero-subtitle', { 
            duration: 1, 
            y: 30, 
            opacity: 0, 
            ease: 'power3.out',
            delay: 0.4
        });
    }

    // --- 3. Dark Mode Toggle ---
    const themeToggle = document.getElementById('theme-toggle');
    const html = document.documentElement;

    // Set initial state
    const currentTheme = localStorage.getItem('theme') || 'light';
    html.classList.toggle('dark', currentTheme === 'dark');
    updateToggleIcon(currentTheme === 'dark');

    themeToggle.addEventListener('click', () => {
        // Toggle theme
        const isDark = html.classList.toggle('dark');
        
        // Update icon
        updateToggleIcon(isDark);

        // Save preference
        const theme = isDark ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        
        // Set cookie for PHP to read on next page load
        document.cookie = `theme=${theme};path=/;max-age=31536000`;
    });

    function updateToggleIcon(isDark) {
        const sunIcon = themeToggle.querySelector('.fa-sun');
        const moonIcon = themeToggle.querySelector('.fa-moon');
        if (isDark) {
            sunIcon.classList.add('hidden');
            moonIcon.classList.remove('hidden');
        } else {
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
        }
    }

    // --- 4. Animated Counters ---
    const counters = document.querySelectorAll('[data-counter]');
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-counter');
        
        // Use Intersection Observer to trigger when visible
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                animateCounter(counter, target);
                observer.disconnect(); // Only run once
            }
        }, { threshold: 0.5 });

        observer.observe(counter);
    });

    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 100; // Speed of animation
        const update = () => {
            current += increment;
            if (current < target) {
                element.innerText = Math.ceil(current).toLocaleString() + (element.innerText.includes('+') ? '+' : '');
                requestAnimationFrame(update);
            } else {
                element.innerText = target.toLocaleString() + (element.innerText.includes('+') ? '+' : '');
            }
        };
        update();
    }
});