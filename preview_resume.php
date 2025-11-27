<?php
/**
 * Resume Preview Page
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

try {
    $pdo = getDBConnection();
    
    // Get resume and verify ownership
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND user_id = ?");
    $stmt->execute([$resume_id, $user_id]);
    $resume = $stmt->fetch();
    
    if (!$resume) {
        redirectWithMessage('my_resumes.php', 'Resume not found.', 'danger');
    }
    
    // Get personal information
    $stmt = $pdo->prepare("SELECT * FROM personal_info WHERE resume_id = ?");
    $stmt->execute([$resume_id]);
    $personal_info = $stmt->fetch();
    
    // Get education
    $stmt = $pdo->prepare("SELECT * FROM education WHERE resume_id = ? ORDER BY sort_order, start_date DESC");
    $stmt->execute([$resume_id]);
    $education = $stmt->fetchAll();
    
    // Get work experience
    $stmt = $pdo->prepare("SELECT * FROM work_experience WHERE resume_id = ? ORDER BY sort_order, start_date DESC");
    $stmt->execute([$resume_id]);
    $experience = $stmt->fetchAll();
    
    // Get skills grouped by category
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE resume_id = ? ORDER BY category, sort_order, skill_name");
    $stmt->execute([$resume_id]);
    $skills_data = $stmt->fetchAll();
    
    // Group skills by category
    $skills = [];
    foreach ($skills_data as $skill) {
        $category = $skill['category'] ?: 'General';
        $skills[$category][] = $skill;
    }
    
    // Get projects
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE resume_id = ? ORDER BY sort_order, start_date DESC");
    $stmt->execute([$resume_id]);
    $projects = $stmt->fetchAll();
    
} catch (PDOException $e) {
    redirectWithMessage('my_resumes.php', 'Error loading resume.', 'danger');
}

// Helper function to format date range
function formatDateRange($start_date, $end_date, $current = false) {
    $start = $start_date ? formatDate($start_date, 'M Y') : '';
    $end = $current ? 'Present' : ($end_date ? formatDate($end_date, 'M Y') : '');
    
    if ($start && $end) {
        return "$start - $end";
    } elseif ($start) {
        return $start;
    } elseif ($end) {
        return $end;
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($resume['title']); ?> - Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .container { max-width: none !important; width: 100% !important; }
            .resume-preview { box-shadow: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation (hidden in print) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary no-print">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-file-earmark-person"></i> <?php echo APP_NAME; ?>
            </a>
            
            <div class="navbar-nav ms-auto">
                <a href="edit_resume.php?id=<?php echo $resume_id; ?>" class="btn btn-outline-light me-2">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <button onclick="window.print()" class="btn btn-light me-2">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="my_resumes.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </nav>

    <!-- Resume Preview -->
    <div class="container mt-4">
        <div class="resume-preview">
            <!-- Header -->
            <div class="resume-header text-center">
                <h1 class="mb-2"><?php echo htmlspecialchars($personal_info['full_name'] ?? ''); ?></h1>
                <div class="contact-info">
                    <?php if ($personal_info['email']): ?>
                        <span><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($personal_info['email']); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($personal_info['phone']): ?>
                        <span class="ms-3"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($personal_info['phone']); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($personal_info['address'] || $personal_info['city'] || $personal_info['state']): ?>
                        <span class="ms-3">
                            <i class="bi bi-geo-alt"></i> 
                            <?php 
                            $address_parts = array_filter([
                                $personal_info['address'],
                                $personal_info['city'],
                                $personal_info['state'],
                                $personal_info['zip_code']
                            ]);
                            echo htmlspecialchars(implode(', ', $address_parts));
                            ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="links mt-2">
                    <?php if ($personal_info['linkedin_url']): ?>
                        <a href="<?php echo htmlspecialchars($personal_info['linkedin_url']); ?>" target="_blank" class="me-3">
                            <i class="bi bi-linkedin"></i> LinkedIn
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($personal_info['website_url']): ?>
                        <a href="<?php echo htmlspecialchars($personal_info['website_url']); ?>" target="_blank">
                            <i class="bi bi-globe"></i> Website
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Professional Summary -->
            <?php if ($personal_info['summary']): ?>
                <div class="resume-section">
                    <h3>Professional Summary</h3>
                    <p><?php echo nl2br(htmlspecialchars($personal_info['summary'])); ?></p>
                </div>
            <?php endif; ?>

            <!-- Work Experience -->
            <?php if (!empty($experience)): ?>
                <div class="resume-section">
                    <h3>Work Experience</h3>
                    <?php foreach ($experience as $exp): ?>
                        <div class="experience-item">
                            <div class="item-header">
                                <div>
                                    <div class="item-title"><?php echo htmlspecialchars($exp['position']); ?></div>
                                    <div class="item-company"><?php echo htmlspecialchars($exp['company']); ?></div>
                                    <?php if ($exp['location']): ?>
                                        <div class="text-muted"><?php echo htmlspecialchars($exp['location']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="item-date">
                                    <?php echo formatDateRange($exp['start_date'], $exp['end_date'], $exp['current']); ?>
                                </div>
                            </div>
                            <?php if ($exp['description']): ?>
                                <div class="mt-2">
                                    <?php echo nl2br(htmlspecialchars($exp['description'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Education -->
            <?php if (!empty($education)): ?>
                <div class="resume-section">
                    <h3>Education</h3>
                    <?php foreach ($education as $edu): ?>
                        <div class="education-item">
                            <div class="item-header">
                                <div>
                                    <div class="item-title"><?php echo htmlspecialchars($edu['degree']); ?></div>
                                    <div class="item-institution"><?php echo htmlspecialchars($edu['institution']); ?></div>
                                    <?php if ($edu['field_of_study']): ?>
                                        <div class="text-muted"><?php echo htmlspecialchars($edu['field_of_study']); ?></div>
                                    <?php endif; ?>
                                    <?php if ($edu['gpa']): ?>
                                        <div class="text-muted">GPA: <?php echo htmlspecialchars($edu['gpa']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="item-date">
                                    <?php echo formatDateRange($edu['start_date'], $edu['end_date'], $edu['current']); ?>
                                </div>
                            </div>
                            <?php if ($edu['description']): ?>
                                <div class="mt-2">
                                    <?php echo nl2br(htmlspecialchars($edu['description'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Skills -->
            <?php if (!empty($skills)): ?>
                <div class="resume-section">
                    <h3>Skills</h3>
                    <?php foreach ($skills as $category => $category_skills): ?>
                        <div class="mb-3">
                            <?php if (count($skills) > 1): ?>
                                <h5 class="text-primary"><?php echo htmlspecialchars($category); ?></h5>
                            <?php endif; ?>
                            <div class="skills-list">
                                <?php foreach ($category_skills as $skill): ?>
                                    <span class="skill-tag">
                                        <?php echo htmlspecialchars($skill['skill_name']); ?>
                                        <small class="text-muted">(<?php echo htmlspecialchars($skill['skill_level']); ?>)</small>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Projects -->
            <?php if (!empty($projects)): ?>
                <div class="resume-section">
                    <h3>Projects</h3>
                    <?php foreach ($projects as $project): ?>
                        <div class="project-item">
                            <div class="item-header">
                                <div>
                                    <div class="item-title"><?php echo htmlspecialchars($project['project_name']); ?></div>
                                    <?php if ($project['technologies']): ?>
                                        <div class="text-muted">Technologies: <?php echo htmlspecialchars($project['technologies']); ?></div>
                                    <?php endif; ?>
                                    <div class="mt-1">
                                        <?php if ($project['project_url']): ?>
                                            <a href="<?php echo htmlspecialchars($project['project_url']); ?>" target="_blank" class="me-3">
                                                <i class="bi bi-globe"></i> Live Demo
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($project['github_url']): ?>
                                            <a href="<?php echo htmlspecialchars($project['github_url']); ?>" target="_blank">
                                                <i class="bi bi-github"></i> GitHub
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="item-date">
                                    <?php echo formatDateRange($project['start_date'], $project['end_date']); ?>
                                </div>
                            </div>
                            <?php if ($project['description']): ?>
                                <div class="mt-2">
                                    <?php echo nl2br(htmlspecialchars($project['description'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

