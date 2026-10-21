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

<!-- Your custom CSS -->
<link rel="stylesheet" href="style.css?v=2">

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

   
<!-- ================= NEW PHARMACY HERO ================= -->
<section class="mf-hero" id="find-medicine">
    <div class="container">
        <div class="row align-items-center g-4 mf-hero-main">

            <div class="col-lg-7">
                <span class="mf-eyebrow">
                    <i class="bi bi-heart-pulse-fill"></i>
                    YOUR HEALTH, MADE SIMPLER
                </span>

                <h1>
                    Your Trusted<br>
                    <span>Online Pharmacy</span>
                </h1>

                <p class="mf-hero-text">
                    Find medicines, check listed pharmacy availability,
                    and get the healthcare essentials you need.
                </p>

                <form class="mf-search" onsubmit="event.preventDefault(); searchMedicine();">
                    <i class="bi bi-search"></i>
                    <input
                        type="text"
                        id="medicineSearch"
                        placeholder="Search medicines by name..."
                        aria-label="Search medicines"
                    >
                    <button type="submit">Search</button>
                </form>

                <div class="mf-search-examples">
                    Try: Paracetamol, Azithromycin, Vitamin C
                </div>

                <div class="mf-hero-actions">
                    <a href="pages/prescription.html" class="mf-prescription-btn">
                        <i class="bi bi-cloud-arrow-up"></i>
                        Upload Prescription
                    </a>
                    <a href="#popular-categories" class="mf-browse-link">
                        Browse categories <i class="bi bi-arrow-down"></i>
                    </a>
                </div>

                <div class="mf-trust-points">
                    <span><i class="bi bi-search"></i> Easy medicine search</span>
                    <span><i class="bi bi-shop"></i> Pharmacy information</span>
                    <span><i class="bi bi-shield-check"></i> Health-focused support</span>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="mf-hero-art">
                    <div class="mf-art-circle"></div>
                    <div class="mf-art-leaf leaf-one"></div>
                    <div class="mf-art-leaf leaf-two"></div>

                    <div class="mf-medicine-bottle bottle-one">
                        <i class="bi bi-capsule-pill"></i>
                        <span>CARE</span>
                    </div>

                    <div class="mf-medicine-bottle bottle-two">
                        <i class="bi bi-heart-pulse"></i>
                        <span>WELLNESS</span>
                    </div>

                    <div class="mf-art-pill pill-one"></div>
                    <div class="mf-art-pill pill-two"></div>

                    <div class="mf-art-note">
                        <span class="mf-note-icon">
                            <i class="bi bi-check2-circle"></i>
                        </span>
                        <span>
                            <strong>Find your medicine</strong>
                            <small>Explore listed availability</small>
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Keep search results outside the two hero columns -->
        <div id="searchResults" class="search-results mt-4"></div>
    </div>
</section>

<!-- ================= POPULAR CATEGORIES ================= -->
<section class="mf-categories" id="popular-categories">
    <div class="container">
        <div class="mf-section-heading">
            <div>
                <span class="mf-section-label">EXPLORE MEDICINES</span>
                <h2>Browse popular categories</h2>
                <p>Start with a category or search for a specific medicine.</p>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">

            <div class="col">
                <button type="button" class="mf-category-card"
                    onclick="mfChooseCategory('Paracetamol')">
                    <span class="mf-category-icon category-blue">
                        <i class="bi bi-thermometer-half"></i>
                    </span>
                    <strong>Pain & Fever</strong>
                    <small>Find medicines</small>
                </button>
            </div>

            <div class="col">
                <button type="button" class="mf-category-card"
                    onclick="mfChooseCategory('Vitamin C')">
                    <span class="mf-category-icon category-green">
                        <i class="bi bi-apple"></i>
                    </span>
                    <strong>Vitamins</strong>
                    <small>Explore supplements</small>
                </button>
            </div>

            <div class="col">
                <button type="button" class="mf-category-card"
                    onclick="mfChooseCategory('Azithromycin')">
                    <span class="mf-category-icon category-purple">
                        <i class="bi bi-capsule"></i>
                    </span>
                    <strong>Prescription medicines</strong>
                    <small>Check medicine details</small>
                </button>
            </div>

            <div class="col">
                <button type="button" class="mf-category-card"
                    onclick="mfChooseCategory('')">
                    <span class="mf-category-icon category-orange">
                        <i class="bi bi-basket2"></i>
                    </span>
                    <strong>All medicines</strong>
                    <small>Search the catalogue</small>
                </button>
            </div>

            <div class="col">
                <a href="pages/prescription.html" class="mf-category-card">
                    <span class="mf-category-icon category-pink">
                        <i class="bi bi-file-earmark-medical"></i>
                    </span>
                    <strong>Prescription help</strong>
                    <small>Upload a prescription</small>
                </a>
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

    <script src="script.js"></script>

</body>

</html>