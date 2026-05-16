const loginForm = document.getElementById("loginForm");
const usernameInput = document.getElementById("username");
const passwordInput = document.getElementById("password");
const errorAlert = document.getElementById("error-alert");

if (errorType) {
    if (errorType === "empty_user") errorAlert.textContent = "Please enter your ID Number or Email Address.";
    if (errorType === "empty_pass") errorAlert.textContent = "Mobile number password required for Students/Faculty/Staff.";
    if (errorType === "wrong") errorAlert.textContent = "Invalid credentials. Account records not found.";
    errorAlert.style.display = "block";
}

loginForm.addEventListener("submit", (e) => {
    errorAlert.style.display = "none";
    const userValue = usernameInput.value.trim();
    const passValue = passwordInput.value.trim();

    if (userValue === "") {
        e.preventDefault(); 
        errorAlert.textContent = "Please enter your ID Number or Email.";
        errorAlert.style.display = "block";
        return;
    }

    if (isNaN(userValue) && passValue === "") {
        e.preventDefault(); 
        errorAlert.textContent = "Please enter your password.";
        errorAlert.style.display = "block";
    }
});