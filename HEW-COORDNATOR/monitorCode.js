
document.addEventListener('DOMContentLoaded', function () {

  // --- Element Selection using IDs ---
  const hewSelect = document.getElementById('hewSelect');
  const activityReviewArea = document.getElementById('activityReviewArea');
  const openActivityBtn = document.getElementById('openActivityBtn');
  const submitReviewBtn = document.getElementById('submitReviewBtn');

  // --- Sample HEW Activity Data (replace with real data from server) ---
  // Using \n for line breaks within the textarea for better readability.
  const sampleHewActivity = {
    "Abeba_kebade_Arade": "HEW: Abeba Kebede (Arade Kable)\nReporting Period: Oct 2023\n\nHousehold Visits: 20\nFP Counseling Sessions: 5\nNew ANC Registrations: 1\nImmunization Rate: 95%\nMalaria Cases Identified: 0\nPending Follow-ups: 2 (vaccine defaulters)\nData Quality: All forms submitted on time, high accuracy. No critical issues.",
    "Mirate_Debabe_Arade": "HEW: Mirate Debabe (Arade Kable)\nReporting Period: Oct 2023\n\nHousehold Visits: 18\nChild Growth Monitoring Sessions: 3\nMalaria Cases Identified: 1\nPending Tasks: 1 community meeting for hygiene education (scheduled).\nData Quality: Minor discrepancies found in 2 forms, needs correction. Overall good.",
    "Melkamu_Godebo_Lich_Amba": "HEW: Melkamu Godebo (Lich Amba Kable)\nReporting Period: Oct 2023\n\nHousehold Visits: 25\nNewborn Follow-ups: 8\nHIV Testing Referrals: 2\nPending Follow-ups: 3 (chronic illness patients).\nData Quality: Excellent, all reports validated and complete. Exemplary performance.",
    "Lommbame_Lamam_Lich_Amba": "HEW: Lommbame Lamam (Lich Amba Kable)\nReporting Period: Oct 2023\n\nHousehold Visits: 15\nHealth Education Talks: 4\nMaternal Health Follow-ups: 1\nPending Tasks: No outstanding pending tasks.\nData Quality: Good, one submission was slightly late last week but data was accurate.",
    "Yonas_Loba_Licha_Amba": "HEW: Yonas Loba (Licha Amba Kable)\nReporting Period: Oct 2023\n\nHousehold Visits: 22\nImmunization Sessions: 6\nNew Referrals: 0\nPending Tasks: 4 household surveys for nutrition (in progress).\nData Quality: Consistently good reporting and data submission.",
    "Adan_Ayela_Arade": "HEW: Adan Ayela (Arade Kable)\nReporting Period: Oct 2023\n\nHousehold Visits: 19\nFamily Planning Distribution: 7\nEPI Sessions: 2\nPending Follow-ups: 1 case of child malnutrition.\nData Quality: Good overall, but minor incomplete fields in two reports that were corrected.",
    "Abeba_kufebo_Arade": "HEW: Abeba Kufebo (Arade Kable)\nReporting Period: Oct 2023\n\nHousehold Visits: 16\nWater Source Inspections: 3\nSuspected TB Referrals: 1\nPending Tasks: 2 home visits for sanitation assessment.\nData Quality: All data submitted, minor errors in one household record require attention."
  };

  // --- Initial State ---
  if (activityReviewArea) activityReviewArea.value = '';
  if (submitReviewBtn) {
      submitReviewBtn.disabled = true; // Disable submit button initially
  }

  // --- Helper function to normalize HEW ID (if needed, though IDs are cleaner) ---
  function getHewIdFromOptionValue(value) {
    return (value || '').trim(); // Values from HTML options are already clean
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

      const activityDetails = sampleHewActivity[selectedHewId];
      let textToShow = '';

      if (activityDetails) {
        textToShow = activityDetails;
      } else {
        textToShow = `No activity details found for HEW ID: ${selectedHewId}. Data may be missing or not yet uploaded.`;
      }

      // Display data in the review textarea
      if (activityReviewArea) {
        activityReviewArea.value = textToShow;
        activityReviewArea.style.borderColor = '#8bc34a'; // Highlight to show it's loaded
        activityReviewArea.focus(); // Give focus for potential editing
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

      // Validate that an HEW is selected and there's some content to review
      if (!selectedHewId) {
        alert('Please select an HEW before submitting a review.');
        return;
      }
      if (!reviewNotes) {
        alert('The review notes are empty. Please open activity first or enter your review comments.');
        return;
      }

      // Prepare payload (data to send to the server)
      const payload = {
        hewId: selectedHewId,
        reviewContent: reviewNotes,
        reviewedBy: 'HEW Coordinator (Current User ID/Name)', // Replace with actual logged-in user info
        reviewDate: new Date().toISOString() // Timestamp of review
      };

      // --- Simulate Submission ---
      // Disable UI elements to prevent double submission and show activity
      submitReviewBtn.disabled = true;
      submitReviewBtn.textContent = 'Submitting...'; // Change button text
      openActivityBtn.disabled = true;
      hewSelect.disabled = true;
      if (activityReviewArea) activityReviewArea.disabled = true; // Disable textarea during submission

      console.log('Simulating HEW Activity Review submission with payload:', payload);
 // Simulating a network delay for the demo
      setTimeout(() => {
        alert(`Review for ${selectedHewId} submitted successfully (simulated)!`);
        resetForm(); // Reset form after simulated success
      }, 1500); // 1.5 seconds delay to mimic network latency

    });
  }

  // --- UX Improvement: Clear/Disable on HEW Selection Change ---
  // If user changes HEW selection, the review area should be cleared, and submit disabled
  if (hewSelect) {
    hewSelect.addEventListener('change', function () {
      if (activityReviewArea) {
        activityReviewArea.value = '';
        activityReviewArea.style.borderColor = '#ccc'; // Reset border color
      }
      if (submitReviewBtn) {
        submitReviewBtn.disabled = true;
        submitReviewBtn.textContent = 'Submit Review'; // Reset button text
      }
    });
  }

  // --- Helper Function to Reset Form State ---
  function resetForm(isError = false) {
    if (submitReviewBtn) {
      // If there was an error, re-enable button to allow retry.
      // Otherwise, keep it disabled if no HEW is selected (after success).
      submitReviewBtn.disabled = !isError && !hewSelect.value;
      submitReviewBtn.textContent = 'Submit Review'; // Reset button text
    }
    if (openActivityBtn) openActivityBtn.disabled = false;
    if (hewSelect) hewSelect.disabled = false;
    if (activityReviewArea) {
      activityReviewArea.disabled = false; // Re-enable textarea
      if (!isError) { // Only clear on successful submission
          activityReviewArea.value = '';
          activityReviewArea.style.borderColor = '#ccc';
      }
    }
  }

});
