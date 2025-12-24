
document.addEventListener('DOMContentLoaded', function () {
    const forwardDataTypeSelect = document.getElementById('forwardDataTypeSelect');
    const forwardNotes = document.getElementById('forwardNotes');
    const forwardBtn = document.getElementById('forwardBtn');

    // --- Initial State ---
    if (forwardBtn) {
        forwardBtn.disabled = true;
    }

    if (forwardDataTypeSelect) {
        forwardDataTypeSelect.addEventListener('change', function () {
            if (forwardBtn) {
                forwardBtn.disabled = !this.value;
            }
        });
    }

    // --- Event Listener for "Forward Data" Button ---
    if (forwardBtn) {
        forwardBtn.addEventListener('click', function () {
            const selectedDataType = forwardDataTypeSelect ? forwardDataTypeSelect.value.trim() : '';
            const notesForLinkage = forwardNotes ? forwardNotes.value.trim() : '';

            if (!selectedDataType) {
                alert('Please choose which data type to forward.');
                return;
            }

            const payload = {
                dataType: selectedDataType,
                notes: notesForLinkage
            };

            forwardBtn.disabled = true;
            forwardBtn.textContent = 'Forwarding...';

            // Sending to Real API
            fetch('../api/hew_coordinator.php?action=forward', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Success! " + data.message);
                        resetForm();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Network error occurred.");
                })
                .finally(() => {
                    forwardBtn.textContent = 'Forward Data';
                    // Keep disabled until selection changes or handled in reset
                });
        });
    }

    function resetForm() {
        if (forwardBtn) {
            forwardBtn.disabled = true;
        }
        if (forwardDataTypeSelect) {
            forwardDataTypeSelect.value = "";
            forwardDataTypeSelect.disabled = false;
        }
        if (forwardNotes) forwardNotes.value = '';
    }

});
