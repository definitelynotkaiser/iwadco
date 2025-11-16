<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Online Payments - IWADCO</title>
  <link rel="stylesheet" href="payment.css">
  <!-- Html5Qrcode for QR scanning -->
  <script src="https://unpkg.com/html5-qrcode@2.3.8/dist/html5-qrcode.min.js"></script>
  <style>
    /* GLOBAL */
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f5f7fa;
      color: #333;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* HEADER — STICKY NAV */
    .site-header {
      background: #0d3b66;
      color: white;
      position: sticky;
      top: 0;
      z-index: 999;
      width: 100%;
    }

    .header-inner {
      max-width: 980px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .logo {
      font-weight: bold;
      font-size: 22px;
      flex-shrink: 0;
      cursor: pointer;
    }

    .main-nav {
      display: flex;
      gap: 18px;
      flex-wrap: wrap;
    }

    .main-nav a {
      color: white;
      text-decoration: none;
      padding: 6px 10px;
      border-radius: 4px;
      transition: 0.2s;
      white-space: nowrap;
    }

    .main-nav a:hover { background: rgba(255,255,255,0.18); }
    .main-nav a.active { border-bottom: 2px solid #fff; font-weight: bold; }

    .hamburger {
      font-size: 26px;
      cursor: pointer;
      display: none;
      flex-shrink: 0;
    }

    /* MOBILE NAV */
    @media(max-width:700px){
      .hamburger { display: block; }

      .main-nav {
        display: none;
        flex-direction: column;
        background: #0d3b66;
        width: 100%;
        margin-top: 10px;
        border-radius: 6px;
        padding: 10px;
        gap: 10px;
      }

      body.nav-open .main-nav { display: flex; }

      .main-nav a { padding: 8px 12px; font-size: 16px; }
      .header-inner { padding: 10px 15px; }
    }

    /* PAGE CONTENT */
    .container { flex: 1; max-width: 980px; margin: auto; padding: 20px; }

    .payment-logos { display:flex; justify-content:center; gap:18px; margin-top:20px; }
    .payment-logos img { height:50px; opacity:0.95; }

    .page-title { text-align:center; margin-top:18px; }

    .bill-card {
      background: white;
      padding: 22px;
      border-radius: 10px;
      box-shadow: 0 4px 18px rgba(0,0,0,.08);
      margin-top: 20px;
    }

    .bill-card .amount { font-size: 18px; margin: 20px 0; }

    .btn {
      width: 100%;
      padding: 14px;
      border-radius: 6px;
      font-size: 16px;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: .2s;
      color: white;
    }

    .btn img { height: 22px; }

    .gcash-btn { background: #0277ff; margin-top: 10px; }
    .maya-btn { background: #00c66a; margin-top: 10px; }

    .btn:hover { opacity: .88; transform: translateY(-1px); }

    .message { margin-top: 18px; text-align: center; color: #0d3b66; font-size: 15px; }

    .site-footer { background: #0d3b66; color: white; text-align: center; padding: 12px; margin-top: auto; }

    /* MODAL */
    .modal-bg {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }

    .modal {
      background:white;
      padding:20px;
      border-radius:10px;
      width:320px;
      max-width:90%;
      box-shadow:0 6px 18px rgba(0,0,0,0.2);
    }

    .field { margin-bottom:10px; display:flex; flex-direction:column;}
    label { font-size:14px; margin-bottom:4px;}
    input { padding:8px; border-radius:6px; border:1px solid #ccc;}
    button { padding:10px; border:none; border-radius:6px; cursor:pointer;}
    .close { background:#ccc; color:#111; }
    .result { background:#f3f4f6; padding:8px; border-radius:6px; margin-top:8px;}
    .qr-toggle { display:flex; align-items:center; gap:6px; margin-bottom:8px;}
    #qrContainer { display:none; margin-bottom:10px;}
  </style>
</head>

<body>
  <!-- HEADER -->
  <header class="site-header">
    <div class="header-inner">
      <!-- ✅ fixed: now points to home.php -->
      <div class="logo" onclick="window.location.href='home.php'">IWADCO</div>
      <div class="hamburger" onclick="document.body.classList.toggle('nav-open')">☰</div>
      <nav class="main-nav">
        <a href="home.php">Home</a>
        <a href="payments.php" class="active">Online Payments</a>
        <a href="contact.php">Contact</a>
        <a href="login.php">Login</a>
      </nav>
    </div>
  </header>

  <!-- PAGE BODY -->
  <main class="container payments-page">
    <h1 class="page-title">Online Payments</h1>
    <p class="subtitle">Pay securely using your preferred e-Wallet.</p>

    <div class="payment-logos">
      <img src="assets/logos/gcash.png" alt="GCash" class="logo-icon">
      <img src="assets/logos/paymaya.png" alt="PayMaya" class="logo-icon">
    </div>

    <h2 class="section-title">Outstanding Bill</h2>

    <div class="bill-card">
      <div class="bill-info">
        <p><span>Account:</span> <strong>ACC-IW-000123</strong></p>
        <p><span>Bill ID:</span> <strong id="bill-id">1</strong></p>
        <p class="amount"><span>Amount Due:</span> <strong id="bill-amount">₱ 1,000.00 PHP</strong></p>
        <p><span>Due Date:</span> <strong>2025-09-10</strong></p>
      </div>

      <button class="btn gcash-btn" onclick="startPayment('gcash')">
        <img src="assets/logos/gcash.png" alt="GCash"> Pay with GCash
      </button>

      <button class="btn maya-btn" onclick="startPayment('maya')">
        <img src="assets/logos/paymaya.png" alt="PayMaya"> Pay with Maya
      </button>
    </div>

    <div id="message" class="message"></div>
  </main>

  <!-- PAYMENT MODAL -->
  <div id="paymentModal" class="modal-bg">
    <div class="modal">
      <h2 id="modalTitle">Pay</h2>

      <div class="field">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" placeholder="09XXXXXXXXX">
      </div>

      <div class="field">
        <label for="fullname">Full Name</label>
        <input type="text" id="fullname" placeholder="Juan dela Cruz">
      </div>

      <div class="field">
        <label for="card">Card Number (optional)</label>
        <input type="text" id="card" placeholder="**** **** **** ****">
      </div>

      <div class="qr-toggle">
        <input type="checkbox" id="useScanner">
        <label for="useScanner">Scan QR code instead</label>
      </div>

      <div id="qrContainer">
        <div id="reader" style="width:100%;"></div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
        <button id="showInfo">Show Info</button>
        <button id="closeModal" class="close">Close</button>
      </div>

      <div class="result" id="result" style="display:none;"></div>
    </div>
  </div>

  <footer class="site-footer">
    © IWADCO — All rights reserved.
  </footer>

  <script>
    const modal = document.getElementById('paymentModal');
    const modalTitle = document.getElementById('modalTitle');
    const closeModalBtn = document.getElementById('closeModal');
    const showInfoBtn = document.getElementById('showInfo');
    const phone = document.getElementById('phone');
    const fullname = document.getElementById('fullname');
    const card = document.getElementById('card');
    const result = document.getElementById('result');
    const useScanner = document.getElementById('useScanner');
    const qrContainer = document.getElementById('qrContainer');
    let html5QrScanner = null;

    function startPayment(method){
      modalTitle.textContent = method==='gcash' ? 'Pay with GCash' : 'Pay with PayMaya';
      modal.style.display='flex';
      phone.value = '';
      fullname.value = '';
      card.value = '';
      result.style.display='none';
      useScanner.checked = false;
      qrContainer.style.display='none';
      stopScanner();
    }

    closeModalBtn.addEventListener('click', () => modal.style.display='none');

    showInfoBtn.addEventListener('click', () => {
      result.style.display='block';
      result.innerHTML = `
        <strong>Payment Info:</strong><br>
        Phone: ${phone.value || '-'}<br>
        Full Name: ${fullname.value || '-'}<br>
        Card: ${card.value || '-'}
      `;
    });

    useScanner.addEventListener('change', () => {
      if(useScanner.checked){
        qrContainer.style.display='block';
        startScanner();
      } else {
        qrContainer.style.display='none';
        stopScanner();
      }
    });

    function startScanner(){
      if(typeof Html5Qrcode==='undefined'){ 
        qrContainer.innerHTML = '<div style="color:red;">QR scanning library not loaded.</div>';
        return; 
      }
      html5QrScanner = new Html5Qrcode("reader");
      Html5Qrcode.getCameras().then(cameras=>{
        if(cameras && cameras.length){
          html5QrScanner.start(cameras[0].id, {fps:10, qrbox:250}, qrCodeMessage=>{
            phone.value = qrCodeMessage.replace(/\D/g,'').slice(-11);
            stopScanner();
          });
        } else { qrContainer.innerHTML='<div style="color:red;">No camera found.</div>'; }
      }).catch(err=>{ qrContainer.innerHTML='<div style="color:red;">Camera error.</div>'; });
    }

    function stopScanner(){
      if(html5QrScanner){
        html5QrScanner.stop().then(()=>html5QrScanner.clear()).catch(()=>{});
        html5QrScanner=null;
      }
    }
  </script>
</body>
</html>
