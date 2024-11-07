document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.pickup-form');

    const wasteType = document.getElementById('waste_type');
    const quantity = document.getElementById('quantity');
    const date = document.getElementById('date');
    const time = document.getElementById('time');

    const cancelBtn = document.querySelector('.cancel-btn');
    const myPickupsTab = document.getElementById('myPickupsTab');
    const schedulePickupTab = document.getElementById('schedulePickupTab');
    const formSection = document.querySelector('.form-section');
    const myPickupsSection = document.querySelector('.pickup-list-section');
    const pickupTableBody = document.querySelector('.pickup-table tbody');

    // Set the default date to today's date
    const today = new Date().toISOString().split('T')[0];
    date.setAttribute('min', today);

    // Form validation function
    function validateForm() {
        if (quantity.value <= 0) {
            alert("Please enter a valid quantity greater than 0.");
            return false;
        }
        if (!date.value) {
            alert("Please select a date for pickup.");
            return false;
        }
        if (!time.value) {
            alert("Please select a time for pickup.");
            return false;
        }
        return true;
    }

    // Form submission event listener
    form.addEventListener('submit', (event) => {
        //event.preventDefault(); // Prevent form submission for validation
        if (validateForm()) {
            const formData = {
                wasteType: wasteType.value,
                quantity: quantity.value,
                date: date.value,
                time: time.value,
            };
            savePickupData(formData);

            alert("Pickup scheduled successfully!");
            form.reset();
        }
    });

    // Cancel button event listener
    cancelBtn.addEventListener('click', () => {
        if (confirm("Are you sure you want to cancel? All input data will be lost.")) {
            form.reset(); // Reset form values if the user confirms
        }
    });

    // Tab event listeners
    myPickupsTab.addEventListener('click', () => {
        formSection.style.display = 'none';
        myPickupsSection.style.display = 'block';
        loadPickups();
        schedulePickupTab.classList.remove('active');
        myPickupsTab.classList.add('active');
    });

    schedulePickupTab.addEventListener('click', () => {
        myPickupsSection.style.display = 'none';
        formSection.style.display = 'block';
        myPickupsTab.classList.remove('active');
        schedulePickupTab.classList.add('active');
    });
});