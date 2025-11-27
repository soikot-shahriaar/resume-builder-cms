<?php
/**
 * Edit Resume Page
 * Resume Builder CMS
 */

require_once 'config/config.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$resume_id = (int)($_GET['id'] ?? 0);

if (!$resume_id) {
    redirectWithMessage('my_resumes.php', 'Resume not found.', 'danger');
}

$errors = [];
$success = false;

// Get existing resume data
try {
    $pdo = getDBConnection();
    
    // Get resume and verify ownership
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND user_id = ?");
    $stmt->execute([$resume_id, $user_id]);
    $resume = $stmt->fetch();
    
    if (!$resume) {
        redirectWithMessage('my_resumes.php', 'Resume not found.', 'danger');
    }
    
    // Get existing data
    $stmt = $pdo->prepare("SELECT * FROM personal_info WHERE resume_id = ?");
    $stmt->execute([$resume_id]);
    $personal_info = $stmt->fetch() ?: [];
    
    $stmt = $pdo->prepare("SELECT * FROM education WHERE resume_id = ? ORDER BY sort_order");
    $stmt->execute([$resume_id]);
    $existing_education = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT * FROM work_experience WHERE resume_id = ? ORDER BY sort_order");
    $stmt->execute([$resume_id]);
    $existing_experience = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE resume_id = ? ORDER BY sort_order");
    $stmt->execute([$resume_id]);
    $existing_skills = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE resume_id = ? ORDER BY sort_order");
    $stmt->execute([$resume_id]);
    $existing_projects = $stmt->fetchAll();
    
} catch (PDOException $e) {
    redirectWithMessage('my_resumes.php', 'Error loading resume.', 'danger');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        try {
            $pdo = getDBConnection();
            $pdo->beginTransaction();

            // Update resume title
            $resume_title = sanitizeInput($_POST['resume_title'] ?? 'My Resume');
            $stmt = $pdo->prepare("UPDATE resumes SET title = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
            $stmt->execute([$resume_title, $resume_id, $user_id]);

            // Delete existing data
            $stmt = $pdo->prepare("DELETE FROM personal_info WHERE resume_id = ?");
            $stmt->execute([$resume_id]);
            
            $stmt = $pdo->prepare("DELETE FROM education WHERE resume_id = ?");
            $stmt->execute([$resume_id]);
            
            $stmt = $pdo->prepare("DELETE FROM work_experience WHERE resume_id = ?");
            $stmt->execute([$resume_id]);
            
            $stmt = $pdo->prepare("DELETE FROM skills WHERE resume_id = ?");
            $stmt->execute([$resume_id]);
            
            $stmt = $pdo->prepare("DELETE FROM projects WHERE resume_id = ?");
            $stmt->execute([$resume_id]);

            // Insert updated personal information
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
            redirectWithMessage('preview_resume.php?id=' . $resume_id, 'Resume updated successfully!', 'success');

        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = 'Failed to update resume. Please try again.';
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
    <title>Edit Resume - <?php echo APP_NAME; ?></title>
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
                        <a class="nav-link" href="create_resume.php">Create Resume</a>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-pencil"></i> Edit Resume</h2>
                    <a href="preview_resume.php?id=<?php echo $resume_id; ?>" class="btn btn-outline-success">
                        <i class="bi bi-eye"></i> Preview
                    </a>
                </div>

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
                                   value="<?php echo htmlspecialchars($resume['title']); ?>" required>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3>Personal Information</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo htmlspecialchars($personal_info['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($personal_info['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($personal_info['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?php echo htmlspecialchars($personal_info['address'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?php echo htmlspecialchars($personal_info['city'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state" 
                                       value="<?php echo htmlspecialchars($personal_info['state'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="zip_code" class="form-label">ZIP Code</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                       value="<?php echo htmlspecialchars($personal_info['zip_code'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                                <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                       value="<?php echo htmlspecialchars($personal_info['linkedin_url'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="website_url" class="form-label">Website URL</label>
                                <input type="url" class="form-control" id="website_url" name="website_url" 
                                       value="<?php echo htmlspecialchars($personal_info['website_url'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="summary" class="form-label">Professional Summary</label>
                            <textarea class="form-control" id="summary" name="summary" rows="4" 
                                      placeholder="Write a brief professional summary..."><?php echo htmlspecialchars($personal_info['summary'] ?? ''); ?></textarea>
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
                            <i class="bi bi-check-circle"></i> Update Resume
                        </button>
                        <a href="preview_resume.php?id=<?php echo $resume_id; ?>" class="btn btn-secondary btn-lg ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/resume-form.js"></script>
    <script>
        // Pre-populate existing data
        document.addEventListener('DOMContentLoaded', function() {
            // Clear default entries first
            document.getElementById('education-container').innerHTML = '';
            document.getElementById('experience-container').innerHTML = '';
            document.getElementById('skills-container').innerHTML = '';
            document.getElementById('projects-container').innerHTML = '';
            
            // Reset counters
            educationCount = 0;
            experienceCount = 0;
            skillsCount = 0;
            projectsCount = 0;
            
            // Add existing education
            <?php foreach ($existing_education as $edu): ?>
                addEducation();
                const eduIndex = educationCount - 1;
                const eduContainer = document.getElementById(`education-${eduIndex}`);
                eduContainer.querySelector('input[name="education[' + eduIndex + '][institution]"]').value = <?php echo json_encode($edu['institution']); ?>;
                eduContainer.querySelector('input[name="education[' + eduIndex + '][degree]"]').value = <?php echo json_encode($edu['degree']); ?>;
                eduContainer.querySelector('input[name="education[' + eduIndex + '][field_of_study]"]').value = <?php echo json_encode($edu['field_of_study']); ?>;
                eduContainer.querySelector('input[name="education[' + eduIndex + '][gpa]"]').value = <?php echo json_encode($edu['gpa']); ?>;
                eduContainer.querySelector('input[name="education[' + eduIndex + '][start_date]"]').value = <?php echo json_encode($edu['start_date']); ?>;
                eduContainer.querySelector('input[name="education[' + eduIndex + '][end_date]"]').value = <?php echo json_encode($edu['end_date']); ?>;
                eduContainer.querySelector('textarea[name="education[' + eduIndex + '][description]"]').value = <?php echo json_encode($edu['description']); ?>;
                eduContainer.querySelector('input[name="education[' + eduIndex + '][current]"]').checked = <?php echo $edu['current'] ? 'true' : 'false'; ?>;
                if (<?php echo $edu['current'] ? 'true' : 'false'; ?>) {
                    toggleEndDate('education', eduIndex);
                }
            <?php endforeach; ?>
            
            // Add existing experience
            <?php foreach ($existing_experience as $exp): ?>
                addExperience();
                const expIndex = experienceCount - 1;
                const expContainer = document.getElementById(`experience-${expIndex}`);
                expContainer.querySelector('input[name="experience[' + expIndex + '][company]"]').value = <?php echo json_encode($exp['company']); ?>;
                expContainer.querySelector('input[name="experience[' + expIndex + '][position]"]').value = <?php echo json_encode($exp['position']); ?>;
                expContainer.querySelector('input[name="experience[' + expIndex + '][location]"]').value = <?php echo json_encode($exp['location']); ?>;
                expContainer.querySelector('input[name="experience[' + expIndex + '][start_date]"]').value = <?php echo json_encode($exp['start_date']); ?>;
                expContainer.querySelector('input[name="experience[' + expIndex + '][end_date]"]').value = <?php echo json_encode($exp['end_date']); ?>;
                expContainer.querySelector('textarea[name="experience[' + expIndex + '][description]"]').value = <?php echo json_encode($exp['description']); ?>;
                expContainer.querySelector('input[name="experience[' + expIndex + '][current]"]').checked = <?php echo $exp['current'] ? 'true' : 'false'; ?>;
                if (<?php echo $exp['current'] ? 'true' : 'false'; ?>) {
                    toggleEndDate('experience', expIndex);
                }
            <?php endforeach; ?>
            
            // Add existing skills
            <?php foreach ($existing_skills as $skill): ?>
                addSkill();
                const skillIndex = skillsCount - 1;
                const skillContainer = document.getElementById(`skill-${skillIndex}`);
                skillContainer.querySelector('input[name="skills[' + skillIndex + '][skill_name]"]').value = <?php echo json_encode($skill['skill_name']); ?>;
                skillContainer.querySelector('select[name="skills[' + skillIndex + '][skill_level]"]').value = <?php echo json_encode($skill['skill_level']); ?>;
                skillContainer.querySelector('input[name="skills[' + skillIndex + '][category]"]').value = <?php echo json_encode($skill['category']); ?>;
            <?php endforeach; ?>
            
            // Add existing projects
            <?php foreach ($existing_projects as $project): ?>
                addProject();
                const projectIndex = projectsCount - 1;
                const projectContainer = document.getElementById(`project-${projectIndex}`);
                projectContainer.querySelector('input[name="projects[' + projectIndex + '][project_name]"]').value = <?php echo json_encode($project['project_name']); ?>;
                projectContainer.querySelector('input[name="projects[' + projectIndex + '][technologies]"]').value = <?php echo json_encode($project['technologies']); ?>;
                projectContainer.querySelector('input[name="projects[' + projectIndex + '][start_date]"]').value = <?php echo json_encode($project['start_date']); ?>;
                projectContainer.querySelector('input[name="projects[' + projectIndex + '][end_date]"]').value = <?php echo json_encode($project['end_date']); ?>;
                projectContainer.querySelector('input[name="projects[' + projectIndex + '][project_url]"]').value = <?php echo json_encode($project['project_url']); ?>;
                projectContainer.querySelector('input[name="projects[' + projectIndex + '][github_url]"]').value = <?php echo json_encode($project['github_url']); ?>;
                projectContainer.querySelector('textarea[name="projects[' + projectIndex + '][description]"]').value = <?php echo json_encode($project['description']); ?>;
            <?php endforeach; ?>
            
            // If no existing data, add one empty entry for each section
            if (<?php echo count($existing_education); ?> === 0) addEducation();
            if (<?php echo count($existing_experience); ?> === 0) addExperience();
            if (<?php echo count($existing_skills); ?> === 0) addSkill();
            if (<?php echo count($existing_projects); ?> === 0) addProject();
        });
    </script>
</body>
</html>

