<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Anonymous Complaint Form</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      /* Subtle geometric background pattern from Hero Patterns */
      background-color: #f8fafc;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='49' viewBox='0 0 28 49'%3E%3Cg fill-rule='evenodd'%3E%3Cg id='hexagons' fill='%23e2e8f0' fill-opacity='0.4' fill-rule='nonzero'%3E%3Cpath d='M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.99-7.5L26 15v18.5l-13 7.5L0 33.5V15z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
  </style>
</head>
<body class="bg-gray-50">

  <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-12">
      
      <!-- Complaint Submission Form -->
      <div class="bg-white p-8 sm:p-10 rounded-2xl shadow-xl border border-gray-200/50">
        <div class="text-center">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">File a Complaint</h1>
          <p class="text-gray-600">Your identity will remain confidential and secure.</p>
        </div>
        
        <form action="submit_complaint.php" method="post" class="mt-8 space-y-6">
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Complaint Category</label>
            <input type="text" id="category" name="category" placeholder="e.g., Harassment, Workload, Safety Concern" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
          </div>
          
          <div>
            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Complaint Message</label>
            <textarea id="message" name="message" rows="5" placeholder="Please describe your issue in detail..." required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"></textarea>
          </div>
          
          <div>
            <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300">
              Submit Complaint Securely
            </button>
          </div>
        </form>
      </div>

      <!-- Complaint Tracking Form -->
      <div class="bg-white p-8 sm:p-10 rounded-2xl shadow-xl border border-gray-200/50">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Track Your Complaint</h2>
            <p class="text-gray-600">Enter your complaint ID to check the status.</p>
        </div>
        <form action="track_complaint.php" method="get" class="mt-8 sm:flex sm:items-center sm:gap-4">
          <div class="w-full mb-4 sm:mb-0">
            <label for="complaint_id" class="sr-only">Enter Complaint ID:</label>
            <input type="text" id="complaint_id" name="complaint_id" placeholder="Enter your unique complaint ID" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition duration-150 ease-in-out">
          </div>
          <button type="submit" class="w-full sm:w-auto flex-shrink-0 justify-center py-3 px-6 border border-transparent text-sm font-semibold rounded-lg text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-300">
            Track Status
          </button>
        </form>
      </div>

    </div>
  </div>

<!-- START: Live Chat Widget -->
<div class="fixed bottom-6 right-6 z-50">
    <!-- Chat Bubble Button -->
    <button id="chat-toggle-button" class="bg-indigo-600 text-white rounded-full p-4 shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-transform transform hover:scale-110">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
    </button>

    <!-- Chat Window -->
    <div id="chat-window" class="hidden absolute bottom-20 right-0 w-80 sm:w-96 bg-white rounded-xl shadow-2xl border border-gray-200/80 flex flex-col transition-all duration-300 transform translate-y-4 opacity-0">
        <!-- Header -->
        <div class="bg-indigo-600 text-white p-4 rounded-t-xl flex justify-between items-center">
            <h3 class="font-semibold">Live Chat with Admin</h3>
            <button id="chat-close-button" class="text-white hover:text-indigo-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
        </div>
        <!-- Messages Area -->
        <div id="chat-messages" class="p-4 h-80 overflow-y-auto space-y-4 flex-1">
            <!-- Admin Welcome Message -->
            <div class="flex items-start gap-2.5">
                <div class="flex flex-col w-full max-w-xs leading-1.5 p-3 border-gray-200 bg-gray-100 rounded-e-xl rounded-es-xl">
                    <p class="text-sm font-normal text-gray-900">Hello! How can we help you today?</p>
                </div>
            </div>
            <!-- Messages will be dynamically added here -->
        </div>
        <!-- Input Area -->
        <div class="p-4 border-t border-gray-200">
            <form id="chat-form" class="relative">
                <input type="text" id="chat-input" placeholder="Type your message..." autocomplete="off" class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-indigo-600 text-white rounded-full p-2 hover:bg-indigo-700">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" /></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Firebase SDKs -->
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>

<script>
    // --- Your Correct Firebase Configuration ---
    const firebaseConfig = {
        apiKey: "AIzaSyBCBiykwJl0X3kE6xPqrJI29Oz1z4fn_Ng",
        authDomain: "complaintsystemchat.firebaseapp.com",
        databaseURL: "https://complaintsystemchat-default-rtdb.firebaseio.com/",
        projectId: "complaintsystemchat",
        storageBucket: "complaintsystemchat.appspot.com",
        messagingSenderId: "1077457852214",
        appId: "1:1077457852214:web:1cf8863cbaade6097c0597"
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const database = firebase.database();

    // --- DOM Elements ---
    const chatToggleButton = document.getElementById('chat-toggle-button');
    const chatWindow = document.getElementById('chat-window');
    const chatCloseButton = document.getElementById('chat-close-button');
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');

    // --- Chat Logic ---
    let userId = localStorage.getItem('chatUserId');
    if (!userId) {
        userId = 'user_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('chatUserId', userId);
    }

    const chatRef = database.ref('chats/' + userId + '/messages');

    // Function to render a message
    const renderMessage = (sender, text) => {
        const isUser = sender === 'user';
        const messageContainer = document.createElement('div');
        messageContainer.className = `flex items-start gap-2.5 ${isUser ? 'justify-end' : ''}`;
        
        const messageBubble = document.createElement('div');
        messageBubble.className = `flex flex-col w-full max-w-xs leading-1.5 p-3 border-gray-200 ${isUser ? 'bg-indigo-500 text-white rounded-s-xl rounded-ee-xl' : 'bg-gray-100 rounded-e-xl rounded-es-xl'}`;
        
        const messageText = document.createElement('p');
        messageText.className = 'text-sm font-normal';
        messageText.textContent = text;

        messageBubble.appendChild(messageText);
        messageContainer.appendChild(messageBubble);
        chatMessages.appendChild(messageContainer);

        // Scroll to the bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };

    // Listen for new messages from the database
    chatRef.on('child_added', (snapshot) => {
        const message = snapshot.val();
        renderMessage(message.sender, message.text);
    });

    // Handle sending a message
    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const messageText = chatInput.value.trim();
        if (messageText) {
            const newMessage = {
                sender: 'user',
                text: messageText,
                timestamp: firebase.database.ServerValue.TIMESTAMP
            };
            chatRef.push(newMessage);
            
            // Also update the 'lastMessage' for the admin panel preview
            database.ref('chats/' + userId).update({
                lastMessage: messageText,
                timestamp: firebase.database.ServerValue.TIMESTAMP,
                unread: true
            });

            chatInput.value = '';
        }
    });

    // --- UI Toggle Logic ---
    const toggleChatWindow = () => {
        if (chatWindow.classList.contains('hidden')) {
            chatWindow.classList.remove('hidden');
            setTimeout(() => {
                chatWindow.classList.remove('opacity-0', 'translate-y-4');
            }, 10);
        } else {
            chatWindow.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => {
                chatWindow.classList.add('hidden');
            }, 300);
        }
    };
    
    chatToggleButton.addEventListener('click', toggleChatWindow);
    chatCloseButton.addEventListener('click', toggleChatWindow);

</script>
</body>
</html>
