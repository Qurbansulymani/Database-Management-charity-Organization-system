<?php
// reports.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Debug information
echo "<!-- Starting reports.php -->";

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add'])) {
        $title = $_POST['Report_title'];
        $date = $_POST['Report_date'];
        $type = $_POST['Report_type'];
        $project_id = $_POST['Project_ID'];
        $event_id = $_POST['Event_ID'];
        
        // Convert empty strings to NULL
        if ($project_id === '') $project_id = null;
        if ($event_id === '') $event_id = null;
        
        try {
            $sql = "INSERT INTO Report (Report_title, Report_date, Report_type, Project_ID, Event_ID) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$title, $date, $type, $project_id, $event_id]);
            $success_message = "Report added successfully!";
            echo "<!-- Insert successful -->";
        } catch(PDOException $e) {
            $error_message = "Error adding report: " . $e->getMessage();
            echo "<!-- Insert error: " . $e->getMessage() . " -->";
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['Report_ID'];
        $title = $_POST['Report_title'];
        $date = $_POST['Report_date'];
        $type = $_POST['Report_type'];
        $project_id = $_POST['Project_ID'];
        $event_id = $_POST['Event_ID'];
        
        // Convert empty strings to NULL
        if ($project_id === '') $project_id = null;
        if ($event_id === '') $event_id = null;
        
        try {
            $sql = "UPDATE Report SET Report_title=?, Report_date=?, Report_type=?, Project_ID=?, Event_ID=? 
                    WHERE Report_ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$title, $date, $type, $project_id, $event_id, $id]);
            $success_message = "Report updated successfully!";
            echo "<!-- Update successful -->";
        } catch(PDOException $e) {
            $error_message = "Error updating report: " . $e->getMessage();
            echo "<!-- Update error: " . $e->getMessage() . " -->";
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $sql = "DELETE FROM Report WHERE Report_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $success_message = "Report deleted successfully!";
        echo "<!-- Delete successful -->";
    } catch(PDOException $e) {
        $error_message = "Error deleting report: " . $e->getMessage();
        echo "<!-- Delete error: " . $e->getMessage() . " -->";
    }
}

// Fetch all reports with related data
try {
    $sql = "SELECT r.*, p.Project_Name, e.Event_Type 
            FROM Report r 
            LEFT JOIN Project p ON r.Project_ID = p.Project_ID 
            LEFT JOIN Eventes e ON r.Event_ID = e.Event_ID 
            ORDER BY r.Report_date DESC, r.Report_ID DESC";
    echo "<!-- SQL Query: " . $sql . " -->";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<!-- Found " . count($reports) . " reports -->";
    
} catch(PDOException $e) {
    echo "<!-- Error fetching reports: " . $e->getMessage() . " -->";
    
    // Try without JOIN if there's an error
    try {
        $sql = "SELECT * FROM Report ORDER BY Report_date DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<!-- Found " . count($reports) . " reports (without JOIN) -->";
    } catch(PDOException $e2) {
        echo "<!-- Error fetching reports without JOIN: " . $e2->getMessage() . " -->";
        $reports = [];
    }
}

// Fetch projects for dropdown
try {
    $sql_projects = "SELECT * FROM Project";
    $stmt_projects = $conn->prepare($sql_projects);
    $stmt_projects->execute();
    $projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);
    echo "<!-- Found " . count($projects) . " projects -->";
} catch(PDOException $e) {
    echo "<!-- Error fetching projects: " . $e->getMessage() . " -->";
    $projects = [];
}

// Fetch events for dropdown
try {
    $sql_events = "SELECT * FROM Eventes";
    $stmt_events = $conn->prepare($sql_events);
    $stmt_events->execute();
    $events = $stmt_events->fetchAll(PDO::FETCH_ASSOC);
    echo "<!-- Found " . count($events) . " events -->";
} catch(PDOException $e) {
    echo "<!-- Error fetching events: " . $e->getMessage() . " -->";
    $events = [];
}

// Get report data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    try {
        $sql = "SELECT * FROM Report WHERE Report_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_GET['edit']]);
        $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<!-- Editing report ID: " . $_GET['edit'] . " -->";
    } catch(PDOException $e) {
        echo "<!-- Error fetching edit data: " . $e->getMessage() . " -->";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Management</title>
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
        .report-type-badge {
            font-size: 0.8em;
        }
        .debug-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-family: monospace;
            font-size: 12px;
            border-left: 4px solid #dc3545;
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
                <a class="nav-link" href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a>
                <a class="nav-link" href="events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a class="nav-link active" href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
                <a class="nav-link" href="balance.php"><i class="fas fa-balance-scale"></i> Balance</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">
                <i class="fas fa-chart-bar"></i>
                Report Management
            </h2>
            <a href="reports.php" class="btn btn-outline-primary">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </a>
        </div>

        <!-- Debug Information -->
        <div class="debug-info">
            <strong>Debug Information:</strong><br>
            Reports: <?php echo count($reports); ?><br>
            Projects: <?php echo count($projects); ?><br>
            Events: <?php echo count($events); ?><br>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo count($reports); ?></h4>
                                <p class="mb-0">Total Reports</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-file-alt fa-2x"></i>
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
                                    $projectReports = array_filter($reports, function($report) {
                                        return !empty($report['Project_ID']);
                                    });
                                    echo count($projectReports);
                                    ?>
                                </h4>
                                <p class="mb-0">Project Reports</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-project-diagram fa-2x"></i>
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
                                    $eventReports = array_filter($reports, function($report) {
                                        return !empty($report['Event_ID']);
                                    });
                                    echo count($eventReports);
                                    ?>
                                </h4>
                                <p class="mb-0">Event Reports</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-alt fa-2x"></i>
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
                                    $thisMonth = array_filter($reports, function($report) {
                                        return date('Y-m', strtotime($report['Report_date'])) == date('Y-m');
                                    });
                                    echo count($thisMonth);
                                    ?>
                                </h4>
                                <p class="mb-0">This Month</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar fa-2x"></i>
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
                        'Edit Report (ID: ' . $edit_data['Report_ID'] . ')' : 
                        'Add New Report'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="reportForm">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="Report_ID" value="<?php echo $edit_data['Report_ID']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Report Title</label>
                                <input type="text" class="form-control" name="Report_title" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Report_title']) : ''; ?>" 
                                       required maxlength="50" placeholder="Enter report title">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Report Date</label>
                                <input type="date" class="form-control" name="Report_date" 
                                       value="<?php echo $edit_data ? $edit_data['Report_date'] : date('Y-m-d'); ?>" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Report Type</label>
                                <input type="text" class="form-control" name="Report_type" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Report_type']) : ''; ?>" 
                                       required maxlength="40" placeholder="Enter report type (e.g., Progress, Financial, Summary)">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Related Project</label>
                                <select class="form-select" name="Project_ID">
                                    <option value="">-- Select Project --</option>
                                    <?php foreach ($projects as $project): ?>
                                        <option value="<?php echo $project['Project_ID']; ?>"
                                            <?php echo ($edit_data && $edit_data['Project_ID'] == $project['Project_ID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($project['Project_Name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Related Event</label>
                                <select class="form-select" name="Event_ID">
                                    <option value="">-- Select Event --</option>
                                    <?php foreach ($events as $event): ?>
                                        <option value="<?php echo $event['Event_ID']; ?>"
                                            <?php echo ($edit_data && $edit_data['Event_ID'] == $event['Event_ID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($event['Event_Type']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="<?php echo isset($_GET['edit']) ? 'update' : 'add'; ?>" 
                                class="btn btn-<?php echo isset($_GET['edit']) ? 'warning' : 'success'; ?>">
                            <i class="fas fa-<?php echo isset($_GET['edit']) ? 'edit' : 'plus'; ?>"></i>
                            <?php echo isset($_GET['edit']) ? 'Update Report' : 'Add Report'; ?>
                        </button>
                        
                        <?php if (isset($_GET['edit'])): ?>
                            <a href="reports.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                        
                        <button type="reset" class="btn btn-outline-secondary" onclick="clearForm()">
                            <i class="fas fa-undo"></i> Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reports List -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i>
                    Reports List (<?php echo count($reports); ?> reports)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($reports)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No reports found in the system.</p>
                        <p class="text-muted small">Check the debug information above for details.</p>
                        <a href="reports.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add First Report
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Report ID</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Project</th>
                                    <th>Event</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><strong><?php echo $report['Report_ID']; ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($report['Report_title']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary report-type-badge">
                                            <?php echo htmlspecialchars($report['Report_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo date('M j, Y', strtotime($report['Report_date'])); ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($report['Project_Name'])): ?>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($report['Project_Name']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($report['Event_Type'])): ?>
                                            <span class="badge bg-success"><?php echo htmlspecialchars($report['Event_Type']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="reports.php?edit=<?php echo $report['Report_ID']; ?>" 
                                               class="btn btn-sm btn-warning btn-action" 
                                               title="Edit Report">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="reports.php?delete=<?php echo $report['Report_ID']; ?>" 
                                               class="btn btn-sm btn-danger btn-action" 
                                               title="Delete Report"
                                               onclick="return confirm('Are you sure you want to delete report <?php echo htmlspecialchars($report['Report_title']); ?>?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-rocket"></i> Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <a href="projects.php" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-project-diagram"></i> Manage Projects
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="events.php" class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-calendar-alt"></i> Manage Events
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="balance.php" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-balance-scale"></i> View Balance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function clearForm() {
            document.getElementById('reportForm').reset();
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Scroll to form when editing
        <?php if (isset($_GET['edit'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('reportForm').scrollIntoView({ behavior: 'smooth' });
        });
        <?php endif; ?>
    </script>
</body>
</html>