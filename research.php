<?php
// This is research.php
include('includes/header.php');

// Fetch all *approved* research papers to display
try {
    $stmt = $pdo->prepare("
        SELECT r.*, u.full_name 
        FROM research_papers r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.is_approved = 1
        ORDER BY r.uploaded_at DESC
    ");
    $stmt->execute();
    $papers = $stmt->fetchAll();
} catch (PDOException $e) {
    $papers = [];
    error_log("Research Hub Error: " . $e->getMessage());
}
?>

<div class="relative bg-gradient-to-r from-teal-800 to-indigo-800 text-white py-24 px-6 text-center">
    
    <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-down">Research & Innovation Hub</h1>
    <p class="text-lg md:text-xl text-blue-200" data-aos="fade-up" data-aos-delay="100">Upload, share, and discover research with AI-powered insights.</p>
</div>

<div class="container mx-auto px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
        <div class="lg:col-span-1" data-aos="fade-right">
            <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg sticky top-24">
                <h2 class="text-2xl font-semibold mb-4">Upload Your Paper</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-4 text-sm">Upload a PDF. Our AI will generate a summary and find collaborators. Your paper will be public after admin approval.</p>
                
                <form id="upload-form" enctype="multipart/form-data">
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Paper Title</label>
                            <input type="text" name="title" id="title" required class="mt-1 block w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="paper_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">PDF File</label>
                            <input type="file" name="paper_file" id="paper_file" required accept="application/pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                        </div>
                        <button type="submit" id="upload-button" class="w-full bg-blue-500 text-white px-5 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">
                            <i class="fas fa-upload"></i> Upload & Summarize
                        </button>
                    </div>
                </form>
                
                <div id="upload-status" class="mt-4 text-sm"></div>

                <div id="collaborator-suggestions" class="mt-4"></div>
            </div>
        </div>
        
        <div class="lg:col-span-2 space-y-6" data-aos="fade-left" data-aos-delay="100">
            <h2 class="text-3xl font-bold">Community Submissions</h2>
            
            <?php if (empty($papers)): ?>
                <p class="text-gray-500 dark:text-gray-400">No research papers have been approved yet. Be the first to contribute!</p>
            <?php else: ?>
                <?php foreach ($papers as $paper): ?>
                    <div class="glassmorphism bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                        <h3 class="text-2xl font-semibold text-blue-600 dark:text-blue-400"><?php echo htmlspecialchars($paper['title']); ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                            Uploaded by <span class="font-medium"><?php echo htmlspecialchars($paper['full_name']); ?></span> on <?php echo date('M d, Y', strtotime($paper['uploaded_at'])); ?>
                        </p>
                        
                        <h4 class="font-semibold mt-4 mb-2">AI-Generated Summary</h4>
                        <div class="text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/50 p-4 rounded-lg">
                            <?php if (!empty($paper['ai_summary'])): ?>
                                <?php echo nl2br(htmlspecialchars($paper['ai_summary'])); ?>
                            <?php else: ?>
                                <p class="text-gray-500 italic">Summary is processing...</p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($paper['ai_keywords'])): ?>
                            <h4 class="font-semibold mt-4 mb-2">AI Keywords</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach (explode(',', $paper['ai_keywords']) as $keyword): ?>
                                    <span class="inline-block bg-blue-500/20 text-blue-300 text-xs font-semibold px-3 py-1 rounded-full">
                                        <?php echo htmlspecialchars(trim($keyword)); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <a href="<?php echo BASE_URL . htmlspecialchars($paper['file_path']); ?>" target="_blank" class="text-blue-500 font-semibold hover:underline">
                                <i class="fas fa-file-pdf"></i> Download/View PDF
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const uploadForm = document.getElementById('upload-form');
    const uploadButton = document.getElementById('upload-button');
    const uploadStatus = document.getElementById('upload-status');
    // NEW: Collaborator suggestions div
    const collabSuggestions = document.getElementById('collaborator-suggestions');

    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Check login status (simple client-side check)
        <?php if (!isLoggedIn()): ?>
            uploadStatus.innerHTML = `<div class="p-3 bg-red-500/20 text-red-300 rounded-lg">You must be <a href="login.php" class="font-bold underline">logged in</a> to upload.</div>`;
            return;
        <?php endif; ?>

        const formData = new FormData(uploadForm);
        
        // Disable button and show loading
        uploadButton.disabled = true;
        uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        uploadStatus.innerHTML = `<div class="p-3 bg-blue-500/20 text-blue-300 rounded-lg">Uploading & generating AI insights. This may take a moment...</div>`;
        collabSuggestions.innerHTML = ''; // Clear old suggestions

        try {
            const response = await fetch('api/handle_research_upload.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                uploadStatus.innerHTML = `<div class="p-3 bg-green-500/20 text-green-300 rounded-lg">${result.message}</div>`;
                uploadForm.reset();

                // --- NEW: Handle Collaborators ---
                if (result.collaborators && result.collaborators.length > 0) {
                    let collabHtml = '<h3 class="font-semibold text-lg mb-2">Potential Collaborators Found!</h3>';
                    collabHtml += '<p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Our AI found other users with similar research interests:</p>';
                    collabHtml += '<ul class="space-y-2">';
                    
                    result.collaborators.forEach(user => {
                        collabHtml += `
                            <li class="flex items-center space-x-3 bg-gray-100 dark:bg-gray-700/50 p-2 rounded-lg">
                                <i class="fas fa-user-circle text-blue-400"></i>
                                <span class="font-medium">${user.full_name}</span>
                            </li>
                        `;
                    });
                    
                    collabHtml += '</ul>';
                    collabSuggestions.innerHTML = collabHtml;
                } else {
                    collabSuggestions.innerHTML = `<p class="text-sm text-gray-500 dark:text-gray-400">No immediate collaborators found. Your paper will help others find you once approved!</p>`;
                }
                // --- END NEW ---

                // Don't reload, as the paper needs approval first.
                // setTimeout(() => location.reload(), 5000); 
            } else {
                throw new Error(result.message);
            }

        } catch (error) {
            uploadStatus.innerHTML = `<div class="p-3 bg-red-500/20 text-red-300 rounded-lg">Error: ${error.message}</div>`;
        } finally {
            // Re-enable button
            uploadButton.disabled = false;
            uploadButton.innerHTML = '<i class="fas fa-upload"></i> Upload & Summarize';
        }
    });
});
</script>

<?php include('includes/footer.php'); ?>