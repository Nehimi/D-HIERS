
document.addEventListener('DOMContentLoaded', function () {
  const kebeleSelect = document.getElementById('kebeleSelect');
  const dataSelect = document.getElementById('dataSelect');
  const openBtn = document.getElementById('openBtn');
  const reviewArea = document.getElementById('reviewArea');
  const submitBtn = document.getElementById('submitBtn');

  // Initial state
  if (reviewArea) reviewArea.value = '';
  if (submitBtn) submitBtn.disabled = true;

  // "Open" button loads data from server using API
  if (openBtn) {
    openBtn.addEventListener('click', function () {
      const kebele = kebeleSelect ? kebeleSelect.value : '';
      const dataType = dataSelect ? dataSelect.value : '';

      if (!kebele || !dataType) {
        alert('Please choose a kebele and data type first.');
        return;
      }

      openBtn.textContent = 'Loading...';
      openBtn.disabled = true;

      // API Call
      fetch(`../api/hew_coordinator.php?action=review&kebele=${encodeURIComponent(kebele)}&dataType=${encodeURIComponent(dataType)}`)
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            if (data.data.length === 0) {
              reviewArea.value = "No records found for this selection.";
            } else {
              // Format JSON data into string
              reviewArea.value = JSON.stringify(data.data, null, 2);
            }
            reviewArea.style.borderColor = '#0f766e';
            if (submitBtn) submitBtn.disabled = false;
          } else {
            alert("Error: " + data.message);
          }
        })
        .catch(err => {
          console.error(err);
          alert("Failed to fetch data.");
        })
        .finally(() => {
          openBtn.textContent = 'Open Record';
          openBtn.disabled = false;
        });
    });
  }

  // "Submit" button logic (Validates the data locally or marks as reviewed)
  if (submitBtn) {
    submitBtn.addEventListener('click', function () {
      const kebele = kebeleSelect.value;
      const dataType = dataSelect.value;

      if (!kebele || !dataType) return;

      if (confirm(`Mark data for ${kebele} - ${dataType} as Reviewed?`)) {
        alert("Data marked as Reviewed.");
        // In a full implementation, we'd send a POST to update status here
        reviewArea.value = '';
        submitBtn.disabled = true;
      }
    });
  }

  // Cleanup UI on change
  [kebeleSelect, dataSelect].forEach(sel => {
    if (!sel) return;
    sel.addEventListener('change', function () {
      if (reviewArea) reviewArea.value = '';
      if (submitBtn) submitBtn.disabled = true;
    });
  });
});
