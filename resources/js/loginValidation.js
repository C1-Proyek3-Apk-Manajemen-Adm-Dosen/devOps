// resources/js/loginValidation.js
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("loginForm");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");

    form.addEventListener("submit", (e) => {
        let isValid = true;

        // Reset state
        emailError.classList.add("hidden");
        passwordError.classList.add("hidden");
        emailInput.classList.remove("border-red-500");
        passwordInput.classList.remove("border-red-500");

        const originalEmail = emailInput.value;

        // Deteksi spasi boundary tanpa mengubah nilai (biarkan user melihat kesalahan)
        if (originalEmail !== originalEmail.trim()) {
            emailError.textContent = "Email tidak boleh diawali atau diakhiri spasi.";
            emailError.classList.remove("hidden");
            emailInput.classList.add("border-red-500");
            e.preventDefault();
            return;
        }

        // Validasi email
        if (emailInput.value === "") {
            emailError.textContent = "Email wajib diisi.";
            emailError.classList.remove("hidden");
            emailInput.classList.add("border-red-500");
            isValid = false;
        } else if (!emailInput.value.includes("@")) {
            emailError.textContent = "Format email tidak valid.";
            emailError.classList.remove("hidden");
            emailInput.classList.add("border-red-500");
            isValid = false;
        } else if (emailInput.value.length > 200) {
            emailError.textContent = "Email tidak valid / terlalu panjang.";
            emailError.classList.remove("hidden");
            emailInput.classList.add("border-red-500");
            isValid = false;
        }

        // Validasi password
        if (passwordInput.value.trim() === "") {
            passwordError.textContent = "Password tidak boleh kosong.";
            passwordError.classList.remove("hidden");
            passwordInput.classList.add("border-red-500");
            isValid = false;
        } else if (passwordInput.value.length < 6) {
            passwordError.textContent = "Password terlalu pendek (min 6 karakter).";
            passwordError.classList.remove("hidden");
            passwordInput.classList.add("border-red-500");
            isValid = false;
        }

        if (!isValid) e.preventDefault();
    });
});
