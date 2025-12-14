document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');
  const messageContainer = document.getElementById('loginMessage');

  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      // Simple loading state
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Signing In...';
      submitBtn.disabled = true;

      fetch('login.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            messageContainer.innerHTML = `<div class="success-message" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">Login successful! Redirecting...</div>`;
            setTimeout(() => {
              window.location.href = data.redirect;
            }, 1000);
          } else {
            messageContainer.innerHTML = `<div class="error-message" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">${data.message}</div>`;
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          messageContainer.innerHTML = `<div class="error-message" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">An unexpected error occurred. Please try again.</div>`;
          submitBtn.innerHTML = originalBtnText;
          submitBtn.disabled = false;
        });
    });
  }
});

function openPopup() {
  document.getElementById("popup").style.display = "flex";
}

function closePopup() {
  document.getElementById("popup").style.display = "none";
}