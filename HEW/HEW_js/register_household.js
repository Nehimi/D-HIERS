document.addEventListener('DOMContentLoaded', () => {
  const householdForm = document.getElementById('householdForm');
  const messageContainer = document.getElementById('formMessage');

  if (householdForm) {
    householdForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      formData.append('SaveFamily', 'true');
      formData.append('ajax_request', '1');

      const submitBtn = this.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
      submitBtn.disabled = true;

      fetch('register_household.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            if (messageContainer) {
              messageContainer.innerHTML = `<div class="success-message" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">${data.message}</div>`;
            }

            setTimeout(() => {
              // Reload page to show fresh state
              window.location.reload();
            }, 1500);
          } else {
            if (messageContainer) {
              messageContainer.innerHTML = `<div class="error-message" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">${data.message}</div>`;
              messageContainer.scrollIntoView({ behavior: 'smooth' });
            }
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          if (messageContainer) {
            messageContainer.innerHTML = `<div class="error-message" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">An unexpected error occurred.</div>`;
            messageContainer.scrollIntoView({ behavior: 'smooth' });
          }
          submitBtn.innerHTML = originalBtnText;
          submitBtn.disabled = false;
        });
    });
  }
});




