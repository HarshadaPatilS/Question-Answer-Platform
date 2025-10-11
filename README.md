# Question & Answer Platform

A fully functional web-based Q&A platform similar to Stack Overflow, built with PHP, MySQL, and Bootstrap.

## Features

### User Management
- User registration and authentication
- Secure password hashing
- User profiles with statistics
- Session management

### Question Features
- Post questions with title, description, and category
- View question details
- Track question views
- Like/upvote questions
- Category-based filtering
- Related questions display

### Answer Features
- Post answers to questions
- Like/upvote answers
- Answer sorting by likes
- Multiple answers per question

### Interactive UI
- Responsive design with Bootstrap 5
- AJAX-based like system
- Real-time notifications
- Clean and modern interface
- Mobile-friendly layout

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5.3
- **Libraries**: jQuery 3.6
- **Icons**: Bootstrap Icons
- **Tools**: XAMPP, phpMyAdmin, VS Code

## Installation Instructions

### Prerequisites
- XAMPP (or any PHP + MySQL environment)
- Web browser
- Text editor (VS Code recommended)

### Step 1: Setup XAMPP
1. Download and install XAMPP from https://www.apachefriends.org/
2. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Clone/Download Project
```bash
git clone https://github.com/HarshadaPatilS/Question-Answer-Platform.git
```
Or download ZIP and extract to `C:\xampp\htdocs\qa-platform\`

### Step 3: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `qa_platform`
3. Import the `database/setup.sql` file
   - Click on the `qa_platform` database
   - Go to "Import" tab
   - Choose `setup.sql` file
   - Click "Go"

### Step 4: Configure Database Connection
Open `config/database.php` and update if needed:
```php
private $host = "localhost";
private $db_name = "qa_platform";
private $username = "root";
private $password = "";
private $port = "4306";
```

### Step 5: Run the Application
1. Open your browser
2. Navigate to `http://localhost/qa-platform/`
3. Sign up for a new account or use demo credentials:
   - Username: john_doe
   - Password: password

## Project Structure

```
qa-platform/
├── config/
│   └── database.php          # Database connection
├── includes/
│   ├── header.php           # Common header
│   ├── footer.php           # Common footer
│   └── functions.php        # Helper functions
├── assets/
│   ├── css/
│   │   └── style.css        # Custom styles
│   ├── js/
│   │   └── script.js        # Custom JavaScript
│   └── images/              # Images
├── auth/
│   ├── login.php            # Login page
│   ├── signup.php           # Registration
│   └── logout.php           # Logout
├── questions/
│   ├── ask_question.php     # Create question
│   ├── view_question.php    # View details
│   └── like_question.php    # Like handler
├── answers/
│   ├── post_answer.php      # Submit answer
│   └── like_answer.php      # Like handler
├── user/
│   ├── profile.php          # User profile
│   └── my_questions.php     # User's questions
├── database/
│   └── setup.sql            # Database schema
├── index.php                # Homepage
└── README.md                # Documentation
```

## Database Schema

### Tables
1. **users** - User accounts
2. **questions** - Posted questions
3. **answers** - Question answers
4. **question_likes** - Question upvotes
5. **answer_likes** - Answer upvotes
6. **tags** - Question tags (optional)
7. **question_tags** - Question-tag relationships

## Usage Guide

### For Users
1. **Register**: Create account with username, email, password
2. **Login**: Access your account
3. **Ask Question**: Post new questions with details
4. **Answer**: Help others by answering questions
5. **Like**: Upvote helpful questions and answers
6. **Profile**: View your statistics and activity

### For Developers
1. Clone the repository
2. Set up local environment
3. Import database
4. Configure settings
5. Start development

## API Endpoints (AJAX)

- `POST /questions/like_question.php` - Like a question
- `POST /answers/like_answer.php` - Like an answer
- `POST /answers/post_answer.php` - Submit answer

---

<img width="1887" height="985" alt="Screenshot (133)" src="https://github.com/user-attachments/assets/6c9e4a52-7f47-4c2a-bc0b-a4b3d8405434" />

<img width="1892" height="992" alt="Screenshot (136)" src="https://github.com/user-attachments/assets/c8b8506b-f41a-4a80-b93a-7e659f342f27" />

<img width="1890" height="986" alt="Screenshot (134)" src="https://github.com/user-attachments/assets/c1eeace3-d064-4d95-8aed-f65f7258b172" />

<img width="1920" height="785" alt="Screenshot (135)" src="https://github.com/user-attachments/assets/4be97cc1-d2e3-4e16-a3a1-2840c0f738e0" />

<img width="1891" height="986" alt="Screenshot (138)" src="https://github.com/user-attachments/assets/7f77f661-9501-452a-a850-595fd26331cc" />

<img width="1888" height="979" alt="Screenshot (140)" src="https://github.com/user-attachments/assets/c79455f9-9597-4cd7-a80e-8cd980625ef6" />

<img width="1887" height="985" alt="Screenshot (141)" src="https://github.com/user-attachments/assets/d492ff75-89bc-4d34-bf2f-06dc982c2b37" />

<img width="1889" height="981" alt="Screenshot (147)" src="https://github.com/user-attachments/assets/38dea7c7-4bb4-4449-9cd4-756899538b6b" />

<img width="1891" height="987" alt="Screenshot (144)" src="https://github.com/user-attachments/assets/e6081bb9-51ff-4689-94d5-ca1b876be82f" />

<img width="1893" height="984" alt="Screenshot (145)" src="https://github.com/user-attachments/assets/3a24057d-3e3f-4833-b35e-854c93cf61ab" />

<img width="1887" height="982" alt="Screenshot (146)" src="https://github.com/user-attachments/assets/055f1488-ff35-4154-9697-bd977eace193" />
