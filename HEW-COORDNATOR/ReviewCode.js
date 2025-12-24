
document.addEventListener('DOMContentLoaded', function () {
  const kebeleSelect = document.getElementById('kebeleSelect');
  const dataSelect = document.getElementById('dataTypeSelect');
  const openBtn = document.getElementById('loadDataBtn');
  const reviewArea = document.getElementById('reviewDisplayArea');
  const submitBtn = document.getElementById('submitBtn'); // Note: review.html is missing this, will add it.

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
              reviewArea.innerHTML = '<div style="text-align:center; padding:3rem; color:var(--text-light);"><i class="fa-solid fa-face-empty" style="font-size:3rem; margin-bottom:1rem; display:block;"></i><p>No records found for this selection.</p></div>';
            } else {
              renderTable(data.data, dataType);
            }
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

  function renderTable(data, type) {
    let html = `
      <table class="review-table">
        <thead>
          <tr>
            ${Object.keys(data[0]).map(key => {
      let label = key.replace('_', ' ');
      if (key === 'hew_name') label = 'Submitted By (HEW)';
      return `<th>${label}</th>`;
    }).join('')}
          </tr>
        </thead>
        <tbody>
          ${data.map(row => `
            <tr>
              ${Object.values(row).map(val => `<td>${val === null ? '-' : val}</td>`).join('')}
            </tr>
          `).join('')}
        </tbody>
      </table>
      <div style="margin-top: 2rem; text-align: right;">
        <button id="submitBtn" class="btn-review" style="max-width: 250px;">
          <i class="fa-solid fa-check-circle"></i> Mark as Reviewed
        </button>
      </div>
    `;
    reviewArea.innerHTML = html;

    // Re-attach listener to the newly created button
    const newSubmitBtn = document.getElementById('submitBtn');
    if (newSubmitBtn) {
      newSubmitBtn.addEventListener('click', () => handleReviewSubmit(data, type));
    }
  }

  function handleReviewSubmit(data, dataType) {
    const kebele = kebeleSelect.value;
    if (confirm(`Mark data for ${kebele} - ${dataType} as Reviewed?`)) {
      const submitBtn = document.getElementById('submitBtn');
      submitBtn.textContent = 'Submitting...';
      submitBtn.disabled = true;

      fetch('../api/hew_coordinator.php?action=mark_reviewed', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ kebele, dataType })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert("Data marked as Reviewed successfully!");
            reviewArea.innerHTML = '<div style="text-align:center; padding:4rem; color:var(--text-light);"><i class="fa-solid fa-circle-check" style="font-size:3rem; margin-bottom:1rem; display:block; color:var(--primary);"></i><p>Selection Reviewed.</p></div>';
          } else {
            alert("Error: " + data.message);
            submitBtn.disabled = false;
          }
        })
        .catch(err => {
          console.error(err);
          alert("Network error occurred.");
          submitBtn.disabled = false;
        })
        .finally(() => {
          submitBtn.textContent = 'Mark as Reviewed';
        });
    }
  }

  // Cleanup UI on change
  [kebeleSelect, dataSelect].forEach(sel => {
    if (!sel) return;
    sel.addEventListener('change', function () {
      if (reviewArea) reviewArea.innerHTML = '<div style="text-align:center; padding:4rem; color:var(--text-light);"><i class="fa-solid fa-folder-open" style="font-size:3rem; margin-bottom:1rem; display:block;"></i><p>Select criteria and click "Load Reports" to view data.</p></div>';
    });
  });
});
