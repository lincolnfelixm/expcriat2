document.querySelector(".login").style.display = "block";
document.querySelector(".register").style.display = "none";
document.querySelector(".forgot").style.display = "none";

function showWrapper() {
    document.querySelector(".wrapper").style.display = "block";
    document.querySelector(".wrapper").style.transform = "scale(1)";
    document.querySelector(".wrapper").style.transition = "all 0.6s ease-in-out";
}

function closeWrapper() {
    document.querySelector(".wrapper").style.display = "none";
}

function showLogin() {
    document.querySelector(".login").style.display = "block";
    document.querySelector(".register").style.display = "none";
    document.querySelector(".forgot").style.display = "none";
    document.querySelector(".login").style.transition = "all 0.6s ease-in-out";
}

function showRegister() {
    document.querySelector(".login").style.display = "none";
    document.querySelector(".register").style.display = "block";
    document.querySelector(".forgot").style.display = "none";
    document.querySelector(".register").style.transition = "all 0.6s ease-in-out";
}

function showForgot() {
    document.querySelector(".login").style.display = "none";
    document.querySelector(".register").style.display = "none";
    document.querySelector(".forgot").style.display = "block";
    document.querySelector(".forgot").style.transition = "all 0.6s ease-in-out";
}

// Define the hash function
function hash(input) {
    return CryptoJS.SHA256(input).toString();
}

// Define the function to fetch the public key
async function fetchPublicKey(filename) {
    try {
        const response = await fetch(filename);
        if (!response.ok) {
            throw new Error('HTTP error! status: ${response.status}');
        }
        const publicKey = await response.text();
        return publicKey;
    } catch (error) {
        console.error('Error fetching public key:', error);
        return null;
    }
}

// Define the login function
async function login() {
    let username = document.getElementById("login-username").value;
    let password = document.getElementById("login-password").value;
    let hashedPassword = hash(password); // Defined in script.js

    let publicKey = await fetchPublicKey('serverPublicKey.pem'); // Defined in script.js
    let encrypt = new JSEncrypt();
    let symmetricKey = CryptoJS.lib.WordArray.random(16);
    let iv = CryptoJS.lib.WordArray.random(16); // initialization vector
    encrypt.setPublicKey(publicKey);
    
    // Encrypt using symmetric key (AES)
    let encryptedUsername = CryptoJS.AES.encrypt(username, symmetricKey, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    let encryptedPassword = CryptoJS.AES.encrypt(hashedPassword, symmetricKey, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    
    let encryptedSymmetricKey = encrypt.encrypt(symmetricKey.toString(CryptoJS.enc.Hex));
    let encryptedIV = encrypt.encrypt(iv.toString(CryptoJS.enc.Hex));

    let loginData = {
        'username': encryptedUsername.toString(),
        'password': encryptedPassword.toString(),
        'symmetricKey': encryptedSymmetricKey,
        'iv': encryptedIV
    };

    fetch('login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(loginData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = "main.html";
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

async function register() {
    let username = document.getElementById("register-username").value;
    let email = document.getElementById("register-email").value;
    let password = document.getElementById("register-password").value;
    let hashedPassword = hash(password);

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        return;
    }

    const isAccepted = document.querySelector(".register .remember-forgot input[type='checkbox']").checked;
    if (!isAccepted) {
        alert('Please accept the terms and conditions.');
        return;
    }

    let publicKey = await fetchPublicKey('serverPublicKey.pem');
    let encrypt = new JSEncrypt();
    let symmetricKey = CryptoJS.lib.WordArray.random(16);
    let iv = CryptoJS.lib.WordArray.random(16); // initialization vector
    encrypt.setPublicKey(publicKey);

    // Encrypt using symmetric key (AES)
    let encryptedUsername = CryptoJS.AES.encrypt(username, symmetricKey, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    let encryptedEmail = CryptoJS.AES.encrypt(email, symmetricKey, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    let encryptedPassword = CryptoJS.AES.encrypt(hashedPassword, symmetricKey, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });

    let encryptedSymmetricKey = encrypt.encrypt(symmetricKey.toString(CryptoJS.enc.Hex));
    let encryptedIV = encrypt.encrypt(iv.toString(CryptoJS.enc.Hex));

    let registerData = {
        username: encryptedUsername.toString(),
        email: encryptedEmail.toString(),
        password: encryptedPassword.toString(),
        symmetricKey: encryptedSymmetricKey,
        iv: encryptedIV
    };

    fetch('register.php', {
        method: 'POST',
        body: JSON.stringify(registerData),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            showLogin();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

async function recover() {
    const email = document.querySelector(".form-box.forgot input[type='email']").value;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert('Please enter a valid email address.');
      return;
    }

    const publicKey = await fetchPublicKey('serverPublicKey.pem');

    const encryptedEmail = CryptoJS.AES.encrypt(email, publicKey).toString();

    const recoverData = {
      email: encryptedEmail,
    };

    fetch('recover.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(recoverData),
    })
      .then(response => response.json())
      .then(data => console.log(data.message))
      .catch(console.error);
}

  
function goMain(){
    window.location.href = "index.html";
}  
