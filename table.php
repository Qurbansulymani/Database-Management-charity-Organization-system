<?php
// table.php - dynamic CRUD for any table (SQL Server)
// Usage: table.php?schema=dbo&table=YourTable
require_once 'db_connect.php';

$schema = isset($_GET['schema']) ? $_GET['schema'] : 'dbo';
$table  = isset($_GET['table']) ? $_GET['table'] : null;
$page   = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$pagesize = 50;

if (!$table) { echo "No table specified."; exit; }

// helper to safely quote identifiers
function qid($s){ return "[" . str_replace("]", "]]", $s) . "]"; }
$fullTable = qid($schema) . "." . qid($table);

// get columns metadata
$colsSql = "SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION";
$colsStmt = sqlsrv_query($conn, $colsSql, [$schema, $table]);
$columns = [];
while ($c = sqlsrv_fetch_array($colsStmt, SQLSRV_FETCH_ASSOC)) { $columns[] = $c; }

// primary keys
$pkSql = "SELECT k.COLUMN_NAME
          FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
          JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
            ON t.CONSTRAINT_NAME = k.CONSTRAINT_NAME
           AND t.TABLE_SCHEMA = k.TABLE_SCHEMA
           AND t.TABLE_NAME = k.TABLE_NAME
          WHERE t.TABLE_SCHEMA = ? AND t.TABLE_NAME = ? AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
          ORDER BY k.ORDINAL_POSITION";
$pkStmt = sqlsrv_query($conn, $pkSql, [$schema, $table]);
$pks = [];
while ($r = sqlsrv_fetch_array($pkStmt, SQLSRV_FETCH_ASSOC)) { $pks[] = $r['COLUMN_NAME']; }

// Handle actions: insert, update, delete (POST)
$action = $_POST['action'] ?? null;
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'insert') {
    // build insert
    $fields = []; $place = []; $params = [];
    foreach ($columns as $col) {
        $name = $col['COLUMN_NAME'];
        if (isset($_POST[$name]) && $_POST[$name] !== '') {
            $fields[] = qid($name);
            $place[] = '?';
            $params[] = $_POST[$name];
        } elseif (isset($_POST[$name]) && $_POST[$name] === '') {
            // explicit empty string => insert empty string
            $fields[] = qid($name);
            $place[] = '?';
            $params[] = '';
        }
    }
    if (count($fields) > 0) {
        $sql = "INSERT INTO $fullTable (" . implode(',', $fields) . ") VALUES (" . implode(',', $place) . ")";
        $r = sqlsrv_query($conn, $sql, $params);
        if ($r === false) { $message = "Insert failed: " . print_r(sqlsrv_errors(), true); }
        else { $message = "Record inserted."; }
    } else { $message = "Nothing to insert."; }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    if (count($pks) === 0) { $message = "No primary key defined — update not possible."; }
    else {
        $setParts = []; $params = [];
        foreach ($columns as $col) {
            $name = $col['COLUMN_NAME'];
            if (!in_array($name, $pks)) {
                $setParts[] = qid($name) . " = ?";
                $params[] = $_POST[$name] ?? null;
            }
        }
        foreach ($pks as $pk) { $whereParts[] = qid($pk) . " = ?"; $params[] = $_POST['__pk__' . $pk]; }
        $sql = "UPDATE $fullTable SET " . implode(',', $setParts) . " WHERE " . implode(' AND ', $whereParts);
        $r = sqlsrv_query($conn, $sql, $params);
        if ($r === false) { $message = "Update failed: " . print_r(sqlsrv_errors(), true); }
        else { $message = "Record updated."; }
    }
}elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    $message = '';

    if (count($pks) === 0) {
        $message = "❌ No primary key defined — cannot delete.";
    } else {
        $whereParts = [];
        $params = [];

        foreach ($pks as $pk) {
            $val = $_POST['__pk__' . $pk] ?? null;

            if ($val === null || $val === '') {
                $message = "❌ Invalid ID for $pk.";
                break;
            }

            $whereParts[] = qid($pk) . " = ?";
            $params[] = $val;
        }

        if ($message === '') {
            $sql = "DELETE FROM $fullTable WHERE " . implode(' AND ', $whereParts);
            $r = sqlsrv_query($conn, $sql, $params);

            if ($r === false) {
                $message = "❌ Delete failed: " . print_r(sqlsrv_errors(), true);
            } else {
                $message = "✅ Record deleted successfully.";
            }
        }
    }
}


// fetch total count for pagination
$countSql = "SELECT COUNT(*) AS cnt FROM $fullTable";
$countStmt = sqlsrv_query($conn, $countSql);
$totalRows = 0;
if ($countStmt !== false && $row = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC)) { $totalRows = $row['cnt']; }
$totalPages = max(1, ceil($totalRows / $pagesize));
$offset = ($page - 1) * $pagesize;

// ---- SEARCH SUPPORT ----
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = '';
$paramsForQuery = [];
if ($search !== '') {
    // Build WHERE for all text-like columns
    $likeParts = [];
    foreach ($columns as $col) {
        $dt = strtolower($col['DATA_TYPE']);
        if (strpos($dt, 'char') !== false || strpos($dt, 'text') !== false || strpos($dt, 'nchar') !== false) {
            $likeParts[] = qid($col['COLUMN_NAME']) . " LIKE ?";
            $paramsForQuery[] = '%' . $search . '%';
        }
    }
    if (count($likeParts) > 0) {
        $whereClause = "WHERE (" . implode(" OR ", $likeParts) . ")";
    }
}

// ---- COUNT QUERY (with search) ----
$countSql = "SELECT COUNT(*) AS cnt FROM $fullTable $whereClause";
$countStmt = sqlsrv_query($conn, $countSql, $paramsForQuery);
$totalRows = 0;
if ($countStmt !== false && $row = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC)) { $totalRows = $row['cnt']; }
$totalPages = max(1, ceil($totalRows / $pagesize));
$offset = ($page - 1) * $pagesize;

// ---- SELECT QUERY (with search) ----
$selectSql = "SELECT * FROM $fullTable $whereClause ORDER BY (SELECT NULL) OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$paramsForQuery[] = $offset;
$paramsForQuery[] = $pagesize;
$selectStmt = sqlsrv_query($conn, $selectSql, $paramsForQuery);

$rows = [];
if ($selectStmt !== false) {
    while ($r = sqlsrv_fetch_array($selectStmt, SQLSRV_FETCH_ASSOC)) { $rows[] = $r; }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars("$schema.$table"); ?> — Manage</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
   td, th { 
    white-space: nowrap;
   } 
   .table-responsive {
     overflow: auto;
      }
      .bg-light{
        background-color: aqua;
        justify-content: center;
        align-items: center;
        height: 50%;
        margin: 0;
        padding: 0;

      }
      .container{
     
        color: black;
      }
      header{
        height: 120px;
        width: 100%;
        background-image: url(world_-_Main.png);
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
      }
      header nav{
        height: 120px;
        width: 100%;
        background-image: url(world_-_Main.png);
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;

      }
     .logo{
      height: 100px;
      margin-top: 10px;
      width: 100px;
    position: absolute;

     }
     .logo img{
      height: 100px;
      width: 100px;
      border-radius: 50px;
      margin-left: 20px;
     }
      .search{
        height: 120px;
        width: 950px;
        margin-left: 120px;
      
      }
      .minue{
        height: 120px;
        position: absolute;
         margin-top: 20px;
         align-items: center;
        width: 150px;
        margin-left: 970px;
        margin-top: -120px;
      }
      .btn-secondary{
        margin-top: 50px;
        background-color: #333;
        color: white;
        position: absolute;
        padding: 4px 1.5px;
        border: 2px solid #333;
        border-radius: 10px;
      }
      .btn-secondary:hover{
        background-color: rgba(0, 0, 0, 0.2);
        color: white;
        border-radius: 8px;

      }
      form{
        padding: 2px 20px;
        background-color: white;
        font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      }
      form.btn{
        padding: 0 10px;
        background-color: #999999ff;
        border-radius: 3px;
        color: #1b1b1bff;
 margin-left: -20px;
      }
      form.btn:hover{
        background-color: #3f2c2cff;
        color: #c2c0c0ff;
        border-radius: 2px;
       
      }
      .search-box{
        margin-top: 20px;
        height: 80px;
        width: 500px;
        margin-left: 400px;
      
      }
      </style>
</head>
<body class="bg-light">
  <header>
    <nav>
      <div class="logo">
        <img src="15d910f0-3185-4f9c-b1cd-051c7f8a3d83.png" alt="logo" title="Logo picture">
      </div>
      <div class="search">
       
        <div class="container py-4">
           <div class="search-box">
<!-- Search bar -->
<form  method="get" class="mb-3 d-flex" role="search">
  <input type="hidden" name="schema" value="<?php echo htmlspecialchars($schema); ?>">
  <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
  <input style="color: black;" type="text" name="search" class="form-control me-2" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
  <button style="padding:1px 10px; " class="btn btn-outline-primary">Search</button>

           </div>
  <div style="margin-top: -80px
  ;" class="d-flex justify-content-between align-items-center mb-3">
    
    <div>
      <h2 class="h4"><?php echo htmlspecialchars("$schema.$table"); ?></h2>
      <div class="small text-muted">Rows: <?php echo $totalRows; ?> — Page <?php echo $page; ?> / <?php echo $totalPages; ?></div>
    </div>
      </div>
      <div class="minue">
        <div>
      <a class="btn btn-secondary" href="index.php">← Back to tables</a>
    </div>
      </div>
    </nav>
  </header>
  
  </div>

  <?php if (!empty($_GET['search'])): ?>
    <a href="?schema=<?php echo urlencode($schema); ?>&table=<?php echo urlencode($table); ?>" class="btn btn-outline-secondary ms-2">Clear</a>
  <?php endif; ?>
</form
  <?php if ($message): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>

  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-body table-responsive">
          <table style="background-color: #333;color:white;padding:6px
          " class="table table-striped table-sm">
            <thead>
              <tr>
                <?php foreach ($columns as $col): ?>
                  <th><?php echo htmlspecialchars($col['COLUMN_NAME']); ?></th>
                <?php endforeach; ?>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <?php foreach ($columns as $col):
          $name = $col['COLUMN_NAME'];
          $val = isset($r[$name]) ? $r[$name] : '';
          if ($val instanceof DateTime) $val = $val->format('Y-m-d H:i:s');
          echo "<td>" . htmlspecialchars((string)$val) . "</td>";
      endforeach; ?>
      <td>
        <button class="btn btn-sm btn-outline-primary" onclick='openEdit(<?php echo json_encode($r, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT); ?>)'>Edit</button>
        <?php if (count($pks)>0): ?>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete this row?');">
  <input type="hidden" name="table_name" value="<?php echo htmlspecialchars($table); ?>">
  <input type="hidden" name="id" value="<?php echo htmlspecialchars($r[$pks[0]]); ?>">
  <input type="hidden" name="action" value="delete">
  <button class="btn btn-sm btn-outline-danger">Delete</button>
</form>

        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (count($rows)===0): ?>
    <tr><td colspan="<?php echo count($columns)+1; ?>">No rows found.</td></tr>
  <?php endif; ?>
</tbody>

          </table>
        </div>
      </div>

      <!-- pagination -->
      <nav aria-label="Page navigation">
        <ul class="pagination">
          <?php for ($p=1;$p<=$totalPages;$p++): ?>
            <li class="page-item <?php echo $p==$page ? 'active' : ''; ?>"><a class="page-link" href="?schema=<?php echo urlencode($schema); ?>&table=<?php echo urlencode($table); ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a></li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>

    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Insert new record</h5>
          <form method="post" id="insertForm">
            <?php foreach ($columns as $col):
                $name = $col['COLUMN_NAME'];
                $type = $col['DATA_TYPE'];
                $nullable = $col['IS_NULLABLE'] === 'YES';
            ?>
              <div class="mb-2">
                <label class="form-label small"><?php echo htmlspecialchars($name); ?> <span class="text-muted small">(<?php echo $type; ?>)</span></label>
                <input name="<?php echo htmlspecialchars($name); ?>" class="form-control form-control-sm" placeholder="<?php echo $nullable ? 'NULL allowed' : ''; ?>">
              </div>
            <?php endforeach; ?>
            <input type="hidden" name="action" value="insert">
            <button class="btn btn-primary btn-sm">Insert</button>
          </form>
        </div>
      </div>

      <?php if (count($pks)>0): ?>
      <div class="card">
        <div class="card-body">
          <h6 class="card-title">Notes</h6>
          <p class="small text-muted">Editing & deleting works when the table has primary key(s). For complex forms (files, images, relations) customize further.</p>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editForm" method="post">
        <div class="modal-header">
          <h5 class="modal-title">Edit row</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="editBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
        <input type="hidden" name="action" value="update">
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
const columns = <?php echo json_encode($columns); ?>;
const pks = <?php echo json_encode($pks); ?>;
let editModal = null;

function openEdit(row) {
  const body = document.getElementById('editBody');
  body.innerHTML = '';
  columns.forEach(col => {
    const name = col.COLUMN_NAME;
    let val = row[name] !== null && row[name] !== undefined ? row[name] : '';
    const div = document.createElement('div');
    div.className = 'mb-2';
    div.innerHTML = `<label class="form-label small">${name} <span class="text-muted small">(${col.DATA_TYPE})</span></label>`;
    const input = document.createElement('input');
    input.className = 'form-control form-control-sm';
    input.name = name;
    input.value = val;
    div.appendChild(input);
    if (pks.includes(name)) {
      const hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = '__pk__' + name;
      hidden.value = val;
      div.appendChild(hidden);
    }
    body.appendChild(div);
  });
  if (!editModal) editModal = new bootstrap.Modal(document.getElementById('editModal'));
  editModal.show();
}

// ✅ Properly handle form submission for updates
document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault(); // prevent default reload
  const form = e.target;
  const data = new FormData(form);

  fetch(window.location.href, {
    method: 'POST',
    body: data
  })
  .then(res => res.text())
  .then(html => {
    document.open();
    document.write(html);
    document.close();
  })
  .catch(err => alert('Update failed: ' + err));
});
</script>
</body>
</html>