-- Demo Data for Resume Builder CMS
-- This file contains sample data to populate the database with realistic examples

-- Get the demo user ID
SET @demo_user_id = (SELECT id FROM users WHERE username = 'demo_user' LIMIT 1);

-- Create a sample resume for the demo user
INSERT INTO resumes (user_id, title, created_at) VALUES
(@demo_user_id, 'Software Developer Resume', NOW());

SET @demo_resume_id = LAST_INSERT_ID();

-- Personal Information
INSERT INTO personal_info (resume_id, full_name, email, phone, address, city, state, zip_code, country, linkedin_url, website_url, summary) VALUES
(@demo_resume_id, 'John Smith', 'john.smith@email.com', '+1 (555) 123-4567', '123 Main Street', 'San Francisco', 'CA', '94102', 'United States', 'https://linkedin.com/in/johnsmith', 'https://johnsmith.dev', 'Experienced software developer with 5+ years of expertise in full-stack web development, specializing in PHP, JavaScript, and modern frameworks. Passionate about creating scalable, user-friendly applications and contributing to open-source projects.');

-- Education
INSERT INTO education (resume_id, institution, degree, field_of_study, start_date, end_date, current, gpa, description, sort_order) VALUES
(@demo_resume_id, 'University of California, Berkeley', 'Bachelor of Science', 'Computer Science', '2016-09-01', '2020-05-15', 0, '3.8/4.0', 'Graduated with honors. Completed coursework in Data Structures, Algorithms, Database Systems, and Software Engineering. Member of the Computer Science Honor Society.', 1),
(@demo_resume_id, 'Stanford University', 'Master of Science', 'Computer Science', '2020-09-01', '2022-06-15', 0, '3.9/4.0', 'Specialized in Artificial Intelligence and Machine Learning. Thesis: "Optimizing Neural Networks for Real-time Applications."', 2);

-- Work Experience
INSERT INTO work_experience (resume_id, company, position, location, start_date, end_date, current, description, sort_order) VALUES
(@demo_resume_id, 'TechCorp Inc.', 'Senior Software Developer', 'San Francisco, CA', '2022-07-01', NULL, 1, 'Lead development of enterprise web applications using PHP, Laravel, and Vue.js. Mentored junior developers and implemented CI/CD pipelines. Improved application performance by 40% through optimization techniques.', 1),
(@demo_resume_id, 'StartupXYZ', 'Full Stack Developer', 'San Francisco, CA', '2020-06-01', '2022-06-30', 0, 'Developed and maintained multiple web applications using PHP, MySQL, and JavaScript. Collaborated with cross-functional teams to deliver features on time. Implemented responsive design principles and ensured cross-browser compatibility.', 2),
(@demo_resume_id, 'InternTech', 'Software Engineering Intern', 'Palo Alto, CA', '2019-06-01', '2019-08-31', 0, 'Assisted in developing RESTful APIs and database schemas. Participated in code reviews and agile development processes. Contributed to the development of a customer management system.', 3);

-- Skills
INSERT INTO skills (resume_id, skill_name, skill_level, category, sort_order) VALUES
(@demo_resume_id, 'PHP', 'Expert', 'Programming Languages', 1),
(@demo_resume_id, 'JavaScript', 'Advanced', 'Programming Languages', 2),
(@demo_resume_id, 'Python', 'Advanced', 'Programming Languages', 3),
(@demo_resume_id, 'MySQL', 'Advanced', 'Databases', 4),
(@demo_resume_id, 'PostgreSQL', 'Intermediate', 'Databases', 5),
(@demo_resume_id, 'Laravel', 'Expert', 'Frameworks', 6),
(@demo_resume_id, 'Vue.js', 'Advanced', 'Frameworks', 7),
(@demo_resume_id, 'React', 'Intermediate', 'Frameworks', 8),
(@demo_resume_id, 'Docker', 'Advanced', 'DevOps', 9),
(@demo_resume_id, 'Git', 'Expert', 'Version Control', 10),
(@demo_resume_id, 'AWS', 'Intermediate', 'Cloud Services', 11),
(@demo_resume_id, 'RESTful APIs', 'Expert', 'Web Development', 12),
(@demo_resume_id, 'Agile/Scrum', 'Advanced', 'Methodologies', 13),
(@demo_resume_id, 'Unit Testing', 'Advanced', 'Testing', 14),
(@demo_resume_id, 'CI/CD', 'Intermediate', 'DevOps', 15);

-- Projects
INSERT INTO projects (resume_id, project_name, description, technologies, start_date, end_date, project_url, github_url, sort_order) VALUES
(@demo_resume_id, 'E-commerce Platform', 'Built a full-featured e-commerce platform with payment processing, inventory management, and admin dashboard. Handled 10,000+ concurrent users.', 'PHP, Laravel, MySQL, Vue.js, Stripe API', '2023-01-01', '2023-06-30', 'https://demo-ecommerce.com', 'https://github.com/johnsmith/ecommerce-platform', 1),
(@demo_resume_id, 'Task Management App', 'Developed a collaborative task management application with real-time updates, file sharing, and team collaboration features.', 'PHP, MySQL, JavaScript, WebSockets', '2022-03-01', '2022-08-31', 'https://taskmanager-demo.com', 'https://github.com/johnsmith/task-manager', 2),
(@demo_resume_id, 'Weather Dashboard', 'Created a weather dashboard that aggregates data from multiple APIs and provides personalized weather forecasts with interactive charts.', 'PHP, JavaScript, Chart.js, Weather APIs', '2021-09-01', '2021-12-31', 'https://weather-dashboard-demo.com', 'https://github.com/johnsmith/weather-dashboard', 3),
(@demo_resume_id, 'Portfolio Website', 'Designed and developed a responsive portfolio website with blog functionality, contact forms, and SEO optimization.', 'PHP, HTML5, CSS3, JavaScript, MySQL', '2021-01-01', '2021-03-31', 'https://johnsmith.dev', 'https://github.com/johnsmith/portfolio', 4);

-- Create a second resume for variety
INSERT INTO resumes (user_id, title, created_at) VALUES
(@demo_user_id, 'Marketing Manager Resume', NOW());

SET @demo_resume_id_2 = LAST_INSERT_ID();

-- Personal Information for second resume
INSERT INTO personal_info (resume_id, full_name, email, phone, address, city, state, zip_code, country, linkedin_url, website_url, summary) VALUES
(@demo_resume_id_2, 'John Smith', 'john.smith@email.com', '+1 (555) 123-4567', '123 Main Street', 'San Francisco', 'CA', '94102', 'United States', 'https://linkedin.com/in/johnsmith', 'https://johnsmith.dev', 'Results-driven marketing professional with 7+ years of experience in digital marketing, brand strategy, and campaign management. Proven track record of increasing brand awareness and driving revenue growth through innovative marketing initiatives.');

-- Education for second resume
INSERT INTO education (resume_id, institution, degree, field_of_study, start_date, end_date, current, gpa, description, sort_order) VALUES
(@demo_resume_id_2, 'University of California, Berkeley', 'Bachelor of Arts', 'Marketing', '2015-09-01', '2019-05-15', 0, '3.7/4.0', 'Graduated with honors. Completed coursework in Marketing Strategy, Consumer Behavior, Digital Marketing, and Business Analytics.', 1);

-- Work Experience for second resume
INSERT INTO work_experience (resume_id, company, position, location, start_date, end_date, current, description, sort_order) VALUES
(@demo_resume_id_2, 'MarketingPro Inc.', 'Senior Marketing Manager', 'San Francisco, CA', '2021-03-01', NULL, 1, 'Lead marketing campaigns that increased brand awareness by 150% and drove 300% revenue growth. Manage a team of 5 marketing specialists and oversee $2M annual marketing budget.', 1),
(@demo_resume_id_2, 'Digital Marketing Agency', 'Marketing Specialist', 'San Francisco, CA', '2019-06-01', '2021-02-28', 0, 'Developed and executed digital marketing campaigns for 20+ clients. Increased client ROI by an average of 200% through targeted advertising and content marketing strategies.', 2);

-- Skills for second resume
INSERT INTO skills (resume_id, skill_name, skill_level, category, sort_order) VALUES
(@demo_resume_id_2, 'Digital Marketing', 'Expert', 'Marketing', 1),
(@demo_resume_id_2, 'Social Media Marketing', 'Expert', 'Marketing', 2),
(@demo_resume_id_2, 'Google Analytics', 'Advanced', 'Analytics', 3),
(@demo_resume_id_2, 'Facebook Ads', 'Advanced', 'Advertising', 4),
(@demo_resume_id_2, 'Google Ads', 'Advanced', 'Advertising', 5),
(@demo_resume_id_2, 'Content Marketing', 'Expert', 'Marketing', 6),
(@demo_resume_id_2, 'Email Marketing', 'Advanced', 'Marketing', 7),
(@demo_resume_id_2, 'SEO/SEM', 'Advanced', 'Marketing', 8),
(@demo_resume_id_2, 'Marketing Automation', 'Intermediate', 'Marketing', 9),
(@demo_resume_id_2, 'Adobe Creative Suite', 'Intermediate', 'Design', 10),
(@demo_resume_id_2, 'HubSpot', 'Advanced', 'CRM', 11),
(@demo_resume_id_2, 'Salesforce', 'Intermediate', 'CRM', 12);

-- Projects for second resume
INSERT INTO projects (resume_id, project_name, description, technologies, start_date, end_date, project_url, github_url, sort_order) VALUES
(@demo_resume_id_2, 'Brand Rebranding Campaign', 'Led a complete brand rebranding initiative for a tech startup, resulting in 200% increase in brand recognition and 150% growth in social media engagement.', 'Adobe Creative Suite, Social Media Platforms, Google Analytics', '2023-01-01', '2023-06-30', 'https://rebrand-case-study.com', NULL, 1),
(@demo_resume_id_2, 'E-commerce Marketing Strategy', 'Developed and executed a comprehensive marketing strategy for an e-commerce platform, driving 300% increase in online sales and 400% growth in customer acquisition.', 'Google Ads, Facebook Ads, Email Marketing, SEO', '2022-03-01', '2022-12-31', 'https://ecommerce-marketing.com', NULL, 2);
