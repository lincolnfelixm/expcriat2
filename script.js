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
    const email = document.getElementById("login-email").value;
    let password = document.getElementById("login-passwd").value;

    password = hash(password);

    const publicKey = await fetchPublicKey('serverPublicKey.pem');

    const encryptionKey = CryptoJS.lib.WordArray.random(16); // 16 bytes (128 bits)
    const encryptedPassword = CryptoJS.AES.encrypt(password, publicKey).toString();

    const loginData = {
        email: email,
        password: encryptedPassword,
        encryptionKey: encryptionKey.toString()
    };

    fetch('login.php', {
        method: 'POST',
        body: JSON.stringify(loginData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(data.message);
        } else {
            console.log(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

async function register() {
    let username = document.getElementById("register-username").value;
    let email = document.getElementById("register-email").value;
    let password = document.getElementById("register-passwd").value;

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

    password = hash(password);

    const publicKey = await fetchPublicKey('serverPublicKey.pem');

    const encryptionKey = CryptoJS.lib.WordArray.random(16); // 16 bytes (128 bits)

    const encryptedUsername = CryptoJS.AES.encrypt(username, publicKey).toString();
    const encryptedEmail = CryptoJS.AES.encrypt(email, publicKey).toString();
    const encryptedPassword = CryptoJS.AES.encrypt(password, publicKey).toString();

    const registerData = {
        username: encryptedUsername,
        email: encryptedEmail,
        password: encryptedPassword,
        encryptionKey: encryptionKey.toString(),
    };

    fetch('register.php', {
        method: 'POST',
        body: JSON.stringify(registerData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(data.message);
        } else {
            console.log(data.message);
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
      body: JSON.stringify(recoverData),
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log(data.message);
        } else {
          console.log(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
}
  
function goMain(){
    window.location.href = "index.html";
}  
