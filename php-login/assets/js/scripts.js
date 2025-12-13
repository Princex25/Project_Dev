document.addEventListener("DOMContentLoaded", () => {
  const passwordInput = document.querySelector("#password");
  const toggleButton = document.querySelector("#togglePassword");
  const toggleIcon = document.querySelector("#togglePasswordIcon");
  const loginForm = document.querySelector("#loginForm");
  const submitButton = document.querySelector("#submitButton");

  if (toggleButton && passwordInput && toggleIcon) {
    toggleButton.addEventListener("click", (event) => {
      event.preventDefault();
      const isHidden = passwordInput.type === "password";
      passwordInput.type = isHidden ? "text" : "password";
      toggleIcon.classList.toggle("bi-eye");
      toggleIcon.classList.toggle("bi-eye-slash");
      toggleButton.setAttribute("aria-label", isHidden ? "Masquer le mot de passe" : "Afficher le mot de passe");
    });
  }

  if (loginForm && submitButton) {
    loginForm.addEventListener("submit", () => {
      submitButton.disabled = true;
      const originalLabel = submitButton.dataset.label || submitButton.textContent;
      submitButton.dataset.label = originalLabel;
      submitButton.textContent = "Chargement...";
    });
  }
});
