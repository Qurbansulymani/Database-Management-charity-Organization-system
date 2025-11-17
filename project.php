<?php
// projects.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add'])) {
        $name = $_POST['Project_Name'];
        $description = $_POST['Project_Description'];
        $location = $_POST['Project_Locatin'];
        $date = $_POST['Project_date'];
        $branch_id = $_POST['Branch_ID'];
        
        // Convert empty string to NULL
        if ($branch_id === '') {
            $branch_id = null;
        }
        
        try {
            $sql = "INSERT INTO Project (Project_Name, Project_Description, Project_Locatin, Project_date, Branch_ID) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $description, $location, $date, $branch_id]);
            $success_message = "Project added successfully!";
        } catch(PDOException $e) {
            $error_message = "Error adding project: " . $e->getMessage();
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['Project_ID'];
        $name = $_POST['Project_Name'];
        $description = $_POST['Project_Description'];
        $location = $_POST['Project_Locatin'];
        $date = $_POST['Project_date'];
        $branch_id = $_POST['Branch_ID'];
        
        // Convert empty string to NULL
        if ($branch_id === '') {
            $branch_id = null;
        }
        
        try {
            $sql = "UPDATE Project SET Project_Name=?, Project_Description=?, Project_Locatin=?, Project_date=?, Branch_ID=? 
                    WHERE Project_ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $description, $location, $date, $branch_id, $id]);
            $success_message = "Project updated successfully!";
        } catch(PDOException $e) {
            $error_message = "Error updating project: " . $e->getMessage();
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $sql = "DELETE FROM Project WHERE Project_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $success_message = "Project deleted successfully!";
    } catch(PDOException $e) {
        $error_message = "Error deleting project: " . $e->getMessage();
    }
}

// Fetch all projects
$sql = "SELECT p.*, b.Branch_name FROM Project p 
        LEFT JOIN Branch b ON p.Branch_ID = b.Branch_ID 
        ORDER BY p.Project_ID DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch branches for dropdown
$sql_branches = "SELECT * FROM Branch";
$stmt_branches = $conn->prepare($sql_branches);
$stmt_branches->execute();
$branches = $stmt_branches->fetchAll(PDO::FETCH_ASSOC);

// Get project data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $sql = "SELECT * FROM Project WHERE Project_ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management</title>
    <link href="bootstrap-5.0.2-dist/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
            border: none;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .btn-action {
            margin-right: 5px;
        }
        .required::after {
            content: " *";
            color: red;
        }
        .form-label {
            font-weight: 500;
        }
        .stats-card {
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .project-description {
            max-height: 60px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-hands-helping"></i>
                Charity Foundation System
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                <a class="nav-link" href="beneficiaries.php"><i class="fas fa-users"></i> Beneficiaries</a>
                <a class="nav-link active" href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a>
                <a class="nav-link" href="events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a class="nav-link" href="donations.php"><i class="fas fa-donate"></i> Donations</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">
                <i class="fas fa-project-diagram"></i>
                Project Management
            </h2>
            <a href="projects.php" class="btn btn-outline-primary">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo count($projects); ?></h4>
                                <p class="mb-0">Total Projects</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-project-diagram fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>
                                    <?php 
                                    $withBranch = array_filter($projects, function($project) {
                                        return !empty($project['Branch_ID']);
                                    });
                                    echo count($withBranch);
                                    ?>
                                </h4>
                                <p class="mb-0">With Branch</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-code-branch fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>
                                    <?php 
                                    $upcoming = array_filter($projects, function($project) {
                                        return !empty($project['Project_date']) && strtotime($project['Project_date']) > time();
                                    });
                                    echo count($upcoming);
                                    ?>
                                </h4>
                                <p class="mb-0">Upcoming</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-plus fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>
                                    <?php 
                                    $past = array_filter($projects, function($project) {
                                        return !empty($project['Project_date']) && strtotime($project['Project_date']) < time();
                                    });
                                    echo count($past);
                                    ?>
                                </h4>
                                <p class="mb-0">Completed</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="card mb-4">
            <div class="card-header bg-<?php echo isset($_GET['edit']) ? 'warning' : 'primary'; ?> text-white">
                <h5 class="mb-0">
                    <i class="fas fa-<?php echo isset($_GET['edit']) ? 'edit' : 'plus'; ?>"></i>
                    <?php echo isset($_GET['edit']) ? 
                        'Edit Project (ID: ' . $edit_data['Project_ID'] . ')' : 
                        'Add New Project'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="projectForm">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="Project_ID" value="<?php echo $edit_data['Project_ID']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Project Name</label>
                                <input type="text" class="form-control" name="Project_Name" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Project_Name']) : ''; ?>" 
                                       required maxlength="60" placeholder="Enter project name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Project Location</label>
                                <input type="text" class="form-control" name="Project_Locatin" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Project_Locatin']) : ''; ?>" 
                                       required maxlength="50" placeholder="Enter project location">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label required">Project Description</label>
                                <textarea class="form-control" name="Project_Description" 
                                          required maxlength="100" placeholder="Enter project description" 
                                          rows="3"><?php echo $edit_data ? htmlspecialchars($edit_data['Project_Description']) : ''; ?></textarea>
                                <div class="form-text">Maximum 100 characters</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Project Date</label>
                                <input type="date" class="form-control" name="Project_date" 
                                       value="<?php echo $edit_data ? $edit_data['Project_date'] : ''; ?>">
                                <div class="form-text">Optional: Set project start or completion date</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Responsible Branch</label>
                                <select class="form-select" name="Branch_ID">
                                    <option value="">-- Select Branch --</option>
                                    <?php foreach ($branches as $branch): ?>
                                        <option value="<?php echo $branch['Branch_ID']; ?>"
                                            <?php echo ($edit_data && $edit_data['Branch_ID'] == $branch['Branch_ID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($branch['Branch_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Optional: Assign to a specific branch</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="<?php echo isset($_GET['edit']) ? 'update' : 'add'; ?>" 
                                class="btn btn-<?php echo isset($_GET['edit']) ? 'warning' : 'success'; ?>">
                            <i class="fas fa-<?php echo isset($_GET['edit']) ? 'edit' : 'plus'; ?>"></i>
                            <?php echo isset($_GET['edit']) ? 'Update Project' : 'Add Project'; ?>
                        </button>
                        
                        <?php if (isset($_GET['edit'])): ?>
                            <a href="projects.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                        
                        <button type="reset" class="btn btn-outline-secondary" onclick="clearForm()">
                            <i