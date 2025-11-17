<?php
// beneficiaries.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add'])) {
        $name = $_POST['Bene_name'];
        $fathername = $_POST['Bene_Fathername'];
        $lastname = $_POST['Bene_Lastname'];
        $phone = $_POST['Bene_phone'];
        $email = $_POST['Bene_Email'];
        $address = $_POST['Bene_Address'];
        $branch_id = $_POST['Branch_ID'];
        
        // Convert empty string to NULL
        if ($branch_id === '') {
            $branch_id = null;
        }
        
        try {
            $sql = "INSERT INTO Beneficiary (Bene_name, Bene_Fathername, Bene_Lastname, Bene_phone, Bene_Email, Bene_Address, Branch_ID) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $fathername, $lastname, $phone, $email, $address, $branch_id]);
            $success_message = "Beneficiary added successfully!";
        } catch(PDOException $e) {
            $error_message = "Error adding beneficiary: " . $e->getMessage();
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['Bene_ID'];
        $name = $_POST['Bene_name'];
        $fathername = $_POST['Bene_Fathername'];
        $lastname = $_POST['Bene_Lastname'];
        $phone = $_POST['Bene_phone'];
        $email = $_POST['Bene_Email'];
        $address = $_POST['Bene_Address'];
        $branch_id = $_POST['Branch_ID'];
        
        // Convert empty string to NULL
        if ($branch_id === '') {
            $branch_id = null;
        }
        
        try {
            $sql = "UPDATE Beneficiary SET Bene_name=?, Bene_Fathername=?, Bene_Lastname=?, Bene_phone=?, 
                    Bene_Email=?, Bene_Address=?, Branch_ID=? WHERE Bene_ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $fathername, $lastname, $phone, $email, $address, $branch_id, $id]);
            $success_message = "Beneficiary updated successfully!";
        } catch(PDOException $e) {
            $error_message = "Error updating beneficiary: " . $e->getMessage();
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $sql = "DELETE FROM Beneficiary WHERE Bene_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $success_message = "Beneficiary deleted successfully!";
    } catch(PDOException $e) {
        $error_message = "Error deleting beneficiary: " . $e->getMessage();
    }
}

// Fetch all beneficiaries
$sql = "SELECT b.*, br.Branch_name FROM Beneficiary b 
        LEFT JOIN Branch br ON b.Branch_ID = br.Branch_ID 
        ORDER BY b.Bene_ID DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$beneficiaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch branches for dropdown
$sql_branches = "SELECT * FROM Branch";
$stmt_branches = $conn->prepare($sql_branches);
$stmt_branches->execute();
$branches = $stmt_branches->fetchAll(PDO::FETCH_ASSOC);

// Get beneficiary data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $sql = "SELECT * FROM Beneficiary WHERE Bene_ID=?";
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
    <title>Beneficiary Management</title>
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
                <a class="nav-link" href="donar.php"><i class="fas fa-donate"></i> Donors</a>
                <a class="nav-link" href="staff.php"><i class="fas fa-users"></i> Staff</a>
                <a class="nav-link" href="branches.php"><i class="fas fa-code-branch"></i> Branches</a>
                <a class="nav-link active" href="beneficiaries.php"><i class="fas fa-users"></i> Beneficiaries</a>
                <a class="nav-link" href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">
                <i class="fas fa-users"></i>
                Beneficiary Management
            </h2>
            <a href="beneficiaries.php" class="btn btn-outline-primary">
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
                                <h4><?php echo count($beneficiaries); ?></h4>
                                <p class="mb-0">Total Beneficiaries</p>
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
                                    $withBranch = array_filter($beneficiaries, function($beneficiary) {
                                        return !empty($beneficiary['Branch_ID']);
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
                                    $withPhone = array_filter($beneficiaries, function($beneficiary) {
                                        return !empty($beneficiary['Bene_phone']);
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
                                    $withEmail = array_filter($beneficiaries, function($beneficiary) {
                                        return !empty($beneficiary['Bene_Email']);
                                    });
                                    echo count($withEmail);
                                    ?>
                                </h4>
                                <p class="mb-0">With Email</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-envelope fa-2x"></i>
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
                        'Edit Beneficiary (ID: ' . $edit_data['Bene_ID'] . ')' : 
                        'Add New Beneficiary'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="beneficiaryForm">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="Bene_ID" value="<?php echo $edit_data['Bene_ID']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">First Name</label>
                                <input type="text" class="form-control" name="Bene_name" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Bene_name']) : ''; ?>" 
                                       required maxlength="40" placeholder="Enter first name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Father Name</label>
                                <input type="text" class="form-control" name="Bene_Fathername" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Bene_Fathername']) : ''; ?>" 
                                       required maxlength="40" placeholder="Enter father's name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="Bene_Lastname" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Bene_Lastname']) : ''; ?>" 
                                       maxlength="40" placeholder="Enter last name">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="Bene_phone" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Bene_phone']) : ''; ?>"
                                       placeholder="Enter phone number" maxlength="40">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="Bene_Email" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Bene_Email']) : ''; ?>" 
                                       maxlength="30" placeholder="Enter email address">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="Bene_Address" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Bene_Address']) : ''; ?>" 
                                       maxlength="60" placeholder="Enter full address">
                            </div>
                        </div>
                        <div class="col-md-4">
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
                                <div class="form-text">Optional: Associate with a branch</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="<?php echo isset($_GET['edit']) ? 'update' : 'add'; ?>" 
                                class="btn btn-<?php echo isset($_GET['edit']) ? 'warning' : 'success'; ?>">
                            <i class="fas fa-<?php echo isset($_GET['edit']) ? 'edit' : 'plus'; ?>"></i>
                            <?php echo isset($_GET['edit']) ? 'Update Beneficiary' : 'Add Beneficiary'; ?>
                        </button>
                        
                        <?php if (isset($_GET['edit'])): ?>
                            <a href="beneficiaries.php" class="btn btn-secondary">
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

        <!-- Beneficiaries List -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i>
                    Beneficiaries List (<?php echo count($beneficiaries); ?> beneficiaries)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($beneficiaries)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No beneficiaries found in the system.</p>
                        <a href="beneficiaries.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add First Beneficiary
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Father Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Branch</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($beneficiaries as $beneficiary): ?>
                                <tr>
                                    <td><strong><?php echo $beneficiary['Bene_ID']; ?></strong></td>
                                    <td>
                                        <?php 
                                        $fullName = htmlspecialchars($beneficiary['Bene_name']);
                                        if (!empty($beneficiary['Bene_Lastname'])) {
                                            $fullName .= ' ' . htmlspecialchars($beneficiary['Bene_Lastname']);
                                        }
                                        echo $fullName;
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($beneficiary['Bene_Fathername']); ?></td>
                                    <td>
                                        <?php if (!empty($beneficiary['Bene_phone'])): ?>
                                            <?php echo htmlspecialchars($beneficiary['Bene_phone']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($beneficiary['Bene_Email'])): ?>
                                            <a href="mailto:<?php echo htmlspecialchars($beneficiary['Bene_Email']); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($beneficiary['Bene_Email']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($beneficiary['Bene_Address'])): ?>
                                            <span title="<?php echo htmlspecialchars($beneficiary['Bene_Address']); ?>">
                                                <?php echo strlen($beneficiary['Bene_Address']) > 25 ? 
                                                    substr(htmlspecialchars($beneficiary['Bene_Address']), 0, 25) . '...' : 
                                                    htmlspecialchars($beneficiary['Bene_Address']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($beneficiary['Branch_name'])): ?>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($beneficiary['Branch_name']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="beneficiaries.php?edit=<?php echo $beneficiary['Bene_ID']; ?>" 
                                               class="btn btn-sm btn-warning btn-action" 
                                               title="Edit Beneficiary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="beneficiaries.php?delete=<?php echo $beneficiary['Bene_ID']; ?>" 
                                               class="btn btn-sm btn-danger btn-action" 
                                               title="Delete Beneficiary"
                                               onclick="return confirm('Are you sure you want to delete beneficiary <?php echo htmlspecialchars($beneficiary['Bene_name']); ?>?')">
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
            document.getElementById('beneficiaryForm').reset();
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
            document.getElementById('beneficiaryForm').scrollIntoView({ behavior: 'smooth' });
        });
        <?php endif; ?>
    </script>
</body>
</html>