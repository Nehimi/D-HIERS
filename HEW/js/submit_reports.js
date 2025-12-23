document.getElementById('reportForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const report = {
    type: document.getElementById('reportType').value,
    date: document.getElementById('reportDate').value,
    notes: document.getElementById('reportNotes').value,
    file: document.getElementById('reportFile').value
  };

  console.log('Report submitted:', report);
  alert('✅ Report submitted successfully! Redirecting to Dashboard...');

  setTimeout(() => {
    window.location.href = 'hew.html';
  }, 2000);

  this.reset();
});
