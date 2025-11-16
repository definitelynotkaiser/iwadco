<?php
session_start();
include("db_connect.php");

// ✅ Check if admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Handle single status update
if (isset($_POST['update_status'])) {
    $id = intval($_POST['request_id']);
    $status = $_POST['status'];
    $update = $conn->prepare("UPDATE service_requests SET status=? WHERE id=?");
    $update->bind_param("si", $status, $id);
    $update->execute();
    header("Location: admin_service_requests.php");
    exit();
}

// ✅ Handle batch update
if (isset($_POST['batch_resolve'])) {
    if (!empty($_POST['selected_requests'])) {
        $ids = $_POST['selected_requests'];
        $ids_placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $conn->prepare("UPDATE service_requests SET status='Resolved' WHERE id IN ($ids_placeholders)");
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        header("Location: admin_service_requests.php");
        exit();
    }
}

// ✅ Fetch service requests
$query = "SELECT * FROM service_requests ORDER BY created_at DESC";
$result = $conn->query($query);

// Function for status badge color
function statusBadge($status) {
    switch($status){
        case 'Pending': return '<span class="status Pending">Pending</span>';
        case 'In Progress': return '<span class="status InProgress">In Progress</span>';
        case 'Resolved': return '<span class="status Resolved">Resolved</span>';
        case 'Rejected': return '<span class="status Rejected">Rejected</span>';
        default: return '<span class="status Pending">'.$status.'</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Service Requests - IWADCO Admin</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
body { background:#f4f6f9; color:#333; min-height:100vh; display:flex; flex-direction:column; }
header { background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.1); position:fixed; top:0; width:100%; z-index:1000; }
.navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 50px; flex-wrap:wrap; }
.logo { font-size:1.6rem; font-weight:bold; color:#0056a6; cursor:default; }
nav { display:flex; align-items:center; gap:25px; flex-wrap:wrap; }
nav a { text-decoration:none; color:#0056a6; font-weight:500; font-size:14px; padding:6px 10px; border-radius:5px; transition:0.3s; }
nav a:hover, nav a.active { background:#e1efff; color:#0077d1; }

.container { max-width:1200px; margin:130px auto 40px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
h1 { color:#0056a6; margin-bottom:20px; text-align:center; user-select:none; }

.filter-search { display:flex; justify-content:space-between; margin-bottom:10px; flex-wrap:wrap; gap:10px; }
.filter-search input, .filter-search select { padding:8px 10px; border-radius:5px; border:1px solid #ccc; width:200px; }

table { width:100%; border-collapse:collapse; margin-top:20px; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 6px 15px rgba(0,0,0,0.05);}
th, td { padding:12px 15px; font-size:14px; text-align:left; }
th { background:#0077d1; color:white; }
tr:hover { background:#f1f7ff; }

.status { padding:5px 10px; border-radius:6px; font-weight:bold; font-size:0.9rem; }
.status.Pending { background:#ffc107; color:#1a1a1a; }
.status.InProgress { background:#17a2b8; color:#fff; }
.status.Resolved { background:#28a745; color:#fff; }
.status.Rejected { background:#dc3545; color:#fff; }

form select { padding:5px; border:1px solid #ccc; border-radius:5px; margin-right:5px; }
form button { background:#0056a6; color:white; border:none; padding:6px 12px; border-radius:5px; cursor:pointer; margin-top:3px; }
form button:hover { background:#0077d1; }

.back-btn { display:inline-block; margin-bottom:15px; text-decoration:none; color:#0056a6; font-weight:600; }
.back-btn:hover { text-decoration:underline; }

.modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; }
.modal-content { background:#fff; padding:20px; border-radius:10px; max-width:600px; width:90%; position:relative; }
.modal-content h2 { margin-bottom:10px; color:#0056a6; }
.modal-content p { margin-bottom:10px; }
.close-btn { position:absolute; top:10px; right:15px; font-size:18px; cursor:pointer; color:#333; }

footer { background:#0056a6; color:white; text-align:center; padding:12px 0; margin-top:auto; }

@media(max-width:768px){ table, th, td, .filter-search input, .filter-search select { font-size:12px; } }
</style>
<script>
function openModal(details, attachment) {
    document.getElementById('modal-details').innerHTML = details;
    document.getElementById('modal-attachment').innerHTML = attachment ? '<a href="'+attachment+'" target="_blank">View Attachment</a>' : 'None';
    document.getElementById('detailsModal').style.display='flex';
}
function closeModal(){ document.getElementById('detailsModal').style.display='none'; }
function toggleSelectAll(source){
    let checkboxes = document.getElementsByName('selected_requests[]');
    checkboxes.forEach(cb => cb.checked = source.checked);
}
function filterTable(){
    let status = document.getElementById('filterStatus').value.toLowerCase();
    let search = document.getElementById('searchInput').value.toLowerCase();
    let table = document.getElementById('requestsTable');
    let tr = table.getElementsByTagName('tr');
    for(let i=1;i<tr.length;i++){
        let tds = tr[i].getElementsByTagName('td');
        let text = (tds[1].textContent+tds[2].textContent+tds[3].textContent+tds[4].textContent+tds[5].textContent).toLowerCase();
        tr[i].style.display = (text.includes(search) && (status=='' || tds[5].textContent.toLowerCase()==status)) ? '' : 'none';
    }
}
</script>
</head>
<body>
<header>
  <div class="navbar">
    <div class="logo">IWADCO Admin</div>
    <nav>
      <a href="admin.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin.php'?'active':'' ?>">Dashboard</a>
      <a href="user.php" class="<?= basename($_SERVER['PHP_SELF'])=='user.php'?'active':'' ?>">Users</a>
      <a href="bills.php" class="<?= basename($_SERVER['PHP_SELF'])=='bills.php'?'active':'' ?>">Bills</a>
      <a href="payments.php" class="<?= basename($_SERVER['PHP_SELF'])=='payments.php'?'active':'' ?>">Payments</a>
      <a href="admin_applications.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin_applications.php'?'active':'' ?>">Water Applications</a>
      <a href="admin_service_requests.php" class="active">Service Requests</a>
      <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF'])=='settings.php'?'active':'' ?>">Settings</a>
      <a href="logout.php" style="font-weight:bold;">Logout</a>
    </nav>
  </div>
</header>

<div class="container">
  <a href="admin.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
  <h1>Service Requests</h1>

  <div class="filter-search">
      <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search by username/fullname/service...">
      <select id="filterStatus" onchange="filterTable()">
          <option value="">All Status</option>
          <option value="Pending">Pending</option>
          <option value="In Progress">In Progress</option>
          <option value="Resolved">Resolved</option>
          <option value="Rejected">Rejected</option>
      </select>
  </div>

  <form method="POST">
  <?php if ($result->num_rows > 0): ?>
  <table id="requestsTable">
    <tr>
      <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
      <th>ID</th>
      <th>Full Name</th>
      <th>Username</th>
      <th>Service Type</th>
      <th>Status</th>
      <th>Date Submitted</th>
      <th>Action</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><input type="checkbox" name="selected_requests[]" value="<?= $row['id'] ?>"></td>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['fullname']) ?></td>
      <td><?= htmlspecialchars($row['username']) ?></td>
      <td><?= htmlspecialchars($row['service_type']) ?></td>
      <td><?= statusBadge($row['status']) ?></td>
      <td><?= date("M d, Y H:i", strtotime($row['created_at'])) ?></td>
      <td>
        <button type="button" onclick="openModal('<?= nl2br(addslashes(htmlspecialchars($row['details']))) ?>','<?= htmlspecialchars($row['attachment']) ?>')">
          <i class="fas fa-eye"></i> View
        </button>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
            <select name="status">
                <option value="Pending" <?= $row['status']=='Pending'?'selected':'' ?>>Pending</option>
                <option value="In Progress" <?= $row['status']=='In Progress'?'selected':'' ?>>In Progress</option>
                <option value="Resolved" <?= $row['status']=='Resolved'?'selected':'' ?>>Resolved</option>
                <option value="Rejected" <?= $row['status']=='Rejected'?'selected':'' ?>>Rejected</option>
            </select>
            <button type="submit" name="update_status">Update</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
  <br>
  <button type="submit" name="batch_resolve">Mark Selected as Resolved</button>
  <?php else: ?>
    <p style="text-align:center; color:#777;">No service requests yet.</p>
  <?php endif; ?>
  </form>
</div>

<!-- Modal -->
<div class="modal" id="detailsModal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h2>Request Details</h2>
    <p id="modal-details"></p>
    <p id="modal-attachment"></p>
  </div>
</div>

<footer>
  &copy; <?= date('Y') ?> IWADCO. All rights reserved.
</footer>

</body>
</html>
