const btnChangePassword = document.getElementById('btnChangePassword');
const form = document.getElementById('registerForm');

btnChangePassword.addEventListener('click', () => {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const token = document.getElementById('token').value;

    if (newPassword !== confirmPassword) {
        alert('Passwords do not match');
        return;
    }

    // Hash the password
    const hashedPassword = CryptoJS.SHA256(newPassword).toString();

    // Encrypt the data using AES
    const encryptedData = CryptoJS.AES.encrypt(hashedPassword, token).toString();

    // Encrypt the token using RSA
    const encrypt = new JSEncrypt();
    encrypt.setPublicKey('serverPublicKey.pem');
    const encryptedToken = encrypt.encrypt(token);

    const formData = new FormData();
    formData.append('password', encryptedData);
    formData.append('token', encryptedToken);

    fetch('newpasswd.php', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (!response.ok) {
            throw new Error('HTTP error! status: ${response.status}');
        }
        return response.text();
    }).then(data => {
        console.log(data);
        window.location.href = 'recover.html';
    }).catch(error => {
        console.log('There was a problem with the fetch operation: ' + error.message);
    });
});