// JavaScript for saving and redirecting --
    document.getElementById('householdForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const data = {
        kebele: document.getElementById('kebele').value,
        id: document.getElementById('householdId').value,
        name: document.getElementById('memberName').value,
        age: document.getElementById('age').value,
        sex: document.getElementById('sex').value,
      };

      console.log('Household member saved:', data);
      alert('✅ Family member added successfully! Redirecting to Dashboard...');

      // Wait 2 seconds, then go to dashboard
      setTimeout(() => {
        window.location.href = 'hew_dashboard.html'; }, 2000);

     
      // Clear form
      this.reset();
    });




 