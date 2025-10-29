<?php
// This is contact.php
include('includes/header.php');

$errors = [];
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and retrieve data
    $full_name = filter_var(trim($_POST['full_name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

    // 2. Validation
    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if (empty($subject)) {
        $errors[] = "Subject is required.";
    }
    if (empty($message)) {
        $errors[] = "Message cannot be empty.";
    }

    // 3. Send email
    if (empty($errors)) {
        $to = "admin@smart-uni-verse.com"; // !! CHANGE THIS !!
        $headers = "From: " . $full_name . " <" . $email . ">" . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $email_body = "You have received a new message from your website contact form.\n\n";
        $email_body .= "Name: $full_name\n";
        $email_body .= "Email: $email\n";
        $email_body .= "Subject: $subject\n";
        $email_body .= "Message:\n$message\n";

        if (mail($to, $subject, $email_body, $headers)) {
            $success = "Your message has been sent successfully. We will get back to you shortly!";
            // Clear form fields
            $_POST = [];
        } else {
            $errors[] = "There was a problem sending your message. Please try again later.";
        }
    }
}

?>

<div class="relative bg-gradient-to-r from-gray-700 to-gray-900 text-white py-24 px-6 text-center">
    
    <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-down">Get In Touch</h1>
    <p class="text-lg md:text-xl text-gray-300" data-aos="fade-up" data-aos-delay="100">We're here to help. Send us a message or find us on the map.</p>
</div>

<div class="container mx-auto px-6 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        
        <div data-aos="fade-right">
            <h2 class="text-3xl font-bold mb-6">Send Us a Message</h2>
            
            <?php if ($success): ?>
                <div class="bg-green-500/20 text-green-300 p-3 rounded-lg mb-4"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="bg-red-500/20 text-red-300 p-3 rounded-lg mb-4">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="contact.php" method="POST" class="space-y-6">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                    <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required class="mt-1 block w-full input-style">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required class="mt-1 block w-full input-style">
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                    <input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" required class="mt-1 block w-full input-style">
                </div>
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                    <textarea name="message" id="message" rows="6" required class="mt-1 block w-full input-style"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">
                        Send Message
                    </button>
                </div>
            </form>
            
            <blockquote>
                <p class="mt-6 p-4 bg-yellow-500/20 text-yellow-300 rounded-lg text-sm">
                    <strong>Note for XAMPP users:</strong> PHP's `mail()` function will not work on a default XAMPP setup. You must configure `php.ini` and `sendmail.ini` to use an external SMTP server (like Gmail). For production, using a library like **PHPMailer** is strongly recommended.
                </p>
            </blockquote>
        </div>
        
        <div data-aos="fade-left" data-aos-delay="100">
            <h2 class="text-3xl font-bold mb-6">Visit Us</h2>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden glassmorphism">
                <div class.="w-full h-64 bg-gray-300 dark:bg-gray-700">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.902931211776!2d-79.99836378522648!3d40.44062457936166!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8834f3e6a9f5d1e7%3A0x7b58c14f09d3b1e3!2sCarnegie%20Mellon%20University!5e0!3m2!1sen!2sus!4v1678886450123" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="p-6">
                    <p class="flex items-center text-lg mb-3"><i class="fas fa-map-marker-alt w-6 text-blue-500"></i> &nbsp; 123 Digital Drive, Metaverse, 90210</p>
                    <p class="flex items-center text-lg mb-3"><i class="fas fa-phone w-6 text-blue-500"></i> &nbsp; (123) 456-7890</p>
                    <p class="flex items-center text-lg"><i class="fas fa-envelope w-6 text-blue-500"></i> &nbsp; info@smart-uni-verse.com</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Simple style for form inputs */
.input-style {
    @apply border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg;
}
</style>

<?php include('includes/footer.php'); ?>