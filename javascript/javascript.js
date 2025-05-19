document.addEventListener('DOMContentLoaded', function() {
    const loginBtn = document.getElementById('loginBtn');
    const loginDropdown = document.getElementById('loginDropdown');

    loginBtn.addEventListener('click', function(e) {
        e.preventDefault();
        loginDropdown.style.display = loginDropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', function(e) {
        if (!loginBtn.contains(e.target) && !loginDropdown.contains(e.target)) {
            loginDropdown.style.display = 'none';
        }
    });

    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // Här kan du lägga till kod för att hantera inloggningen
        console.log('Inloggning skickad');
    });
});