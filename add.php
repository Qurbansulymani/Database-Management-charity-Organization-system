<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$table = $_GET['table'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $columns = $_POST['columns'];
    $values = $_POST['values'];

    $cols = implode(",", $columns);
    $vals = implode(",", array_map(fn($v) => "'" . str_replace("'", "''", $v) . "'", $values));

    $query = "INSERT INTO $table ($cols) VALUES ($vals)";
    $stmt = sqlsrv_query($conn, $query);

    if ($stmt) {
        header("Location: table_view.php?table=$table");
        exit;
    } else {
        die(print_r(sqlsrv_errors(), true));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add - <?= $table ?></title>
   <link href="bootstrap-5.0.2-dist/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">Add New Record to <?= $table ?></h3>
    <form method="post">
        <div class="row">
            <div class="col-md-6">
                <label>Columns (comma separated)</label>
                <input type="text" name="columns[]" class="form-control" placeholder="Example: Name, Age, City" required>
            </div>
            <div class="col-md-6">
                <label>Values (comma separated)</label>
                <input type="text" name="values[]" class="form-control" placeholder="Example: John, 25, London" required>
            </div>
        </div>
        <button class="btn btn-success mt-3">Add Record</button>
    </form>
</div>
</body>
</html>
