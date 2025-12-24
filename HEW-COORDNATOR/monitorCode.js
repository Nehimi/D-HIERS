
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

      // Basic validation
      if (!selectedHewId) {
        alert('Please select an HEW to open their activity details.');
        return;
      }

      const activityDetails = hewData[selectedHewId];
      let textToShow = '';

      if (activityDetails) {
        // Format object into readable text
        textToShow = `HEW Name: ${activityDetails.name}\nKebele: ${activityDetails.kebele}\nStatus: ${activityDetails.status}\n\nMetrics:\n- Total Visits: ${activityDetails.visits}\n`;
      } else {
        textToShow = `No live activity details found for HEW ID: ${selectedHewId}. Data may be missing or not yet uploaded.`;
      }

      // Display data in the review textarea
      if (activityReviewArea) {
        activityReviewArea.value = textToShow;
        activityReviewArea.style.borderColor = '#0f766e'; // Highlight to show it's loaded
        activityReviewArea.focus();
      }

      // Enable the submit button once data is loaded
      if (submitReviewBtn) {
        submitReviewBtn.disabled = false;
      }
    });
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
    if (submitReviewBtn) submitReviewBtn.disabled = true;
    if (activityReviewArea) {
      activityReviewArea.value = '';
      activityReviewArea.style.borderColor = '#ccc';
    }
    if (hewSelect) hewSelect.value = '';
  }

});
