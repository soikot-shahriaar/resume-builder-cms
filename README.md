# 📄 Resume Builder CMS

A modern, responsive web application for creating and managing professional resumes. Built with PHP, MySQL, and Bootstrap 5, this CMS provides an intuitive interface for users to build, edit, and preview their resumes with ease.

## 🛠️ Technologies Used

### Backend
- **PHP 7.4+** - Server-side scripting language
- **MySQL 5.7+** - Relational database management system
- **PDO** - Database abstraction layer for secure connections

### Frontend
- **HTML5** - Semantic markup structure
- **CSS3** - Modern styling with custom properties and gradients
- **JavaScript (ES6+)** - Dynamic form functionality and user interactions
- **Bootstrap 5.1.3** - Responsive CSS framework
- **Bootstrap Icons** - Icon library for UI elements

### Development & Tools
- **XAMPP** - Local development environment
- **Git** - Version control system
- **Google Fonts (Inter)** - Typography enhancement

## 📋 Project Overview

The Resume Builder CMS is a comprehensive web application designed to simplify the resume creation process. It provides users with a user-friendly interface to create, manage, and preview professional resumes. The system features a modern design with responsive layouts, ensuring optimal viewing across all devices.

### Core Functionality
- User authentication and account management
- Dynamic resume creation with multiple sections
- Real-time preview functionality
- Responsive design for all screen sizes
- Secure data handling and storage

## ✨ Key Features

### 🔐 User Authentication
- Secure user registration and login system
- Password hashing with bcrypt
- CSRF protection for all forms
- Session management and security

### 📝 Resume Management
- **Multiple Resumes**: Create and manage multiple resume versions
- **Dynamic Sections**: Add/remove education, experience, skills, and projects
- **Real-time Preview**: Instant preview of resume changes
- **Professional Templates**: Clean, modern resume layouts

### 🎨 Modern UI/UX
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Modern Gradients**: Beautiful gradient backgrounds and buttons
- **Smooth Animations**: Enhanced user experience with transitions
- **Accessibility**: Keyboard navigation and screen reader support

### 🔒 Security Features
- **SQL Injection Prevention**: Prepared statements for all queries
- **XSS Protection**: Input sanitization and output encoding
- **CSRF Protection**: Token-based form validation
- **Secure Sessions**: Proper session configuration and management

### 📊 Data Management
- **Personal Information**: Contact details, summary, and social links
- **Education History**: Academic background with GPA and descriptions
- **Work Experience**: Professional history with detailed descriptions
- **Skills Section**: Categorized skills with proficiency levels
- **Project Portfolio**: Showcase projects with technologies and links

## 👥 User Roles

### Regular Users
- **Registration**: Create new accounts with email verification
- **Login/Logout**: Secure authentication system
- **Resume Creation**: Build resumes from scratch
- **Resume Management**: Edit, preview, and delete resumes
- **Profile Management**: Update personal information

### Demo User
- **Pre-configured Account**: Username: `demo_user`, Password: `password`
- **Sample Data**: Access to pre-populated resume examples
- **Full Functionality**: All features available for testing

## 📁 Project Structure

```
resume-builder-cms/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet with modern design
│   └── js/
│       └── resume-form.js     # Dynamic form functionality
├── config/
│   ├── config.php            # Main configuration and constants
│   └── database.php          # Database connection settings
├── includes/
│   └── functions.php         # Common functions and utilities
├── sql/
│   ├── setup.sql            # Database schema and tables
│   └── demo_data.sql        # Sample data for testing
├── index.php               # Main entry point and routing
├── login.php               # User authentication
├── register.php            # User registration
├── logout.php              # Session termination
├── dashboard.php           # User dashboard and overview
├── create_resume.php       # Resume creation interface
├── edit_resume.php         # Resume editing functionality
├── preview_resume.php      # Resume preview and printing
├── my_resumes.php          # Resume listing and management
└── README.md              # Project documentation
```

## 🚀 Setup Instructions

### Prerequisites
- **Web Server**: Apache/Nginx with PHP support
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Browser**: Modern browser with JavaScript enabled

### 1. Download and Setup
```bash
# Clone or download the project
git clone <repository-url> resume-builder-cms
cd resume-builder-cms

# Set proper permissions
chmod 755 -R .
```

### 2. Database Setup
```sql
-- Create database
CREATE DATABASE resume_builder_cms;

-- Import the database schema
mysql -u your_username -p resume_builder_cms < sql/setup.sql

-- Import sample data (optional)
mysql -u your_username -p resume_builder_cms < sql/demo_data.sql
```

### 3. Configuration
Edit `config/database.php` to match your database settings:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'resume_builder_cms');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 4. Web Server Configuration

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

### 5. Access the Application
- **Local Development**: Navigate to `http://localhost/resume-builder-cms` (or your project folder name) in a web browser
- **Note**: The application automatically detects the project path and handles redirects correctly
- You'll be redirected to the login page
- Use the demo account or create a new account

## 📖 Usage

### Getting Started

1. **Access the Application**
   - Navigate to `http://localhost/resume-builder-cms` (or your project folder name) in a web browser
   - You'll be redirected to the login page

2. **Create an Account**
   - Click "Register here" on the login page
   - Fill in your details and create an account
   - You'll be redirected to the dashboard after successful registration

3. **Demo Account**
   - Username: `demo_user`
   - Password: `password`

### Creating Your First Resume

1. **Dashboard Overview**
   - View your resume statistics
   - Access quick actions
   - See recent resumes

2. **Create New Resume**
   - Click "Create New Resume" from dashboard or navigation
   - Fill in the resume title
   - Complete each section:
     - Personal Information (required)
     - Education History
     - Work Experience
     - Skills
     - Projects

3. **Dynamic Form Features**
   - Add/remove sections dynamically
   - Reorder items with drag-and-drop
   - Real-time validation
   - Auto-save functionality

4. **Preview and Export**
   - Preview your resume in real-time
   - Print-friendly layout
   - Professional formatting
   - Mobile-responsive design

### Managing Resumes

1. **Resume Listing**
   - View all your resumes
   - Sort by creation/update date
   - Quick actions for each resume

2. **Editing Resumes**
   - Modify any section
   - Add new content
   - Update existing information
   - Preview changes instantly

3. **Resume Actions**
   - Preview resume
   - Edit resume
   - Delete resume (with confirmation)
   - Duplicate resume

## 🎯 Intended Use

### Personal Use
- **Job Seekers**: Create professional resumes for job applications
- **Students**: Build academic resumes for internships and scholarships
- **Freelancers**: Showcase skills and experience for client projects
- **Career Changers**: Present transferable skills in new formats

### Educational Use
- **Learning PHP/MySQL**: Study modern web development practices
- **UI/UX Design**: Explore responsive design principles
- **Database Design**: Understand relational database relationships
- **Security Implementation**: Learn web application security

### Development Use
- **Portfolio Project**: Demonstrate full-stack development skills
- **Code Reference**: Use as a reference for similar projects
- **Customization Base**: Modify for specific requirements
- **Learning Resource**: Study clean, well-documented code

### Demo and Testing
- **Feature Testing**: Test resume creation workflows
- **UI/UX Evaluation**: Assess user interface design
- **Performance Testing**: Evaluate application performance
- **Security Assessment**: Review security implementations

## 📄 License

**License for RiverTheme**

RiverTheme makes this project available for demo, instructional, and personal use. You can ask for or buy a license from [RiverTheme.com](https://RiverTheme.com) if you want a pro website, sophisticated features, or expert setup and assistance. A Pro license is needed for production deployments, customizations, and commercial use.

**Disclaimer**

The free version is offered "as is" with no warranty and might not function on all devices or browsers. It might also have some coding or security flaws. For additional information or to get a Pro license, please get in touch with [RiverTheme.com](https://RiverTheme.com).

---