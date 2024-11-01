const register = document.getElementById("register");
    const login = document.getElementById("login");
    const guest = document.getElementById("guest");

    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");
    login.style.pointerEvents = "none";

    const positions = {
        register: { top: '-35%', left: '49%', rotate: 57 },
        login: { top: '65%', left: '20%', rotate: 0 },
        guest: { top: '51%', left: '12%', rotate: 301 }
    };

    // Rotates buttons clockwise
    function rotatePositionsClockwise() {
        const temp = positions.login;
        positions.login = positions.guest;
        positions.guest = positions.register;
        positions.register = temp;
        applyPositions();
    }

    // Rotates buttons counterclockwise
    function rotatePositionsCounterClockwise() {
        const temp = positions.register;
        positions.register = positions.guest;
        positions.guest = positions.login;
        positions.login = temp;
        applyPositions();
    }

    // Apply position changes
    function applyPositions() {
        register.style.top = positions.register.top;
        register.style.left = positions.register.left;
        register.style.transform = `rotate(${positions.register.rotate}deg)`;

        login.style.top = positions.login.top;
        login.style.left = positions.login.left;
        login.style.transform = `rotate(${positions.login.rotate}deg)`;

        guest.style.top = positions.guest.top;
        guest.style.left = positions.guest.left;
        guest.style.transform = `rotate(${positions.guest.rotate}deg)`;
    }

    // Click register button
    register.addEventListener("click", () => {
        rotatePositionsClockwise();
        loginForm.classList.remove('active');
        registerForm.classList.add('active');
        register.style.pointerEvents = "none";
        login.style.pointerEvents = "auto";
    });

    // Click login button
    login.addEventListener("click", () => {
        rotatePositionsCounterClockwise();
        registerForm.classList.remove('active');
        loginForm.classList.add('active');
        login.style.pointerEvents = "none";
        register.style.pointerEvents = "auto";
    });

    // Guest button keeps rotating (optional)
    guest.addEventListener("click", () => {
        rotatePositionsClockwise();
        window.location.href = "../PHP/Guest.php";
    });

    // Function to handle role selection (Admin or User)
    function selectRole(role) {
        const roleInput = document.getElementById('roleInput');
        const adminBtn = document.getElementById('adminBtn');
        const userBtn = document.getElementById('userBtn');
        const communityField = document.getElementById('communityField');
        const communitySelect = document.getElementById('communitySelect');

        if (role === 'admin') {
            roleInput.value = 'admin';
            adminBtn.style.backgroundColor = '#007bff';
            adminBtn.style.color = '#fff';
            userBtn.style.backgroundColor = '#f8f9fa';
            userBtn.style.color = '#000';

            communityField.style.display = 'block';
            communitySelect.style.display = 'none';
            communityField.required = true;
            communitySelect.required = false;
        } else {
            roleInput.value = 'user';
            userBtn.style.backgroundColor = '#007bff';
            userBtn.style.color = '#fff';
            adminBtn.style.backgroundColor = '#f8f9fa';
            adminBtn.style.color = '#000';

            communityField.style.display = 'none';
            communitySelect.style.display = 'block';
            communityField.required = false;
            communitySelect.required = true;
        }
    }

    // Form validation
    function validateForm() {
        const fullName = document.querySelector('input[name="fullname"]').value;
        const phone = document.querySelector('input[name="phone"]').value;

        const fullNameRegex = /^[a-zA-Z\s]+$/;
        if (!fullNameRegex.test(fullName)) {
            alert("Full name must contain only letters and spaces.");
            return false;
        }

        const phoneRegex = /^\d+$/;
        if (!phoneRegex.test(phone)) {
            alert("Phone number must contain only digits.");
            return false;
        }

        return true;
    }

    // Initialize on load
    if (typeof showRegisterForm !== 'undefined' && showRegisterForm) {
        loginForm.classList.remove("active");
        registerForm.classList.add("active");
    }