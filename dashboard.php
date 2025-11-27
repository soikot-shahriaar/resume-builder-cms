<?php
/**
 * User Dashboard
 * Resume Builder CMS
 */

require_once 'config/config.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();

// Get user's resumes
try {
    $pdo = getDBConnection();
    
    // Get user info
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    // Get resumes count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM resumes WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $resume_count = $stmt->fetch()['count'];
    
    // Get recent resumes
    $stmt = $pdo->prepare("SELECT id, title, created_at, updated_at FROM resumes WHERE user_id = ? ORDER BY updated_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $recent_resumes = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error occurred.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
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

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                    <p class="lead">Manage your resumes and create professional documents.</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="create_resume.php" class="btn btn-light btn-lg">
                        <i class="bi bi-plus-circle"></i> Create New Resume
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <?php 
        $flash = getFlashMessage();
        if ($flash): 
        ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="bi bi-file-earmark-text display-4 text-primary"></i>
                    <h3><?php echo $resume_count; ?></h3>
                    <p class="text-muted">Total Resumes</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="bi bi-eye display-4 text-success"></i>
                    <h3>0</h3>
                    <p class="text-muted">Views This Month</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="bi bi-download display-4 text-info"></i>
                    <h3>0</h3>
                    <p class="text-muted">Downloads</p>
                </div>
            </div>
        </div>

        <!-- Recent Resumes -->
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Resumes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_resumes)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-file-earmark-plus display-1 text-muted"></i>
                                <h5 class="mt-3">No resumes yet</h5>
                                <p class="text-muted">Create your first resume to get started.</p>
                                <a href="create_resume.php" class="btn btn-primary">Create Resume</a>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recent_resumes as $resume): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($resume['title']); ?></h6>
                                            <small class="text-muted">
                                                Updated: <?php echo formatDate($resume['updated_at'], 'M j, Y'); ?>
                                            </small>
                                        </div>
                                        <div>
                                            <a href="edit_resume.php?id=<?php echo $resume['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="preview_resume.php?id=<?php echo $resume['id']; ?>" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-eye"></i> Preview
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($resume_count > 5): ?>
                                <div class="text-center mt-3">
                                    <a href="my_resumes.php" class="btn btn-outline-primary">View All Resumes</a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="create_resume.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create New Resume
                            </a>
                            <a href="my_resumes.php" class="btn btn-outline-primary">
                                <i class="bi bi-folder"></i> View All Resumes
                            </a>
                            <a href="profile.php" class="btn btn-outline-secondary">
                                <i class="bi bi-person"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Tips Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-lightbulb text-warning"></i>
                                Keep your resume updated regularly
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-lightbulb text-warning"></i>
                                Use action verbs in descriptions
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-lightbulb text-warning"></i>
                                Tailor your resume for each job
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo APP_NAME; ?></h5>
                    <p>Create professional resumes with ease.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>&copy; <?php echo date('Y'); ?> Resume Builder CMS. All rights reserved.</p>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="copyright">
            <div class="text-center my-2">
                <div>
                    <span>© 2025 . </span>
                    <span class="text-primary">Developed by </span>
                    <a href="https://rivertheme.com" class="fw-semibold text-decoration-none" target="_blank" rel="noopener">RiverTheme</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

