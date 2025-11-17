<?php
include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charity Foundation System</title>
    <link href="bootstrap-5.0.2-dist/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .navbar{
            height: 100px;
            width: 100%;
            background-color:yellow;
            margin: top -10px;;


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
        <div class="navbar-nav ms-auto">
            
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Charity Foundation System</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Dashboard</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Charity Foundations</h5>
                        <p class="card-text">Manage main charity organizations</p>
                        <a href="charity_foundation.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Branches</h5>
                        <p class="card-text">Manage branch locations</p>
                        <a href="branches.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Donors</h5>
                        <p class="card-text">Manage donor information</p>
                        <a href="donar.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Donations</h5>
                        <p class="card-text">Manage donation records</p>
                        <a href="donations.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Project</h5>
                        <p class="card-text">Manage charity projects</p>
                        <a href="project.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-secondary">
                    <div class="card-body">
                        <h5 class="card-title">Events</h5>
                        <p class="card-text">Manage charity events</p>
                        <a href="event.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-dark">
                    <div class="card-body">
                        <h5 class="card-title">Beneficiary</h5>
                        <p class="card-text">Manage beneficiary information</p>
                        <a href="beneficiary.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Staff</h5>
                        <p class="card-text">Manage staff members</p>
                        <a href="staff.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Volunteers</h5>
                        <p class="card-text">Manage volunteer information</p>
                        <a href="volunteers.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Balance</h5>
                        <p class="card-text">View financial balance</p>
                        <a href="balance.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Reports</h5>
                        <p class="card-text">View system reports</p>
                        <a href="reports.php" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>