<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'] ?? $_SESSION['username'];

// Check for new submissions (for notifications)
$newApplications = 0;
if($conn->query("SHOW COLUMNS FROM applications LIKE 'notified'")->num_rows == 1){
    $newApplications = $conn->query("SELECT COUNT(*) AS total FROM applications WHERE request='Pending' AND notified=0")->fetch_assoc()['total'];
    if($newApplications > 0){
        $conn->query("UPDATE applications SET notified=1 WHERE request='Pending' AND notified=0");
    }
}

// Fetch all applications
$applicationsResult = $conn->query("SELECT * FROM applications ORDER BY created_at DESC");

// Analytics summary
$totalApplications = $conn->query("SELECT COUNT(*) AS total FROM applications")->fetch_assoc()['total'] ?? 0;
$pendingApplications = $conn->query("SELECT COUNT(*) AS total FROM applications WHERE request='Pending'")->fetch_assoc()['total'] ?? 0;
$approvedApplications = $conn->query("SELECT COUNT(*) AS total FROM applications WHERE request='Approved'")->fetch_assoc()['total'] ?? 0;
$rejectedApplications = $conn->query("SELECT COUNT(*) AS total FROM applications WHERE request='Rejected'")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Water Applications</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
html, body { height:100%; }
body { display:flex; flex-direction:column; background:#f4f6f9; color:#333; transition:0.3s; }

/* NAVBAR */
header { background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.1); position:fixed; top:0; width:100%; z-index:1000; }
.navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 50px; flex-wrap:wrap; }
.navbar .logo { font-size:1.6rem; font-weight:bold; color:#0056a6; cursor:default; }
nav { display:flex; align-items:center; gap:25px; flex-wrap:wrap; }
nav a { text-decoration:none; color:#0056a6; font-weight:500; font-size:14px; padding:6px 10px; border-radius:5px; transition:0.3s; }
nav a:hover, nav a.active { background:#e1efff; color:#0077d1; }

.main-content { flex:1; padding:130px 40px 40px 40px; }
h1 { margin-bottom:20px; color:#0056a6; user-select:none; }

/* Summary Cards */
.summary-cards { display:flex; gap:20px; flex-wrap:wrap; margin-bottom:30px; }
.card { flex:1 1 180px; background:#0077d1; color:#fff; border-radius:12px; padding:20px; box-shadow:0 6px 18px rgba(0,0,0,0.1); position:relative; overflow:hidden; }
.card h3{ margin:0 0 10px 0; font-size:14px; font-weight:500; }
.card p{ margin:0; font-size:22px; font-weight:bold; }
.card::after{ content:""; position:absolute; width:100%; height:100%; background:linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0)); top:0; left:0; pointer-events:none; }
.progress-bar { height:12px; border-radius:6px; background:#ddd; margin-top:5px; }
.progress { height:12px; border-radius:6px; background:#28a745; transition:0.6s ease; }

/* Table */
table { width:100%; border-collapse:collapse; margin-top:20px; background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); border-radius:12px; overflow:hidden; box-shadow:0 8px 20px rgba(0,0,0,0.08); }
table th, table td { padding:10px; font-size:14px; text-align:center; position:relative; }
table th { background:#0077d1; color:#fff; cursor:pointer; user-select:none; }
table tr:nth-child(even) { background:#f2f2f2; }
table tr:hover { background: rgba(0,119,209,0.1); transform:scale(1.01); transition:0.3s; }

/* Request badges */
.status { padding:5px 10px; border-radius:6px; color:#fff; font-weight:bold; font-size:12px; animation: pulse 1.5s infinite; }
.status.Pending { background: linear-gradient(90deg,#ffc107,#ffb300); color:#1a1a1a; }
.status.Approved { background: linear-gradient(90deg,#28a745,#20c997); }
.status.Rejected { background: linear-gradient(90deg,#dc3545,#e66767); }

@keyframes pulse { 0%{transform:scale(1);}50%{transform:scale(1.05);}100%{transform:scale(1);} }

.action-btn, button { padding:5px 10px; border:none; border-radius:5px; cursor:pointer; font-size:12px; margin:0 2px; transition:0.3s; user-select:none; -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; }
button:focus { outline:none; }
.action-btn:hover { transform:scale(1.05); }
.approve { background:#28a745; color:white; }
.reject { background:#dc3545; color:white; }
.view-btn { background:#0077cc; color:white; }

/* Details row */
.details-row td{ background: rgba(0,119,209,0.05); padding:12px; text-align:left; display:none; transition:all 0.3s ease; }

/* Controls row */
.controls-row input, .controls-row select, .controls-row button{
    padding:5px 8px;
    border-radius:6px;
    border:1px solid #ccc;
    width:100%;
    font-size:12px;
}
.controls-row button{ background:#0077d1; color:#fff; border:none; cursor:pointer; transition:0.3s; }
.controls-row button:hover{ background:#005fa3; transform:scale(1.05); }

.tooltip { position:relative; cursor:help; }
.tooltip:hover::after{
    content: attr(data-tip);
    position:absolute;
    left:50%;
    top:-25px;
    transform:translateX(-50%);
    background:#333;
    color:#fff;
    padding:4px 8px;
    border-radius:4px;
    font-size:12px;
    white-space:nowrap;
    z-index:100;
    opacity:0.95;
}

/* Sticky header */
thead tr { position: sticky; top:0; z-index:2; }

/* Mobile */
@media(max-width:768px){
    table, thead, tbody, th, td, tr{ display:block; }
    tr { margin-bottom:15px; }
    td { text-align:right; padding-left:50%; position:relative; }
    td::before { content:attr(data-label); position:absolute; left:15px; width:45%; font-weight:bold; text-align:left; }
    .details-row td{ padding-left:15px; }
    .controls-row input, .controls-row select, .controls-row button{ width:100%; margin-bottom:5px; }
}
</style>
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
            <a href="admin_applications.php" class="active">Water Applications</a>
            <a href="admin_service_requests.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin_service_requests.php'?'active':'' ?>">Service Requests</a>
            <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF'])=='settings.php'?'active':'' ?>">Settings</a>
            <a href="logout.php" style="font-weight:bold;">Logout</a>
        </nav>
    </div>
</header>

<div class="main-content">
    <h1>Water Connection Applications</h1>

    <div class="summary-cards">
        <div class="card"><h3>Total Applications</h3><p id="totalCard"><?= $totalApplications;?></p></div>
        <div class="card"><h3>Pending</h3><p id="pendingCard"><?= $pendingApplications;?></p>
            <div class="progress-bar"><div class="progress" id="pendingProgress" style="width:<?= ($totalApplications>0)?($pendingApplications/$totalApplications*100):0;?>%"></div></div></div>
        <div class="card"><h3>Approved</h3><p id="approvedCard"><?= $approvedApplications;?></p>
            <div class="progress-bar"><div class="progress" id="approvedProgress" style="width:<?= ($totalApplications>0)?($approvedApplications/$totalApplications*100):0;?>;background:#28a745;"></div></div></div>
        <div class="card"><h3>Rejected</h3><p id="rejectedCard"><?= $rejectedApplications;?></p>
            <div class="progress-bar"><div class="progress" id="rejectedProgress" style="width:<?= ($totalApplications>0)?($rejectedApplications/$totalApplications*100):0;?>;background:#dc3545;"></div></div></div>
    </div>

    <form method="POST">
        <table id="applicationsTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Contact</th>
                    <th>Connection Type</th>
                    <th>Request</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if($applicationsResult && $applicationsResult->num_rows>0): ?>
                <?php while($row=$applicationsResult->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox" name="application_ids[]" value="<?= $row['id']; ?>"></td>
                    <td data-label="ID"><?= $row['id'];?></td>
                    <td data-label="Username"><?= htmlspecialchars($row['username']);?></td>
                    <td data-label="Full Name"><?= htmlspecialchars($row['fullname']);?></td>
                    <td data-label="Contact"><?= htmlspecialchars($row['contact']);?></td>
                    <td data-label="Connection Type"><?= htmlspecialchars($row['connection_type']);?></td>
                    <td data-label="Request"><span class="status <?= $row['request'];?>"><?= $row['request'];?></span></td>
                    <td data-label="Actions">
                        <button type="button" class="action-btn view-btn" onclick="toggleDetails(this)">View</button>
                        <button type="button" class="action-btn approve" onclick="updateRequest(<?= $row['id'];?>,'approve')">Approve</button>
                        <button type="button" class="action-btn reject" onclick="updateRequest(<?= $row['id'];?>,'reject')">Reject</button>
                    </td>
                </tr>
                <tr class="details-row">
                    <td colspan="8">
                        <strong>Address:</strong> <?= htmlspecialchars($row['address']);?><br>
                        <strong>Email:</strong> <?= htmlspecialchars($row['email']);?><br>
                        <strong>DOB:</strong> <?= htmlspecialchars($row['dob']);?><br>
                        <strong>ID Number:</strong> <?= htmlspecialchars($row['id_number']);?><br>
                        <strong>Preferred Date:</strong> <?= htmlspecialchars($row['preferred_date']);?><br>
                        <strong>Notes:</strong> <?= htmlspecialchars($row['notes']);?><br>
                        <?php if(!empty($row['id_file'])): ?>
                            <strong>ID File:</strong> <a href="<?= $row['id_file'];?>" target="_blank">View / Download</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center; padding:15px;">No applications found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </form>
</div>

<script>
// Toggle details row
function toggleDetails(btn){
    const tr = btn.closest('tr');
    const nextTr = tr.nextElementSibling;
    if(nextTr.classList.contains('details-row')){
        nextTr.style.display = nextTr.style.display==='table-row'?'none':'table-row';
    }
}

// Check all
document.getElementById('checkAll').addEventListener('change',function(){
    const checked=this.checked;
    document.querySelectorAll('input[name="application_ids[]"]').forEach(cb=>cb.checked=checked);
});

// Update Request
function updateRequest(id, requestStatus){
    if(!confirm('Are you sure you want to ' + requestStatus + ' this application?')) return;

    fetch('update_application_request.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id='+id+'&request='+requestStatus
    })
    .then(response => response.text())
    .then(data => {
        if(data.trim() === 'success'){
            const row = document.querySelector('input[value="'+id+'"]').closest('tr');
            const statusCell = row.querySelector('.status');
            statusCell.textContent = requestStatus.charAt(0).toUpperCase() + requestStatus.slice(1);
            statusCell.className = 'status ' + statusCell.textContent;
            alert('Application updated successfully.');
        } else {
            alert('Failed to update.');
        }
    });
}
</script>

<footer>&copy; 2025 IWADCO. All rights reserved.</footer>
</body>
</html>
