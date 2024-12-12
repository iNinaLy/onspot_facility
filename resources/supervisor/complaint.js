import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'toastr/build/toastr.min.css';
import './complaint.css'; 

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('cleanerModal');
    const openModalBtn = document.getElementById('selectCleanersBtn');
    const closeModalElements = document.querySelectorAll('.close, #cancelBtn');
    const saveBtn = document.getElementById('saveBtn');
    const assignBtn = document.getElementById('assignBtn');
    const selectedCleanersDiv = document.getElementById('selectedCleaners');
    const cleanerCheckboxes = document.querySelectorAll('.cleaner-checkbox');
    const noOfCleanersSelect = document.getElementById('no_of_cleaners');
    const maxCleanersDisplay = document.getElementById('maxCleanersDisplay');

    let maxCleaners = parseInt(noOfCleanersSelect.value);

    // Update maxCleaners when number of cleaners changes
    noOfCleanersSelect.addEventListener('change', function() {
      maxCleaners = parseInt(noOfCleanersSelect.value);
      maxCleanersDisplay.textContent = maxCleaners;

      // Reset checkboxes
      cleanerCheckboxes.forEach(function(checkbox) {
        checkbox.checked = false;
        checkbox.disabled = false;
        checkbox.parentElement.querySelector('.check-box').style.borderColor = '#000';
      });
    });

    // Function to update checkbox states
    function updateCheckboxStates() {
      const selectedCount = Array.from(cleanerCheckboxes).filter(cb => cb.checked).length;
      if (selectedCount >= maxCleaners) {
        cleanerCheckboxes.forEach(function(checkbox) {
          if (!checkbox.checked) {
            checkbox.disabled = true;
            checkbox.parentElement.querySelector('.check-box').style.borderColor = '#ccc';
          }
        });
      } else {
        cleanerCheckboxes.forEach(function(checkbox) {
          checkbox.disabled = false;
          checkbox.parentElement.querySelector('.check-box').style.borderColor = '#000';
        });
      }
    }

    // Add event listeners to checkboxes
    cleanerCheckboxes.forEach(function(checkbox) {
      checkbox.addEventListener('change', function() {
        updateCheckboxStates();
      });
    });

    // Open Modal
    openModalBtn.addEventListener('click', function() {
      modal.style.display = 'block';

      // Reset checkboxes
      cleanerCheckboxes.forEach(function(checkbox) {
        checkbox.checked = false;
        checkbox.disabled = false;
        checkbox.parentElement.querySelector('.check-box').style.borderColor = '#000';
      });

      // Update maxCleaners in case it changed
      maxCleaners = parseInt(noOfCleanersSelect.value);
      maxCleanersDisplay.textContent = maxCleaners;

      updateCheckboxStates();
    });

    // Close Modal
    closeModalElements.forEach(function(element) {
      element.addEventListener('click', function() {
        modal.style.display = 'none';
      });
    });

    // When the user clicks anywhere outside of the modal, close it
    window.addEventListener('click', function(event) {
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    });

    // Save Selection
    saveBtn.addEventListener('click', function() {
      const selectedCleaners = Array.from(cleanerCheckboxes)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => {
          return {
            id: checkbox.value,
            name: checkbox.parentElement.querySelector('.cleaner-name').textContent.trim()
          };
        });

      if (selectedCleaners.length !== maxCleaners) {
        alert(`Please select exactly ${maxCleaners} cleaner(s).`);
        return;
      }

      // Clear previous selections
      selectedCleanersDiv.innerHTML = '';

      // Append selected cleaners to the form
      selectedCleaners.forEach(function(cleaner) {
        const cleanerInput = document.createElement('input');
        cleanerInput.type = 'hidden';
        cleanerInput.name = 'cleaners[]'; // Adjusted to match controller
        cleanerInput.value = cleaner.id;
        selectedCleanersDiv.appendChild(cleanerInput);

        const cleanerLabel = document.createElement('span');
        cleanerLabel.classList.add('selected-cleaner-name');
        cleanerLabel.textContent = cleaner.name;
        selectedCleanersDiv.appendChild(cleanerLabel);
      });

      assignBtn.style.display = 'inline-block';

      // Close modal
      modal.style.display = 'none';
    });

    // Initialize max cleaners display
    maxCleanersDisplay.textContent = maxCleaners;
  });