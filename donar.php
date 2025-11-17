<?php
include 'connection.php';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add'])) {
        $name = $_POST['Donar_name'];
        $fathername = $_POST['Donar_fathername'];
        $lname = $_POST['Donar_Lname'];
        $address = $_POST['Donar_Address'];
        $phone = $_POST['Donar_phone'];
        $email = $_POST['Donar_Email'];
        $main_id = $_POST['Main_ID'];
        
        // Convert empty string to NULL
        if ($main_id === '') {
            $main_id = null;
        }
        
        try {
            $sql = "INSERT INTO Donar (Donar_name, Donar_fathername, Donar_Lname, Donar_Address, Donar_phone, Donar_Email, Main_ID) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $fathername, $lname, $address, $phone, $email, $main_id]);
            $success_message = "Donor added successfully!";
        } catch(PDOException $e) {
            $error_message = "Error adding donor: " . $e->getMessage();
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['Donar_ID'];
        $name = $_POST['Donar_name'];
        $fathername = $_POST['Donar_fathername'];
        $lname = $_POST['Donar_Lname'];
        $address = $_POST['Donar_Address'];
        $phone = $_POST['Donar_phone'];
        $email = $_POST['Donar_Email'];
        $main_id = $_POST['Main_ID'];
        
        // Convert empty string to NULL
        if ($main_id === '') {
            $main_id = null;
        }
        
        try {
            $sql = "UPDATE Donar SET Donar_name=?, Donar_fathername=?, Donar_Lname=?, Donar_Address=?, 
                    Donar_phone=?, Donar_Email=?, Main_ID=? WHERE Donar_ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $fathername, $lname, $address, $phone, $email, $main_id, $id]);
            $success_message = "Donor updated successfully!";
        } catch(PDOException $e) {
            $error_message = "Error updating donor: " . $e->getMessage();
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $sql = "DELETE FROM Donar WHERE Donar_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $success_message = "Donor deleted successfully!";
    } catch(PDOException $e) {
        $error_message = "Error deleting donor: " . $e->getMessage();
    }
}

// Fetch all donors
$sql = "SELECT d.*, c.Main_name FROM Donar d 
        LEFT JOIN Charity_foundation c ON d.Main_ID = c.Main_ID
        ORDER BY d.Donar_ID DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$donors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch charity foundations for dropdown
$sql_foundations = "SELECT * FROM Charity_foundation";
$stmt_foundations = $conn->prepare($sql_foundations);
$stmt_foundations->execute();
$foundations = $stmt_foundations->fetchAll(PDO::FETCH_ASSOC);

// Get donor data for editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $sql = "SELECT * FROM Donar WHERE Donar_ID=?";
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
    <title>Donors Management</title>
   <link href="bootstrap-5.0.2-dist/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .btn-action {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Charity Foundation System</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link active" href="donar.php">Donors</a>
                <a class="nav-link" href="charity_foundations.php">Foundations</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Donors Management</h2>
            <a href="donar.php" class="btn btn-outline-primary">Refresh</a>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="card mb-4">
            <div class="card-header bg-<?php echo isset($_GET['edit']) ? 'warning' : 'primary'; ?> text-white">
                <h5 class="mb-0">
                    <?php echo isset($_GET['edit']) ? 
                        'Edit Donor (ID: ' . $edit_data['Donar_ID'] . ')' : 
                        'Add New Donor'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="donorForm">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="Donar_ID" value="<?php echo $edit_data['Donar_ID']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="Donar_name" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Donar_name']) : ''; ?>" 
                                       required maxlength="40">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Father Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="Donar_fathername" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Donar_fathername']) : ''; ?>" 
                                       required maxlength="40">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="Donar_Lname" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Donar_Lname']) : ''; ?>" 
                                       maxlength="40">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="number" class="form-control" name="Donar_phone" 
                                       value="<?php echo $edit_data ? $edit_data['Donar_phone'] : ''; ?>"
                                       placeholder="Enter phone number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="Donar_Email" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Donar_Email']) : ''; ?>" 
                                       required maxlength="40">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="Donar_Address" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['Donar_Address']) : ''; ?>" 
                                       maxlength="60" placeholder="Enter full address">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Associated Foundation</label>
                                <select class="form-select" name="Main_ID">
                                    <option value="">-- No Foundation --</option>
                                    <?php foreach ($foundations as $foundation): ?>
                                        <option value="<?php echo $foundation['Main_ID']; ?>"
                                            <?php echo ($edit_data && $edit_data['Main_ID'] == $foundation['Main_ID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($foundation['Main_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Optional: Select a charity foundation</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="<?php echo isset($_GET['edit']) ? 'update' : 'add'; ?>" 
                                class="btn btn-<?php echo isset($_GET['edit']) ? 'warning' : 'success'; ?>">
                            <i class="fas fa-<?php echo isset($_GET['edit']) ? 'edit' : 'plus'; ?>"></i>
                            <?php echo isset($_GET['edit']) ? 'Update Donor' : 'Add Donor'; ?>
                        </button>
                        
                        <?php if (isset($_GET['edit'])): ?>
                            <a href="donar.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel Edit
                            </a>
                        <?php endif; ?>
                        
                        <button type="reset" class="btn btn-outline-secondary" onclick="clearForm()">
                            <i class="fas fa-undo"></i> Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Donors List -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">All Donors (<?php echo count($donors); ?> found)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($donors)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">No donors found in the database.</p>
                        <a href="donar.php" class="btn btn-primary">Add First Donor</a>
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
                                    <th>Foundation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($donors as $donor): ?>
                                <tr>
                                    <td><strong><?php echo $donor['Donar_ID']; ?></strong></td>
                                    <td>
                                        <?php 
                                        $fullName = htmlspecialchars($donor['Donar_name']);
                                        if (!empty($donor['Donar_Lname'])) {
                                            $fullName .= ' ' . htmlspecialchars($donor['Donar_Lname']);
                                        }
                                        echo $fullName;
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($donor['Donar_fathername']); ?></td>
                                    <td>
                                        <?php if (!empty($donor['Donar_phone'])): ?>
                                            <?php echo htmlspecialchars($donor['Donar_phone']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($donor['Donar_Email']); ?>">
                                            <?php echo htmlspecialchars($donor['Donar_Email']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if (!empty($donor['Donar_Address'])): ?>
                                            <?php echo htmlspecialchars($donor['Donar_Address']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($donor['Main_name'])): ?>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($donor['Main_name']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="donar.php?edit=<?php echo $donor['Donar_ID']; ?>" 
                                               class="btn btn-sm btn-warning btn-action" 
                                               title="Edit Donor">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="donar.php?delete=<?php echo $donor['Donar_ID']; ?>" 
                                               class="btn btn-sm btn-danger btn-action" 
                                               title="Delete Donor"
                                               onclick="return confirm('Are you sure you want to delete donor <?php echo htmlspecialchars($donor['Donar_name']); ?>? This action cannot be undone.')">
                                                <i class="fas fa-trash"></i> Delete
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

        <!-- Foundation Reference -->
        <?php if (!empty($foundations)): ?>
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Available Charity Foundations Reference</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach($foundations as $foundation): ?>
                        <div class="col-md-3 mb-2">
                            <div class="border p-2 rounded">
                                <small class="text-muted">ID: <?php echo $foundation['Main_ID']; ?></small><br>
                                <strong><?php echo htmlspecialchars($foundation['Main_name']); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function clearForm() {
            document.getElementById('donorForm').reset();
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
            document.getElementById('donorForm').scrollIntoView({ behavior: 'smooth' });
        });
        <?php endif; ?>
    </script>
</body>
</html>