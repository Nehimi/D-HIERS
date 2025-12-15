
document.addEventListener('DOMContentLoaded', function () {
  const forwardDataTypeSelect = document.getElementById('forwardDataTypeSelect');
  const forwardNotes = document.getElementById('forwardNotes');
  const forwardBtn = document.getElementById('forwardBtn');

  // --- Initial State ---
  if (forwardBtn) {
      forwardBtn.disabled = true; // Disable the button initially
  }

 if (forwardDataTypeSelect) {
    forwardDataTypeSelect.addEventListener('change', function () {
      if (forwardBtn) {
        // Enable button if a value (other than the default empty option) is selected
        forwardBtn.disabled = !this.value;
      }
    });
  }

  // --- Event Listener for "Forward Data" Button ---
  if (forwardBtn) {
    forwardBtn.addEventListener('click', function () {
      const selectedDataType = forwardDataTypeSelect ? forwardDataTypeSelect.value.trim() : '';
      const notesForLinkage = forwardNotes ? forwardNotes.value.trim() : '';

      // Basic validation
      if (!selectedDataType) {
        alert('Please choose which data type to forward.');
        return;
      }       

      // Prepare payload (data to send to the server)
      const payload = {
        dataType: selectedDataType,
        notes: notesForLinkage, // This is optional
        forwardedBy: 'HEW Coordinator (Current User ID/Name)', // Replace with actual logged-in user info
        forwardedAt: new Date().toISOString() // Timestamp of forwarding
      };

  
      forwardBtn.disabled = true;
      forwardBtn.textContent = 'Forwarding...'; // Change button text
      forwardDataTypeSelect.disabled = true;
      if (forwardNotes) forwardNotes.disabled = true;

      console.log('Simulating forwarding data to Linkage Focal Person with payload:', payload);
   // Simulating a network delay for the demo
      setTimeout(() => {
        alert(`"${selectedDataType}" data (with notes) forwarded to Linkage Focal Person (simulated).`);
        resetForm(); // Reset form after simulated success
      }, 1500); // 1.5 seconds delay to mimic network latency

    });
  }

  // --- Helper Function to Reset Form State ---
  function resetForm(isError = false) {
    if (forwardBtn) {
      // If there was an error, re-enable button to allow retry.
      // Otherwise, keep it disabled if no data type is selected (after success).
      forwardBtn.disabled = !isError && !forwardDataTypeSelect.value;
      forwardBtn.textContent = 'Forward Data'; // Reset button text
    }
    if (forwardDataTypeSelect) forwardDataTypeSelect.disabled = false;
    if (forwardNotes) {
      forwardNotes.disabled = false;
      if (!isError) forwardNotes.value = ''; // Clear notes on successful submission
    }
  }

});
