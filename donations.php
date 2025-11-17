<?php
// donations.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add'])) {
        $type = $_POST['Donation_Type'];
        $amount = $_POST['Donation_amount'];
        $donar_id = $_POST['Donar_ID'];
        
        // Convert empty string to NULL
        if ($donar_id === '') {
            $donar_id = null;
        }
        
        try {
            $sql = "INSERT INTO Donation (Donation_Type, Donation_amount, Donar_ID) 
                    VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$type, $amount, $donar_id]);
            $success_message = "Donation added successfully!";
        } catch(PDOException $e) {
            $error_message = "Error adding donation: " . $e->getMessage();
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['Donation_ID'];
        $type = $_POST['Donation_Type'];
        $amount = $_POST['Donation_amount'];
        $donar_id = $_POST['Donar_ID'];
        
        // Convert empty string to NULL
        if ($donar_id === '') {
            $donar_id = null;
        }
        
        try {
            $sql = "UPDATE Donation SET Donation_Type=?, Donation_amount=?, Donar_ID=? 
                    WHERE Donation_ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$type, $amount, $donar_id, $id]);
            $success_message = "Donation updated successfully!";
        } catch(PDOException $e) {
            $error_message = "Error updating donation: " . $e->getMessage();
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $sql = "DELETE FROM Donation WHERE Donation_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $success_message = "Donation deleted successfully!";
    } catch(PDOException $e) {
        $error_message = "Error deleting donation: " . $e->getMessage();
    }
}

// Fetch all donations with donor information
$sql = "SELECT d.*, dr.Donar_name, dr.Donar_fathername, dr.Donar_Lname 
        FROM Donation d 
        LEFT JOIN Donar dr ON d.Donar_ID = dr.Donar_ID 
        ORDER BY d.Donation_ID DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch donors for dropdown
$sql_donors = "SELECT * FROM Donar";
$stmt_donors = $conn->prepare($sql_donors);
$stmt_donors->execute();
$donors = $stmt_donors->fetchAll(PDO::FETCH_ASSOC);

// Get donation data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $sql = "SELECT * FROM Donation WHERE Donation_ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Calculate total donations
$total_donations = 0;
foreach ($donations as $donation) {
    $total_donations += floatval($donation['Donation_amount']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Management</title>
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
        .amount-cell {
            font-weight: 600;
            text-align: right;
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
                <a class="nav-link active" href="donations.php"><i class="fas fa-hand-holding-usd"></i> Donations</a>
                <a class="nav-link" href="balance.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a class="nav-link" href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">
                <i class="fas fa-hand-holding-usd"></i>
                Donation Management
            </h2>
            <a href="donations.php" class="btn btn-outline-primary">
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
                                <h4><?php echo count($donations); ?></h4>
                                <p class="mb-0">Total Donations</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-hand-holding-usd fa-2x"></i>
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
                                <h4>$<?php echo number_format($total_donations, 2); ?></h4>
                                <p class="mb-0">Total Amount</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x"></i>
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
                                    $withDonor = array_filter($donations, function($donation) {
                                        return !empty($donation['Donar_ID']);
                                    });
                                    echo count($withDonor);
                                    ?>
                                </h4>
                                <p class="mb-0">With Donor</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-user fa-2x"></i>
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
                                    $anonymous = array_filter($donations, function($donation) {
                                        return empty($donation['Donar_ID']);
                                    });
                                    echo count($anonymous);
                                    ?>
                                </h4>
                                <p class="mb-0">Anonymous</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-user-secret fa-2x"></i>
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
                        'Edit Donation (ID: ' . $edit_data['Donation_ID'] . ')' : 
                        'Add New Donation'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="donationForm">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="Donation_ID" value="<?php echo $edit_data['Donation_ID']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Donation Type</label>
                                <input type="text" class="form-control" name="Donation_Type" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Donation_Type']) : ''; ?>" 
                                       required maxlength="50" placeholder="Enter donation type (e.g., Cash, Food, Clothes)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Donation Amount</label>
                                <input type="text" class="form-control" name="Donation_amount" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Donation_amount']) : ''; ?>" 
                                       required maxlength="20" placeholder="Enter amount or quantity">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Donor</label>
                                <select class="form-select" name="Donar_ID">
                                    <option value="">-- Anonymous Donation --</option>
                                    <?php foreach ($donors as $donor): ?>
                                        <option value="<?php echo $donor['Donar_ID']; ?>"
                                            <?php echo ($edit_data && $edit_data['Donar_ID'] == $donor['Donar_ID']) ? 'selected' : ''; ?>>
                                            <?php 
                                            $donorName = htmlspecialchars($donor['Donar_name']);
                                            if (!empty($donor['Donar_Lname'])) {
                                                $donorName .= ' ' . htmlspecialchars($donor['Donar_Lname']);
                                            }
                                            echo $donorName . ' (' . htmlspecialchars($donor['Donar_Email']) . ')';
                                            ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Optional: Select donor or leave blank for anonymous donation</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="<?php echo isset($_GET['edit']) ? 'update' : 'add'; ?>" 
                                class="btn btn-<?php echo isset($_GET['edit']) ? 'warning' : 'success'; ?>">
                            <i class="fas fa-<?php echo isset($_GET['edit']) ? 'edit' : 'plus'; ?>"></i>
                            <?php echo isset($_GET['edit']) ? 'Update Donation' : 'Add Donation'; ?>
                        </button>
                        
                        <?php if (isset($_GET['edit'])): ?>
                            <a href="donations.php" class="btn btn-secondary">
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

        <!-- Donations List -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i>
                    Donations List (<?php echo count($donations); ?> donations)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($donations)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-hand-holding-usd fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No donations found in the system.</p>
                        <a href="donations.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add First Donation
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Donation ID</th>
                                    <th>Type</th>
                                    <th class="text-end">Amount</th>
                                    <th>Donor</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($donations as $donation): ?>
                                <tr>
                                    <td><strong><?php echo $donation['Donation_ID']; ?></strong></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($donation['Donation_Type']); ?></span>
                                    </td>
                                    <td class="amount-cell">
                                        <strong><?php echo htmlspecialchars($donation['Donation_amount']); ?></strong>
                                    </td>
                                    <td>
                                        <?php if (!empty($donation['Donar_name'])): ?>
                                            <?php 
                                            $donorName = htmlspecialchars($donation['Donar_name']);
                                            if (!empty($donation['Donar_Lname'])) {
                                                $donorName .= ' ' . htmlspecialchars($donation['Donar_Lname']);
                                            }
                                            echo $donorName;
                                            ?>
                                        <?php else: ?>
                                            <span class="text-muted">Anonymous</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="donations.php?edit=<?php echo $donation['Donation_ID']; ?>" 
                                               class="btn btn-sm btn-warning btn-action" 
                                               title="Edit Donation">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="donations.php?delete=<?php echo $donation['Donation_ID']; ?>" 
                                               class="btn btn-sm btn-danger btn-action" 
                                               title="Delete Donation"
                                               onclick="return confirm('Are you sure you want to delete this donation?')">
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
            document.getElementById('donationForm').reset();
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
            document.getElementById('donationForm').scrollIntoView({ behavior: 'smooth' });
        });
        <?php endif; ?>
    </script>
</body>
</html>