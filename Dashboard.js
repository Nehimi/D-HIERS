<script>
// Simulated database (replaceable with real API calls)
let db = [
  {id:1, facility:'Central Health Center', program:'MCH', patients:124, validated:true, submitted:false, date:'2025-11-01'},
  {id:2, facility:'North Clinic', program:'HIV', patients:52, validated:false, submitted:false, date:'2025-11-02'},
  {id:3, facility:'East Hospital', program:'TB', patients:33, validated:true, submitted:true, date:'2025-11-03'},
  {id:4, facility:'West Health Post', program:'MCH', patients:78, validated:false, submitted:false, date:'2025-11-05'},
  {id:5, facility:'Central Health Center', program:'HIV', patients:64, validated:true, submitted:false, date:'2025-11-09'},
];

// --- UI helpers ---
const tbody = document.querySelector('#data-table tbody');
const totalRecordsEl = document.getElementById('total-records');
const validatedCountEl = document.getElementById('validated-count');
const submittedCountEl = document.getElementById('submitted-count');
const reportPreview = document.getElementById('report-preview');
const selectAllCheckbox = document.getElementById('select-all');
let selectedIds = new Set();

function renderTable(){
  tbody.innerHTML = '';
  db.forEach(row => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input type="checkbox" data-id="${row.id}" ${selectedIds.has(row.id)?'checked':''}></td>
      <td>${row.id}</td>
      <td>${row.facility}</td>
      <td>${row.program}</td>
      <td>${row.patients}</td>
      <td>${row.validated?'<span class="small">Yes</span>':'<span class="small">No</span>'}</td>
      <td>${row.submitted?'<span class="small">Yes</span>':'<span class="small">No</span>'}</td>
      <td>${row.date}</td>
    `;
    tbody.appendChild(tr);
  });
  // attach checkbox listeners
  tbody.querySelectorAll('input[type=checkbox]').forEach(cb => {
    cb.addEventListener('change', e => {
      const id = Number(cb.dataset.id);
      if(cb.checked) selectedIds.add(id); else selectedIds.delete(id);
      updateCounts();
    });
  });
  updateCounts();
}

function updateCounts(){
  totalRecordsEl.textContent = db.length;
  validatedCountEl.textContent = db.filter(r=>r.validated).length;
  submittedCountEl.textContent = db.filter(r=>r.submitted).length;
}

// Select all behavior
selectAllCheckbox.addEventListener('change', ()=>{
  if(selectAllCheckbox.checked) db.forEach(r=>selectedIds.add(r.id)); else selectedIds.clear();
  renderTable();
});

// --- Chart ---
const ctx = document.getElementById('chart').getContext('2d');
let chart;
function drawChart(aggregated){
  const labels = Object.keys(aggregated);
  const data = labels.map(k=>aggregated[k]);
  if(chart) chart.destroy();
  chart = new Chart(ctx, {
    type: 'bar',
    data: {labels, datasets:[{label:'Patients',data}]},
    options:{responsive:true,plugins:{legend:{display:false}}}
  });
}

// --- Core features ---
// Convert Statistical Report: aggregate selected (or all) by program
function convertStatisticalReport(){
  const rows = getSelectedOrAll();
  const agg = {};
  rows.forEach(r=>{ agg[r.program] = (agg[r.program]||0) + r.patients });
  // update chart and preview
  drawChart(agg);
  const lines = ['Statistical Report (by Program):','Program | Patients','-----------------------'];
  for(const p of Object.keys(agg)) lines.push(`${p} | ${agg[p]}`);
  reportPreview.textContent = lines.join('\n');
}

// Submit Validated Data (to HMIS Officer): mark selected validated rows as submitted
function submitValidatedData(){
  const rows = getSelectedOrAll();
  let changed = 0;
  rows.forEach(r=>{
    if(r.validated && !r.submitted){ r.submitted = true; changed++; }
  });
  renderTable();
  alert(changed + ' record(s) submitted to HMIS Officer (locally).');
}

// Generate Reports (PDF) using current preview
async function generateReportPDF(){
  const text = reportPreview.textContent || 'No report';
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  const lines = text.split('\n');
  doc.setFontSize(12);
  doc.text('HMIS Generated Report', 14, 18);
  doc.setFontSize(10);
  let y = 28;
  lines.forEach(line => { doc.text(line, 14, y); y += 6; if(y > 280){ doc.addPage(); y=20; } });
  doc.save('HMIS_report.pdf');
}

// Export Reports (CSV)
function exportCSV(){
  const rows = getSelectedOrAll();
  if(rows.length===0){ alert('No rows to export'); return; }
  const keys = ['id','facility','program','patients','validated','submitted','date'];
  const csv = [keys.join(',')].concat(rows.map(r=> keys.map(k=>JSON.stringify(r[k]===undefined?'':r[k])).join(','))).join('\n');
  const blob = new Blob([csv],{type:'text/csv'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a'); a.href = url; a.download = 'HMIS_export.csv'; a.click(); URL.revokeObjectURL(url);
}

// Utility: get selected rows, or all if none selected
function getSelectedOrAll(){
  const sel = Array.from(selectedIds);
  if(sel.length===0) return db.map(r=>Object.assign({},r)); // return copies
  return db.filter(r=>sel.includes(r.id)).map(r=>Object.assign({},r));
}

// Wire buttons
document.getElementById('btn-convert').addEventListener('click', convertStatisticalReport);
document.getElementById('btn-submit').addEventListener('click', submitValidatedData);
document.getElementById('btn-generate').addEventListener('click', generateReportPDF);
document.getElementById('btn-export-csv').addEventListener('click', exportCSV);

// Navigation shortcuts (simple)
document.getElementById('nav-data').addEventListener('click', ()=>{window.scrollTo({top:400,behavior:'smooth'})});
document.getElementById('nav-reports').addEventListener('click', ()=>{window.scrollTo({top:0,behavior:'smooth'})});

// initial render
renderTable();
convertStatisticalReport();

</script>