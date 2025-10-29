<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Uni-Verse 2.0</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- AOS (Animate on Scroll) CSS & JS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- GSAP (Animation Library) CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide-react@0.378.0/dist/lucide.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
        /* Inter font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* slate-900 */
            color: #f1f5f9; /* slate-100 */
            overflow-x: hidden;
        }
        
        /* Glassmorphism Effect */
        .glass-morphism {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Main page container */
        .page-container {
            min-height: 100vh;
            padding-top: 80px; /* Offset for fixed header */
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1e293b; /* slate-800 */
        }
        ::-webkit-scrollbar-thumb {
            background: #334155; /* slate-700 */
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569; /* slate-600 */
        }
        
        /* 3D Campus Canvas */
        #three-canvas-container {
            width: 100%;
            height: calc(100vh - 80px); /* Full viewport minus header */
            position: relative;
            overflow: hidden;
        }
        #three-canvas-container canvas {
            display: block;
        }
        
        /* 3D Labels */
        .three-label {
            color: #fff;
            background: rgba(0, 0, 0, 0.5);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            pointer-events: none;
            user-select: none;
        }

        /* AI Chat Bubbles */
        .chat-bubble-user {
            background-color: #2563eb; /* blue-600 */
            color: white;
        }
        .chat-bubble-ai {
            background-color: #334155; /* slate-700 */
            color: #f1f5f9; /* slate-100 */
        }
        .chat-bubble-ai code {
            background: #0f172a;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
        
        /* Custom Keyframes for Hero */
        @keyframes subtle-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .hero-float {
            animation: subtle-float 6s ease-in-out infinite;
        }

        /* Simple Toast Notification */
        #toast-notification {
            position: fixed;
            bottom: -100px;
            left: 50%;
            transform: translateX(-50%);
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            z-index: 1000;
            transition: bottom 0.5s ease-in-out;
        }
        #toast-notification.show {
            bottom: 30px;
        }
    </style>
</head>

<!-- App runs on hash change and load -->
<body onhashchange="App.router()" onload="App.init()">

    <!-- Main Navigation -->
    <header id="main-header" class="glass-morphism fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="#home" class="flex items-center space-x-2">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-400"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span class="text-2xl font-extrabold text-white">SmartUni</span>
                </a>
                
                <!-- Desktop Nav -->
                <div id="desktop-nav-links" class="hidden md:flex items-center space-x-6">
                    <!-- Links will be populated by JS -->
                </div>
                
                <!-- Mobile Nav Button -->
                <div class="md:hidden">
                    <button id="mobile-nav-toggle" class="text-slate-300 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    </button>
                </div>
            </div>
        </nav>
        
        <!-- Mobile Nav Menu -->
        <div id="mobile-nav-menu" class="md:hidden hidden absolute top-20 left-0 right-0 bg-slate-800/95 p-4 border-t border-slate-700">
            <div id="mobile-nav-links" class="flex flex-col space-y-3">
                <!-- Mobile links will be populated by JS -->
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <!-- Pages will be dynamically rendered here by the router -->
    <main id="page-content">
        <!-- Loading Spinner -->
        <div class="page-container flex items-center justify-center">
            <svg class="animate-spin h-10 w-10 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-slate-400">
            <p>&copy; 2025 Smart Uni-Verse 2.0. All rights reserved.</p>
            <p class="text-sm mt-2">A futuristic digital campus prototype.</p>
        </div>
    </footer>
    
    <!-- Toast Notification -->
    <div id="toast-notification" class="bg-green-500">
        <span id="toast-message"></span>
    </div>

    <!-- Main Application Logic -->
    <script type="module">
        // Import Three.js and modules from CDN
        import * as THREE from 'https://cdn.skypack.dev/three@0.136.0';
        import { OrbitControls } from 'https://cdn.skypack.dev/three@0.136.0/examples/jsm/controls/OrbitControls.js';
        import { CSS2DRenderer, CSS2DObject } from 'https://cdn.skypack.dev/three@0.136.0/examples/jsm/renderers/CSS2DRenderer.js';
        
        // Import AOS
        import AOS from 'https://unpkg.com/aos@2.3.1/dist/aos.js';

        // --- GLOBAL APP STATE ---
        window.App = {
            state: {
                user: null, // Holds the logged-in user object
                db: {
                    users: [],
                    courses: [],
                    enrollments: [],
                    payments: [],
                    research_papers: [],
                    events: [],
                    event_rsvps: [],
                    ai_chat_history: [],
                    achievements: [],
                    user_achievements: [],
                    forum_categories: [],
                    forum_threads: [],
                    forum_posts: [],
                }
            },
            
            // Three.js scene variables
            three: {
                scene: null,
                camera: null,
                renderer: null,
                labelRenderer: null,
                controls: null,
                raycaster: null,
                mouse: null,
                clickableObjects: [],
                animationFrameId: null
            },

            // --- INITIALIZATION ---
            init() {
                console.log("Smart Uni-Verse 2.0 Initializing...");
                this.loadDatabase();
                this.checkLoginState();
                this.updateNavigation();
                this.router();
                
                // Init AOS
                AOS.init({
                    duration: 800,
                    once: true,
                });
                
                // Init Lucide Icons
                lucide.createIcons();
                
                // Mobile nav toggle
                document.getElementById('mobile-nav-toggle').addEventListener('click', () => {
                    document.getElementById('mobile-nav-menu').classList.toggle('hidden');
                });
            },

            // --- ROUTER ---
            /**
             * Handles page rendering based on URL hash
             */
            router() {
                const pageContent = document.getElementById('page-content');
                const route = window.location.hash || '#home';
                
                // Stop any running 3D animation before changing page
                if (this.three.animationFrameId) {
                    cancelAnimationFrame(this.three.animationFrameId);
                    this.three.animationFrameId = null;
                    // Clean up renderers and event listeners
                    if(this.three.renderer) this.three.renderer.dispose();
                    if(this.three.labelRenderer && this.three.labelRenderer.domElement.parentNode) {
                        this.three.labelRenderer.domElement.parentNode.removeChild(this.three.labelRenderer.domElement);
                    }
                    window.removeEventListener('resize', this.onWindowResize);
                    window.removeEventListener('click', this.onCanvasClick);
                }

                // Page routes
                switch (route) {
                    case '#home':
                        pageContent.innerHTML = this.renderHomePage();
                        this.runGsapHeroAnimations();
                        break;
                    case '#login':
                        pageContent.innerHTML = this.renderLoginPage();
                        this.attachAuthHandlers('login-form', this.handleLogin);
                        break;
                    case '#register':
                        pageContent.innerHTML = this.renderRegisterPage();
                        this.attachAuthHandlers('register-form', this.handleRegister);
                        break;
                    case '#dashboard':
                        if (!this.isLoggedIn()) return this.redirectToLogin();
                        pageContent.innerHTML = this.renderDashboardPage();
                        this.renderDashboardCharts();
                        break;
                    case '#courses':
                        pageContent.innerHTML = this.renderCoursesPage();
                        this.attachCourseHandlers();
                        break;
                    case '#ai':
                        if (!this.isLoggedIn()) return this.redirectToLogin();
                        pageContent.innerHTML = this.renderAiAssistantPage();
                        this.attachAiChatHandlers();
                        break;
                    case '#research':
                        if (!this.isLoggedIn()) return this.redirectToLogin();
                        pageContent.innerHTML = this.renderResearchPage();
                        this.attachResearchHandlers();
                        break;
                    case '#events':
                        pageContent.innerHTML = this.renderEventsPage();
                        this.attachEventHandlers();
                        break;
                    case '#payments':
                        if (!this.isLoggedIn()) return this.redirectToLogin();
                        pageContent.innerHTML = this.renderPaymentsPage();
                        this.attachPaymentHandlers();
                        break;
                    case '#achievements':
                        if (!this.isLoggedIn()) return this.redirectToLogin();
                        pageContent.innerHTML = this.renderAchievementsPage();
                        break;
                    case '#community':
                        if (!this.isLoggedIn()) return this.redirectToLogin();
                        pageContent.innerHTML = this.renderCommunityPage();
                        this.attachCommunityHandlers();
                        break;
                    case '#campus':
                        if (!this.isLoggedIn()) return this.redirectToLogin();
                        pageContent.innerHTML = this.renderCampusPage();
                        // Delay init to ensure DOM is ready
                        setTimeout(() => this.initThreeJS(), 100); 
                        break;
                    case '#about':
                        pageContent.innerHTML = this.renderAboutPage();
                        break;
                    case '#contact':
                        pageContent.innerHTML = this.renderContactPage();
                        this.attachContactHandler();
                        break;
                    // Admin Routes
                    case '#admin':
                        if (!this.isAdmin()) return this.redirectToHome();
                        pageContent.innerHTML = this.renderAdminDashboard();
                        this.renderAdminDashboardCharts();
                        break;
                    case '#admin-users':
                        if (!this.isAdmin()) return this.redirectToHome();
                        pageContent.innerHTML = this.renderAdminUsers();
                        this.attachAdminUserHandlers();
                        break;
                    case '#admin-courses':
                        if (!this.isAdmin()) return this.redirectToHome();
                        pageContent.innerHTML = this.renderAdminCourses();
                        this.attachAdminCourseHandlers();
                        break;
                    case '#admin-events':
                        if (!this.isAdmin()) return this.redirectToHome();
                        pageContent.innerHTML = this.renderAdminEvents();
                        this.attachAdminEventHandlers();
                        break;
                    case '#admin-research':
                        if (!this.isAdmin()) return this.redirectToHome();
                        pageContent.innerHTML = this.renderAdminResearch();
                        this.attachAdminResearchHandlers();
                        break;
                    // Default
                    default:
                        // Handle thread pages like #thread-1
                        if (route.startsWith('#thread-')) {
                            if (!this.isLoggedIn()) return this.redirectToLogin();
                            const threadId = parseInt(route.split('-')[1]);
                            pageContent.innerHTML = this.renderThreadPage(threadId);
                            this.attachThreadHandlers(threadId);
                        } else {
                            pageContent.innerHTML = this.renderNotFoundPage();
                        }
                }
                
                // Refresh AOS animations
                setTimeout(() => AOS.refresh(), 100);
                // Re-create icons
                lucide.createIcons();
                // Close mobile nav on route change
                document.getElementById('mobile-nav-menu').classList.add('hidden');
                // Scroll to top
                window.scrollTo(0, 0);
            },
            
            // --- AUTHENTICATION ---
            isLoggedIn() {
                return this.state.user !== null;
            },
            
            isAdmin() {
                return this.isLoggedIn() && this.state.user.role === 'admin';
            },
            
            redirectToLogin() {
                window.location.hash = '#login';
            },
            
            redirectToHome() {
                window.location.hash = '#home';
            },

            checkLoginState() {
                const user = localStorage.getItem('smartuni_user');
                if (user) {
                    this.state.user = JSON.parse(user);
                }
            },
            
            attachAuthHandlers(formId, handler) {
                const form = document.getElementById(formId);
                if (form) {
                    // Need to bind `this` to the App object
                    form.addEventListener('submit', handler.bind(this));
                }
            },

            handleLogin(e) {
                e.preventDefault();
                const email = e.target.email.value;
                const password = e.target.password.value;
                
                const user = this.state.db.users.find(u => u.email === email);
                
                // Simple password check (in a real app, this would be a hash comparison)
                if (user && user.password === password) {
                    this.state.user = user;
                    localStorage.setItem('smartuni_user', JSON.stringify(user));
                    this.updateNavigation();
                    this.showToast('Login successful! Welcome back.', 'success');
                    window.location.hash = '#dashboard';
                } else {
                    this.showToast('Invalid email or password.', 'error');
                }
            },
            
            handleRegister(e) {
                e.preventDefault();
                const username = e.target.username.value;
                const email = e.target.email.value;
                const password = e.target.password.value;
                
                if (this.state.db.users.find(u => u.email === email)) {
                    this.showToast('An account with this email already exists.', 'error');
                    return;
                }
                
                const newUser = {
                    id: this.getNextId('users'),
                    username,
                    email,
                    password, // In a real app, hash this!
                    role: 'student',
                    total_points: 0,
                    created_at: new Date().toISOString()
                };
                
                this.state.db.users.push(newUser);
                this.saveDatabase();
                
                // Log the new user in
                this.state.user = newUser;
                localStorage.setItem('smartuni_user', JSON.stringify(newUser));
                
                // Gamification: Award first badge
                this.awardAchievement('Community Member');
                
                this.updateNavigation();
                this.showToast('Registration successful! Welcome to Smart Uni-Verse.', 'success');
                window.location.hash = '#dashboard';
            },
            
            handleLogout() {
                this.state.user = null;
                localStorage.removeItem('smartuni_user');
                this.updateNavigation();
                this.showToast('You have been logged out.', 'success');
                window.location.hash = '#home';
            },

            // --- NAVIGATION ---
            updateNavigation() {
                const desktopNav = document.getElementById('desktop-nav-links');
                const mobileNav = document.getElementById('mobile-nav-links');
                
                let links = `
                    <a href="#home" class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">Home</a>
                    <a href="#courses" class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">Courses</a>
                    <a href="#events" class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">Events</a>
                    <a href="#about" class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">About</a>
                    <a href="#contact" class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                `;
                
                let mobileLinks = `
                    <a href="#home" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">Home</a>
                    <a href="#courses" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">Courses</a>
                    <a href="#events" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">Events</a>
                    <a href="#about" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">About</a>
                    <a href="#contact" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">Contact</a>
                    <hr class="border-slate-700 my-2">
                `;
                
                if (this.isLoggedIn()) {
                    links += `
                        <a href="#dashboard" class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="#campus" class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">3D Campus</a>
                        <a href="#ai" class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">AI Assistant</a>
                        <a href="#community" class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">Community</a>
                    `;
                    mobileLinks += `
                        <a href="#dashboard" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                        <a href="#campus" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">3D Campus</a>
                        <a href="#ai" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">AI Assistant</a>
                        <a href="#community" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">Community</a>
                        <a href="#research" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">Research Hub</a>
                        <a href="#achievements" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">Achievements</a>
                        <a href="#payments" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">Payments</a>
                        <hr class="border-slate-700 my-2">
                    `;
                    
                    if (this.isAdmin()) {
                         links += `<a href="#admin" class="text-yellow-400 hover:text-yellow-300 px-3 py-2 rounded-md text-sm font-medium">Admin Panel</a>`;
                         mobileLinks += `<a href="#admin" class="text-yellow-400 hover:text-yellow-300 block px-3 py-2 rounded-md text-base font-medium">Admin Panel</a>`;
                    }
                    
                    links += `
                        <a href="#dashboard" class="text-slate-300 hover:text-white ml-4">${this.state.user.username} (${this.state.user.total_points} pts)</a>
                        <button id="logout-btn" class="ml-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">Logout</button>
                    `;
                    mobileLinks += `
                        <button id="mobile-logout-btn" class="w-full text-left bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-base font-medium mt-2">Logout</button>
                    `;
                } else {
                    links += `
                        <a href="#login" class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                        <a href="#register" class="ml-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">Register</a>
                    `;
                    mobileLinks += `
                        <a href="#login" class="text-slate-300 hover:text-blue-400 block px-3 py-2 rounded-md text-base font-medium">Login</a>
                        <a href="#register" class="bg-blue-600 hover:bg-blue-700 text-white block px-3 py-2 rounded-md text-base font-medium mt-2">Register</a>
                    `;
                }
                
                desktopNav.innerHTML = links;
                mobileNav.innerHTML = mobileLinks;
                
                // Add event listeners for logout buttons
                const logoutBtn = document.getElementById('logout-btn');
                if (logoutBtn) logoutBtn.addEventListener('click', this.handleLogout.bind(this));
                
                const mobileLogoutBtn = document.getElementById('mobile-logout-btn');
                if (mobileLogoutBtn) mobileLogoutBtn.addEventListener('click', this.handleLogout.bind(this));
            },
            
            // --- DATABASE (LocalStorage Simulation) ---
            loadDatabase() {
                const db = localStorage.getItem('smartuni_db');
                if (db) {
                    this.state.db = JSON.parse(db);
                } else {
                    // Seed database with initial data
                    this.seedDatabase();
                    this.saveDatabase();
                }
            },
            
            saveDatabase() {
                localStorage.setItem('smartuni_db', JSON.stringify(this.state.db));
            },
            
            getNextId(table) {
                const items = this.state.db[table];
                if (!items || items.length === 0) return 1;
                return Math.max(...items.map(i => i.id)) + 1;
            },

            seedDatabase() {
                this.state.db.users = [
                    { id: 1, username: 'admin', email: 'admin@smartuni.edu', password: 'admin', role: 'admin', total_points: 999, created_at: '2025-01-01T10:00:00Z' },
                    { id: 2, username: 'student', email: 'student@smartuni.edu', password: 'student', role: 'student', total_points: 150, created_at: '2025-01-02T11:00:00Z' }
                ];
                this.state.db.courses = [
                    { id: 1, title: 'AI in the 21st Century', description: 'Explore the fundamentals and future of Artificial Intelligence.', department: 'Computer Science', credits: 3, cover_image_url: 'https://placehold.co/600x400/0f172a/FFF?text=AI+Course' },
                    { id: 2, title: 'Quantum Computing Basics', description: 'A primer on the next generation of computing technology.', department: 'Physics', credits: 4, cover_image_url: 'https://placehold.co/600x400/1d4ed8/FFF?text=Quantum' },
                    { id: 3, title: 'Metaverse Design Principles', description: 'Learn to design and build immersive virtual worlds.', department: 'Digital Media', credits: 3, cover_image_url: 'https://placehold.co/600x400/6d28d9/FFF?text=Metaverse' },
                    { id: 4, title: 'Sustainable Future Tech', description: 'Innovations in green technology and sustainable engineering.', department: 'Engineering', credits: 3, cover_image_url: 'https://placehold.co/600x400/166534/FFF?text=Eco-Tech' }
                ];
                this.state.db.events = [
                    { id: 1, title: 'Virtual Tech Summit 2.0', description: 'Join industry leaders for a 3-day summit on future tech.', event_date: '2025-11-15T14:00:00Z', location: '3D Campus Auditorium' },
                    { id: 2, title: 'AI Research Symposium', description: 'Presentation of new papers by faculty and students.', event_date: '2025-11-20T09:00:00Z', location: 'Research Hub' }
                ];
                this.state.db.achievements = [
                    { id: 1, badge_name: 'Community Member', description: 'Joined the Smart Uni-Verse community.', points_reward: 50, icon: 'users' },
                    { id: 2, badge_name: 'First Enrollment', description: 'Enrolled in your first course.', points_reward: 100, icon: 'book-open' },
                    { id: 3, badge_name: 'First Research', description: 'Uploaded your first research paper.', points_reward: 150, icon: 'flask-conical' },
                    { id: 4, badge_name: 'First Event', description: 'RSVP\'d to your first event.', points_reward: 50, icon: 'calendar-plus' },
                    { id: 5, badge_name: 'Avid Learner', description: 'Completed 3 courses.', points_reward: 200, icon: 'graduation-cap' }
                ];
                this.state.db.forum_categories = [
                    { id: 1, title: 'General Discussion', description: 'Talk about anything university-related.' },
                    { id: 2, title: 'Course Help', description: 'Get help with courses and homework.' },
                    { id: 3, title: 'Research & Innovation', description: 'Discuss new ideas and collaborations.' }
                ];
                this.state.db.forum_threads = [
                    { id: 1, user_id: 1, category_id: 2, title: 'Help with Quantum Computing problem set?', created_at: '2025-01-10T14:00:00Z' }
                ];
                this.state.db.forum_posts = [
                    { id: 1, user_id: 1, thread_id: 1, content: 'I\'m stuck on question 3 about superposition. Can anyone explain it?', created_at: '2025-01-10T14:00:00Z' },
                    { id: 2, user_id: 2, thread_id: 1, content: 'Sure! Think of it like a coin spinning in the air. It\'s not heads or tails until it lands (is measured). It\'s in a state of *both* at the same time.', created_at: '2025-01-10T14:05:00Z' }
                ];
                // Seed some achievements for the student
                this.state.db.user_achievements = [
                    { id: 1, user_id: 2, achievement_id: 1, earned_at: '2025-01-02T11:00:00Z' }
                ];
            },
            
            
            // --- GAMIFICATION ---
            awardAchievement(badgeName) {
                if (!this.isLoggedIn()) return;
                
                const achievement = this.state.db.achievements.find(a => a.badge_name === badgeName);
                if (!achievement) return;
                
                const alreadyEarned = this.state.db.user_achievements.find(ua => ua.user_id === this.state.user.id && ua.achievement_id === achievement.id);
                
                if (!alreadyEarned) {
                    // Add to user_achievements
                    this.state.db.user_achievements.push({
                        id: this.getNextId('user_achievements'),
                        user_id: this.state.user.id,
                        achievement_id: achievement.id,
                        earned_at: new Date().toISOString()
                    });
                    
                    // Update user's points
                    this.state.user.total_points += achievement.points_reward;
                    
                    // Update user in db.users array as well
                    const userInDb = this.state.db.users.find(u => u.id === this.state.user.id);
                    if (userInDb) {
                        userInDb.total_points = this.state.user.total_points;
                    }
                    
                    // Save to local storage
                    localStorage.setItem('smartuni_user', JSON.stringify(this.state.user));
                    this.saveDatabase();
                    
                    // Show notification
                    this.showToast(`Badge Unlocked: ${badgeName} (+${achievement.points_reward} pts)!`, 'success');
                    
                    // Refresh nav to show new points
                    this.updateNavigation();
                }
            },

            // --- UI & HELPERS ---
            showToast(message, type = 'success') {
                const toast = document.getElementById('toast-notification');
                const messageEl = document.getElementById('toast-message');
                
                messageEl.textContent = message;
                toast.className = ''; // Clear existing classes
                
                if (type === 'success') {
                    toast.classList.add('bg-green-600');
                } else {
                    toast.classList.add('bg-red-600');
                }
                
                toast.classList.add('show');
                
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3000);
            },
            
            formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            },
            
            getUserById(userId) {
                return this.state.db.users.find(u => u.id === userId) || { username: 'Unknown' };
            },

            // --- PAGE RENDER FUNCTIONS ---
            
            // --- HOME PAGE ---
            renderHomePage() {
                return `
                    <div class="page-container overflow-hidden">
                        <!-- Hero Section -->
                        <section class="relative flex items-center justify-center h-screen bg-slate-900 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/50 to-slate-900 z-10"></div>
                            <!-- Background elements -->
                            <div class="absolute inset-0 z-0 opacity-20">
                                <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-blue-600 rounded-full filter blur-3xl opacity-50 hero-float"></div>
                                <div class="absolute bottom-1/4 right-1/4 w-72 h-72 bg-purple-600 rounded-full filter blur-3xl opacity-50 hero-float" style="animation-delay: -3s;"></div>
                            </div>
                            
                            <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center z-20">
                                <h1 data-aos="fade-down" class="text-5xl md:text-7xl lg:text-8xl font-extrabold text-white leading-tight mb-6">
                                    Welcome to <span class="text-blue-400">Smart</span>
                                    <span class="text-purple-400">Uni-Verse</span> 2.0
                                </h1>
                                <p data-aos="fade-up" data-aos-delay="200" class="text-xl md:text-2xl text-slate-300 max-w-3xl mx-auto mb-10">
                                    Your AI-powered digital campus. Learn, collaborate, and innovate in the metaverse of education.
                                </p>
                                <div data-aos="fade-up" data-aos-delay="400" class="flex flex-col sm:flex-row items-center justify-center gap-4">
                                    <a href="#register" class="hero-cta-1 bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold px-8 py-4 rounded-lg shadow-lg transition duration-300 transform hover:scale-105">
                                        Join the Future
                                    </a>
                                    <a href="#courses" class="hero-cta-2 bg-transparent hover:bg-slate-800 text-slate-200 text-lg font-semibold px-8 py-4 rounded-lg border-2 border-slate-700 hover:border-slate-600 transition duration-300 transform hover:scale-105">
                                        Explore Courses
                                    </a>
                                </div>
                            </div>
                        </section>
                        
                        <!-- Stats Section -->
                        <section class="py-20 bg-slate-950">
                            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                                    <div data-aos="fade-up" class="glass-morphism p-8 rounded-xl">
                                        <div data-count="1000" class="stat-number text-5xl font-extrabold text-blue-400 mb-2">0+</div>
                                        <p class="text-lg text-slate-300">Active Students</p>
                                    </div>
                                    <div data-aos="fade-up" data-aos-delay="200" class="glass-morphism p-8 rounded-xl">
                                        <div data-count="50" class="stat-number text-5xl font-extrabold text-purple-400 mb-2">0+</div>
                                        <p class="text-lg text-slate-300">Future-Ready Courses</p>
                                    </div>
                                    <div data-aos="fade-up" data-aos-delay="400" class="glass-morphism p-8 rounded-xl">
                                        <div data-count="200" class="stat-number text-5xl font-extrabold text-green-400 mb-2">0+</div>
                                        <p class="text-lg text-slate-300">Faculty & Researchers</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                        
                        <!-- Features Section -->
                        <section class="py-24 bg-slate-900">
                            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                                <h2 class="text-4xl font-extrabold text-center text-white mb-16" data-aos="fade-up">A New Dimension of Learning</h2>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                    <!-- Feature 1: 3D Campus -->
                                    <div data-aos="fade-up" class="glass-morphism p-8 rounded-xl border border-slate-800 hover:border-blue-500 transition-colors duration-300">
                                        <i data-lucide="map" class="w-12 h-12 text-blue-400 mb-4"></i>
                                        <h3 class="text-2xl font-bold text-white mb-3">3D Virtual Campus</h3>
                                        <p class="text-slate-300">Explore a fully immersive 3D campus. Attend lectures, visit the library, and meet peers in a stunning virtual environment.</p>
                                    </div>
                                    <!-- Feature 2: AI Assistant -->
                                    <div data-aos="fade-up" data-aos-delay="200" class="glass-morphism p-8 rounded-xl border border-slate-800 hover:border-purple-500 transition-colors duration-300">
                                        <i data-lucide="brain-circuit" class="w-12 h-12 text-purple-400 mb-4"></i>
                                        <h3 class="text-2xl font-bold text-white mb-3">AI Study Assistant</h3>
                                        <p class="text-slate-300">Your personal AI tutor, available 24/7. Get help with complex topics, debug code, or brainstorm research ideas.</p>
                                    </div>
                                    <!-- Feature 3: Gamified Learning -->
                                    <div data-aos="fade-up" data-aos-delay="400" class="glass-morphism p-8 rounded-xl border border-slate-800 hover:border-green-500 transition-colors duration-300">
                                        <i data-lucide="award" class="w-12 h-12 text-green-400 mb-4"></i>
                                        <h3 class="text-2xl font-bold text-white mb-3">Gamified Learning</h3>
                                        <p class="text-slate-300">Earn points, unlock badges, and climb the leaderboard. Turn your academic journey into an engaging adventure.</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                `;
            },
            
            runGsapHeroAnimations() {
                // GSAP Animations for Hero
                gsap.from(".hero-cta-1", { duration: 1, y: 30, opacity: 0, delay: 0.6, ease: "power3.out" });
                gsap.from(".hero-cta-2", { duration: 1, y: 30, opacity: 0, delay: 0.8, ease: "power3.out" });
                
                // GSAP Stat Counters
                gsap.utils.toArray(".stat-number").forEach(el => {
                    const count = el.dataset.count;
                    gsap.fromTo(el, { innerText: 0 }, {
                        innerText: count,
                        duration: 2,
                        snap: { innerText: 1 },
                        scrollTrigger: {
                            trigger: el,
                            start: "top 80%",
                        },
                        onUpdate: function() {
                            el.innerText = Math.ceil(this.targets()[0].innerText) + "+";
                        }
                    });
                });
            },
            
            // --- AUTH PAGES ---
            renderLoginPage() {
                return `
                    <div class="page-container flex items-center justify-center bg-slate-900 py-12 px-4 sm:px-6 lg:px-8">
                        <div class="max-w-md w-full space-y-8 glass-morphism p-10 rounded-2xl shadow-xl" data-aos="fade-in">
                            <div>
                                <h2 class="mt-6 text-center text-4xl font-extrabold text-white">
                                    Welcome Back
                                </h2>
                                <p class="mt-2 text-center text-sm text-slate-300">
                                    Sign in to access your digital campus.
                                </p>
                            </div>
                            <form id="login-form" class="mt-8 space-y-6">
                                <input type="hidden" name="remember" value="true">
                                <div class="rounded-md shadow-sm -space-y-px">
                                    <div>
                                        <label for="email-address" class="sr-only">Email address</label>
                                        <input id="email-address" name="email" type="email" autocomplete="email" required
                                            class="appearance-none rounded-none relative block w-full px-3 py-3 border border-slate-700 bg-slate-800 text-white placeholder-slate-400 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                            placeholder="Email address" value="student@smartuni.edu">
                                    </div>
                                    <div>
                                        <label for="password" class="sr-only">Password</label>
                                        <input id="password" name="password" type="password" autocomplete="current-password" required
                                            class="appearance-none rounded-none relative block w-full px-3 py-3 border border-slate-700 bg-slate-800 text-white placeholder-slate-400 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                            placeholder="Password" value="student">
                                    </div>
                                </div>
                                
                                <div class="text-sm text-right">
                                    <a href="#" class="font-medium text-blue-400 hover:text-blue-300">
                                        Forgot your password?
                                    </a>
                                </div>
                                
                                <div>
                                    <button type="submit"
                                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-slate-900 transition duration-300">
                                        Sign in
                                    </button>
                                </div>
                                <p class="text-center text-sm text-slate-300">
                                    Don't have an account? <a href="#register" class="font-medium text-blue-400 hover:text-blue-300">Register here</a>
                                </p>
                            </form>
                        </div>
                    </div>
                `;
            },
            
            renderRegisterPage() {
                return `
                    <div class="page-container flex items-center justify-center bg-slate-900 py-12 px-4 sm:px-6 lg:px-8">
                        <div class="max-w-md w-full space-y-8 glass-morphism p-10 rounded-2xl shadow-xl" data-aos="fade-in">
                            <div>
                                <h2 class="mt-6 text-center text-4xl font-extrabold text-white">
                                    Join Smart Uni-Verse
                                </h2>
                                <p class="mt-2 text-center text-sm text-slate-300">
                                    Create your account to begin.
                                </p>
                            </div>
                            <form id="register-form" class="mt-8 space-y-6">
                                <div class="rounded-md shadow-sm space-y-4">
                                    <div>
                                        <label for="username" class="sr-only">Username</label>
                                        <input id="username" name="username" type="text" required
                                            class="appearance-none relative block w-full px-3 py-3 border border-slate-700 bg-slate-800 text-white placeholder-slate-400 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            placeholder="Username">
                                    </div>
                                    <div>
                                        <label for="email-address" class="sr-only">Email address</label>
                                        <input id="email-address" name="email" type="email" autocomplete="email" required
                                            class="appearance-none relative block w-full px-3 py-3 border border-slate-700 bg-slate-800 text-white placeholder-slate-400 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            placeholder="Email address">
                                    </div>
                                    <div>
                                        <label for="password" class="sr-only">Password</label>
                                        <input id="password" name="password" type="password" autocomplete="new-password" required
                                            class="appearance-none relative block w-full px-3 py-3 border border-slate-700 bg-slate-800 text-white placeholder-slate-400 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            placeholder="Password">
                                    </div>
                                </div>
                                
                                <div>
                                    <button type="submit"
                                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-slate-900 transition duration-300">
                                        Create Account
                                    </button>
                                </div>
                                <p class="text-center text-sm text-slate-300">
                                    Already have an account? <a href="#login" class="font-medium text-blue-400 hover:text-blue-300">Sign in</a>
                                </p>
                            </form>
                        </div>
                    </div>
                `;
            },
            
            // --- DASHBOARD PAGE ---
            renderDashboardPage() {
                const user = this.state.user;
                
                // Get enrolled courses
                const userEnrollments = this.state.db.enrollments.filter(e => e.user_id === user.id);
                const enrolledCourses = userEnrollments.map(enrollment => {
                    const course = this.state.db.courses.find(c => c.id === enrollment.course_id);
                    return { ...course, ...enrollment };
                }).slice(0, 3); // Show 3
                
                // Get recent forum threads
                const recentThreads = [...this.state.db.forum_threads].reverse().slice(0, 3);
                
                // Get recent badges
                const userBadges = this.state.db.user_achievements
                    .filter(ua => ua.user_id === user.id)
                    .map(ua => this.state.db.achievements.find(a => a.id === ua.achievement_id))
                    .reverse()
                    .slice(0, 5);
                
                return `
                    <div class="page-container bg-slate-900 py-12">
                        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                            <h1 class="text-4xl font-extrabold text-white mb-8" data-aos="fade-down">Welcome, ${user.username}!</h1>
                            
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                <!-- Main Content (Left/Top) -->
                                <div class="lg:col-span-2 space-y-8">
                                
                                    <!-- Enrolled Courses -->
                                    <div class="glass-morphism p-6 rounded-2xl" data-aos="fade-up">
                                        <h2 class="text-2xl font-bold text-white mb-4">My Courses</h2>
                                        <div class="space-y-4">
                                            ${enrolledCourses.length > 0 ? enrolledCourses.map(course => `
                                                <div class="bg-slate-800 p-4 rounded-lg flex items-center justify-between">
                                                    <div>
                                                        <h3 class="text-lg font-semibold text-white">${course.title}</h3>
                                                        <p class="text-sm text-slate-400">${course.department}</p>
                                                    </div>
                                                    <div class="w-1/3 text-right">
                                                        <p class="text-sm text-slate-300 mb-1">${course.progress}% Complete</p>
                                                        <div class="w-full bg-slate-700 rounded-full h-2.5">
                                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: ${course.progress}%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `).join('') : '<p class="text-slate-400">You are not enrolled in any courses. <a href="#courses" class="text-blue-400 hover:underline">Explore courses</a></p>'}
                                        </div>
                                        ${userEnrollments.length > 3 ? '<a href="#courses" class="text-blue-400 hover:underline mt-4 inline-block">View all courses...</a>' : ''}
                                    </div>
                                    
                                    <!-- Quick Actions -->
                                    <div class="glass-morphism p-6 rounded-2xl" data-aos="fade-up" data-aos-delay="100">
                                        <h2 class="text-2xl font-bold text-white mb-4">Quick Actions</h2>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                            <a href="#ai" class="flex flex-col items-center justify-center p-4 bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors">
                                                <i data-lucide="brain-circuit" class="w-10 h-10 text-purple-400 mb-2"></i>
                                                <span class="text-sm font-medium text-white text-center">AI Assistant</span>
                                            </a>
                                            <a href="#research" class="flex flex-col items-center justify-center p-4 bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors">
                                                <i data-lucide="flask-conical" class="w-10 h-10 text-green-400 mb-2"></i>
                                                <span class="text-sm font-medium text-white text-center">Research Hub</span>
                                            </a>
                                            <a href="#community" class="flex flex-col items-center justify-center p-4 bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors">
                                                <i data-lucide="users" class="w-10 h-10 text-blue-400 mb-2"></i>
                                                <span class="text-sm font-medium text-white text-center">Community</span>
                                            </a>
                                            <a href="#campus" class="flex flex-col items-center justify-center p-4 bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors">
                                                <i data-lucide="map" class="w-10 h-10 text-yellow-400 mb-2"></i>
                                                <span class="text-sm font-medium text-white text-center">3D Campus</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Student Progress Chart -->
                                    <div class="glass-morphism p-6 rounded-2xl" data-aos="fade-up" data-aos-delay="200">
                                        <h2 class="text-2xl font-bold text-white mb-4">My Progress</h2>
                                        <canvas id="dashboardProgressChart"></canvas>
                                    </div>

                                </div>
                                
                                <!-- Sidebar (Right/Bottom) -->
                                <div class="space-y-8">
                                
                                    <!-- Gamification Widget -->
                                    <div class="glass-morphism p-6 rounded-2xl" data-aos="fade-left" data-aos-delay="100">
                                        <h2 class="text-2xl font-bold text-white mb-4">My Achievements</h2>
                                        <div class="text-center mb-4">
                                            <div class="text-5xl font-extrabold text-blue-400">${user.total_points}</div>
                                            <p class="text-lg text-slate-300">Total Points</p>
                                        </div>
                                        <h3 class="text-lg font-semibold text-white mb-2">Recent Badges</h3>
                                        <div class="flex flex-wrap gap-3">
                                            ${userBadges.length > 0 ? userBadges.map(badge => `
                                                <div class="flex items-center p-2 bg-slate-800 rounded-lg" title="${badge.description}">
                                                    <i data-lucide="${badge.icon}" class="w-6 h-6 text-yellow-400"></i>
                                                    <span class="ml-2 text-sm text-white">${badge.badge_name}</span>
                                                </div>
                                            `).join('') : '<p class="text-slate-400 text-sm">No badges earned yet.</p>'}
                                        </div>
                                        <a href="#achievements" class="text-blue-400 hover:underline mt-4 inline-block">View all achievements...</a>
                                    </div>
                                
                                    <!-- Community Widget -->
                                    <div class="glass-morphism p-6 rounded-2xl" data-aos="fade-left" data-aos-delay="200">
                                        <h2 class="text-2xl font-bold text-white mb-4">Recent Discussions</h2>
                                        <div class="space-y-3">
                                            ${recentThreads.map(thread => `
                                                <a href="#thread-${thread.id}" class="block p-3 bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors">
                                                    <h4 class="text-md font-semibold text-white truncate">${thread.title}</h4>
                                                    <p class="text-sm text-slate-400">by ${this.getUserById(thread.user_id).username} &bull; ${this.formatDate(thread.created_at)}</p>
                                                </a>
                                            `).join('')}
                                        </div>
                                        <a href="#community" class="text-blue-400 hover:underline mt-4 inline-block">Go to community hub...</a>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            },
            
            renderDashboardCharts() {
                // Chart for Dashboard
                const ctx = document.getElementById('dashboardProgressChart');
                if (ctx) {
                    const userEnrollments = this.state.db.enrollments.filter(e => e.user_id === this.state.user.id);
                    const courseData = userEnrollments.map(enrollment => {
                        const course = this.state.db.courses.find(c => c.id === enrollment.course_id);
                        return { title: course.title, progress: enrollment.progress };
                    });

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: courseData.map(c => c.title),
                            datasets: [{
                                label: 'Course Progress',
                                data: courseData.map(c => c.progress),
                                backgroundColor: 'rgba(59, 130, 246, 0.6)', // blue-500
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    },
                                    ticks: {
                                        color: '#cbd5e1' // slate-300
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#cbd5e1' // slate-300
                                    }
                                }
                            }
                        }
                    });
                }
            },
            
            // --- COURSES PAGE ---
            renderCoursesPage() {
                const courses = this.state.db.courses;
                const userEnrollments = this.isLoggedIn() ? this.state.db.enrollments.filter(e => e.user_id === this.state.user.id).map(e => e.course_id) : [];
                
                return `
                    <div class="page-container bg-slate-900 py-12">
                        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                            <h1 class="text-4xl font-extrabold text-white mb-8" data-aos="fade-down">Course Catalog</h1>
                            
                            <!-- Filters -->
                            <div class="mb-8 p-4 glass-morphism rounded-xl" data-aos="fade-up">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <input type="text" id="course-search" placeholder="Search for courses..." class="w-full px-4 py-2 border border-slate-700 bg-slate-800 text-white placeholder-slate-400 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <select id="course-department" class="w-full px-4 py-2 border border-slate-700 bg-slate-800 text-white rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Departments</option>
                                        <option value="Computer Science">Computer Science</option>
                                        <option value="Physics">Physics</option>
                                        <option value="Digital Media">Digital Media</option>
                                        <option value="Engineering">Engineering</option>
                                    </select>
                                    <button id="filter-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">Filter</button>
                                </div>
                            </div>
                            
                            <!-- Course Grid -->
                            <div id="course-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                ${courses.map((course, index) => `
                                    <div class="glass-morphism rounded-xl overflow-hidden shadow-lg border border-slate-800 transition-all duration-300 hover:shadow-2xl hover:border-blue-500" data-aos="fade-up" data-aos-delay="${index * 100}">
                                        <img class="w-full h-48 object-cover" src="${course.cover_image_url}" alt="${course.title}">
                                        <div class="p-6">
                                            <span class="inline-block bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-full mb-3">${course.department}</span>
                                            <h3 class="text-2xl font-bold text-white mb-2">${course.title}</h3>
                                            <p class="text-slate-300 mb-4">${course.description}</p>
                                            <div class="flex justify-between items-center">
                                                <span class="text-lg font-semibold text-blue-300">${course.credits} Credits</span>
                                                ${this.isLoggedIn() ? (
                                                    userEnrollments.includes(course.id) 
                                                    ? `<button class="bg-slate-700 text-slate-400 px-4 py-2 rounded-md text-sm font-medium cursor-not-allowed" disabled>Enrolled</button>`
                                                    : `<button class="enroll-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium" data-course-id="${course.id}">Enroll</button>`
                                                ) : `<a href="#login" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">Login to Enroll</a>`}
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                `;
            },
            
            attachCourseHandlers() {
                // Filter button
                document.getElementById('filter-btn').addEventListener('click', () => {
                    const search = document.getElementById('course-search').value.toLowerCase();
                    const dept = document.getElementById('course-department').value;
                    const userEnrollments = this.isLoggedIn() ? this.state.db.enrollments.filter(e => e.user_id === this.state.user.id).map(e => e.course_id) : [];
                    
                    const filteredCourses = this.state.db.courses.filter(course => {
                        const matchesSearch = course.title.toLowerCase().includes(search) || course.description.toLowerCase().includes(search);
                        const matchesDept = dept === "" || course.department === dept;
                        return matchesSearch && matchesDept;
                    });
                    
                    const grid = document.getElementById('course-grid');
                    grid.innerHTML = filteredCourses.map((course, index) => `
                        <div class="glass-morphism rounded-xl overflow-hidden shadow-lg border border-slate-800 transition-all duration-300 hover:shadow-2xl hover:border-blue-500" data-aos="fade-up" data-aos-delay="${index * 100}">
                            <img class="w-full h-48 object-cover" src="${course.cover_image_url}" alt="${course.title}">
                            <div class="p-6">
                                <span class="inline-block bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-full mb-3">${course.department}</span>
                                <h3 class="text-2xl font-bold text-white mb-2">${course.title}</h3>
                                <p class="text-slate-300 mb-4">${course.description}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-blue-300">${course.credits} Credits</span>
                                    ${this.isLoggedIn() ? (
                                        userEnrollments.includes(course.id) 
                                        ? `<button class="bg-slate-700 text-slate-400 px-4 py-2 rounded-md text-sm font-medium cursor-not-allowed" disabled>Enrolled</button>`
                                        : `<button class="enroll-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium" data-course-id="${course.id}">Enroll</button>`
                                    ) : `<a href="#login" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">Login to Enroll</a>`}
                                </div>
                            </div>
                        </div>
                    `).join('');
                    
                    // Re-attach enroll handlers
                    this.attachEnrollHandlers();
                });
                
                // Enroll buttons
                this.attachEnrollHandlers();
            },
            
            attachEnrollHandlers() {
                document.querySelectorAll('.enroll-btn').forEach(button => {
                    button.addEventListener('click', (e) => {
                        if (!this.isLoggedIn()) {
                            this.redirectToLogin();
                            return;
                        }
                        
                        const courseId = parseInt(e.target.dataset.courseId);
                        
                        // Add enrollment
                        this.state.db.enrollments.push({
                            id: this.getNextId('enrollments'),
                            user_id: this.state.user.id,
                            course_id: courseId,
                            enrolled_at: new Date().toISOString(),
                            status: 'enrolled',
                            progress: 0
                        });
                        
                        // Gamification: Award first enrollment badge
                        if (this.state.db.enrollments.filter(en => en.user_id === this.state.user.id).length === 1) {
                            this.awardAchievement('First Enrollment');
                        }
                        
                        this.saveDatabase();
                        this.showToast('Successfully enrolled!', 'success');
                        
                        // Re-render the page to update button state
                        this.router();
                    });
                });
            },
            
            // --- AI ASSISTANT PAGE ---
            renderAiAssistantPage() {
                const chatHistory = this.state.db.ai_chat_history.filter(c => c.user_id === this.state.user.id);
                
                return `
                    <div class="page-container flex justify-center bg-slate-900 py-12 px-4">
                        <div class="w-full max-w-3xl glass-morphism rounded-2xl shadow-xl overflow-hidden flex flex-col" style="height: calc(100vh - 180px);" data-aos="fade-in">
                            <!-- Header -->
                            <div class="p-4 border-b border-slate-700">
                                <h2 class="text-2xl font-bold text-white text-center">AI Study Assistant</h2>
                            </div>
                            
                            <!-- Chat History -->
                            <div id="chat-history" class="flex-1 p-6 space-y-4 overflow-y-auto">
                                <!-- Initial AI message -->
                                <div class="flex">
                                    <div class="chat-bubble-ai p-4 rounded-lg max-w-lg">
                                        <p>Hello, ${this.state.user.username}! I'm your AI assistant. How can I help you today? You can ask me to explain concepts, debug code, or brainstorm ideas.</p>
                                    </div>
                                </div>
                                
                                ${chatHistory.map(chat => `
                                    <div class="flex ${chat.role === 'user' ? 'justify-end' : ''}">
                                        <div class="chat-bubble-${chat.role} p-4 rounded-lg max-w-lg">
                                            <p>${this.formatChatMessage(chat.message)}</p>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                            
                            <!-- Typing Indicator -->
                            <div id="typing-indicator" class="p-6 hidden">
                                <div class="flex">
                                    <div class="chat-bubble-ai p-4 rounded-lg">
                                        <div class="flex space-x-1">
                                            <div class="w-2 h-2 bg-slate-400 rounded-full animate-pulse" style="animation-delay: 0s;"></div>
                                            <div class="w-2 h-2 bg-slate-400 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                                            <div class="w-2 h-2 bg-slate-400 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Input Form -->
                            <form id="ai-chat-form" class="p-4 border-t border-slate-700 bg-slate-800">
                                <div class="flex items-center space-x-3">
                                    <input id="chat-input" type="text" placeholder="Type your message..." autocomplete="off"
                                        class="flex-1 px-4 py-3 border border-slate-700 bg-slate-900 text-white placeholder-slate-400 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg">
                                        <i data-lucide="send" class="w-6 h-6"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;
            },
            
            attachAiChatHandlers() {
                const form = document.getElementById('ai-chat-form');
                form.addEventListener('submit', this.handleAiChatSubmit.bind(this));
                
                // Scroll to bottom
                this.scrollChatToBottom();
            },
            
            scrollChatToBottom() {
                const chatHistory = document.getElementById('chat-history');
                if (chatHistory) {
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                }
            },
            
            formatChatMessage(message) {
                // Simple markdown-like formatting for code blocks
                return message.replace(/```(.*?)```/gs, (match, code) => {
                    return `<pre><code class="block whitespace-pre-wrap">${code.trim()}</code></pre>`;
                });
            },
            
            handleAiChatSubmit(e) {
                e.preventDefault();
                const input = document.getElementById('chat-input');
                const message = input.value.trim();
                
                if (!message) return;
                
                // Add user message to state
                this.state.db.ai_chat_history.push({
                    id: this.getNextId('ai_chat_history'),
                    user_id: this.state.user.id,
                    role: 'user',
                    message,
                    created_at: new Date().toISOString()
                });
                this.saveDatabase();
                
                // Add user message to UI
                const chatHistory = document.getElementById('chat-history');
                chatHistory.innerHTML += `
                    <div class="flex justify-end">
                        <div class="chat-bubble-user p-4 rounded-lg max-w-lg">
                            <p>${message}</p>
                        </div>
                    </div>
                `;
                
                input.value = '';
                this.scrollChatToBottom();
                
                // Show typing indicator
                document.getElementById('typing-indicator').classList.remove('hidden');
                
                // --- FAKE AI RESPONSE ---
                // In a real app, this would be an AJAX call to api/handle_ai_chat.php
                setTimeout(() => {
                    const aiResponse = this.getMockAiResponse(message);
                    
                    // Add AI response to state
                    this.state.db.ai_chat_history.push({
                        id: this.getNextId('ai_chat_history'),
                        user_id: this.state.user.id,
                        role: 'assistant',
                        message: aiResponse,
                        created_at: new Date().toISOString()
                    });
                    this.saveDatabase();
                    
                    // Add AI response to UI
                    chatHistory.innerHTML += `
                        <div class="flex">
                            <div class="chat-bubble-ai p-4 rounded-lg max-w-lg">
                                <p>${this.formatChatMessage(aiResponse)}</p>
                            </div>
                        </div>
                    `;
                    
                    // Hide typing indicator
                    document.getElementById('typing-indicator').classList.add('hidden');
                    this.scrollChatToBottom();
                    
                }, 1500);
            },
            
            getMockAiResponse(userMessage) {
                const msg = userMessage.toLowerCase();
                if (msg.includes('hello') || msg.includes('hi')) {
                    return "Hi there! How can I assist with your studies today?";
                }
                if (msg.includes('quantum computing')) {
                    return "Quantum computing uses quantum-mechanical phenomena like superposition and entanglement to perform computation. Unlike classical bits (0 or 1), a qubit can be in a superposition of both states simultaneously.";
                }
                if (msg.includes('debug')) {
                    return "Sure, I can help with that. Please paste your code, and I'll take a look. Remember to explain what it's supposed to do and what error you're getting. \nFor example: \n```\nfunction example() {\n  return 'hello'\n}\n```";
                }
                return "That's an interesting question! I'll need more information to help you with that. Could you please provide more details?";
            },
            
            // --- RESEARCH PAGE ---

