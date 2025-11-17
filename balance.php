<?php
// balance.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Debug information
echo "<!-- Starting balance.php -->";

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add'])) {
        $main_balance = $_POST['Main_Balance'];
        $balance_project = $_POST['Balance_project'];
        $balance_event = $_POST['Balance_Event'];
        $main_id = $_POST['Main_ID'];
        
        // Convert empty string to NULL
        if ($main_id === '') {
            $main_id = null;
        }
        
        try {
            $sql = "INSERT INTO Balance (Main_Balance, Balance_project, Balance_Event, Main_ID) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$main_balance, $balance_project, $balance_event, $main_id]);
            $success_message = "Balance record added successfully!";
            echo "<!-- Insert successful -->";
        } catch(PDOException $e) {
            $error_message = "Error adding balance record: " . $e->getMessage();
            echo "<!-- Insert error: " . $e->getMessage() . " -->";
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['Balance_ID'];
        $main_balance = $_POST['Main_Balance'];
        $balance_project = $_POST['Balance_project'];
        $balance_event = $_POST['Balance_Event'];
        $main_id = $_POST['Main_ID'];
        
        // Convert empty string to NULL
        if ($main_id === '') {
            $main_id = null;
        }
        
        try {
            $sql = "UPDATE Balance SET Main_Balance=?, Balance_project=?, Balance_Event=?, Main_ID=? 
                    WHERE Balance_ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$main_balance, $balance_project, $balance_event, $main_id, $id]);
            $success_message = "Balance record updated successfully!";
            echo "<!-- Update successful -->";
        } catch(PDOException $e) {
            $error_message = "Error updating balance record: " . $e->getMessage();
            echo "<!-- Update error: " . $e->getMessage() . " -->";
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $sql = "DELETE FROM Balance WHERE Balance_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $success_message = "Balance record deleted successfully!";
        echo "<!-- Delete successful -->";
    } catch(PDOException $e) {
        $error_message = "Error deleting balance record: " . $e->getMessage();
        echo "<!-- Delete error: " . $e->getMessage() . " -->";
    }
}

// Fetch all balance records with foundation information
try {
    $sql = "SELECT b.*, c.Main_name FROM Balance b 
            LEFT JOIN Charity_foundation c ON b.Main_ID = c.Main_ID 
            ORDER BY b.Balance_ID DESC";
    echo "<!-- SQL Query: " . $sql . " -->";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $balances = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<!-- Found " . count($balances) . " balance records -->";
    
} catch(PDOException $e) {
    echo "<!-- Error fetching balances: " . $e->getMessage() . " -->";
    
    // Try without JOIN if there's an error
    try {
        $sql = "SELECT * FROM Balance ORDER BY Balance_ID DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $balances = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<!-- Found " . count($balances) . " balance records (without JOIN) -->";
    } catch(PDOException $e2) {
        echo "<!-- Error fetching balances without JOIN: " . $e2->getMessage() . " -->";
        $balances = [];
    }
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

// Get balance data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    try {
        $sql = "SELECT * FROM Balance WHERE Balance_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_GET['edit']]);
        $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<!-- Editing balance ID: " . $_GET['edit'] . " -->";
    } catch(PDOException $e) {
        echo "<!-- Error fetching edit data: " . $e->getMessage() . " -->";
    }
}

// Calculate totals
$total_main = 0;
$total_project = 0;
$total_event = 0;
$overall_total = 0;

foreach ($balances as $balance) {
    $total_main += floatval($balance['Main_Balance']);
    $total_project += floatval($balance['Balance_project']);
    if (!empty($balance['Balance_Event'])) {
        $total_event += floatval($balance['Balance_Event']);
    }
}
$overall_total = $total_main + $total_project + $total_event;

echo "<!-- Totals - Main: $total_main, Project: $total_project, Event: $total_event, Overall: $overall_total -->";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Management</title>
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
        .total-row {
            background-color: #e9ecef;
            font-weight: 700;
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
                <a class="nav-link" href="donations.php"><i class="fas fa-hand-holding-usd"></i> Donations</a>
                <a class="nav-link active" href="balance.php"><i class="fas fa-balance-scale"></i> Balance</a>
                <a class="nav-link" href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
                <a class="nav-link" href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">
                <i class="fas fa-balance-scale"></i>
                Financial Balance Management
            </h2>
            <a href="balance.php" class="btn btn-outline-primary">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </a>
        </div>

        <!-- Debug Information -->
        <div class="debug-info">
            <strong>Debug Information:</strong><br>
            Balance Records: <?php echo count($balances); ?><br>
            Foundations: <?php echo count($foundations); ?><br>
            Totals - Main: $<?php echo number_format($total_main, 2); ?>, 
            Project: $<?php echo number_format($total_project, 2); ?>, 
            Event: $<?php echo number_format($total_event, 2); ?>, 
            Overall: $<?php echo number_format($overall_total, 2); ?>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>$<?php echo number_format($total_main, 2); ?></h4>
                                <p class="mb-0">Main Balance</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-wallet fa-2x"></i>
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
                                <h4>$<?php echo number_format($total_project, 2); ?></h4>
                                <p class="mb-0">Project Funds</p>
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
                                <h4>$<?php echo number_format($total_event, 2); ?></h4>
                                <p class="mb-0">Event Funds</p>
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
                                <h4>$<?php echo number_format($overall_total, 2); ?></h4>
                                <p class="mb-0">Total Funds</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-line fa-2x"></i>
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
                        'Edit Balance Record (ID: ' . $edit_data['Balance_ID'] . ')' : 
                        'Add New Balance Record'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="balanceForm">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="Balance_ID" value="<?php echo $edit_data['Balance_ID']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Main Balance ($)</label>
                                <input type="number" step="0.01" class="form-control" name="Main_Balance" 
                                       value="<?php echo $edit_data ? $edit_data['Main_Balance'] : '0.00'; ?>" 
                                       required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Project Funds ($)</label>
                                <input type="number" step="0.01" class="form-control" name="Balance_project" 
                                       value="<?php echo $edit_data ? $edit_data['Balance_project'] : '0.00'; ?>" 
                                       required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Event Funds ($)</label>
                                <input type="number" step="0.01" class="form-control" name="Balance_Event" 
                                       value="<?php echo $edit_data ? $edit_data['Balance_Event'] : '0.00'; ?>" 
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Foundation</label>
                                <select class="form-select" name="Main_ID">
                                    <option value="">-- General Balance --</option>
                                    <?php foreach ($foundations as $foundation): ?>
                                        <option value="<?php echo $foundation['Main_ID']; ?>"
                                            <?php echo ($edit_data && $edit_data['Main_ID'] == $foundation['Main_ID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($foundation['Main_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Optional: Associate with a specific foundation</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="<?php echo isset($_GET['edit']) ? 'update' : 'add'; ?>" 
                                class="btn btn-<?php echo isset($_GET['edit']) ? 'warning' : 'success'; ?>">
                            <i class="fas fa-<?php echo isset($_GET['edit']) ? 'edit' : 'plus'; ?>"></i>
                            <?php echo isset($_GET['edit']) ? 'Update Balance' : 'Add Balance Record'; ?>
                        </button>
                        
                        <?php if (isset($_GET['edit'])): ?>
                            <a href="balance.php" class="btn btn-secondary">
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

        <!-- Balance List -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i>
                    Financial Balance (<?php echo count($balances); ?> records)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($balances)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-balance-scale fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No balance records found in the system.</p>
                        <p class="text-muted small">Check the debug information above for details.</p>
                        <a href="balance.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add First Balance Record
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Balance ID</th>
                                    <th>Foundation</th>
                                    <th class="text-end">Main Balance</th>
                                    <th class="text-end">Project Funds</th>
                                    <th class="text-end">Event Funds</th>
                                    <th class="text-end">Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($balances as $balance): 
                                    $row_total = floatval($balance['Main_Balance']) + floatval($balance['Balance_project']) + floatval($balance['Balance_Event'] ?? 0);
                                ?>
                                <tr>
                                    <td><strong><?php echo $balance['Balance_ID']; ?></strong></td>
                                    <td>
                                        <?php if (!empty($balance['Main_name'])): ?>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($balance['Main_name']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">General</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="amount-cell">$<?php echo number_format($balance['Main_Balance'], 2); ?></td>
                                    <td class="amount-cell">$<?php echo number_format($balance['Balance_project'], 2); ?></td>
                                    <td class="amount-cell">
                                        <?php if (!empty($balance['Balance_Event'])): ?>
                                            $<?php echo number_format($balance['Balance_Event'], 2); ?>
                                        <?php else: ?>
                                            <span class="text-muted">$0.00</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="amount-cell text-success">
                                        <strong>$<?php echo number_format($row_total, 2); ?></strong>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="balance.php?edit=<?php echo $balance['Balance_ID']; ?>" 
                                               class="btn btn-sm btn-warning btn-action" 
                                               title="Edit Balance">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="balance.php?delete=<?php echo $balance['Balance_ID']; ?>" 
                                               class="btn btn-sm btn-danger btn-action" 
                                               title="Delete Balance"
                                               onclick="return confirm('Are you sure you want to delete this balance record?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <!-- Total Row -->
                                <tr class="total-row">
                                    <td colspan="2"><strong>GRAND TOTAL</strong></td>
                                    <td class="amount-cell"><strong>$<?php echo number_format($total_main, 2); ?></strong></td>
                                    <td class="amount-cell"><strong>$<?php echo number_format($total_project, 2); ?></strong></td>
                                    <td class="amount-cell"><strong>$<?php echo number_format($total_event, 2); ?></strong></td>
                                    <td class="amount-cell text-success">
                                        <strong>$<?php echo number_format($overall_total, 2); ?></strong>
                                    </td>
                                    <td></td>
                                </tr>
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
            document.getElementById('balanceForm').reset();
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
            document.getElementById('balanceForm').scrollIntoView({ behavior: 'smooth' });
        });
        <?php endif; ?>
    </script>
</body>
</html>