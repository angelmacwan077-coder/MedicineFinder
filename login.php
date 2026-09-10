<?php

session_start();

require_once "config.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $message = "Please enter email and password.";
        $messageType = "danger";

    } else {

        $stmt = $conn->prepare(
            "SELECT user_id, name, email, password_hash, status
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if ($user["status"] !== "active") {

                $message = "Your account is inactive.";
                $messageType = "warning";

            }

            elseif (
                password_verify(
                    $password,
                    $user["password_hash"]
                )
            ) {

                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];

                header("Location: index.php");
                exit;

            } else {

                $message = "Invalid email or password.";
                $messageType = "danger";

            }

        } else {

            $message = "Invalid email or password.";
            $messageType = "danger";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Login | Medicine Finder</title>

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

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            background: #ffffff;
            padding: 42px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(30, 70, 120, 0.12);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
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
        }

        .form-label {
            font-weight: 600;
        }

        .form-control {
            min-height: 52px;
            border-radius: 12px;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.12);
        }

        .btn-login {
            width: 100%;
            min-height: 52px;
            border: none;
            border-radius: 13px;
            background: #3b82f6;
            color: white;
            font-weight: 600;
        }

        .btn-login:hover {
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

    </style>

</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="logo">

            <div class="logo-icon">
                <i class="bi bi-capsule-pill"></i>
            </div>

            <h2>
                Medicine<span>Finder</span>
            </h2>

            <p>Login to your account</p>

        </div>


        <?php if (!empty($message)): ?>

            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form method="POST" action="login.php">

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


            <div class="mb-4">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter your password"
                    required>

            </div>


            <button type="submit" class="btn-login">

                <i class="bi bi-box-arrow-in-right me-2"></i>

                Login

            </button>

        </form>


        <div class="bottom-text">

            Don't have an account?

            <a href="register.php">
                Create Account
            </a>

        </div>


        <a href="index.php" class="home-link">

            <i class="bi bi-arrow-left"></i>
            Back to Home

        </a>

    </div>

</div>

</body>

</html>