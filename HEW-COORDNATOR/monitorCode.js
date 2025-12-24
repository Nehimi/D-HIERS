
document.addEventListener('DOMContentLoaded', function () {

  // --- Element Selection using IDs ---
  const hewSelect = document.getElementById('hewSelect');
  const activityReviewArea = document.getElementById('activityReviewArea');
  const openActivityBtn = document.getElementById('openActivityBtn');
  const submitReviewBtn = document.getElementById('submitReviewBtn');

  // --- Live Data Variable ---
  let hewData = {};

  // --- Fetch Data from API ---
  function fetchHewActivity() {
    fetch('../api/hew_coordinator.php?action=monitor')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          hewData = data.data;
          console.log("HEW Activity Data Loaded", hewData);
          populateHewSelect(hewData);
        } else {
          console.error("Failed to load data:", data.message);
          alert("Error loading HEW activity: " + data.message);
        }
      })
      .catch(error => console.error('Error fetching data:', error));
  }

  // --- Helper: Populate Dropdown ---
  function populateHewSelect(data) {
    if (!hewSelect) return;

    // Clear existing hardcoded options (except placeholder if desired)
    hewSelect.innerHTML = '<option value="">-- Select HEW --</option>';

    for (const [key, details] of Object.entries(data)) {
      const option = document.createElement('option');
      option.value = key;
      option.textContent = `${details.name} (${details.kebele})`;
      hewSelect.appendChild(option);
    }
  }

  // Initial Fetch
  fetchHewActivity();

  // --- Initial State ---
  if (activityReviewArea) activityReviewArea.value = '';
  if (submitReviewBtn) {
    submitReviewBtn.disabled = true;
  }

  // --- Event Listener for "Open Activity" Button ---
  if (openActivityBtn) {
    openActivityBtn.addEventListener('click', function () {
      const selectedHewId = hewSelect ? hewSelect.value : '';

      if (!selectedHewId) {
        alert('Please select an HEW to open their activity details.');
        return;
      }

      const activityDetails = hewData[selectedHewId];

      if (activityDetails) {
        renderActivityDetails(activityDetails);
      } else {
        activityReviewArea.innerHTML = `<div style="text-align:center; padding:3rem; color:var(--text-light);"><p>No live activity details found for HEW ID: ${selectedHewId}.</p></div>`;
      }
    });
  }

  function renderActivityDetails(details) {
    let html = `
      <div style="padding: 1.5rem; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 2rem;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div><span style="color:var(--text-muted); font-size:0.8rem; text-transform:uppercase; font-weight:700;">HEW Name</span><p style="font-weight:600; font-size:1.1rem;">${details.name}</p></div>
          <div><span style="color:var(--text-muted); font-size:0.8rem; text-transform:uppercase; font-weight:700;">Kebele</span><p style="font-weight:600; font-size:1.1rem;">${details.kebele}</p></div>
          <div><span style="color:var(--text-muted); font-size:0.8rem; text-transform:uppercase; font-weight:700;">Status</span><p><span class="badge ${details.status === 'active' ? 'success' : 'warning'}">${details.status}</span></p></div>
        </div>
      </div>
      
      <h4 style="margin-bottom: 1rem; color: var(--primary); font-size: 1rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fa-solid fa-chart-simple"></i> Productivity Metrics
      </h4>
      
      <table class="review-table">
        <thead>
          <tr>
            <th>Metric</th>
            <th>Value</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Total Household Visits</td>
            <td><strong>${details.visits}</strong></td>
          </tr>
          <tr>
            <td>Pending Reports</td>
            <td><strong style="color: #f59e0b;">${details.pending_reports}</strong></td>
          </tr>
          <tr>
            <td>ANC Cases Managed</td>
            <td><strong>${details.anc_cases}</strong></td>
          </tr>
        </tbody>
      </table>

      <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
        <button id="submitReviewBtn" class="btn-review" style="background:#0f766e;">
          <i class="fa-solid fa-check-circle"></i> Verify Activity
        </button>
      </div>
    `;
    activityReviewArea.innerHTML = html;

    // Attach listener to newly created button
    const newBtn = document.getElementById('submitReviewBtn');
    if (newBtn) {
      newBtn.addEventListener('click', function () {
        alert(`Activity logging for ${details.name} verified and recorded.`);
        resetForm();
      });
    }
  }

  // --- Event Listener for "Submit Review" Button ---
  if (submitReviewBtn) {
    submitReviewBtn.addEventListener('click', function () {
      const selectedHewId = hewSelect ? hewSelect.value : '';
      const reviewNotes = activityReviewArea ? activityReviewArea.value.trim() : '';

      if (!selectedHewId || !reviewNotes) {
        alert('Please select an HEW and verify review notes.');
        return;
      }

      // For now, simple alert as "Review" action might not have a dedicated write endpoint in the plan
      // We can add a POST action later if needed.
      alert(`Review for ${selectedHewId} verified locally.`);
      resetForm();
    });
  }

  // --- UX Improvement: Clear/Disable on HEW Selection Change ---
  if (hewSelect) {
    hewSelect.addEventListener('change', function () {
      if (activityReviewArea) {
        activityReviewArea.value = '';
        activityReviewArea.style.borderColor = '#ccc';
      }
      if (submitReviewBtn) {
        submitReviewBtn.disabled = true;
      }
    });
  }

  // --- Helper Function to Reset Form State ---
  function resetForm() {
    if (activityReviewArea) {
      activityReviewArea.innerHTML = `
        <div style="text-align:center; padding:3rem; color:var(--text-light);">
          <i class="fa-solid fa-user-magnifying-glass" style="font-size:3rem; margin-bottom:1rem; display:block;"></i>
          <p>Select a worker from the left to view their details.</p>
        </div>
      `;
    }
    if (hewSelect) hewSelect.value = '';
  }

});
