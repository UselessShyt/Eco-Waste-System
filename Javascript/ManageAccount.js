const register = document.getElementById("register");
const login = document.getElementById("login");
const guest = document.getElementById("guest");

const loginForm = document.getElementById("login-form");
const registerForm = document.getElementById("register-form");
login.style.pointerEvents = "none";

const positions = {
    register: { top: '9%', left: '52%', rotate: 60 },
    login: { top: '65%', left: '20%', rotate: 0 },
    guest: { top: '52.6%', left: '13%', rotate: -60 }
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
});