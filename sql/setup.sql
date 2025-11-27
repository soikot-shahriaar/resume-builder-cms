-- Resume Builder CMS Database Setup
-- Create database and tables for the resume builder system

CREATE DATABASE IF NOT EXISTS resume_builder_cms;
USE resume_builder_cms;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Resumes table - main resume information
CREATE TABLE resumes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL DEFAULT 'My Resume',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Personal information table
CREATE TABLE personal_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resume_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    zip_code VARCHAR(10),
    country VARCHAR(50),
    linkedin_url VARCHAR(255),
    website_url VARCHAR(255),
    summary TEXT,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
);

-- Education table
CREATE TABLE education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resume_id INT NOT NULL,
    institution VARCHAR(100) NOT NULL,
    degree VARCHAR(100) NOT NULL,
    field_of_study VARCHAR(100),
    start_date DATE,
    end_date DATE,
    current BOOLEAN DEFAULT FALSE,
    gpa VARCHAR(10),
    description TEXT,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
);

-- Work experience table
CREATE TABLE work_experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resume_id INT NOT NULL,
    company VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    start_date DATE,
    end_date DATE,
    current BOOLEAN DEFAULT FALSE,
    description TEXT,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
);

-- Skills table
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resume_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    skill_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') DEFAULT 'Intermediate',
    category VARCHAR(50),
    sort_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
);

-- Projects table
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resume_id INT NOT NULL,
    project_name VARCHAR(100) NOT NULL,
    description TEXT,
    technologies VARCHAR(255),
    start_date DATE,
    end_date DATE,
    project_url VARCHAR(255),
    github_url VARCHAR(255),
    sort_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
);

-- Insert sample data for testing
INSERT INTO users (username, email, password_hash, first_name, last_name) VALUES
('demo_user', 'demo@example.com', '$2y$10$.MRwiY1r85i5eqBNHtyvpudeQDUXxZqvRTGXrQljvH8oqx7I3xEIy', 'Demo', 'User');

-- Create indexes for better performance
CREATE INDEX idx_resumes_user_id ON resumes(user_id);
CREATE INDEX idx_personal_info_resume_id ON personal_info(resume_id);
CREATE INDEX idx_education_resume_id ON education(resume_id);
CREATE INDEX idx_work_experience_resume_id ON work_experience(resume_id);
CREATE INDEX idx_skills_resume_id ON skills(resume_id);
CREATE INDEX idx_projects_resume_id ON projects(resume_id);

