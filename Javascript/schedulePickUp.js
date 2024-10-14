document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.pickup-form');
    const wasteType = document.getElementById('waste-type');
    const quantity = document.getElementById('quantity');
    const date = document.getElementById('date');
    const time = document.getElementById('time');
    const cancelBtn = document.querySelector('.cancel-btn');

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
        event.preventDefault(); // Prevent form submission for validation

        if (validateForm()) {
            // If validation passes, you can proceed with form submission or processing
            const formData = {
                wasteType: wasteType.value,
                quantity: quantity.value,
                date: date.value,
                time: time.value,
            };
            console.log("Form Data:", formData);

            alert("Pickup scheduled successfully!");
            // Optionally, reset the form
            form.reset();
        }
    });

    // Cancel button event listener
    cancelBtn.addEventListener('click', () => {
        if (confirm("Are you sure you want to cancel? All input data will be lost.")) {
            form.reset(); // Reset form values if the user confirms
        }
    });
});