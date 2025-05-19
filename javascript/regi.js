document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('regiForm');
    const errorMessages = document.getElementById('errorMessages');

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        errorMessages.innerHTML = '';
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const terms = document.getElementById('terms').checked;

        let isValid = true;
        let errors = [];

        // Klientvalidering (behåll din existerande validering här)
        if (username.length < 3) {
            errors.push('Användarnamnet måste vara minst 3 tecken långt.');
            isValid = false;
        }
        if (password.length < 6) {
            errors.push('Lösenordet måste vara minst 6 tecken långt.');
            isValid = false;
        }
        if (password !== confirmPassword) {
            errors.push('Lösenorden matchar inte.');
            isValid = false;
        }
        if (!terms) {
            errors.push('Du måste acceptera villkoren för att registrera dig.');
            isValid = false;
        }

        if (isValid) {
            // Skicka data till servern om klientvalideringen passerar
            const formData = new FormData(form);

            fetch('db_2.0.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        form.reset();
                    } else {
                        // Visa serverfelmeddelande
                        const errorElement = document.createElement('p');
                        errorElement.textContent = data.message;
                        errorMessages.appendChild(errorElement);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorElement = document.createElement('p');
                    errorElement.textContent = 'Ett fel uppstod. Försök igen senare.';
                    errorMessages.appendChild(errorElement);
                });
        } else {
            // Visa klientfelmeddelanden
            errors.forEach(error => {
                const errorElement = document.createElement('p');
                errorElement.textContent = error;
                errorMessages.appendChild(errorElement);
            });
        }
    });
});