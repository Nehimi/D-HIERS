// --- Edit Submitted Data Script ---

// Elements
const checkIdBtn = document.getElementById("checkIdBtn");
const householdIdInput = document.getElementById("householdId");
const editForm = document.getElementById("editDataForm");
const displayId = document.getElementById("displayId");
const emptyState = document.querySelector(".empty-state-icon");

// Form fields
const memberName = document.getElementById("memberName");
const age = document.getElementById("age");
const sex = document.getElementById("sex");
const kebele = document.getElementById("kebele");

// --- Step 1: Fetch Data ---
checkIdBtn.addEventListener("click", function () {
  const id = householdIdInput.value.trim();

  if (!id) {
    alert("⚠️ Please enter a valid Household ID!");
    return;
  }

  // Fetch from PHP backend
  fetch(`fetch_household.php?householdId=${encodeURIComponent(id)}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const record = data.data;
        displayId.textContent = record.householdId;

        // Fill form with DB data
        memberName.value = record.memberName;
        age.value = record.age;
        sex.value = record.sex;
        kebele.value = record.kebele; // Ensure kebele select has this value

        // Show edit form & hide empty state
        editForm.classList.remove("hidden");
        if (emptyState) emptyState.style.display = "none";
      } else {
        alert("❌ " + data.message);
        editForm.classList.add("hidden");
        if (emptyState) emptyState.style.display = "block";
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert("❌ Error connecting to server.");
    });
});

// --- Step 2: Save Edited Data ---
editForm.addEventListener("submit", function (e) {
  e.preventDefault();

  const id = displayId.textContent;
  const updatedData = {
    householdId: id,
    memberName: memberName.value.trim(),
    age: parseInt(age.value),
    sex: sex.value,
    kebele: kebele.value
  };

  fetch('update_household.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(updatedData)
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("✅ Data updated successfully!");
        // Optional: kept on page to allow further edits
      } else {
        alert("❌ Error updating: " + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert("❌ Error connecting to server.");
    });
});
