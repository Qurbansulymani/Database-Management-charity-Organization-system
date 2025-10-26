<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charity Management System - Login</title>
    
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .container {
        background: rgba(255, 255, 255, 0.95);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        width: 400px;
        backdrop-filter: blur(10px);
    }

    .logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .logo h1 {
        color: #4a5568;
        font-size: 28px;
        margin-bottom: 5px;
    }

    .logo p {
        color: #718096;
        font-size: 14px;
    }

    h2 {
        text-align: center;
        color: #2d3748;
        margin-bottom: 30px;
        font-weight: 600;
    }

    label {
        font-weight: 600;
        display: block;
        margin-top: 15px;
        color: #4a5568;
    }

    select,
    input {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    select:focus,
    input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    button {
        width: 100%;
        margin-top: 25px;
        padding: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: transform 0.2s ease;
    }

    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .message {
        margin-top: 15px;
        text-align: center;
        font-size: 14px;
        padding: 10px;
        border-radius: 6px;
    }

    .success {
        background: #c6f6d5;
        color: #22543d;
        border: 1px solid #9ae6b4;
    }

    .error {
        background: #fed7d7;
        color: #742a2a;
        border: 1px solid #feb2b2;
    }

    .features {
        margin-top: 25px;
        text-align: center;
    }

    .features h3 {
        color: #4a5568;
        margin-bottom: 10px;
    }

    .feature-list {
        display: flex;
        justify-content: space-around;
        margin-top: 15px;
    }

    .feature-item {
        text-align: center;
        padding: 10px;
    }

    .feature-item i {
        font-size: 20px;
        color: #667eea;
        margin-bottom: 5px;
    }

    .feature-item span {
        font-size: 12px;
        color: #718096;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <h1>🤝 CharityMS</h1>
            <p>Charity Management System</p>
        </div>

        <h2>Welcome Back</h2>

        <label for="role">Select Role:</label>
        <select id="role">
            <option value="admin">Admin</option>
            <option value="staff">Staff</option>
            <option value="donor">Donor</option>
            <option value="beneficiary">Beneficiary</option>

        </select>

        <label for="username">Username:</label>
        <input type="text" id="username" placeholder="Enter your username">

        <label for="password">Password:</label>
        <input type="password" id="password" placeholder="Enter your password">

        <button onclick="login()">Login to Dashboard</button>

        <div id="message" class="message"></div>

        <div class="features">
            <h3>System Features</h3>
            <div class="feature-list">
                <div class="feature-item">
                    <div>💰</div>
                    <span>Donations</span>
                </div>
                <div class="feature-item">
                    <div>👥</div>
                    <span>Beneficiaries</span>
                </div>
                <div class="feature-item">
                    <div>📊</div>
                    <span>Reports</span>
                </div>
                <div class="feature-item">
                    <div>🏢</div>
                    <span>Branches</span>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Predefined valid users for simulation
    const users = {
        donor: {
            username: "donor",
            password: "12345"
        },
        staff: {
            username: "staff",
            password: "12345"
        },
        admin: {
            username: "admin",
            password: "admin123"
        },
        beneficiary: {
            username: "beneficiary",
            password: "12345"
        }
    };

    function login() {
        const role = document.getElementById("role").value;
        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();
        const message = document.getElementById("message");

        // Validation
        if (!username || !password) {
            message.textContent = "Please fill in all fields.";
            message.className = "message error";
            return;
        }

        // Check credentials
        const validUser = users[role];
        if (validUser && username === validUser.username && password === validUser.password) {
            message.textContent = `Login successful! Redirecting to ${role} home page...`;
            message.className = "message success";
            setTimeout(() => {
                window.location.href = "home page.html";
            }, 1500);
        } else {
            message.textContent = "Invalid username or password.";
            message.className = "message error";
        }
    }

    // Enter key support
    document.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            login();
        }
    });
    </script>
</body>

</html>