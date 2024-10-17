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

// Indicate the places of button change
function rotatePositionsCounterClockwise() {
    const temp = positions.register;
    positions.register = positions.guest;
    positions.guest = positions.login;
    positions.login = temp;
    applyPositions();
}

function rotatePositionsClockwise() {
    const temp = positions.login;
    positions.login = positions.guest;
    positions.guest = positions.register;
    positions.register = temp;
    applyPositions();
}

// The Login, Register and Guest Position.
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

// Click register
register.addEventListener("click", () => {
    rotatePositionsClockwise();
    loginForm.classList.remove('active');
    registerForm.classList.add('active');
    register.style.pointerEvents = "none"; // 禁用 register 按钮
    login.style.pointerEvents = "auto"; // 启用 login 按钮
});

// Click login
login.addEventListener("click", () => {
    rotatePositionsCounterClockwise();
    registerForm.classList.remove('active');
    loginForm.classList.add('active');
    login.style.pointerEvents = "none"; // 禁用 login 按钮
    register.style.pointerEvents = "auto"; // 启用 register 按钮
});

// Guest button keeps rotating (optional)
guest.addEventListener("click", () => {
    rotatePositionsClockwise(); // 或者 rotatePositionsCounterClockwise();
    window.location.href = "../PHP/Guest.php";
});

function validateForm() {
    const fullName = document.querySelector('input[name="fullname"]').value;
    const phone = document.querySelector('input[name="phone"]').value;

    // Regex to match only English letters and spaces for full name
    const fullNameRegex = /^[a-zA-Z\s]+$/;
    if (!fullNameRegex.test(fullName)) {
        alert("Full name must contain only letters and spaces.");
        return false;
    }

    // Regex to match only digits for phone number
    const phoneRegex = /^\d+$/;
    if (!phoneRegex.test(phone)) {
        alert("Phone number must contain only digits.");
        return false;
    }

    return true; // Form is valid
}

if (typeof showRegisterForm !== 'undefined' && showRegisterForm) {
    document.getElementById("login-form").classList.remove("active");
    document.getElementById("register-form").classList.add("active");
}

function showForgotPasswordForm() {
    document.getElementById("forgot-password-modal").style.display = "block";
}

function closeModal() {
    document.getElementById("forgot-password-modal").style.display = "none";
}

