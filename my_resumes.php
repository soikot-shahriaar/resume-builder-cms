<?php
/**
 * My Resumes Page
 * Resume Builder CMS
 */

require_once 'config/config.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $resume_id = (int)($_POST['resume_id'] ?? 0);
        
        try {
            $pdo = getDBConnection();
            
            // Verify ownership
            $stmt = $pdo->prepare("SELECT id FROM resumes WHERE id = ? AND user_id = ?");
            $stmt->execute([$resume_id, $user_id]);
            
            if ($stmt->fetch()) {
                // Delete resume (cascade will handle related records)
                $stmt = $pdo->prepare("DELETE FROM resumes WHERE id = ? AND user_id = ?");
                $stmt->execute([$resume_id, $user_id]);
                
                redirectWithMessage('my_resumes.php', 'Resume deleted successfully.', 'success');
            } else {
                redirectWithMessage('my_resumes.php', 'Resume not found.', 'danger');
            }
        } catch (PDOException $e) {
            redirectWithMessage('my_resumes.php', 'Failed to delete resume.', 'danger');
        }
    }
}

// Get user's resumes
try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT id, title, created_at, updated_at FROM resumes WHERE user_id = ? ORDER BY updated_at DESC");
    $stmt->execute([$user_id]);
    $resumes = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error occurred.";
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Resumes - <?php echo APP_NAME; ?></title>
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
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create_resume.php">Create Resume</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my_resumes.php">My Resumes</a>
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
                    <h2><i class="bi bi-folder"></i> My Resumes</h2>
                    <a href="create_resume.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create New Resume
                    </a>
                </div>

                <?php 
                $flash = getFlashMessage();
                if ($flash): 
                ?>
                    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                        <?php echo htmlspecialchars($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($resumes)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-file-earmark-plus display-1 text-muted"></i>
                            <h4 class="mt-3">No resumes yet</h4>
                            <p class="text-muted">Create your first resume to get started building your professional profile.</p>
                            <a href="create_resume.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle"></i> Create Your First Resume
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($resumes as $resume): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="bi bi-file-earmark-text text-primary"></i>
                                            <?php echo htmlspecialchars($resume['title']); ?>
                                        </h5>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> Created: <?php echo formatDate($resume['created_at'], 'M j, Y'); ?><br>
                                                <i class="bi bi-clock"></i> Updated: <?php echo formatDate($resume['updated_at'], 'M j, Y'); ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <a href="preview_resume.php?id=<?php echo $resume['id']; ?>" 
                                               class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-eye"></i> Preview
                                            </a>
                                            <a href="edit_resume.php?id=<?php echo $resume['id']; ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="confirmDelete(<?php echo $resume['id']; ?>, '<?php echo htmlspecialchars($resume['title'], ENT_QUOTES); ?>')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the resume "<span id="resumeTitle"></span>"?</p>
                    <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="resume_id" id="deleteResumeId">
                        <button type="submit" class="btn btn-danger">Delete Resume</button>
                    </form>
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
    <script>
        function confirmDelete(resumeId, resumeTitle) {
            document.getElementById('resumeTitle').textContent = resumeTitle;
            document.getElementById('deleteResumeId').value = resumeId;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
</body>
</html>

