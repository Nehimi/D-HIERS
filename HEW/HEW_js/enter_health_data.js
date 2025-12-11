
    // Check if ID exists
    checkBtn.addEventListener('click', () => {
      const enteredId = idInput.value.trim();
      if (registeredHouseholds.includes(enteredId)) {
        alert('✅ Household found! You can now enter health data.');
        displayId.textContent = enteredId;
        form.classList.remove('hidden');
        document.getElementById('id-check-section').classList.add('hidden');
      } else {
        alert('❌ Household not found! Please register the household first.');
      }
    });

    // Save health data
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const data = {
        id: displayId.textContent,
        serviceType: document.getElementById('serviceType').value,
        notes: document.getElementById('notes').value
      };

      console.log('Health data saved:', data);
      alert('✅ Health data saved successfully! Redirecting to Dashboard...');
      setTimeout(() => window.location.href = 'hew_dashboard.html', 2000);
      form.reset();
    });
