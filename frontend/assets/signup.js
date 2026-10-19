const signupForm = document.getElementById("signup-form");

if (signupForm) {
    const nameInput = document.getElementById("signup-name");
    const phoneInput = document.getElementById("signup-phone");
    const emailInput = document.getElementById("signup-email");
    const passwordInput = document.getElementById("signup-password");
    const captchaInput = document.getElementById("captcha-answer");
    const captchaQuestion = document.getElementById("captcha-question");
    const captchaRefresh = document.getElementById("captcha-refresh");
    const signupNotice = document.getElementById("signup-notice");
    let captchaSolution = 0;

    const showCaptchaChallenge = () => {
        const firstNumber = Math.floor(Math.random() * 8) + 2;
        const secondNumber = Math.floor(Math.random() * 8) + 2;
        captchaSolution = firstNumber + secondNumber;
        captchaQuestion.textContent = `What is ${firstNumber} + ${secondNumber}?`;
        captchaInput.value = "";
        captchaInput.setCustomValidity("");
        captchaInput.removeAttribute("aria-invalid");
        document.getElementById("captcha-error").textContent = "";
    };

    const setFieldError = (input, errorId, message) => {
        const errorElement = document.getElementById(errorId);
        input.setCustomValidity(message);

        if (message) {
            input.setAttribute("aria-invalid", "true");
            errorElement.textContent = message;
            return false;
        }

        input.removeAttribute("aria-invalid");
        errorElement.textContent = "";
        return input.checkValidity();
    };

    const validateName = () => {
        const name = nameInput.value.trim();
        if (!name) {
            return setFieldError(nameInput, "signup-name-error", "Enter your full name.");
        }
        if (name.length < 2 || name.length > 80) {
            return setFieldError(nameInput, "signup-name-error", "Name must be between 2 and 80 characters.");
        }
        if (!/^[\p{L}\p{M}][\p{L}\p{M}\s'.-]*$/u.test(name)) {
            return setFieldError(nameInput, "signup-name-error", "Use letters, spaces, apostrophes, periods, or hyphens.");
        }
        return setFieldError(nameInput, "signup-name-error", "");
    };

    const validatePhone = () => {
        const phone = phoneInput.value.trim();
        const digitCount = phone.replace(/\D/g, "").length;
        if (!phone) {
            return setFieldError(phoneInput, "signup-phone-error", "Enter your phone number.");
        }
        if (!/^\+?[0-9\s().-]+$/.test(phone) || digitCount < 7 || digitCount > 15) {
            return setFieldError(phoneInput, "signup-phone-error", "Enter a valid phone number with 7 to 15 digits.");
        }
        return setFieldError(phoneInput, "signup-phone-error", "");
    };

    const validateEmail = () => {
        const email = emailInput.value.trim();
        emailInput.setCustomValidity("");
        if (!email) {
            return setFieldError(emailInput, "signup-email-error", "Enter your email address.");
        }
        if (!emailInput.validity.valid) {
            return setFieldError(emailInput, "signup-email-error", "Enter a valid email address.");
        }
        return setFieldError(emailInput, "signup-email-error", "");
    };

    const validatePassword = () => {
        const password = passwordInput.value;
        if (!password) {
            return setFieldError(passwordInput, "signup-password-error", "Create a password.");
        }
        if (password.length < 8 || password.length > 128 || !/[A-Za-z]/.test(password) || !/\d/.test(password)) {
            return setFieldError(passwordInput, "signup-password-error", "Use 8 to 128 characters, including a letter and a number.");
        }
        return setFieldError(passwordInput, "signup-password-error", "");
    };

    const validateCaptcha = () => {
        const answer = captchaInput.value.trim();
        if (!answer) {
            return setFieldError(captchaInput, "captcha-error", "Answer the verification question.");
        }
        if (!/^\d+$/.test(answer) || Number(answer) !== captchaSolution) {
            return setFieldError(captchaInput, "captcha-error", "That answer is not correct. Try again or get a new question.");
        }
        return setFieldError(captchaInput, "captcha-error", "");
    };

    const validators = [
        [nameInput, validateName],
        [phoneInput, validatePhone],
        [emailInput, validateEmail],
        [passwordInput, validatePassword],
        [captchaInput, validateCaptcha]
    ];

    validators.forEach(([input, validate]) => {
        input.addEventListener("blur", validate);
        input.addEventListener("input", () => {
            signupNotice.hidden = true;
            if (input.hasAttribute("aria-invalid")) {
                validate();
            }
        });
    });

    captchaRefresh.addEventListener("click", () => {
        showCaptchaChallenge();
        captchaInput.focus();
    });

    signupForm.addEventListener("submit", (event) => {
        event.preventDefault();
        signupNotice.hidden = true;

        let firstInvalidInput = null;
        validators.forEach(([input, validate]) => {
            if (!validate() && !firstInvalidInput) {
                firstInvalidInput = input;
            }
        });

        if (firstInvalidInput) {
            firstInvalidInput.focus();
            firstInvalidInput.reportValidity();
            return;
        }

        signupNotice.textContent = "All fields passed frontend validation. Account creation is not connected yet, so no account was created.";
        signupNotice.hidden = false;
    });

    showCaptchaChallenge();
}
