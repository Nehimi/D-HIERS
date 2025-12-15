
document.addEventListener('DOMContentLoaded', function () {
  // Recommended element IDs:
  // #kebeleSelect, #dataSelect, #openBtn, #reviewArea, #submitBtn

  const kebeleSelect = document.getElementById('kebeleSelect');
  const dataSelect = document.getElementById('dataSelect');
  const openBtn = document.getElementById('openBtn');
  const reviewArea = document.getElementById('reviewArea');
  const submitBtn = document.getElementById('submitBtn');

  // Sample data (for demo). Replace with real data from server.
  const sampleData = {
    "Lich-amba": {
      "household data": "Households: 1,254 total\nAverage members/household: 5.1\nVisited last month: 312",
      "HIV Addis patients Data": "HIV patients on ART: 42\nNew cases (30d): 2\nAdherence issues flagged: 1",
      "Numbers of new born childern": "Newborns registered (30d): 18\nLow-birth-weight: 2\nHome visits done: 15",
      "data of family plan": "FP clients this quarter: 120\nNew acceptors: 34\nMethod mix: Pills 45%, Injectables 40%, Implants 15%",
      "Data of sanitation": "Households with latrine: 980 (78%)\nOpen defecation observed: 12 sites\nHygiene training sessions: 3"
    },
    "Arade": {
      "household data": "Households: 890 total\nAverage members/household: 4.8\nVisited last month: 210",
      "HIV Addis patients Data": "HIV patients on ART: 30\nNew cases (30d): 0\nAdherence issues flagged: 0",
      "Numbers of new born childern": "Newborns registered (30d): 12\nLow-birth-weight: 1\nHome visits done: 12",
      "data of family plan": "FP clients this quarter: 95\nNew acceptors: 21\nMethod mix: Pills 50%, Injectables 35%, Implants 15%",
      "Data of sanitation": "Households with latrine: 710 (80%)\nOpen defecation observed: 7 sites\nHygiene training sessions: 2"
    },
    "Lereba": {
      "household data": "Households: 670 total\nAverage members/household: 5.3\nVisited last month: 150",
      "HIV Addis patients Data": "HIV patients on ART: 18\nNew cases (30d): 1\nAdherence issues flagged: 1",
      "Numbers of new born childern": "Newborns registered (30d): 9\nLow-birth-weight: 0\nHome visits done: 9",
      "data of family plan": "FP clients this quarter: 60\nNew acceptors: 10\nMethod mix: Pills 55%, Injectables 30%, Implants 15%",
      "Data of sanitation": "Households with latrine: 480 (72%)\nOpen defecation observed: 5 sites\nHygiene training sessions: 1"
    }
  };

  // Initial state
  if (reviewArea) reviewArea.value = '';
  if (submitBtn) submitBtn.disabled = true;

  function normalizeKey(s) {
    return (s || '').trim();
  }

  // "Open" button loads sample data into the review area
  if (openBtn) {
    openBtn.addEventListener('click', function () {
      const kebele = kebeleSelect ? kebeleSelect.value : '';
      const dataType = dataSelect ? dataSelect.value : '';

      if (!kebele) {
        alert('Please choose a kebele first.');
        return;
      }
      if (!dataType) {
        alert('Please choose which data to review.');
        return;
      }

      const kebeleData = sampleData[kebele];
      let textToShow = '';

      if (kebeleData && kebeleData[normalizeKey(dataType)]) {
        textToShow = kebeleData[normalizeKey(dataType)];
      } else {
        textToShow = 'No records found for the selected kebele / data type. Please request sync or ask the HEW to re-submit.';
      }

      if (reviewArea) {
        reviewArea.value = textToShow;
        reviewArea.style.borderColor = '#8bc34a';
        reviewArea.focus();
      }

      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.style.opacity = 1;
      }
    });
  }

  // "Submit" button simulates sending review to the server
  if (submitBtn) {
    submitBtn.addEventListener('click', function () {
      const kebele = kebeleSelect ? kebeleSelect.value : '';
      const dataType = dataSelect ? dataSelect.value : '';
      const reviewText = reviewArea ? reviewArea.value.trim() : '';

      if (!kebele || !dataType) {
        alert('Kebele and data type are required before submitting.');
        return;
      }
      if (!reviewText) {
        alert('Nothing to submit. Please open and review the data first.');
        return;
      }

      const payload = {
        kebele,
        dataType,
        reviewText,
        reviewedBy: 'Dr. Admin', // get dynamically from user profile if possible
        reviewedAt: new Date().toISOString()
      };

      // Disable UI while "sending"
      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting...';


      // Simulated response for demo
      setTimeout(() => {
        console.log('Simulated submit payload:', payload);
        alert('Review submitted successfully (simulated).');
        submitBtn.textContent = 'Submit';
        submitBtn.disabled = false;
        if (reviewArea) reviewArea.style.borderColor = '#ccc';
      }, 800);
    });
  }

  // When selects change, clear review area and disable submit
  [kebeleSelect, dataSelect].forEach(sel => {
    if (!sel) return;
    sel.addEventListener('change', function () {
      if (reviewArea) reviewArea.value = '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.style.opacity = 0.6;
      }
    });
  });
});
