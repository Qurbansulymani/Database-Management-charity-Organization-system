<?php
// volunteers.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add'])) {
        $fname = $_POST['Valanter_Fname'];
        $fathername = $_POST['Valanter_Fathername'];
        $lname = $_POST['Valnater_Lname'];
        $phone = $_POST['Valanter_phone'];
        $address = $_POST['Valanter_Address'];
        $branch_id = $_POST['Branch_ID'];
        
        // Convert empty string to NULL
        if ($branch_id === '') {
            $branch_id = null;
        }
        
        try {
            $sql = "INSERT INTO Valanters (Valanter_Fname, Valanter_Fathername, Valnater_Lname, Valanter_phone, Valanter_Address, Branch_ID) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fname, $fathername, $lname, $phone, $address, $branch_id]);
            $success_message = "Volunteer added successfully!";
        } catch(PDOException $e) {
            $error_message = "Error adding volunteer: " . $e->getMessage();
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['Valanter_id'];
        $fname = $_POST['Valanter_Fname'];
        $fathername = $_POST['Valanter_Fathername'];
        $lname = $_POST['Valnater_Lname'];
        $phone = $_POST['Valanter_phone'];
        $address = $_POST['Valanter_Address'];
        $branch_id = $_POST['Branch_ID'];
        
        // Convert empty string to NULL
        if ($branch_id === '') {
            $branch_id = null;
        }
        
        try {
            $sql = "UPDATE Valanters SET Valanter_Fname=?, Valanter_Fathername=?, Valnater_Lname=?, 
                    Valanter_phone=?, Valanter_Address=?, Branch_ID=? WHERE Valanter_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fname, $fathername, $lname, $phone, $address, $branch_id, $id]);
            $success_message = "Volunteer updated successfully!";
        } catch(PDOException $e) {
            $error_message = "Error updating volunteer: " . $e->getMessage();
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $sql = "DELETE FROM Valanters WHERE Valanter_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $success_message = "Volunteer deleted successfully!";
    } catch(PDOException $e) {
        $error_message = "Error deleting volunteer: " . $e->getMessage();
    }
}

// Fetch all volunteers
$sql = "SELECT v.*, b.Branch_name FROM Valanters v 
        LEFT JOIN Branch b ON v.Branch_ID = b.Branch_ID 
        ORDER BY v.Valanter_id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch branches for dropdown
$sql_branches = "SELECT * FROM Branch";
$stmt_branches = $conn->prepare($sql_branches);
$stmt_branches->execute();
$branches = $stmt_branches->fetchAll(PDO::FETCH_ASSOC);

// Get volunteer data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $sql = "SELECT * FROM Valanters WHERE Valanter_id=?";
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
    <title>Volunteer Management</title>
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
                <a class="nav-link" href="staff.php"><i class="fas fa-users"></i> Staff</a>
                <a class="nav-link active" href="volunteers.php"><i class="fas fa-hands-helping"></i> Volunteers</a>
                <a class="nav-link" href="beneficiaries.php"><i class="fas fa-users"></i> Beneficiaries</a>
                <a class="nav-link" href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">
                <i class="fas fa-hands-helping"></i>
                Volunteer Management
            </h2>
            <a href="volunteers.php" class="btn btn-outline-primary">
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
                                <h4><?php echo count($volunteers); ?></h4>
                                <p class="mb-0">Total Volunteers</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-hands-helping fa-2x"></i>
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
                                    $withBranch = array_filter($volunteers, function($volunteer) {
                                        return !empty($volunteer['Branch_ID']);
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
                                    $withPhone = array_filter($volunteers, function($volunteer) {
                                        return !empty($volunteer['Valanter_phone']);
                                    });
                                    echo count($withPhone);
                                    ?>
                                </h4>
                                <p class="mb-0">With Phone</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-phone fa-2x"></i>
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
                                    $withAddress = array_filter($volunteers, function($volunteer) {
                                        return !empty($volunteer['Valanter_Address']);
                                    });
                                    echo count($withAddress);
                                    ?>
                                </h4>
                                <p class="mb-0">With Address</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-map-marker-alt fa-2x"></i>
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
                        'Edit Volunteer (ID: ' . $edit_data['Valanter_id'] . ')' : 
                        'Add New Volunteer'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="volunteerForm">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="Valanter_id" value="<?php echo $edit_data['Valanter_id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">First Name</label>
                                <input type="text" class="form-control" name="Valanter_Fname" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Valanter_Fname']) : ''; ?>" 
                                       required maxlength="40" placeholder="Enter first name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Father Name</label>
                                <input type="text" class="form-control" name="Valanter_Fathername" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Valanter_Fathername']) : ''; ?>" 
                                       required maxlength="40" placeholder="Enter father's name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="Valnater_Lname" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Valnater_Lname']) : ''; ?>" 
                                       maxlength="40" placeholder="Enter last name">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="number" class="form-control" name="Valanter_phone" 
                                       value="<?php echo $edit_data ? $edit_data['Valanter_phone'] : ''; ?>"
                                       placeholder="Enter phone number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Associated Branch</label>
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

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="Valanter_Address" 
                                          maxlength="60" placeholder="Enter full address" 
                                          rows="2"><?php echo $edit_data ? htmlspecialchars($edit_data['Valanter_Address']) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="<?php echo isset($_GET['edit']) ? 'update' : 'add'; ?>" 
                                class="btn btn-<?php echo isset($_GET['edit']) ? 'warning' : 'success'; ?>">
                            <i class="fas fa-<?php echo isset($_GET['edit']) ? 'edit' : 'plus'; ?>"></i>
                            <?php echo isset($_GET['edit']) ? 'Update Volunteer' : 'Add Volunteer'; ?>
                        </button>
                        
                        <?php if (isset($_GET['edit'])): ?>
                            <a href="volunteers.php" class="btn btn-secondary">
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

        <!-- Volunteers List -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i>
                    Volunteers List (<?php echo count($volunteers); ?> volunteers)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($volunteers)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-hands-helping fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No volunteers found in the system.</p>
                        <a href="volunteers.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add First Volunteer
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Volunteer ID</th>
                                    <th>Full Name</th>
                                    <th>Father Name</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Branch</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($volunteers as $volunteer): ?>
                                <tr>
                                    <td><strong><?php echo $volunteer['Valanter_id']; ?></strong></td>
                                    <td>
                                        <?php 
                                        $fullName = htmlspecialchars($volunteer['Valanter_Fname']);
                                        if (!empty($volunteer['Valnater_Lname'])) {
                                            $fullName .= ' ' . htmlspecialchars($volunteer['Valnater_Lname']);
                                        }
                                        echo $fullName;
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($volunteer['Valanter_Fathername']); ?></td>
                                    <td>
                                        <?php if (!empty($volunteer['Valanter_phone'])): ?>
                                            <?php echo htmlspecialchars($volunteer['Valanter_phone']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($volunteer['Valanter_Address'])): ?>
                                            <span title="<?php echo htmlspecialchars($volunteer['Valanter_Address']); ?>">
                                                <?php echo strlen($volunteer['Valanter_Address']) > 25 ? 
                                                    substr(htmlspecialchars($volunteer['Valanter_Address']), 0, 25) . '...' : 
                                                    htmlspecialchars($volunteer['Valanter_Address']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($volunteer['Branch_name'])): ?>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($volunteer['Branch_name']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="volunteers.php?edit=<?php echo $volunteer['Valanter_id']; ?>" 
                                               class="btn btn-sm btn-warning btn-action" 
                                               title="Edit Volunteer">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="volunteers.php?delete=<?php echo $volunteer['Valanter_id']; ?>" 
                                               class="btn btn-sm btn-danger btn-action" 
                                               title="Delete Volunteer"
                                               onclick="return confirm('Are you sure you want to delete volunteer <?php echo htmlspecialchars($volunteer['Valanter_Fname']); ?>?')">
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
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function clearForm() {
            document.getElementById('volunteerForm').reset();
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
            document.getElementById('volunteerForm').scrollIntoView({ behavior: 'smooth' });
        });
        <?php endif; ?>
    </script>
</body>
</html>