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
    window.location.href = "Guest.html";
});

const passwordInput = document.getElementById("password-register");
const progressBar = document.getElementById("progress-bar");
const passwordHint = document.getElementById("password-hint");

passwordInput.addEventListener("input", function () {
    const password = passwordInput.value;
    const strength = evaluatePasswordStrength(password);

    // Update progress bar and hint based on password strength
    if (strength === 'bad') {
        progressBar.style.width = '33%';
        progressBar.className = 'progress bad';
        passwordHint.textContent = 'Password strength: Bad';
    } else if (strength === 'normal') {
        progressBar.style.width = '66%';
        progressBar.className = 'progress normal';
        passwordHint.textContent = 'Password strength: Normal';
    } else if (strength === 'good') {
        progressBar.style.width = '100%';
        progressBar.className = 'progress good';
        passwordHint.textContent = 'Password strength: Good';
    }
});

function evaluatePasswordStrength(password) {
    let strength = 'bad';

    // Define password strength criteria
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    const isLongEnough = password.length >= 8;

    // Calculate password strength
    if (isLongEnough && hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChars) {
        strength = 'good';
    } else if (isLongEnough && (hasUpperCase || hasLowerCase) && (hasNumbers || hasSpecialChars)) {
        strength = 'normal';
    }

    return strength;
}

