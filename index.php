<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Medicine Finder | Find Your Medicine Easily</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Our CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- ================= NAVBAR ================= -->

    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">

        <div class="container">

            <!-- Logo -->
            <a class="navbar-brand fw-bold" href="index.php">
                <span class="logo-icon">
                    <i class="bi bi-capsule-pill"></i>
                </span>
                Medicine<span>Finder</span>
            </a>

            <!-- Mobile Menu Button -->
            <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar">

                <span class="navbar-toggler-icon"></span>

            </button>

            <!-- Navigation -->
            <div class="collapse navbar-collapse" id="mainNavbar">

                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#find-medicine">
                            Find Medicine
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#features">
                            Features
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">
                            How It Works
                        </a>
                    </li>

                    <?php if (isset($_SESSION["user_id"])): ?>

    <li class="nav-item ms-lg-3">
        <span class="nav-link fw-semibold">
            Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
        </span>
    </li>

    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
        <a href="logout.php"
            class="btn btn-primary rounded-pill px-4">
            Logout
        </a>
    </li>

<?php else: ?>

    <li class="nav-item ms-lg-3">
        <a href="login.php"
            class="btn btn-outline-primary rounded-pill px-4">
            Login
        </a>
    </li>

    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
        <a href="register.php"
            class="btn btn-primary rounded-pill px-4">
            Sign Up
        </a>
    </li>

<?php endif; ?>
                </ul>

            </div>

        </div>

    </nav>


    <!-- ================= HERO SECTION ================= -->

    <section class="hero-section">

        <div class="container">

            <div class="row align-items-center">

                <!-- Hero Text -->

                <div class="col-lg-6">

                    <div class="hero-badge">
                        <i class="bi bi-heart-pulse-fill"></i>
                        Smart Medicine Assistance
                    </div>

                    <h1>
                        Find Your Medicine.
                        <span>Find It Faster.</span>
                    </h1>

                    <p class="hero-text">
                        Search medicines, check pharmacy availability,
                        compare company options and request medicines
                        when they are unavailable.
                    </p>


                    <!-- Search Box -->

                    <div class="hero-search" id="find-medicine">

                        <div class="search-icon">
                            <i class="bi bi-search"></i>
                        </div>

                        <input type="text"
                            id="medicineSearch"
                            placeholder="Search medicine name...">

                        <button onclick="searchMedicine()">
                            Search
                        </button>

                    </div>

                    <small class="search-hint">
                        Example: Paracetamol, Azithromycin, Vitamin C
                    </small>


                    <!-- Quick Actions -->

                    <div class="quick-actions">

                        <a href="pages/prescription.html">
                            <i class="bi bi-camera"></i>
                            Scan Prescription
                        </a>

                        <a href="pages/assistant.html">
                            <i class="bi bi-robot"></i>
                            Health Assistant
                        </a>

                    </div>

                </div>


                <!-- Hero Visual -->

                <div class="col-lg-6">

                    <div class="hero-visual">

                        <div class="floating-card card-one">

                            <div class="mini-icon green">
                                <i class="bi bi-check-lg"></i>
                            </div>

                            <div>
                                <strong>Medicine Found</strong>
                                <small>Available nearby</small>
                            </div>

                        </div>


                        <div class="medicine-circle">

                            <div class="cross-symbol">
                                +
                            </div>

                            <i class="bi bi-capsule-pill capsule-icon"></i>

                        </div>


                        <div class="floating-card card-two">

                            <div class="mini-icon blue">
                                <i class="bi bi-shop"></i>
                            </div>

                            <div>
                                <strong>Pharmacy</strong>
                                <small>Stock updated recently</small>
                            </div>

                        </div>


                        <div class="floating-card card-three">

                            <div class="mini-icon purple">
                                <i class="bi bi-shield-check"></i>
                            </div>

                            <div>
                                <strong>Smart Assistance</strong>
                                <small>Simple & helpful</small>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ================= QUICK FEATURES ================= -->

    <section class="quick-feature-section">

        <div class="container">

            <div class="row g-4">

                <div class="col-md-4">

                    <div class="feature-mini">

                        <div class="feature-mini-icon">
                            <i class="bi bi-search"></i>
                        </div>

                        <div>
                            <h5>Find Medicines</h5>
                            <p>
                                Search medicines and check
                                their recorded availability.
                            </p>
                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="feature-mini">

                        <div class="feature-mini-icon">
                            <i class="bi bi-shop"></i>
                        </div>

                        <div>
                            <h5>Check Pharmacies</h5>
                            <p>
                                View pharmacy details,
                                quantity and stock status.
                            </p>
                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="feature-mini">

                        <div class="feature-mini-icon">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>

                        <div>
                            <h5>Request Medicine</h5>
                            <p>
                                Request a medicine when
                                it is currently unavailable.
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ================= FEATURES ================= -->

    <section class="features-section" id="features">

        <div class="container">

            <div class="section-heading">

                <span>OUR FEATURES</span>

                <h2>
                    More Than Just A
                    <strong>Medicine Search</strong>
                </h2>

                <p>
                    Everything you need to find medicines and
                    get useful assistance in one place.
                </p>

            </div>


            <div class="row g-4">


                <!-- Feature 1 -->

                <div class="col-md-6 col-lg-4">

                    <div class="feature-card">

                        <div class="feature-icon">
                            <i class="bi bi-search-heart"></i>
                        </div>

                        <h4>Smart Medicine Finder</h4>

                        <p>
                            Search medicines by name or keyword
                            and find pharmacies where stock is
                            currently recorded.
                        </p>

                        <a href="#find-medicine">
                            Find Medicine
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- Feature 2 -->

                <div class="col-md-6 col-lg-4">

                    <div class="feature-card">

                        <div class="feature-icon">
                            <i class="bi bi-shop-window"></i>
                        </div>

                        <h4>Pharmacy Availability</h4>

                        <p>
                            View pharmacy information, medicine
                            quantity, availability status and
                            latest stock update.
                        </p>

                        <a href="#find-medicine">
                            Check Availability
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- Feature 3 -->

                <div class="col-md-6 col-lg-4">

                    <div class="feature-card">

                        <div class="feature-icon">
                            <i class="bi bi-layers"></i>
                        </div>

                        <h4>Company Alternatives</h4>

                        <p>
                            Compare different company or brand
                            entries associated with the same
                            medicine or generic composition.
                        </p>

                        <a href="#find-medicine">
                            Compare Options
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- Feature 4 -->

                <div class="col-md-6 col-lg-4">

                    <div class="feature-card">

                        <div class="feature-icon">
                            <i class="bi bi-file-earmark-medical"></i>
                        </div>

                        <h4>Prescription Assistant</h4>

                        <p>
                            Upload a supported prescription and
                            get medicine-related information
                            for review and searching.
                        </p>

                        <a href="pages/prescription.html">
                            Upload Prescription
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- Feature 5 -->

                <div class="col-md-6 col-lg-4">

                    <div class="feature-card">

                        <div class="feature-icon">
                            <i class="bi bi-robot"></i>
                        </div>

                        <h4>AI Symptom Assistant</h4>

                        <p>
                            Get general informational guidance
                            and next-step assistance based on
                            symptoms you describe.
                        </p>

                        <a href="pages/assistant.html">
                            Ask Assistant
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- Feature 6 -->

                <div class="col-md-6 col-lg-4">

                    <div class="feature-card emergency-card">

                        <div class="feature-icon">
                            <i class="bi bi-heart-pulse"></i>
                        </div>

                        <h4>Emergency Help</h4>

                        <p>
                            Quickly access available doctor and
                            hospital contact information when
                            urgent assistance is needed.
                        </p>

                        <a href="pages/emergency.html">
                            Get Help
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ================= HOW IT WORKS ================= -->

    <section class="how-section" id="how-it-works">

        <div class="container">

            <div class="section-heading">

                <span>SIMPLE PROCESS</span>

                <h2>
                    How <strong>Medicine Finder</strong> Works
                </h2>

            </div>


            <div class="row g-4">

                <div class="col-md-4">

                    <div class="step-card">

                        <div class="step-number">
                            01
                        </div>

                        <i class="bi bi-search"></i>

                        <h4>Search</h4>

                        <p>
                            Enter the name of the medicine
                            you are looking for.
                        </p>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="step-card">

                        <div class="step-number">
                            02
                        </div>

                        <i class="bi bi-shop"></i>

                        <h4>Check</h4>

                        <p>
                            View pharmacies, quantity,
                            availability and company options.
                        </p>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="step-card">

                        <div class="step-number">
                            03
                        </div>

                        <i class="bi bi-check-circle"></i>

                        <h4>Get Assistance</h4>

                        <p>
                            Request unavailable medicines or
                            use our assistance features.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ================= CTA ================= -->

    <section class="cta-section">

        <div class="container">

            <div class="cta-box">

                <div>

                    <span>
                        CAN'T FIND YOUR MEDICINE?
                    </span>

                    <h2>
                        Don't worry. You can request it.
                    </h2>

                    <p>
                        Submit a medicine request when
                        currently available stock isn't found.
                    </p>

                </div>

                <a href="login.php"
                    class="btn btn-light rounded-pill px-4">
                    Get Started
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

        </div>

    </section>


    <!-- ================= FOOTER ================= -->

    <footer class="footer">

        <div class="container">

            <div class="row g-4">

                <div class="col-lg-5">

                    <a class="footer-logo" href="index.php">
                        <i class="bi bi-capsule-pill"></i>
                        MedicineFinder
                    </a>

                    <p>
                        A smart medicine availability and
                        pharmacy assistance platform designed
                        to help users find medicines more easily.
                    </p>

                </div>


                <div class="col-6 col-lg-2">

                    <h5>Explore</h5>

                    <a href="#find-medicine">Find Medicine</a>
                    <a href="#features">Features</a>
                    <a href="#how-it-works">How It Works</a>

                </div>


                <div class="col-6 col-lg-2">

                    <h5>Assistance</h5>

                    <a href="pages/prescription.html">
                        Prescription
                    </a>

                    <a href="pages/assistant.html">
                        Health Assistant
                    </a>

                    <a href="pages/emergency.html">
                        Emergency Help
                    </a>

                </div>


                <div class="col-12 col-lg-3">

                    <h5>Account</h5>

                    <a href="login.php">
                        Login
                    </a>

                    <a href="register.php">
                        Create Account
                    </a>

                </div>

            </div>


            <hr>

            <div class="footer-bottom">

                <p>
                    © 2026 MedicineFinder. BCA Minor Project.
                </p>

                <p>
                    For informational assistance only.
                </p>

            </div>

        </div>

    </footer>


    <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <!-- Our JS -->

    <script src="js/script.js"></script>

</body>

</html>