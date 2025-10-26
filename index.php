<?php
// index.php - dashboard listing all tables
require_once 'db_connect.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>CHARITY — Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html{
      background-color: blue;
      justify-content: center;
      align-items: center;
      margin: 0;
      padding: 0;
    }
    header{
      height: 150px;
      width: 100%;
      background-image: url(charity-in-islam-hero-1536x0-c-default.jpg);
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
    }
    header nav{
      height: 150px;
      width: 100%;
      background-image: url(charity-in-islam-hero-1536x0-c-default.jpg);
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;

    }
    
    .bg-light{
      background-color: blue;
      color:black;
      box-sizing: border-box;
      align-items: center;
      justify-content: center;
      margin: 0;
      padding: 0;

    }
    .logo-picture{
      height: 100px;
      
      width: 10px;
    display: inline-block;
    
      margin-left: 10px;
    }
    .logo-picture img{
      height: 100px;
      width: 100px;
    
      border-radius: 50px;
    }
    .logo-pictures{
      margin-top: 20px;
      height: 100px;
      width: 100px;
      position: absolute;
      margin-left: 20px;
    }
    .main-containt{
      height: 150px;
      width: 1000px;
      margin-left: 150px;
     position: absolute;

    }
   .btn-outline-secondary{
    background-color: while;
    color: white;
    font-weight: bold;
    border-radius: 5px;
    transition: all;
    opacity: 0.8;
   }
   .btn-outline-secondary:hover{
    background-color: rgba(100, 80, 150, 0.3);
    color: chartreuse;
   }
   .mb-3{
    font-weight: bold;
    color: darkblue;
    text-align: center;
    border: 3px solid darkblue;
    border-radius: 4px;
    transition:  0.4s;
   }
    .mb-3:hover{
      background-color: lightgreen;
      color: black;
      opacity: 1;
    }
    .main-charity{
      width: 100%;
      margin-top: 0;
      position: absolute;
      height: 500px;
      background-color: blue;
      position: absolute;
    }
    .list-group-item{
      display: inline-block;
      color: white;
      margin-top: 2px;
      background-image: url(eid-alfitr-ramadan-concept-background-260nw-2410029605.webp);
      background-size: cover;
      background-repeat: no-repeat;
      background-position: top;
      border: 4px;

    }
    .d-flex{
      display: inline;
      padding: 1px 5px;
      text-align: center;
      position: relative;
    }
    
  </style>
</head>

<body class="bg-light">
  <header>
    <nav>
      <div class="logo-pictures">
        <div class="logo-picture">
          <img src="15d910f0-3185-4f9c-b1cd-051c7f8a3d83.png" alt="logo" title="Logo page">
      </div>
      </div>
      <div class="main-containt">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3">CHARITY <span style="color: blue;font: size 50px;font-family:tahuma;font-weight:bold ">MANAGEMENT</span> SYSTEM <sup style="color: darkblue;font-weight:bold;">QBM</sup></h1>
      <div class="small text-muted"> <?php echo htmlspecialchars($database); ?></div>
    </div>
    <div>
      <a class="btn btn-outline-secondary" href="README.md" target="_blank">README</a>
    </div>
  </div>
      </div>
    </nav>
  </header>
 
  </div>
  <div class="card">
         <main class="main-charity">
</main>
    <div class="card-body">
  
      <h5 class="card-title mb-3">Minues...</h5>
      <div style="display: inline; background-color:aqua" class="list-group">
        <?php
        $sql = "SELECT TABLE_SCHEMA, TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE' AND TABLE_CATALOG = ? ORDER BY TABLE_SCHEMA, TABLE_NAME";
        $stmt = sqlsrv_query($conn, $sql, [$database]);
        if ($stmt === false) {
            echo "<div class='text-danger'>Error fetching tables.</div>";
        } else {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $schema = $row['TABLE_SCHEMA'];
                $table  = $row['TABLE_NAME'];
                $display = $schema . '.' . $table;
                $link = "table.php?schema=" . urlencode($schema) . "&table=" . urlencode($table);
                echo "<a class='list-group-item list-group-item-action d-flex justify-content-between align-items-center' href='$link'>
                        <div><strong>$display</strong><div class='small text-muted'>Manage data (CRUD)</div></div>
                        <span class='badge bg-primary rounded-pill'>Open</span>
                      </a>";
            }
        }
        ?>
      </div>
    </div>
  </div>
  <footer class="mt-4 small text-muted">Built for SQL Server + PHP (sqlsrv) — Windows Authentication</footer>
</div>

</body>
<footer>
  <div class="footer">
    <h3>contact us</h3>
  </div>
</footer>
</html>