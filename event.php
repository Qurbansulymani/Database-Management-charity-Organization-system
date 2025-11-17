<?php
// events.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add'])) {
        $type = $_POST['Event_Type'];
        $location = $_POST['Event_Location'];
        $description = $_POST['Event_Description'];
        $branch_id = $_POST['BRANCH_ID'];
        $main_id = $_POST['Main_ID'];
        
        // Convert empty strings to NULL
        if ($branch_id === '') $branch_id = null;
        if ($main_id === '') $main_id = null;
        
        try {
            $sql = "INSERT INTO Eventes (Event_Type, Event_Location, Event_Description, BRANCH_ID, Main_ID) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$type, $location, $description, $branch_id, $main_id]);
            $success_message = "Event added successfully!";
        } catch(PDOException $e) {
            $error_message = "Error adding event: " . $e->getMessage();
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['Event_ID'];
        $type = $_POST['Event_Type'];
        $location = $_POST['Event_Location'];
        $description = $_POST['Event_Description'];
        $branch_id = $_POST['BRANCH_ID'];
        $main_id = $_POST['Main_ID'];
        
        // Convert empty strings to NULL
        if ($branch_id === '') $branch_id = null;
        if ($main_id === '') $main_id = null;
        
        try {
            $sql = "UPDATE Eventes SET Event_Type=?, Event_Location=?, Event_Description=?, BRANCH_ID=?, Main_ID=? 
                    WHERE Event_ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$type, $location, $description, $branch_id, $main_id, $id]);
            $success_message = "Event updated successfully!";
        } catch(PDOException $e) {
            $error_message = "Error updating event: " . $e->getMessage();
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // First check if there are related reports
        $check_sql = "SELECT COUNT(*) as related_count FROM Report WHERE Event_ID = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$id]);
        $result = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['related_count'] > 0) {
            $error_message = "Cannot delete event. There are " . $result['related_count'] . " reports associated with this event. Please delete or reassign the reports first.";
        } else {
            $sql = "DELETE FROM Eventes WHERE Event_ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $success_message = "Event deleted successfully!";
        }
    } catch(PDOException $e) {
        $error_message = "Error deleting event: " . $e->getMessage();
    }
}

// Fetch all events with related data
$sql = "SELECT e.*, b.Branch_name, c.Main_name 
        FROM Eventes e 
        LEFT JOIN Branch b ON e.BRANCH_ID = b.Branch_ID 
        LEFT JOIN Charity_foundation c ON e.Main_ID = c.Main_ID 
        ORDER BY e.Event_ID DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch branches for dropdown
$sql_branches = "SELECT * FROM Branch";
$stmt_branches = $conn->prepare($sql_branches);
$stmt_branches->execute();
$branches = $stmt_branches->fetchAll(PDO::FETCH_ASSOC);

// Fetch charity foundations for dropdown
$sql_foundations = "SELECT * FROM Charity_foundation";
$stmt_foundations = $conn->prepare($sql_foundations);
$stmt_foundations->execute();
$foundations = $stmt_foundations->fetchAll(PDO::FETCH_ASSOC);

// Get event data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $sql = "SELECT * FROM Eventes WHERE Event_ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Count related reports for each event
$event_reports = [];
foreach ($events as $event) {
    $sql = "SELECT COUNT(*) as report_count FROM Report WHERE Event_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$event['Event_ID']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $event_reports[$event['Event_ID']] = $result['report_count'];
}

// Event type statistics
$event_types = [];
foreach ($events as $event) {
    $type = $event['Event_Type'];
    if (!isset($event_types[$type])) {
        $event_types[$type] = 0;
    }
    $event_types[$type]++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
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
        .event-description {
            max-height: 60px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .event-type-badge {
            font-size: 0.8em;
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
                <a class="nav-link active" href="events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a class="nav-link" href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
                <a class="nav-link" href="volunteers.php"><i class="fas fa-hands-helping"></i> Volunteers</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">
                <i class="fas fa-calendar-alt"></i>
                Event Management
            </h2>
            <a href="events.php" class="btn btn-outline-primary">
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
                                <h4><?php echo count($events); ?></h4>
                                <p class="mb-0">Total Events</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-alt fa-2x"></i>
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
                                    $withBranch = array_filter($events, function($event) {
                                        return !empty($event['BRANCH_ID']);
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
                                    $withFoundation = array_filter($events, function($event) {
                                        return !empty($event['Main_ID']);
                                    });
                                    echo count($withFoundation);
                                    ?>
                                </h4>
                                <p class="mb-0">With Foundation</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-building fa-2x"></i>
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
                                    $withReports = array_filter($event_reports, function($count) {
                                        return $count > 0;
                                    });
                                    echo count($withReports);
                                    ?>
                                </h4>
                                <p class="mb-0">With Reports</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Types Summary -->
        <?php if (!empty($event_types)): ?>
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Event Types Summary</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php 
                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark'];
                    $color_index = 0;
                    foreach ($event_types as $type => $count): 
                    ?>
                        <div class="col-md-3 mb-2">
                            <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                <span class="fw-bold"><?php echo htmlspecialchars($type); ?></span>
                                <span class="badge bg-<?php echo $colors[$color_index % count($colors)]; ?>">
                                    <?php echo $count; ?>
                                </span>
                            </div>
                        </div>
                    <?php 
                    $color_index++;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

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
                        'Edit Event (ID: ' . $edit_data['Event_ID'] . ')' : 
                        'Add New Event'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="eventForm">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="Event_ID" value="<?php echo $edit_data['Event_ID']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Event Type</label>
                                <input type="text" class="form-control" name="Event_Type" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Event_Type']) : ''; ?>" 
                                       required maxlength="60" placeholder="Enter event type (e.g., Fundraising, Awareness, Relief)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Event Location</label>
                                <input type="text" class="form-control" name="Event_Location" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Event_Location']) : ''; ?>" 
                                       required maxlength="60" placeholder="Enter event location">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Event Description</label>
                                <textarea class="form-control" name="Event_Description" 
                                          maxlength="100" placeholder="Enter event description" 
                                          rows="3"><?php echo $edit_data ? htmlspecialchars($edit_data['Event_Description']) : ''; ?></textarea>
                                <div class="form-text">Maximum 100 characters</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Responsible Branch</label>
                                <select class="form-select" name="BRANCH_ID">
                                    <option value="">-- Select Branch --</option>
                                    <?php foreach ($branches as $branch): ?>
                                        <option value="<?php echo $branch['Branch_ID']; ?>"
                                            <?php echo ($edit_data && $edit_data['BRANCH_ID'] == $branch['Branch_ID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($branch['Branch_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Optional: Assign to a specific branch</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Main Foundation</label>
                                <select class="form-select" name="Main_ID">
                                    <option value="">-- Select Foundation --</option>
                                    <?php foreach ($foundations as $foundation): ?>
                                        <option value="<?php echo $foundation['Main_ID']; ?>"
                                            <?php echo ($edit_data && $edit_data['Main_ID'] == $foundation['Main_ID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($foundation['Main_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Optional: Associate with main foundation</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="<?php echo isset($_GET['edit']) ? 'update' : 'add'; ?>" 
                                class="btn btn-<?php echo isset($_GET['edit']) ? 'warning' : 'success'; ?>">
                            <i class="fas fa-<?php echo isset($_GET['edit']) ? 'edit' : 'plus'; ?>"></i>
                            <?php echo isset($_GET['edit']) ? 'Update Event' : 'Add Event'; ?>
                        </button>
                        
                        <?php if (isset($_GET['edit'])): ?>
                            <a href="events.php" class="btn btn-secondary">
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

        <!-- Events List -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i>
                    Events List (<?php echo count($events); ?> events)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($events)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No events found in the system.</p>
                        <a href="events.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add First Event
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Event ID</th>
                                    <th>Event Type</th>
                                    <th>Location</th>
                                    <th>Description</th>
                                    <th>Branch</th>
                                    <th>Foundation</th>
                                    <th>Reports</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><strong><?php echo $event['Event_ID']; ?></strong></td>
                                    <td>
                                        <span class="badge bg-primary event-type-badge">
                                            <?php echo htmlspecialchars($event['Event_Type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span title="<?php echo htmlspecialchars($event['Event_Location']); ?>">
                                            <?php echo strlen($event['Event_Location']) > 20 ? 
                                                substr(htmlspecialchars($event['Event_Location']), 0, 20) . '...' : 
                                                htmlspecialchars($event['Event_Location']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($event['Event_Description'])): ?>
                                            <div class="event-description" title="<?php echo htmlspecialchars($event['Event_Description']); ?>">
                                                <?php echo htmlspecialchars($event['Event_Description']); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($event['Branch_name'])): ?>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($event['Branch_name']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($event['Main_name'])): ?>
                                            <span class="badge bg-success"><?php echo htmlspecialchars($event['Main_name']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $report_count = $event_reports[$event['Event_ID']] ?? 0;
                                        if ($report_count > 0): ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-file-alt"></i>
                                                <?php echo $report_count; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="events.php?edit=<?php echo $event['Event_ID']; ?>" 
                                               class="btn btn-sm btn-warning btn-action" 
                                               title="Edit Event">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="events.php?delete=<?php echo $event['Event_ID']; ?>" 
                                               class="btn btn-sm btn-danger btn-action" 
                                               title="Delete Event"
                                               onclick="return confirm('Are you sure you want to delete event <?php echo htmlspecialchars($event['Event_Type']); ?>? <?php echo ($report_count > 0) ? 'This event has ' . $report_count . ' reports that will be affected.' : ''; ?>')">
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
                        <a href="reports.php" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-file-alt"></i> Manage Reports
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="volunteers.php" class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-hands-helping"></i> Manage Volunteers
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="projects.php" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-project-diagram"></i> Manage Projects
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
            document.getElementById('eventForm').reset();
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
            document.getElementById('eventForm').scrollIntoView({ behavior: 'smooth' });
        });
        <?php endif; ?>

        // Character counter for description field
        document.addEventListener('DOMContentLoaded', function() {
            const descField = document.querySelector('textarea[name="Event_Description"]');
            if (descField) {
                const counter = document.createElement('div');
                counter.className = 'form-text text-end';
                descField.parentNode.appendChild(counter);
                
                function updateCounter() {
                    const remaining = 100 - descField.value.length;
                    counter.textContent = `${remaining} characters remaining`;
                    counter.style.color = remaining < 10 ? 'red' : '';
                }
                
                descField.addEventListener('input', updateCounter);
                updateCounter();
            }
        });
    </script>
</body>
</html>