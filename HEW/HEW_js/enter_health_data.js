document.addEventListener('DOMContentLoaded', () => {
  const checkBtn = document.getElementById('checkIdBtn');
  const idInput = document.getElementById('householdId');
  const displayId = document.getElementById('displayId');
  const form = document.getElementById('healthDataForm');
  const checkMessage = document.getElementById('checkMessage');
  const formMessage = document.getElementById('formMessage');
  const idCheckSection = document.getElementById('id-check-section');

  // Mock data for demo purposes
  const registeredHouseholds = ['HH-001', 'HH-002', 'HH-003', 'HH-004', 'HH-005'];

  // Check if ID exists
  if (checkBtn) {
    checkBtn.addEventListener('click', () => {
      const enteredId = idInput.value.trim();
      if (!enteredId) {
        checkMessage.innerHTML = `<div class="error-message" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 4px; margin-top: 10px;">Please enter a Household ID.</div>`;
        return;
      }

      if (registeredHouseholds.includes(enteredId)) {
        checkMessage.innerHTML = `<div class="success-message" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-top: 10px;">✅ Household found! Proceeding...</div>`;

        setTimeout(() => {
          displayId.textContent = enteredId;
          form.classList.remove('hidden');
          idCheckSection.classList.add('hidden'); // Hide the check section
          checkMessage.innerHTML = ''; // Clear message
        }, 1000);
      } else {
        checkMessage.innerHTML = `<div class="error-message" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 4px; margin-top: 10px;">❌ Household not found! Please register the household first.</div>`;
      }
    });
  }

  // Save health data
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const data = {
        id: displayId.textContent,
        serviceType: document.getElementById('serviceType').value,
        totalServed: document.getElementById('totalServed').value,
        notes: document.getElementById('notes').value
      };

      console.log('Health data saved:', data);

      formMessage.innerHTML = `<div class="success-message" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">✅ Health data saved successfully! Redirecting...</div>`;

      setTimeout(() => window.location.href = 'hew_dashboard.php', 1500);
      form.reset();
    });
  }
});
