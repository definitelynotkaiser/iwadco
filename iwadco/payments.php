<?php
session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'] ?? $_SESSION['username'];

// Connect to database
include("db_connect.php"); // make sure this file sets $conn

// Fetch payments from database
$sql = "SELECT user_id, FULL_NAME, amount, paid_at, method FROM bills ORDER BY paid_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments - IWADCO Admin</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
html, body { height:100%; }
body { display:flex; flex-direction:column; background-color:#f4f6f9; color:#333; }
header { background-color:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.1); position:fixed; top:0; width:100%; z-index:1000; }
.navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 50px; flex-wrap:wrap; }
.navbar .logo { font-size:1.6rem; font-weight:bold; color:#0056a6; cursor:default; }
nav { display:flex; align-items:center; gap:25px; flex-wrap:wrap; }
nav a { text-decoration:none; color:#0056a6; font-weight:500; font-size:14px; padding:6px 10px; border-radius:5px; transition:0.3s; }
nav a:hover { background:#e1efff; color:#0077d1; }
nav a.active { background:#e1efff; color:#0077d1; }

.main-content { flex:1; padding:130px 40px 40px 40px; }
h1 { color:#0056a6; user-select:none; }

table { width:100%; border-collapse:collapse; margin-top:25px; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 6px 15px rgba(0,0,0,0.05); }
table th, table td { padding:12px 15px; text-align:left; font-size:14px; }
table th { background:#0077d1; color:#fff; font-weight:500; position:sticky; top:0; }
table tbody tr { border-bottom:1px solid #eee; transition:background 0.3s; }
table tbody tr:hover { background:#dbe7f5; }

.btn, button { padding:6px 12px; background:#0056a6; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:13px; transition:background 0.3s; text-decoration:none; user-select:none; -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; }
.btn:hover, button:hover { background:#0077d1; }
button:focus { outline:none; }

.search-filter { margin-top:15px; display:flex; flex-wrap:wrap; gap:15px; align-items:center; }
.search-filter input, .search-filter select { padding:6px 10px; font-size:14px; border-radius:6px; border:1px solid #ccc; }

footer { background:#0056a6; color:#fff; text-align:center; padding:15px; font-size:0.9rem; margin-top:auto; }

.no-record { text-align:center; padding:20px; color:#555; }

@media(max-width:992px){ nav{ gap:15px; } }
@media(max-width:768px){ .main-content{ padding:160px 20px 20px 20px; } .navbar{ padding:15px 20px; } }
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
            <a href="admin_applications.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin_applications.php'?'active':'' ?>">Water Applications</a>
            <a href="admin_service_requests.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin_service_requests.php'?'active':'' ?>">Service Requests</a>
            <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF'])=='settings.php'?'active':'' ?>">Settings</a>
            <a href="logout.php" style="font-weight:bold;">Logout</a>
        </nav>
    </div>
</header>

<div class="main-content">
    <h1>Payments Management</h1>

    <div class="search-filter">
        <input type="text" id="searchInput" placeholder="Search by Payment ID or User">
        <select id="methodFilter">
            <option value="">All Methods</option>
            <option value="Cash">Cash</option>
            <option value="GCash">GCash</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>
        <button class="btn" id="addPaymentBtn">Add Payment</button>
        <button class="btn" id="exportCSVBtn">Export CSV</button>
    </div>

    <div style="margin-top:10px;">
        <strong>Total Collected: ₱<span id="totalCollected">0</span></strong>
    </div>

    <table id="paymentsTable">
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>User</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Method</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    echo "<tr>
                        <td>P".str_pad($row['user_id'],3,'0',STR_PAD_LEFT)."</td>
                        <td>".htmlspecialchars($row['FULL_NAME'])."</td>
                        <td>".number_format($row['amount'],2)."</td>
                        <td>".date('Y-m-d', strtotime($row['paid_at']))."</td>
                        <td>".htmlspecialchars($row['method'])."</td>
                        <td>
                            <button class='btn print-btn'>Print</button>
                            <button class='btn delete-btn'>Delete</button>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='no-record'>No payments found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<footer>
    &copy; 2025 IWADCO. All rights reserved.
</footer>

<script>
// Update total collected
function updateTotalCollected(){
    let total = 0;
    document.querySelectorAll('#paymentsTable tbody tr').forEach(row=>{
        const amount = parseFloat(row.children[2].textContent.replace(/,/g,'')) || 0;
        total += amount;
    });
    document.getElementById('totalCollected').textContent = total.toFixed(2);
}
updateTotalCollected();

// Print
document.querySelectorAll('.print-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        const row = btn.closest('tr');
        const receipt = `<div style="padding:20px; font-family:Arial, sans-serif; border:2px solid #0056a6; border-radius:10px;">
            <h2 style="color:#0056a6;">IWADCO Payment Receipt</h2>
            <p><strong>Payment ID:</strong> ${row.children[0].textContent}</p>
            <p><strong>User:</strong> ${row.children[1].textContent}</p>
            <p><strong>Amount:</strong> ₱${row.children[2].textContent}</p>
            <p><strong>Date:</strong> ${row.children[3].textContent}</p>
            <p><strong>Method:</strong> ${row.children[4].textContent}</p>
            <p style="text-align:center; margin-top:20px;">Thank you for your payment!</p>
        </div>`;
        const win = window.open('','', 'width=600,height=700');
        win.document.write(receipt);
        win.document.close();
        win.focus();
        win.print();
        win.close();
    });
});

// Delete
document.querySelectorAll('.delete-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        if(confirm('Delete this payment?')){
            btn.closest('tr').remove();
            updateTotalCollected();
        }
    });
});

// Add Payment
document.getElementById('addPaymentBtn').addEventListener('click', ()=>{
    const tbody = document.querySelector('#paymentsTable tbody');
    const newId = 'P' + String(tbody.children.length+1).padStart(3,'0');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${newId}</td>
        <td>New User</td>
        <td>0</td>
        <td>${new Date().toISOString().split('T')[0]}</td>
        <td>Cash</td>
        <td>
            <button class='btn print-btn'>Print</button>
            <button class='btn delete-btn'>Delete</button>
        </td>`;
    tbody.appendChild(row);

    // Attach events
    row.querySelector('.print-btn').addEventListener('click', ()=>row.querySelector('.print-btn').click());
    row.querySelector('.delete-btn').addEventListener('click', ()=>row.querySelector('.delete-btn').click());

    updateTotalCollected();
});

// Search
document.getElementById('searchInput').addEventListener('input', e=>{
    const val = e.target.value.toLowerCase();
    document.querySelectorAll('#paymentsTable tbody tr').forEach(row=>{
        const pid = row.children[0].textContent.toLowerCase();
        const user = row.children[1].textContent.toLowerCase();
        row.style.display = (pid.includes(val) || user.includes(val)) ? '' : 'none';
    });
});

// Filter by Method
document.getElementById('methodFilter').addEventListener('change', e=>{
    const val = e.target.value;
    document.querySelectorAll('#paymentsTable tbody tr').forEach(row=>{
        row.style.display = (val==='') ? '' : (row.children[4].textContent===val ? '' : 'none');
    });
});

// Export CSV
document.getElementById('exportCSVBtn').addEventListener('click', ()=>{
    let csv='Payment ID,User,Amount,Date,Method\n';
    document.querySelectorAll('#paymentsTable tbody tr').forEach(row=>{
        csv += Array.from(row.children).slice(0,5).map(td=>td.textContent).join(',')+'\n';
    });
    const blob = new Blob([csv], {type:'text/csv'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'payments.csv';
    a.click();
    URL.revokeObjectURL(url);
});
</script>

</body>
</html>
