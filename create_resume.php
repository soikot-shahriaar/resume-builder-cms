<?php
/**
 * Create Resume Page
 * Resume Builder CMS
 */

require_once 'config/config.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        try {
            $pdo = getDBConnection();
            $pdo->beginTransaction();

            // Create resume record
            $resume_title = sanitizeInput($_POST['resume_title'] ?? 'My Resume');
            $stmt = $pdo->prepare("INSERT INTO resumes (user_id, title) VALUES (?, ?)");
            $stmt->execute([$user_id, $resume_title]);
            $resume_id = $pdo->lastInsertId();

            // Insert personal information
            $personal_data = [
                'resume_id' => $resume_id,
                'full_name' => sanitizeInput($_POST['full_name'] ?? ''),
                'email' => sanitizeInput($_POST['email'] ?? ''),
                'phone' => sanitizeInput($_POST['phone'] ?? ''),
                'address' => sanitizeInput($_POST['address'] ?? ''),
                'city' => sanitizeInput($_POST['city'] ?? ''),
                'state' => sanitizeInput($_POST['state'] ?? ''),
                'zip_code' => sanitizeInput($_POST['zip_code'] ?? ''),
                'country' => sanitizeInput($_POST['country'] ?? ''),
                'linkedin_url' => sanitizeInput($_POST['linkedin_url'] ?? ''),
                'website_url' => sanitizeInput($_POST['website_url'] ?? ''),
                'summary' => sanitizeInput($_POST['summary'] ?? '')
            ];

            $stmt = $pdo->prepare("INSERT INTO personal_info (resume_id, full_name, email, phone, address, city, state, zip_code, country, linkedin_url, website_url, summary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array_values($personal_data));

            // Insert education entries
            if (!empty($_POST['education'])) {
                $stmt = $pdo->prepare("INSERT INTO education (resume_id, institution, degree, field_of_study, start_date, end_date, current, gpa, description, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                foreach ($_POST['education'] as $index => $edu) {
                    $end_date = !empty($edu['current']) ? null : ($edu['end_date'] ?? null);
                    $stmt->execute([
                        $resume_id,
                        sanitizeInput($edu['institution'] ?? ''),
                        sanitizeInput($edu['degree'] ?? ''),
                        sanitizeInput($edu['field_of_study'] ?? ''),
                        $edu['start_date'] ?? null,
                        $end_date,
                        !empty($edu['current']) ? 1 : 0,
                        sanitizeInput($edu['gpa'] ?? ''),
                        sanitizeInput($edu['description'] ?? ''),
                        $index
                    ]);
                }
            }

            // Insert work experience entries
            if (!empty($_POST['experience'])) {
                $stmt = $pdo->prepare("INSERT INTO work_experience (resume_id, company, position, location, start_date, end_date, current, description, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                foreach ($_POST['experience'] as $index => $exp) {
                    $end_date = !empty($exp['current']) ? null : ($exp['end_date'] ?? null);
                    $stmt->execute([
                        $resume_id,
                        sanitizeInput($exp['company'] ?? ''),
                        sanitizeInput($exp['position'] ?? ''),
                        sanitizeInput($exp['location'] ?? ''),
                        $exp['start_date'] ?? null,
                        $end_date,
                        !empty($exp['current']) ? 1 : 0,
                        sanitizeInput($exp['description'] ?? ''),
                        $index
                    ]);
                }
            }

            // Insert skills
            if (!empty($_POST['skills'])) {
                $stmt = $pdo->prepare("INSERT INTO skills (resume_id, skill_name, skill_level, category, sort_order) VALUES (?, ?, ?, ?, ?)");
                
                foreach ($_POST['skills'] as $index => $skill) {
                    $stmt->execute([
                        $resume_id,
                        sanitizeInput($skill['skill_name'] ?? ''),
                        sanitizeInput($skill['skill_level'] ?? 'Intermediate'),
                        sanitizeInput($skill['category'] ?? ''),
                        $index
                    ]);
                }
            }

            // Insert projects
            if (!empty($_POST['projects'])) {
                $stmt = $pdo->prepare("INSERT INTO projects (resume_id, project_name, description, technologies, start_date, end_date, project_url, github_url, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                foreach ($_POST['projects'] as $index => $project) {
                    $stmt->execute([
                        $resume_id,
                        sanitizeInput($project['project_name'] ?? ''),
                        sanitizeInput($project['description'] ?? ''),
                        sanitizeInput($project['technologies'] ?? ''),
                        $project['start_date'] ?? null,
                        $project['end_date'] ?? null,
                        sanitizeInput($project['project_url'] ?? ''),
                        sanitizeInput($project['github_url'] ?? ''),
                        $index
                    ]);
                }
            }

            $pdo->commit();
            redirectWithMessage('preview_resume.php?id=' . $resume_id, 'Resume created successfully!', 'success');

        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = 'Failed to create resume. Please try again.';
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Resume - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-file-earmark-person"></i> <?php echo APP_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="create_resume.php">Create Resume</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_resumes.php">My Resumes</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars(getCurrentUsername()); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2><i class="bi bi-plus-circle"></i> Create New Resume</h2>
                <p class="text-muted">Fill out the form below to create your professional resume.</p>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="resumeForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <!-- Resume Title -->
                    <div class="form-section">
                        <h3>Resume Title</h3>
                        <div class="mb-3">
                            <label for="resume_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="resume_title" name="resume_title" 
                                   value="<?php echo htmlspecialchars($_POST['resume_title'] ?? 'My Resume'); ?>" required>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3>Personal Information</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state" 
                                       value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="zip_code" class="form-label">ZIP Code</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                       value="<?php echo htmlspecialchars($_POST['zip_code'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                                <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                       value="<?php echo htmlspecialchars($_POST['linkedin_url'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="website_url" class="form-label">Website URL</label>
                                <input type="url" class="form-control" id="website_url" name="website_url" 
                                       value="<?php echo htmlspecialchars($_POST['website_url'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="summary" class="form-label">Professional Summary</label>
                            <textarea class="form-control" id="summary" name="summary" rows="4" 
                                      placeholder="Write a brief professional summary..."><?php echo htmlspecialchars($_POST['summary'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Education Section -->
                    <div class="form-section">
                        <h3>Education</h3>
                        <div id="education-container">
                            <!-- Education entries will be added here -->
                        </div>
                        <button type="button" class="btn btn-outline-primary add-item" onclick="addEducation()">
                            <i class="bi bi-plus"></i> Add Education
                        </button>
                    </div>

                    <!-- Work Experience Section -->
                    <div class="form-section">
                        <h3>Work Experience</h3>
                        <div id="experience-container">
                            <!-- Experience entries will be added here -->
                        </div>
                        <button type="button" class="btn btn-outline-primary add-item" onclick="addExperience()">
                            <i class="bi bi-plus"></i> Add Experience
                        </button>
                    </div>

                    <!-- Skills Section -->
                    <div class="form-section">
                        <h3>Skills</h3>
                        <div id="skills-container">
                            <!-- Skills entries will be added here -->
                        </div>
                        <button type="button" class="btn btn-outline-primary add-item" onclick="addSkill()">
                            <i class="bi bi-plus"></i> Add Skill
                        </button>
                    </div>

                    <!-- Projects Section -->
                    <div class="form-section">
                        <h3>Projects</h3>
                        <div id="projects-container">
                            <!-- Projects entries will be added here -->
                        </div>
                        <button type="button" class="btn btn-outline-primary add-item" onclick="addProject()">
                            <i class="bi bi-plus"></i> Add Project
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-section text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Create Resume
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary btn-lg ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/resume-form.js"></script>
</body>
</html>

