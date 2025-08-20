<?php
session_start();
require 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// --- DATA FETCHING ---

// 1. Fetch data for Analytics Chart
$stmtAnalytics = $pdo->query("SELECT category, COUNT(*) as count FROM complaints GROUP BY category");
$analyticsData = $stmtAnalytics->fetchAll();
$chartLabels = [];
$chartData = [];
foreach ($analyticsData as $data) {
    $chartLabels[] = $data['category'];
    $chartData[] = $data['count'];
}
$chartLabelsJson = json_encode($chartLabels);
$chartDataJson   = json_encode($chartData);

// 2. Fetch all complaints for the list
$stmtComplaints = $pdo->query("SELECT id, category, message, status, created_at FROM complaints ORDER BY created_at DESC");
$complaints = $stmtComplaints->fetchAll();

// 3. Fetch data for Stat Cards
$totalComplaints = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$openComplaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Open'")->fetchColumn();
$resolvedComplaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Resolved'")->fetchColumn();


// --- HELPER FUNCTIONS ---

// Helper to get color class for status badges
function getStatusBadge($status) {
    switch (strtolower($status)) {
        case 'resolved':
            return 'bg-green-100 text-green-800';
        case 'in progress':
            return 'bg-yellow-100 text-yellow-800';
        case 'open':
        default:
            return 'bg-blue-100 text-blue-800';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex flex-col">
        <div class="p-6 text-2xl font-bold border-b border-gray-700">
            CMS Admin
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-gray-900 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a2 2 0 002 2h10a2 2 0 002-2V10M9 20h6"></path></svg>
                <span>Dashboard</span>
            </a>
            <a href="logout.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 sm:p-8 lg:p-10 overflow-y-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-600 mt-1">Overview of the complaint management system.</p>
        </header>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200/80">
                <h3 class="text-gray-500 font-medium">Total Complaints</h3>
                <p class="text-4xl font-bold text-gray-900 mt-2"><?php echo $totalComplaints; ?></p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200/80">
                <h3 class="text-gray-500 font-medium">Open Cases</h3>
                <p class="text-4xl font-bold text-blue-600 mt-2"><?php echo $openComplaints; ?></p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200/80">
                <h3 class="text-gray-500 font-medium">Resolved Cases</h3>
                <p class="text-4xl font-bold text-green-600 mt-2"><?php echo $resolvedComplaints; ?></p>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Complaints List (takes 2/3 width on large screens) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md border border-gray-200/80">
                <h2 class="text-xl font-bold text-gray-900 mb-4">All Complaints</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Category</th>
                                <th scope="col" class="px-6 py-3">Message</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Date</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($complaints)): ?>
                                <tr><td colspan="6" class="text-center py-8">No complaints found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($complaints as $comp): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-mono text-xs"><?php echo htmlspecialchars($comp['id']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($comp['category']); ?></td>
                                    <td class="px-6 py-4 max-w-xs truncate" title="<?php echo htmlspecialchars($comp['message']); ?>"><?php echo htmlspecialchars($comp['message']); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs <?php echo getStatusBadge($comp['status']); ?>">
                                            <?php echo htmlspecialchars($comp['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4"><?php echo date("M d, Y", strtotime($comp['created_at'])); ?></td>
                                    <td class="px-6 py-4 space-x-2 whitespace-nowrap">
                                        <a href="update_status.php?id=<?php echo $comp['id']; ?>&status=In+Progress" class="font-medium text-yellow-600 hover:underline">In Progress</a>
                                        <a href="update_status.php?id=<?php echo $comp['id']; ?>&status=Resolved" class="font-medium text-green-600 hover:underline">Resolve</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Analytics Chart (takes 1/3 width on large screens) -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200/80">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Analytics by Category</h2>
                <div class="h-80">
                    <canvas id="analyticsChart"></canvas>
                </div>
            </div>
        </div>
        <!-- START: Live Chat System -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
    <div class="lg:col-span-3 bg-white p-6 rounded-xl shadow-md border border-gray-200/80">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Live Chat System</h2>
        <div class="flex h-[32rem] border border-gray-200 rounded-lg">
            
            <!-- Left Pane: Conversation List -->
            <div class="w-1/3 border-r border-gray-200 flex flex-col">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold">Conversations</h3>
                </div>
                <div id="chat-list" class="flex-1 overflow-y-auto">
                    <!-- Chat list items will be dynamically added here -->
                    <p id="no-chats-message" class="p-4 text-center text-gray-500">Waiting for user chats...</p>
                </div>
            </div>

            <!-- Right Pane: Chat Window -->
            <div id="chat-panel" class="w-2/3 flex flex-col">
                <!-- Initial State: No chat selected -->
                <div id="chat-placeholder" class="flex-1 flex flex-col items-center justify-center text-center text-gray-500">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <h3 class="font-semibold">Select a conversation</h3>
                    <p class="text-sm">Choose a user from the list to view messages.</p>
                </div>

                <!-- Active Chat State (hidden by default) -->
                <div id="active-chat" class="hidden flex-1 flex flex-col">
                    <!-- Header -->
                    <div class="p-4 border-b border-gray-200">
                        <h3 id="chat-header" class="font-semibold">Chat with <span id="chat-with-user-id" class="font-mono text-sm text-indigo-600"></span></h3>
                    </div>
                    <!-- Messages -->
                    <div id="admin-chat-messages" class="flex-1 p-4 overflow-y-auto space-y-4">
                        <!-- Messages will be dynamically added here -->
                    </div>
                    <!-- Input -->
                    <div class="p-4 border-t border-gray-200">
                        <form id="admin-chat-form" class="relative">
                            <input type="text" id="admin-chat-input" placeholder="Type your reply..." autocomplete="off" class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-indigo-600 text-white rounded-full p-2 hover:bg-indigo-700">
                               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </main>
</div>
<!-- Firebase SDKs -->
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>

<script>
    // --- IMPORTANT: Use the SAME Firebase config as your user widget ---
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
    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }
    const database = firebase.database();

    // --- DOM Elements ---
    const chatList = document.getElementById('chat-list');
    const noChatsMessage = document.getElementById('no-chats-message');
    const chatPanel = document.getElementById('chat-panel');
    const chatPlaceholder = document.getElementById('chat-placeholder');
    const activeChat = document.getElementById('active-chat');
    const chatHeaderId = document.getElementById('chat-with-user-id');
    const adminChatMessages = document.getElementById('admin-chat-messages');
    const adminChatForm = document.getElementById('admin-chat-form');
    const adminChatInput = document.getElementById('admin-chat-input');

    // --- State ---
    let currentChatId = null;
    let messagesRef = null;

    // --- Functions ---

    // Render a message in the admin panel
    const renderAdminMessage = (sender, text) => {
        const isAdmin = sender === 'admin';
        const messageContainer = document.createElement('div');
        messageContainer.className = `flex items-start gap-2.5 ${isAdmin ? 'justify-end' : ''}`;
        
        const messageBubble = document.createElement('div');
        messageBubble.className = `flex flex-col w-full max-w-xs leading-1.5 p-3 border-gray-200 ${isAdmin ? 'bg-indigo-500 text-white rounded-s-xl rounded-ee-xl' : 'bg-gray-100 rounded-e-xl rounded-es-xl'}`;
        
        const messageText = document.createElement('p');
        messageText.className = 'text-sm font-normal';
        messageText.textContent = text;

        messageBubble.appendChild(messageText);
        messageContainer.appendChild(messageBubble);
        adminChatMessages.appendChild(messageContainer);
        adminChatMessages.scrollTop = adminChatMessages.scrollHeight;
    };

    // Select a chat to view
    const selectChat = (userId) => {
        currentChatId = userId;

        // Update UI
        chatPlaceholder.classList.add('hidden');
        activeChat.classList.remove('hidden');
        activeChat.classList.add('flex');
        chatHeaderId.textContent = userId;
        adminChatMessages.innerHTML = ''; // Clear previous messages

        // Mark chat as read in Firebase
        database.ref('chats/' + userId).update({ unread: false });
        
        // Detach old listener
        if (messagesRef) {
            messagesRef.off();
        }

        // Listen for messages in the selected chat
        messagesRef = database.ref('chats/' + userId + '/messages');
        messagesRef.on('child_added', (snapshot) => {
            const message = snapshot.val();
            renderAdminMessage(message.sender, message.text);
        });

        // Highlight active chat in the list
        document.querySelectorAll('#chat-list > div').forEach(div => {
            if (div.dataset.userId === userId) {
                div.classList.add('bg-indigo-50');
            } else {
                div.classList.remove('bg-indigo-50');
            }
        });
    };

    // Listen for chats being added or changed
    const chatsRef = database.ref('chats').orderByChild('timestamp');
    chatsRef.on('child_added', (snapshot) => {
        noChatsMessage.style.display = 'none';
        const chatData = snapshot.val();
        const userId = snapshot.key;

        const chatItem = document.createElement('div');
        chatItem.className = 'p-4 border-b border-gray-200 cursor-pointer hover:bg-gray-50 transition';
        chatItem.dataset.userId = userId;
        chatItem.innerHTML = `
            <div class="flex justify-between items-center">
                <p class="font-semibold text-sm truncate">${userId}</p>
                ${chatData.unread ? '<span class="w-3 h-3 bg-green-500 rounded-full"></span>' : ''}
            </div>
            <p class="text-xs text-gray-500 truncate mt-1">${chatData.lastMessage || 'No messages yet'}</p>
        `;
        
        chatList.prepend(chatItem); // Add new chats to the top
        chatItem.addEventListener('click', () => selectChat(userId));
    });

    chatsRef.on('child_changed', (snapshot) => {
        const chatData = snapshot.val();
        const userId = snapshot.key;
        const existingItem = document.querySelector(`[data-user-id="${userId}"]`);
        if (existingItem) {
            existingItem.innerHTML = `
                <div class="flex justify-between items-center">
                    <p class="font-semibold text-sm truncate">${userId}</p>
                    ${chatData.unread ? '<span class="w-3 h-3 bg-green-500 rounded-full"></span>' : ''}
                </div>
                <p class="text-xs text-gray-500 truncate mt-1">${chatData.lastMessage || 'No messages yet'}</p>
            `;
            chatList.prepend(existingItem); // Move updated chat to top
        }
    });

    // Handle sending a reply
    adminChatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const messageText = adminChatInput.value.trim();
        if (messageText && currentChatId) {
            const newMessage = {
                sender: 'admin',
                text: messageText,
                timestamp: firebase.database.ServerValue.TIMESTAMP
            };
            // Push the new message to the messages sub-collection
            database.ref('chats/' + currentChatId + '/messages').push(newMessage);
            
            // Update the lastMessage for the list preview
            database.ref('chats/' + currentChatId).update({
                lastMessage: messageText,
                timestamp: firebase.database.ServerValue.TIMESTAMP
            });

            adminChatInput.value = '';
        }
    });

</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut', // Changed to doughnut for a modern look
        data: {
            labels: <?php echo $chartLabelsJson; ?>,
            datasets: [{
                label: 'Complaint Count',
                data: <?php echo $chartDataJson; ?>,
                backgroundColor: [
                    'rgba(79, 70, 229, 0.8)',
                    'rgba(13, 148, 136, 0.8)',
                    'rgba(217, 119, 6, 0.8)',
                    'rgba(220, 38, 38, 0.8)',
                    'rgba(107, 114, 128, 0.8)'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    backgroundColor: '#111827',
                    padding: 10,
                    cornerRadius: 5
                }
            }
        }
    });
});
</script>
</body>
</html>
