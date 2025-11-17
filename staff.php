<?php
// staff.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Debug information
echo "<!-- Starting staff.php -->";

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add'])) {
        $fname = $_POST['Staff_Fname'];
        $fathername = $_POST['Staff_Fathername'];
        $lname = $_POST['Staff_Lname'];
        $phone = $_POST['Staff_Phone'];
        $email = $_POST['Staff_Email'];
        $address = $_POST['Staff_Address'];
        $branch_id = $_POST['Branch_ID'];
        $main_id = $_POST['Main_ID'];
        
        // Convert empty strings to NULL
        if ($branch_id === '') $branch_id = null;
        if ($main_id === '') $main_id = null;
        
        try {
            $sql = "INSERT INTO Staff (Staff_Fname, Staff_Fathername, Staff_Lname, Staff_Phone, Staff_Email, Staff_Address, Branch_ID, Main_ID) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fname, $fathername, $lname, $phone, $email, $address, $branch_id, $main_id]);
            $success_message = "Staff member added successfully!";
            echo "<!-- Insert successful -->";
        } catch(PDOException $e) {
            $error_message = "Error adding staff: " . $e->getMessage();
            echo "<!-- Insert error: " . $e->getMessage() . " -->";
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['Staff_ID'];
        $fname = $_POST['Staff_Fname'];
        $fathername = $_POST['Staff_Fathername'];
        $lname = $_POST['Staff_Lname'];
        $phone = $_POST['Staff_Phone'];
        $email = $_POST['Staff_Email'];
        $address = $_POST['Staff_Address'];
        $branch_id = $_POST['Branch_ID'];
        $main_id = $_POST['Main_ID'];
        
        // Convert empty strings to NULL
        if ($branch_id === '') $branch_id = null;
        if ($main_id === '') $main_id = null;
        
        try {
            $sql = "UPDATE Staff SET Staff_Fname=?, Staff_Fathername=?, Staff_Lname=?, Staff_Phone=?, 
                    Staff_Email=?, Staff_Address=?, Branch_ID=?, Main_ID=? WHERE Staff_ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fname, $fathername, $lname, $phone, $email, $address, $branch_id, $main_id, $id]);
            $success_message = "Staff member updated successfully!";
            echo "<!-- Update successful for Staff ID: " . $id . " -->";
        } catch(PDOException $e) {
            $error_message = "Error updating staff: " . $e->getMessage();
            echo "<!-- Update error: " . $e->getMessage() . " -->";
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $sql = "DELETE FROM Staff WHERE Staff_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $success_message = "Staff member deleted successfully!";
    } catch(PDOException $e) {
        $error_message = "Error deleting staff: " . $e->getMessage();
    }
}

// Fetch all staff
try {
    $sql = "SELECT s.*, b.Branch_name, c.Main_name 
            FROM Staff s 
            LEFT JOIN Branch b ON s.Branch_ID = b.Branch_ID 
            LEFT JOIN Charity_foundation c ON s.Main_ID = c.Main_ID 
            ORDER BY s.Staff_ID DESC";
    echo "<!-- SQL Query: " . $sql . " -->";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<!-- Found " . count($staff) . " staff members -->";
    
} catch(PDOException $e) {
    echo "<!-- Error fetching staff: " . $e->getMessage() . " -->";
    $staff = [];
}

// Fetch branches for dropdown
try {
    $sql_branches = "SELECT * FROM Branch";
    $stmt_branches = $conn->prepare($sql_branches);
    $stmt_branches->execute();
    $branches = $stmt_branches->fetchAll(PDO::FETCH_ASSOC);
    echo "<!-- Found " . count($branches) . " branches -->";
} catch(PDOException $e) {
    echo "<!-- Error fetching branches: " . $e->getMessage() . " -->";
    $branches = [];
}

// Fetch charity foundations for dropdown
try {
    $sql_foundations = "SELECT * FROM Charity_foundation";
    $stmt_foundations = $conn->prepare($sql_foundations);
    $stmt_foundations->execute();
    $foundations = $stmt_foundations->fetchAll(PDO::FETCH_ASSOC);
    echo "<!-- Found " . count($foundations) . " foundations -->";
} catch(PDOException $e) {
    echo "<!-- Error fetching foundations: " . $e->getMessage() . " -->";
    $foundations = [];
}

// Get staff data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    try {
        $sql = "SELECT * FROM Staff WHERE Staff_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_GET['edit']]);
        $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($edit_data) {
            echo "<!-- Editing staff ID: " . $_GET['edit'] . " -->";
            echo "<!-- Edit data: " . print_r($edit_data, true) . " -->";
        } else {
            echo "<!-- No staff found with ID: " . $_GET['edit'] . " -->";
            $error_message = "Staff member not found!";
        }
    } catch(PDOException $e) {
        echo "<!-- Error fetching edit data: " . $e->getMessage() . " -->";
        $error_message = "Error loading staff data: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
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
                <a class="nav-link" href="donar.php"><i class="fas fa-donate"></i> Donors</a>
                <a class="nav-link active" href="staff.php"><i class="fas fa-users"></i> Staff</a>
                <a class="nav-link" href="branches.php"><i class="fas fa-code-branch"></i> Branches</a>
                <a class="nav-link" href="charity_foundations.php"><i class="fas fa-building"></i> Foundations</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">
                <i class="fas fa-users"></i>
                Staff Management
            </h2>
            <a href="staff.php" class="btn btn-outline-primary">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </a>
        </div>

        <!-- Debug Information -->
        <div class="debug-info">
            <strong>Debug Information:</strong><br>
            Staff Members: <?php echo count($staff); ?><br>
            Branches: <?php echo count($branches); ?><br>
            Foundations: <?php echo count($foundations); ?><br>
            <?php if (isset($_GET['edit'])): ?>
                Editing Staff ID: <?php echo $_GET['edit']; ?><br>
                Edit Data Found: <?php echo $edit_data ? 'Yes' : 'No'; ?>
            <?php endif; ?>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo count($staff); ?></h4>
                                <p class="mb-0">Total Staff</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
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
                                    $withBranch = array_filter($staff, function($employee) {
                                        return !empty($employee['Branch_ID']);
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
                                    $withFoundation = array_filter($staff, function($employee) {
                                        return !empty($employee['Main_ID']);
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
                                    $withPhone = array_filter($staff, function($employee) {
                                        return !empty($employee['Staff_Phone']);
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
                    <?php 
                    if (isset($_GET['edit'])) {
                        if ($edit_data) {
                            echo 'Edit Staff Member (ID: ' . $edit_data['Staff_ID'] . ')';
                        } else {
                            echo 'Staff Member Not Found';
                        }
                    } else {
                        echo 'Add New Staff Member';
                    }
                    ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['edit']) && !$edit_data): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Staff member not found. Please check the Staff ID.
                    </div>
                    <a href="staff.php" class="btn btn-primary">Back to Staff List</a>
                <?php else: ?>
                    <form method="POST" id="staffForm">
                        <?php if (isset($_GET['edit']) && $edit_data): ?>
                            <input type="hidden" name="Staff_ID" value="<?php echo $edit_data['Staff_ID']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">First Name</label>
                                    <input type="text" class="form-control" name="Staff_Fname" 
                                           value="<?php echo $edit_data ? htmlspecialchars($edit_data['Staff_Fname']) : ''; ?>" 
                                           required maxlength="40" placeholder="Enter first name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Father Name</label>
                                    <input type="text" class="form-control" name="Staff_Fathername" 
                                           value="<?php echo $edit_data ? htmlspecialchars($edit_data['Staff_Fathername']) : ''; ?>" 
                                           required maxlength="40" placeholder="Enter father's name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Last Name</label>
                                    <input type="text" class="form-control" name="Staff_Lname" 
                                           value="<?php echo $edit_data ? htmlspecialchars($edit_data['Staff_Lname']) : ''; ?>" 
                                           required maxlength="40" placeholder="Enter last name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="number" class="form-control" name="Staff_Phone" 
                                           value="<?php echo $edit_data ? $edit_data['Staff_Phone'] : ''; ?>"
                                           placeholder="Enter phone number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Email Address</label>
                                    <input type="email" class="form-control" name="Staff_Email" 
                                           value="<?php echo $edit_data ? htmlspecialchars($edit_data['Staff_Email']) : ''; ?>" 
                                           required maxlength="30" placeholder="Enter email address">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" name="Staff_Address" 
                                           value="<?php echo $edit_data ? htmlspecialchars($edit_data['Staff_Address']) : ''; ?>" 
                                           maxlength="60" placeholder="Enter full address">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Branch</label>
                                    <select class="form-select" name="Branch_ID">
                                        <option value="">-- Select Branch --</option>
                                        <?php foreach ($branches as $branch): ?>
                                            <option value="<?php echo $branch['Branch_ID']; ?>"
                                                <?php 
                                                if ($edit_data && $edit_data['Branch_ID'] == $branch['Branch_ID']) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                                <?php echo htmlspecialchars($branch['Branch_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Foundation</label>
                                    <select class="form-select" name="Main_ID">
                                        <option value="">-- Select Foundation --</option>
                                        <?php foreach ($foundations as $foundation): ?>
                                            <option value="<?php echo $foundation['Main_ID']; ?>"
                                                <?php 
                                                if ($edit_data && $edit_data['Main_ID'] == $foundation['Main_ID']) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                                <?php echo htmlspecialchars($foundation['Main_name']); ?>
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
                                <?php echo isset($_GET['edit']) ? 'Update Staff' : 'Add Staff'; ?>
                            </button>
                            
                            <?php if (isset($_GET['edit'])): ?>
                                <a href="staff.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            <?php endif; ?>
                            
                            <button type="reset" class="btn btn-outline-secondary" onclick="clearForm()">
                                <i class="fas fa-undo"></i> Reset Form
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Staff List -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i>
                    Staff List (<?php echo count($staff); ?> members)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($staff)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No staff members found in the system.</p>
                        <a href="staff.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add First Staff Member
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Full Name</th>
                                    <th>Father Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Branch</th>
                                    <th>Foundation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($staff as $employee): ?>
                                <tr>
                                    <td><strong><?php echo $employee['Staff_ID']; ?></strong></td>
                                    <td>
                                        <?php 
                                        $fullName = htmlspecialchars($employee['Staff_Fname']) . ' ' . htmlspecialchars($employee['Staff_Lname']);
                                        echo $fullName;
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($employee['Staff_Fathername']); ?></td>
                                    <td>
                                        <?php if (!empty($employee['Staff_Phone'])): ?>
                                            <?php echo htmlspecialchars($employee['Staff_Phone']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($employee['Staff_Email']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($employee['Staff_Email']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if (!empty($employee['Staff_Address'])): ?>
                                            <span title="<?php echo htmlspecialchars($employee['Staff_Address']); ?>">
                                                <?php echo strlen($employee['Staff_Address']) > 20 ? 
                                                    substr(htmlspecialchars($employee['Staff_Address']), 0, 20) . '...' : 
                                                    htmlspecialchars($employee['Staff_Address']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($employee['Branch_name'])): ?>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($employee['Branch_name']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($employee['Main_name'])): ?>
                                            <span class="badge bg-success"><?php echo htmlspecialchars($employee['Main_name']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="staff.php?edit=<?php echo $employee['Staff_ID']; ?>" 
                                               class="btn btn-sm btn-warning btn-action" 
                                               title="Edit Staff">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="staff.php?delete=<?php echo $employee['Staff_ID']; ?>" 
                                               class="btn btn-sm btn-danger btn-action" 
                                               title="Delete Staff"
                                               onclick="return confirm('Are you sure you want to delete staff member <?php echo htmlspecialchars($employee['Staff_Fname'] . ' ' . $employee['Staff_Lname']); ?>?')">
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
            document.getElementById('staffForm').reset();
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
            document.getElementById('staffForm').scrollIntoView({ behavior: 'smooth' });
        });
        <?php endif; ?>
    </script>
</body>
</html>