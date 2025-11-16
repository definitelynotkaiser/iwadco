<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>IWADCO Bill Records</title>
  <style>
    :root{--bg:#0f1724;--card:#0b1420;--muted:#9aa4b2;--accent:#06b6d4;--danger:#ef4444;--ok:#10b981}
    *{box-sizing:border-box}
    body{font-family:Inter,ui-sans-serif,system-ui,Segoe UI,Roboto,Helvetica,Arial;
      margin:0;
      background:linear-gradient(180deg,#031225 0%, #071428 100%);
      color:#e6eef6;
      min-height:100vh}
    .container{max-width:980px;margin:28px auto;padding:20px}
    header{display:flex;gap:16px;align-items:center;justify-content:space-between;flex-wrap:wrap}
    h1{font-size:22px;margin:0}
    p.lead{margin:4px 0 0;color:var(--muted);font-size:13px}
    .card{background:linear-gradient(180deg, rgba(255,255,255,0.025), rgba(255,255,255,0.01));
      padding:16px;border-radius:12px;box-shadow:0 6px 18px rgba(3,6,23,0.6);}
    .top-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
    button, .btn{background:var(--accent);border:0;padding:8px 12px;border-radius:8px;
      color:#042a2f;font-weight:600;cursor:pointer;user-select:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none}
    button:focus, .btn:focus{outline:none}
    button.ghost{background:transparent;color:var(--accent);
      border:1px solid rgba(6,182,212,0.12)}
    .flex{display:flex;gap:12px}
    input,select{padding:8px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);
      background:transparent;color:inherit}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{padding:10px 8px;text-align:left;border-bottom:1px dashed rgba(255,255,255,0.03);
      font-size:14px}
    th{color:var(--muted);font-size:12px}
    .status-paid{color:var(--ok);font-weight:700}
    .status-unpaid{color:var(--danger);font-weight:700}
    .due-soon{background:linear-gradient(90deg, rgba(239,68,68,0.06), transparent);
      padding:6px 8px;border-radius:6px}
    .summary{display:flex;gap:12px;align-items:center;margin-top:12px;flex-wrap:wrap}
    .pill{background:rgba(255,255,255,0.03);padding:10px;border-radius:10px;min-width:160px}
    .muted{color:var(--muted)}
    .small{font-size:13px}
    .actions{display:flex;gap:6px}
    .btn-secondary{background:transparent;border:1px solid rgba(255,255,255,0.04);
      color:var(--muted);padding:6px 8px;border-radius:8px}
    .btn-danger{background:var(--danger);color:#fff}
    .center{text-align:center}
    footer{margin-top:18px;color:var(--muted);font-size:13px}
    .modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;
      background:rgba(2,6,23,0.6);}
    .modal.open{display:flex}
    .modal .panel{width:540px;max-width:95%;background:var(--card);
      padding:16px;border-radius:12px}
    label{display:block;font-size:13px;margin:8px 0 4px;color:var(--muted)}
    .row{display:flex;gap:8px}
    .row > *{flex:1}
    .empty{padding:28px;text-align:center;color:var(--muted)}
    @media (max-width:720px){.summary{flex-direction:column}.row{flex-direction:column}}
  </style>
</head>
<body>
  <div class="container">
    <header>
      <div>
        <h1>IWADCO Bill Records</h1>
        <p class="lead">Track and manage your water bills — add, edit, mark as paid, or export records. View bills by month or show all past records.</p>
      </div>
      <div class="top-actions">
        <input id="search" placeholder="Search (e.g., John Doe, Block 3, Acct #1234)" />
        <select id="monthFilter"></select>
        <button id="addBtn">+ Add Record</button>
      </div>
    </header>

    <section class="card" style="margin-top:12px">
      <div class="summary">
        <div class="pill">
          <div class="small muted">Total Amount</div>
          <div id="totalAll" style="font-size:18px;margin-top:6px">₱0.00</div>
        </div>
        <div class="pill">
          <div class="small muted">Unpaid</div>
          <div id="totalUnpaid" style="font-size:18px;margin-top:6px">₱0.00</div>
        </div>
        <div class="pill">
          <div class="small muted">Paid</div>
          <div id="totalPaid" style="font-size:18px;margin-top:6px">₱0.00</div>
        </div>
        <div style="margin-left:auto;display:flex;gap:8px">
          <button id="exportCsv" class="ghost">Export CSV</button>
          <button id="clearAll" class="btn-secondary">Clear All</button>
        </div>
      </div>

      <div style="margin-top:10px">
        <table id="billsTable" aria-label="Water bills list">
          <thead>
            <tr>
              <th>Customer</th>
              <th>Amount</th>
              <th>Due Date</th>
              <th>Status</th>
              <th class="center">Actions</th>
            </tr>
          </thead>
          <tbody id="tbody">
            <tr><td colspan="5" class="empty">No water bills yet — click <strong>+ Add Record</strong> to begin.</td></tr>
          </tbody>
        </table>
      </div>

      <footer>
        Tip: Use the dropdown to view past months or all records. Click a row to toggle paid/unpaid.
      </footer>
    </section>
  </div>

  <!-- Modal form -->
  <div id="modal" class="modal" role="dialog" aria-modal="true">
    <div class="panel">
      <h3 id="modalTitle">Add Record</h3>
      <div>
        <label for="name">Customer Name</label>
        <input id="name" placeholder="e.g., John Doe or Acct #12345" />
        <div class="row">
          <div>
            <label for="amount">Bill Amount (PHP)</label>
            <input id="amount" type="number" min="0" step="0.01" placeholder="500" />
          </div>
          <div>
            <label for="due">Due Date</label>
            <input id="due" type="date" />
          </div>
        </div>
        <label for="note">Remarks (optional)</label>
        <input id="note" placeholder="Meter reading, area, etc." />
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px">
          <button id="saveBtn">Save</button>
          <button id="cancelBtn" class="btn-secondary">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const LS_KEY = 'iwadco_bills_v1';
    let bills = JSON.parse(localStorage.getItem(LS_KEY) || 'null') || sampleData();
    const tbody = document.getElementById('tbody');
    const totalAll = document.getElementById('totalAll');
    const totalUnpaid = document.getElementById('totalUnpaid');
    const totalPaid = document.getElementById('totalPaid');
    const monthFilter = document.getElementById('monthFilter');

    const modal = document.getElementById('modal');
    const nameI = document.getElementById('name');
    const amountI = document.getElementById('amount');
    const dueI = document.getElementById('due');
    const noteI = document.getElementById('note');
    const modalTitle = document.getElementById('modalTitle');
    let editingId = null;

    function sampleData(){
      return [
        {id: genId(), name:'Acct #10234 - John Doe', amount:450.75, due:'2025-11-05', note:'Zone 2, Meter #5678', paid:false},
        {id: genId(), name:'Acct #20345 - Maria Santos', amount:620.00, due:'2025-10-30', note:'Zone 5, Meter #1122', paid:false},
        {id: genId(), name:'Acct #30456 - Pedro Cruz', amount:310.00, due:'2025-09-12', note:'Zone 1, Meter #3344', paid:true}
      ];
    }

    function genId(){return 'b'+Math.random().toString(36).slice(2,9)}
    function save(){localStorage.setItem(LS_KEY, JSON.stringify(bills));}
    function formatMoney(x){return '₱'+Number(x).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}

    function updateMonthFilter(){
      const months = [...new Set(bills.map(b=>b.due?b.due.slice(0,7):null).filter(Boolean))].sort().reverse();
      monthFilter.innerHTML = `<option value="all">Show All Months</option>`;
      months.forEach(m=>{
        const [y,mo] = m.split('-');
        const monthName = new Date(m+'-01').toLocaleString('en',{month:'long',year:'numeric'});
        const opt = document.createElement('option');
        opt.value=m; opt.textContent=monthName;
        monthFilter.appendChild(opt);
      });
    }

    function render(filter=''){
      const selMonth = monthFilter.value || 'all';
      tbody.innerHTML='';
      const rows = bills.filter(b=>{
        const byName = (b.name||'').toLowerCase().includes(filter.toLowerCase());
        if(selMonth==='all') return byName;
        return byName && b.due && b.due.startsWith(selMonth);
      });
      if(rows.length===0){
        tbody.innerHTML = '<tr><td colspan="5" class="empty">No records found for this month.</td></tr>';
      }
      rows.forEach(b=>{
        const tr = document.createElement('tr');
        tr.tabIndex = 0;
        tr.innerHTML = `
          <td><strong>${escapeHtml(b.name)}</strong><div class="small muted">${escapeHtml(b.note||'')}</div></td>
          <td>${formatMoney(b.amount)}</td>
          <td>${b.due ? dueCell(b.due) : '<span class="muted small">No due date</span>'}</td>
          <td><span class="${b.paid? 'status-paid':'status-unpaid'}">${b.paid? 'PAID':'UNPAID'}</span></td>
          <td class="center">
            <div class="actions">
              <button class="ghost" data-action="edit" data-id="${b.id}">Edit</button>
              <button class="btn-danger" data-action="del" data-id="${b.id}">Delete</button>
            </div>
          </td>`;
        tr.addEventListener('click', e=>{
          if(e.target.tagName.toLowerCase()==='button' || e.target.closest('button')) return;
          togglePaid(b.id);
        });
        tbody.appendChild(tr);
      });
      updateSummary();
    }

    function escapeHtml(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
    function dueCell(dateStr){
      const today = new Date();
      const d = new Date(dateStr+'T00:00:00');
      const diff = Math.ceil((d - new Date(today.getFullYear(),today.getMonth(),today.getDate()))/(1000*60*60*24));
      if(diff<0) return `<span class="muted small">${dateStr} (past due)</span>`;
      if(diff<=3) return `<span class="due-soon">${dateStr} — due in ${diff} day${diff>1?'s':''}</span>`;
      return dateStr;
    }

    function updateSummary(){
      const total = bills.reduce((s,b)=>s+Number(b.amount||0),0);
      const paid = bills.filter(b=>b.paid).reduce((s,b)=>s+Number(b.amount||0),0);
      const unpaid = total - paid;
      totalAll.textContent = formatMoney(total);
      totalPaid.textContent = formatMoney(paid);
      totalUnpaid.textContent = formatMoney(unpaid);
    }

    function togglePaid(id){const itm=bills.find(x=>x.id===id); if(!itm)return; itm.paid=!itm.paid; save(); render(document.getElementById('search').value);}

    document.getElementById('addBtn').addEventListener('click', ()=>openModal());
    document.getElementById('cancelBtn').addEventListener('click', closeModal);
    document.getElementById('saveBtn').addEventListener('click', saveFromModal);
    document.getElementById('search').addEventListener('input', e=>render(e.target.value));
    monthFilter.addEventListener('change', ()=>render(document.getElementById('search').value));

    document.getElementById('tbody').addEventListener('click', e=>{
      const btn = e.target.closest('button'); if(!btn) return;
      const id = btn.dataset.id; const action = btn.dataset.action;
      if(action==='edit') openModal(id);
      if(action==='del') deleteBill(id);
    });

    document.getElementById('clearAll').addEventListener('click', ()=>{
      if(confirm('Are you sure you want to clear all records? This cannot be undone.')){
        bills=[]; save(); render();
      }
    });

    document.getElementById('exportCsv').addEventListener('click', ()=>downloadCSV());

    function openModal(id=null){
      editingId=id; modal.classList.add('open');
      if(id){
        const b=bills.find(x=>x.id===id); if(!b)return;
        modalTitle.textContent='Edit Record';
        nameI.value=b.name; amountI.value=b.amount; dueI.value=b.due||''; noteI.value=b.note||'';
      } else {
        modalTitle.textContent='Add Record';
        nameI.value=''; amountI.value=''; dueI.value=''; noteI.value='';
      }
    }

    function closeModal(){editingId=null; modal.classList.remove('open');}

    function saveFromModal(){
      const name=nameI.value.trim();
      const amount=parseFloat(amountI.value)||0;
      const due=dueI.value||'';
      const note=noteI.value.trim();
      if(!name){alert('Please enter a customer or account name.'); return;}
      if(editingId){
        const b=bills.find(x=>x.id===editingId); if(!b)return;
        b.name=name; b.amount=amount; b.due=due; b.note=note;
      } else {
        bills.push({id:genId(), name, amount, due, note, paid:false});
      }
      save(); updateMonthFilter(); render(document.getElementById('search').value); closeModal();
    }

    function deleteBill(id){
      if(!confirm('Delete this record?')) return;
      bills=bills.filter(x=>x.id!==id); save(); updateMonthFilter(); render(document.getElementById('search').value);
    }

    function downloadCSV(){
      if(bills.length===0){alert('No data to export.'); return}
      const headers=['id','name','amount','due','note','paid'];
      const rows=bills.map(b=>headers.map(h=>('"'+String(b[h]??'').replace(/"/g,'""')+'"')).join(','));
      const csv=[headers.join(','),...rows].join('\n');
      const blob=new Blob([csv],{type:'text/csv'});
      const url=URL.createObjectURL(blob);
      const a=document.createElement('a');
      a.href=url; a.download='iwadco_bill_records.csv';
      document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
    }

    modal.addEventListener('click', e=>{ if(e.target===modal) closeModal(); });
    document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeModal(); });

    updateMonthFilter();
    render();
  </script>
</body>
</html>
