# 🧠 Smart Uni-Verse 2.0: AI-Powered Digital University

`[PHP 8]` `[MySQL]` `[Tailwind CSS]` `[JavaScript]` `[Three.js]` `[OpenAI]`

Smart Uni-Verse 2.0 is a full-stack, futuristic digital university ecosystem built with PHP, MySQL, and a modern frontend stack. It's designed as an all-in-one platform for students, faculty, and administrators, integrating next-generation AI features, community hubs, and a 3D "metaverse" campus portal.



---

## ✨ Key Features

### 👨‍🎓 Core Student Features
* **Secure Authentication:** Full registration, login, and secure session management.
* **Dynamic Dashboard:** A personalized hub showing enrolled courses, progress, payment status, and recent achievements.
* **Course Catalog:** Browse all available courses with search and filter functionality.
* **Enrollment System:** One-click enrollment into courses, instantly reflected on the dashboard.
* **Event System:** View a list of upcoming virtual events and RSVP with a single click.
* **Billing & Payments:** A subscription/payment page with a dynamic payment history.
* **PDF Receipt Generation:** Downloadable PDF receipts (using FPDF) for all completed payments.

### 🤖 Smart AI Features
* **AI Study Assistant:** A ChatGPT-style chat interface (powered by the OpenAI API) to summarize notes, generate quizzes, and answer academic questions.
* **AI Research Summarizer:** Upload PDF research papers, and the system uses AI to extract text, generate a concise summary, and list key terms.
* **AI Collaborator Finder:** When a user uploads research, the AI analyzes its keywords, finds related topics, and searches the database to suggest other users with similar research interests.

### 🎮 Community & Metaverse
* **3D Virtual Campus:** A fully interactive 3D campus portal built with **Three.js**. Users can navigate the scene and click on 3D buildings to access different parts of the site (e.g., "Library" links to Research, "Lecture Hall" links to Courses).
* **Gamification System:**
    * **Points & Badges:** Earn points and achievements for activities (joining, enrolling, RSVP-ing, uploading research).
    * **Leaderboard:** A dedicated page shows the user's rank and a top-10 leaderboard.
* **Community Forums:**
    * A complete forum system with categories, threads, and replies.
    * Users can create new threads, post replies, and see recent activity on their dashboard.

### 🧑‍💻 Full Admin Panel
* **Analytics Dashboard:** The admin homepage features "at-a-glance" stats (revenue, users, etc.) and quick-action links.
* **Detailed Analytics Page:** A separate page with Chart.js graphs for user signups, daily revenue, and course popularity.
* **User Management (CRUD):** View, edit (name, email, role), and delete all users from a central table.
* **Course Management (CRUD):** Create, read, edit (with file upload), and delete all courses in the catalog.
* **Event Management (CRUD):** Create, edit, and delete all events shown on the events page.
* **Research Management:** An approval queue where admins can review user-submitted research, approve it to make it public, or delete it.

---

## 🛠️ Technology Stack

* **Frontend:**
    * HTML5, CSS3
    * **Tailwind CSS:** For all styling and the glassmorphism UI.
    * **JavaScript (ES6+):** For all interactivity (dark mode, modals, async chat).
    * **Three.js:** For the 3D Virtual Campus.
    * **AOS (Animate on Scroll):** For scroll animations.
    * **GSAP:** For hero section animations.
    * **Chart.js:** For all admin analytics graphs.

* **Backend:**
    * **PHP 8:** Core application logic.
    * **PDO (PHP Data Objects):** For secure, prepared-statement-based database interaction.
    * **Secure Sessions:** For handling user authentication.

* **Database:**
    * **MySQL:** (Managed via XAMPP/phpMyAdmin).

* **APIs & Libraries:**
    * **OpenAI API:** For all AI features (via PHP cURL).
    * **FPDF:** For server-side PDF receipt generation.
    * **smalot/pdfparser (Composer):** Required for extracting text from research PDFs.

---

## 🗂️ Project Structure

smart-uni-verse/
├─ admin/
│  ├─ analytics.php
│  ├─ index.php
│  ├─ manage_courses.php
│  ├─ manage_events.php
│  ├─ manage_research.php
│  └─ manage_users.php
├─ api/
│  ├─ handle_ai_chat.php
│  └─ handle_research_upload.php
├─ assets/
│  ├─ css/ (style.css)
│  ├─ js/ (main.js)
│  └─ images/
├─ database/
│  └─ smartuniverse.sql
├─ fpdf/
│  └─ (fpdf library files)
├─ includes/
│  ├─ config.php
│  ├─ header.php
│  ├─ footer.php
│  ├─ ai_helper.php
│  └─ gamification_helper.php
├─ uploads/
│  ├─ courses/
│  └─ research/
├─ vendor/
│  └─ (Composer dependencies, e.g., pdfparser)
├─ about.php
├─ achievements.php
├─ campus.php
├─ community.php
├─ contact.php
├─ courses.php
├─ dashboard.php
├─ enroll.php
├─ events.php
├─ generate_receipt.php
├─ index.php
├─ login.php
├─ logout.php
├─ payments.php
├─ register.php
├─ research.php
├─ rsvp.php
├─ thread.php
└─ composer.json
```

---

## 🚀 Installation & Setup

Follow these steps to run the project on your local machine.

### 1. Prerequisites
* **XAMPP:** Install [XAMPP](https://www.apachefriends.org/index.html) (or any other Apache/MySQL/PHP server like WAMP or MAMP).
* **Composer:** Install [Composer](https://getcomposer.org/) for PHP package management.

### 2. Download Project
* Download the ZIP and extract it to your XAMPP `htdocs` folder.
* Rename the folder to `smart-uni-verse`. The final path should be `C:/xampp/htdocs/smart-uni-verse`.

### 3. Database Setup
1.  Start the **Apache** and **MySQL** services in your XAMPP Control Panel.
2.  Open your browser and go to `http://localhost/phpmyadmin`.
3.  Create a new database named `smartuniverse`.
4.  Select the `smartuniverse` database and click the **Import** tab.
5.  Upload the `database/smartuniverse.sql` file from the project.
6.  **Crucially,** you must also run the SQL queries from our feature implementations. Go to the **SQL** tab for the `smartuniverse` database and run the following commands:

    ```sql
    -- Add column for research approval
    ALTER TABLE `research_papers`
    ADD COLUMN `is_approved` TINYINT(1) NOT NULL DEFAULT 0 AFTER `ai_summary`;
    
    -- Add column for AI keywords
    ALTER TABLE `research_papers`
    ADD COLUMN `ai_keywords` VARCHAR(255) NULL DEFAULT NULL AFTER `ai_summary`;
    
    -- Add 'points' column to users
    ALTER TABLE `users`
    ADD COLUMN `points` INT(11) NOT NULL DEFAULT 0 AFTER `profile_pic`;
    
    -- Create 'achievements' table
    CREATE TABLE `achievements` (
      `achievement_id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(100) NOT NULL UNIQUE, `description` VARCHAR(255) NOT NULL,
      `icon` VARCHAR(50) NOT NULL DEFAULT 'fas fa-star', `points` INT(11) NOT NULL DEFAULT 10,
      PRIMARY KEY (`achievement_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    -- Create 'user_achievements' table
    CREATE TABLE `user_achievements` (
      `user_achievement_id` INT(11) NOT NULL AUTO_INCREMENT,
      `user_id` INT(11) NOT NULL, `achievement_id` INT(11) NOT NULL,
      `date_earned` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`user_achievement_id`),
      FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
      FOREIGN KEY (`achievement_id`) REFERENCES `achievements`(`achievement_id`),
      UNIQUE KEY `user_badge` (`user_id`,`achievement_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    -- Create forum tables
    CREATE TABLE `forum_categories` (
      `category_id` INT(11) NOT NULL AUTO_INCREMENT, `name` VARCHAR(100) NOT NULL,
      `description` VARCHAR(255), `icon` VARCHAR(50) DEFAULT 'fas fa-comments',
      PRIMARY KEY (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE `forum_threads` (
      `thread_id` INT(11) NOT NULL AUTO_INCREMENT, `category_id` INT(11) NOT NULL,
      `user_id` INT(11) NOT NULL, `title` VARCHAR(255) NOT NULL,
      `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `is_sticky` TINYINT(1) NOT NULL DEFAULT 0,
      PRIMARY KEY (`thread_id`),
      FOREIGN KEY (`category_id`) REFERENCES `forum_categories`(`category_id`) ON DELETE CASCADE,
      FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE `forum_posts` (
      `post_id` INT(11) NOT NULL AUTO_INCREMENT, `thread_id` INT(11) NOT NULL,
      `user_id` INT(11) NOT NULL, `content` TEXT NOT NULL,
      `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`post_id`),
      FOREIGN KEY (`thread_id`) REFERENCES `forum_threads`(`thread_id`) ON DELETE CASCADE,
      FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ```

### 4. Install Dependencies

1.  **FPDF (PDF Receipts):**
    * Go to [fpdf.org](http://www.fpdf.org/en/download.php) and download the latest version.
    * Extract the ZIP file.
    * Place the resulting `fpdf` folder (containing `fpdf.php`, `font/`, etc.) into the `smart-uni-verse/` root directory.

2.  **PDFParser (AI Research):**
    * Open a terminal or command prompt in the project root (`C:/xampp/htdocs/smart-uni-verse`).
    * Run the following Composer command:
        ```bash
        composer require smalot/pdfparser
        ```
    * This will create a `vendor` folder and a `composer.json` file.

### 5. Configure API Keys
1.  Open the file `includes/config.php`.
2.  Set your database credentials (XAMPP defaults are `root` for user and `''` (empty) for password).
    ```php
    define('DB_USER', 'root');
    define('DB_PASS', '');
    ```
3.  Add your **OpenAI API Key**:
    ```php
    define('OPENAI_API_KEY', 'YOUR_OPENAI_API_KEY_HERE');
    ```

### 6. Run the Application
You're all set! Open your browser and navigate to:
**`http://localhost/smart-uni-verse/`**

---

## 🔐 Admin Access

To access the admin panel, you must manually promote a user.

1.  **Register** a new account on the website normally.
2.  Go to `http://localhost/phpmyadmin` and open the `users` table.
3.  Find the user you just created.
4.  Click **Edit** and change the value in the `role` column from `student` to `admin`.
5.  Save the changes.
6.  Log out and log back in with that user. You will now see the Admin Dashboard and be able to access the `/admin` pages.