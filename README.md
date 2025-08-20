<b><h1>Anonymous Complaint/Feedback Management System with Live Chat</h1></b>
A full-stack web application built with PHP and Firebase that allows users to anonymously submit and track complaints/Feedback, and provides a comprehensive dashboard for administrators to manage them in real-time.

<b>🚀 Key Features</b>
User Features
📝 Anonymous Submission: Users can file complaints without needing to create an account.

🆔 Unique Tracking ID: Each complaint receives a unique ID for status tracking.

💬 Live Chat: A real-time chat widget to connect instantly with an administrator for support.
<br><br>

<b>Admin Features</b>
🔒 Secure Login: A password-protected dashboard for administrators.

📊 At-a-Glance Stats: View key metrics like total, open, and resolved complaints.

📋 Complaint Management: A detailed table to view and update the status of all complaints.

📈 Visual Analytics: A dynamic chart showing the distribution of complaints by category.

💬 Real-time Chat Panel: A dedicated interface to manage multiple user chats simultaneously.<br><br>

<b>🛠️ Technology Stack
This project leverages a combination of classic backend technologies and modern real-time services.</b>

<p>| Category | Technology | Description |</p>
<p>| Frontend | HTML5, Tailwind CSS, JavaScript (ES6) | For structuring, styling, and providing client-side interactivity. |</p>
<p>| Backend | PHP | The server-side language for form processing, database interaction, and session management. |</p>
<p>| Database | MySQL | Stores all persistent complaint data (ID, category, status, etc.). |</p>
<p>| Real-time | Google Firebase (Realtime Database) | Powers the instant, two-way communication for the live chat feature. |</p>
| Web Server | Apache (via XAMPP) | The local server environment used for development. |<br><br>

<b>⚙️ System Architecture
The application is divided into two main parts: the core PHP/MySQL system for complaint management and the Firebase system for live chat.</b><br>

<p>USER BROWSER                                  SERVER (PHP/MySQL)                                  ADMIN BROWSER
      |                                              |                                                 |
      |---(Submit Complaint)--->|                      |                                                 |
      |                        |---(Save to MySQL)--->|                                                 |
      |                        |<--(Return ID)--------|                                                 |
      |<--(Show Success)-------|                      |                                                 |
      |                                              |---(Fetch Complaints)--->|                         |
      |                                              |<--(Return Data)---------|                         |
      |                                              |                         |---(Display Dashboard)-->|
      |                                              |                         |                         |
      |                                              +-------------------------+                         |
      |                                                                                                  |
      +--------------------(Firebase: Send Message)----------------------------+                         |
      |                                                                        |                         |
      |<-------------------(Firebase: Receive Reply)---------------------------+                         |
      |                                                                                                  |
      +------------------------------------------------------------------------+---(Firebase: Receive Message, Send Reply)--->|</p>



🚀 Getting Started
Follow these instructions to set up the project on your local machine.

Prerequisites
XAMPP installed (or any other Apache/MySQL/PHP environment).

1. Clone the Repository
git clone https://github.com/singh-dn/complaint-management-system.git
cd complaint-management-system



2. Set Up the Database
Start the Apache and MySQL services in your XAMPP control panel.

Open your browser and go to http://localhost/phpmyadmin/.

Create a new database named complaint_system.

Select the database, go to the SQL tab, and execute the following query to create the complaints table:

CREATE TABLE `complaints` (
  `id` varchar(255) NOT NULL PRIMARY KEY,
  `category` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
);



3. Configure Firebase
Create a new project in the Firebase Console.

Add a Web App and copy your firebaseConfig credentials.

Go to Build > Realtime Database, create a database, and start in test mode.

In the Rules tab, replace the default rules with:

{
  "rules": {
    "chats": {
      "$uid": {
        ".read": "auth == null || auth.uid != null",
        ".write": "auth == null || auth.uid != null"
      }
    }
  }
}



Click Publish.

Paste your firebaseConfig object into the <script> sections of index.php and admin_dashboard.php.

4. Run the Application
Place the project folder inside your XAMPP htdocs directory.

Open your browser and navigate to http://localhost/complaint-management-system/.

📖 Usage
User
Navigate to the main page to submit a complaint or track an existing one using its ID.

Use the floating chat bubble in the bottom-right corner to talk to an admin.

Admin
Navigate to http://localhost/complaint-management-system/admin_login.php.

Admin ID: admin

Password: password

From the dashboard, you can view all complaints, see analytics, and manage live chats.

<h3>👨‍💻 Author
Dev Singh - MCA Student</h3>
