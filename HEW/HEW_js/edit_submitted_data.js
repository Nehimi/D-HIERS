// --- Edit Submitted Data Script ---

// Simulated database (can later connect to real backend)
const fakeDatabase = {
  "HH-001": {
    serviceType: "maternal_health",
    totalServed: 5,
    notes: "Follow-up maternal care visits completed."
  },
  "HH-002": {
    serviceType: "child_health",
    totalServed: 3,
    notes: "Child nutrition counseling provided."
  },
  "HH-003": {
    serviceType: "immunization",
    totalServed: 10,
    notes: "Polio and measles immunization done."
  }
};

// Elements
const checkIdBtn = document.getElementById("checkIdBtn");
const householdIdInput = document.getElementById("householdId");
const editForm = document.getElementById("editDataForm");
const displayId = document.getElementById("displayId");

// Form fields
const serviceType = document.getElementById("serviceType");
const totalServed = document.getElementById("totalServed");
const notes = document.getElementById("notes");

// --- Step 1: Fetch Data ---
checkIdBtn.addEventListener("click", function () {
  const id = householdIdInput.value.trim();

  if (!id) {
    alert("⚠️ Please enter a valid Household ID!");
    return;
  }

  if (fakeDatabase[id]) {
    const data = fakeDatabase[id];
    displayId.textContent = id;

    // Fill form with existing data
    serviceType.value = data.serviceType;
    totalServed.value = data.totalServed;
    notes.value = data.notes;

    // Show edit form
    editForm.classList.remove("hidden");
  } else {
    alert("❌ No record found for this Household ID!");
    editForm.classList.add("hidden");
  }
});

// --- Step 2: Save Edited Data ---
editForm.addEventListener("submit", function (e) {
  e.preventDefault();

  const id = displayId.textContent;
  const updatedData = {
    serviceType: serviceType.value,
    totalServed: parseInt(totalServed.value),
    notes: notes.value.trim()
  };

  // Simulate updating in "database"
  fakeDatabase[id] = updatedData;

  console.log("✅ Updated record:", fakeDatabase[id]);
  alert("✅ Data updated successfully! Redirecting to Dashboard...");

  // Redirect to dashboard after 2 seconds
  setTimeout(() => {
    window.location.href = "hew_dashboard.html";
  }, 2000);

  editForm.reset();
  editForm.classList.add("hidden");
  householdIdInput.value = "";
});
