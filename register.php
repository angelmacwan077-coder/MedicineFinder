<?php

session_start();

require_once "config.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $role = $_POST["role"];

    // Only these two roles can register publicly
    if ($role !== "customer" && $role !== "pharmacy_owner") {
        $role = "";
    }

    // Check empty fields
    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($confirmPassword) ||
        empty($role)
    ) {

        $message = "Please fill all fields.";
        $messageType = "danger";

    }

    // Validate email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $messageType = "danger";

    }

    // Check password length
    elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $messageType = "danger";

    }

    // Check passwords
    elseif ($password !== $confirmPassword) {

        $message = "Passwords do not match.";
        $messageType = "danger";

    }

    else {

        // Check whether email already exists
        $check = $conn->prepare(
            "SELECT user_id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);

        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "An account with this email already exists.";
            $messageType = "warning";

        } else {

            // Secure password hashing
            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert user with selected role
            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, password_hash, role)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $passwordHash,
                $role
            );

            if ($stmt->execute()) {

                // Redirect to login after successful registration
                header(
                    "Location: login.php?registered=1"
                );

                exit;

            } else {

                $message = "Something went wrong. Please try again.";
                $messageType = "danger";

            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Account | MedicineFinder</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        body {
            min-height: 100vh;
            background: linear-gradient(
                135deg,
                #eef6ff,
                #ffffff
            );
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 35px 15px;
        }

        .register-card {
            width: 100%;
            max-width: 560px;
            background: #ffffff;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(30, 70, 120, 0.12);
        }

        .logo {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo-icon {
            width: 55px;
            height: 55px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eaf3ff;
            color: #3b82f6;
            border-radius: 16px;
            font-size: 27px;
            margin-bottom: 10px;
        }

        .logo h2 {
            font-weight: 800;
            margin: 5px 0;
        }

        .logo h2 span {
            color: #3b82f6;
        }

        .logo p {
            color: #7b8798;
            margin: 0;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control {
            min-height: 50px;
            border-radius: 12px;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.12);
        }

        .role-title {
            font-weight: 700;
            margin-bottom: 12px;
        }

        .role-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .role-option {
            position: relative;
        }

        .role-option input {
    position: absolute;
    opacity: 0;
    width: 1px;
    height: 1px;
}

.role-card {
    position: relative;
    z-index: 2;
    display: block;
    cursor: pointer;
}

        .role-card {
            display: block;
            padding: 20px 15px;
            text-align: center;
            border: 2px solid #e4e7ec;
            border-radius: 16px;
            cursor: pointer;
            transition: 0.2s;
            height: 100%;
        }

        .role-card:hover {
            border-color: #93c5fd;
            background: #f8fbff;
        }

        .role-option input:checked + .role-card {
            border-color: #3b82f6;
            background: #eef6ff;
        }

        .role-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: #eaf3ff;
            color: #3b82f6;
            font-size: 23px;
        }

        .role-card strong {
            display: block;
            margin-bottom: 5px;
        }

        .role-card small {
            color: #7b8798;
            line-height: 1.4;
        }

        .btn-register {
            width: 100%;
            min-height: 52px;
            border: none;
            border-radius: 13px;
            background: #3b82f6;
            color: #ffffff;
            font-weight: 600;
        }

        .btn-register:hover {
            background: #2563eb;
        }

        .bottom-text {
            text-align: center;
            margin-top: 22px;
            color: #7b8798;
        }

        .bottom-text a {
            color: #3b82f6;
            font-weight: 600;
            text-decoration: none;
        }

        .home-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #667085;
            text-decoration: none;
            font-size: 14px;
        }

        @media (max-width: 500px) {

            .register-card {
                padding: 28px 20px;
            }

            .role-options {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<div class="register-wrapper">

    <div class="register-card">

        <div class="logo">

            <div class="logo-icon">
                <i class="bi bi-capsule-pill"></i>
            </div>

            <h2>
                Medicine<span>Finder</span>
            </h2>

            <p>Create your account to get started</p>

        </div>


        <?php if (!empty($message)): ?>

            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form method="POST" action="register.php">


            <!-- ACCOUNT TYPE -->

            <div class="role-title">
                I want to register as
            </div>

            <div class="role-options">

                <div class="role-option">

                    <input
                        type="radio"
                        id="customer"
                        name="role"
                        value="customer"
                        required>

                    <label
                        for="customer"
                        class="role-card">

                        <div class="role-icon">
                            <i class="bi bi-person"></i>
                        </div>

                        <strong>Customer</strong>

                        <small>
                            Browse medicines, add to cart
                            and place orders.
                        </small>

                    </label>

                </div>


                <div class="role-option">

                    <input
                        type="radio"
                        id="pharmacy_owner"
                        name="role"
                        value="pharmacy_owner"
                        required>

                    <label
                        for="pharmacy_owner"
                        class="role-card">

                        <div class="role-icon">
                            <i class="bi bi-shop"></i>
                        </div>

                        <strong>Pharmacy Owner</strong>

                        <small>
                            Manage your pharmacy,
                            medicines and orders.
                        </small>

                    </label>

                </div>

            </div>


            <!-- NAME -->

            <div class="mb-3">

                <label class="form-label">
                    Full Name
                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Enter your full name"
                    required>

            </div>


            <!-- EMAIL -->

            <div class="mb-3">

                <label class="form-label">
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Enter your email"
                    required>

            </div>


            <!-- PASSWORD -->

            <div class="mb-3">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Minimum 6 characters"
                    required>

            </div>


            <!-- CONFIRM PASSWORD -->

            <div class="mb-4">

                <label class="form-label">
                    Confirm Password
                </label>

                <input
                    type="password"
                    name="confirm_password"
                    class="form-control"
                    placeholder="Re-enter your password"
                    required>

            </div>


            <button
                type="submit"
                class="btn-register">

                <i class="bi bi-person-plus me-2"></i>

                Create Account

            </button>

        </form>


        <div class="bottom-text">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </div>


        <a
            href="index.php"
            class="home-link">

            <i class="bi bi-arrow-left"></i>
            Back to Home

        </a>

    </div>

</div>

</body>

</html>