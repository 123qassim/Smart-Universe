<?php 
include('includes/header.php'); 
if (!isLoggedIn()) {
    // Optional: Allow non-logged-in users, or force login
    // header('Location: login.php');
    // exit();
}
?>

<div class="container mx-auto px-6 py-12" style="max-width: 800px;">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden glassmorphism">
        
        <div class="p-4 border-b dark:border-gray-700">
            <h2 class="text-2xl font-semibold text-center">
                <i class="fas fa-robot text-blue-500"></i> AI Study Assistant
            </h2>
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">Ask me to summarize notes, generate quizzes, or explain concepts.</p>
        </div>

        <div id="chat-window" class="p-6 h-96 overflow-y-auto space-y-4">
            <div class="flex">
                <div class="bg-gray-200 dark:bg-gray-700 p-3 rounded-lg max-w-xs">
                    <p class="text-sm">Hello! How can I help you study today?</p>
                </div>
            </div>
            </div>

        <form id="chat-form" class="p-4 border-t dark:border-gray-700 bg-gray-100 dark:bg-gray-900/50 flex items-center">
            <input 
                type="text" 
                id="message-input" 
                placeholder="Type your message..." 
                class="flex-1 px-4 py-2 rounded-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                autocomplete="off"
            >
            <button type="submit" id="send-button" class="ml-4 bg-blue-500 text-white p-3 rounded-full hover:bg-blue-600 focus:outline-none">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const chatWindow = document.getElementById('chat-window');
    const sendButton = document.getElementById('send-button');

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (message === '') return;

        // 1. Add user message to chat window
        appendMessage(message, 'user');
        messageInput.value = '';
        toggleLoading(true);

        try {
            // 2. Send message to backend API
            const response = await fetch('api/handle_ai_chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ prompt: message })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            // 3. Add AI response to chat window
            appendMessage(data.response, 'ai');

        } catch (error) {
            console.error('Error:', error);
            appendMessage('Sorry, I seem to be having trouble connecting. Please try again.', 'ai');
        } finally {
            toggleLoading(false);
        }
    });

    function appendMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('flex', 'mb-4');

        const contentDiv = document.createElement('div');
        contentDiv.classList.add('p-3', 'rounded-lg', 'max-w-md', 'text-sm');

        if (sender === 'user') {
            messageDiv.classList.add('justify-end');
            contentDiv.classList.add('bg-blue-500', 'text-white');
            contentDiv.textContent = text;
        } else {
            messageDiv.classList.add('justify-start');
            contentDiv.classList.add('bg-gray-200', 'dark:bg-gray-700');
            // Basic markdown for newlines (AI often uses \n)
            contentDiv.innerHTML = text.replace(/\n/g, '<br>');
        }
        
        messageDiv.appendChild(contentDiv);
        chatWindow.appendChild(messageDiv);
        // Scroll to bottom
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    function toggleLoading(isLoading) {
        if (isLoading) {
            sendButton.disabled = true;
            sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        } else {
            sendButton.disabled = false;
            sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
        }
    }
});
</script>

<?php include('includes/footer.php'); ?>