document.addEventListener("DOMContentLoaded", function () {
  const passwordInput = document.getElementById("password");
  const toggleButton = document.getElementById("togglePasswordVisibility");

  if (passwordInput && toggleButton) {
    toggleButton.addEventListener("click", function () {
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      this.textContent = type === "password" ? "Show" : "Hide";

      this.setAttribute(
        "aria-label",
        type === "password" ? "Show password" : "Hide password"
      );
    });
  } else {
    if (!passwordInput)
      console.error("Password input field with id='password' not found.");
    if (!toggleButton)
      console.error(
        "Toggle button with id='togglePasswordVisibility' not found."
      );
  }

  const confirmPasswordInput = document.getElementById("confirm_password");
  const confirmToggleButton = document.getElementById(
    "ctogglePasswordVisibility"
  );

  if (confirmPasswordInput && confirmToggleButton) {
    confirmToggleButton.addEventListener("click", function () {
      const type =
        confirmPasswordInput.getAttribute("type") === "password"
          ? "text"
          : "password";
      confirmPasswordInput.setAttribute("type", type);

      this.textContent = type === "password" ? "Show" : "Hide";

      this.setAttribute(
        "aria-label",
        type === "password" ? "Show password" : "Hide password"
      );
    });
  } else {
    if (!confirmPasswordInput)
      console.error("Password input field with id='password' not found.");
    if (!confirmToggleButton)
      console.error(
        "Toggle button with id='togglePasswordVisibility' not found."
      );
  }
});
