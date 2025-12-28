
document.addEventListener('DOMContentLoaded', function () {
    const forwardDataTypeSelect = document.getElementById('forwardDataTypeSelect');
    const forwardNotes = document.getElementById('forwardNotes');
    const forwardBtn = document.getElementById('forwardBtn');

    // --- Initial State ---
    if (forwardBtn) {
        forwardBtn.disabled = true;
    }

    function loadForwardPreview() {
        const selectedDataType = forwardDataTypeSelect.value;
        if (!selectedDataType) {
            if (forwardBtn) forwardBtn.disabled = true;
            return;
        }

        // Efficiently check for validated data using count_only mode
        fetch(`../api/hew_coordinator.php?action=review&kebele=all&dataType=${encodeURIComponent(selectedDataType)}&status=Validated&count_only=1`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const count = data.data.total || 0;
                    if (count > 0) {
                        forwardBtn.disabled = false;
                        console.log(`Ready to forward ${count} records.`);
                    } else {
                        forwardBtn.disabled = true;
                        alert("No validated data found for this category. Please validate data in the previous step.");
                    }
                }
            });
    }

    if (forwardDataTypeSelect) {
        forwardDataTypeSelect.addEventListener('change', loadForwardPreview);
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
