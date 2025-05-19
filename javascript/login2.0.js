function validateLoginForm() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    let errorMessages = '';

    if (username === '' || password === '') {
        errorMessages += 'Alla fält måste fyllas i.<br>';
    }

    if (errorMessages) {
        document.getElementById('errorMessages').innerHTML = errorMessages;
        return false;
    }
    return true;
}
