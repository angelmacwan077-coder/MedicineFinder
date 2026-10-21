<?php

require_once "config.php";

if (isset($_GET["medicine"])) {

    $medicine = trim($_GET["medicine"]);

    if ($medicine == "") {
        die("Please enter a medicine name.");
    }

    $stmt = $conn->prepare("
        SELECT medicine_id, medicine_name, generic_name,
               category, description, uses, price, availability
        FROM medicines
        WHERE medicine_name LIKE ?
           OR generic_name LIKE ?
    ");

    $search = "%" . $medicine . "%";

    $stmt->bind_param("ss", $search, $search);

    $stmt->execute();

    $result = $stmt->get_result();

} else {
    die("No medicine searched.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Medicine Search Results | MedicineFinder</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="style.css">

</head>

<body>

<div class="container py-5">

    <h2 class="mb-4">
        Medicine Search Results
    </h2>

    <?php if ($result->num_rows > 0): ?>

        <div class="row g-4">

            <?php while ($row = $result->fetch_assoc()): ?>
                <?php

$availability_stmt = $conn->prepare("
    SELECT p.pharmacy_name,
           p.address,
           p.city,
           p.phone,
           mp.availability
    FROM medicine_pharmacy mp
    JOIN pharmacies p
        ON mp.pharmacy_id = p.pharmacy_id
    WHERE mp.medicine_id = ?
");

$availability_stmt->bind_param(
    "i",
    $row["medicine_id"]
);

$availability_stmt->execute();

$pharmacy_result = $availability_stmt->get_result();

?>

                <div class="col-md-6 col-lg-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <h4 class="card-title">
                                <?php echo htmlspecialchars($row["medicine_name"]); ?>
                            </h4>

                            <p>
                                <strong>Generic Name:</strong>
                                <?php echo htmlspecialchars($row["generic_name"]); ?>
                            </p>

                            <p>
                                <strong>Category:</strong>
                                <?php echo htmlspecialchars($row["category"]); ?>
                            </p>

                            <p>
                                <strong>Description:</strong>
                                <?php echo htmlspecialchars($row["description"]); ?>
                            </p>

                            <p>
                                <strong>Uses:</strong>
                                <?php echo htmlspecialchars($row["uses"]); ?>
                            </p>

                            <p>
                                <strong>Price:</strong>
                                ₹<?php echo htmlspecialchars($row["price"]); ?>
                            </p>

                            <p>
                                <strong>Availability:</strong>
                                <?php echo htmlspecialchars($row["availability"]); ?>
                            </p>
                            <hr>

<h5 class="mt-3">
    Pharmacy Availability
</h5>

<?php if ($pharmacy_result->num_rows > 0): ?>

    <?php while ($pharmacy = $pharmacy_result->fetch_assoc()): ?>

        <div class="border rounded p-3 mb-2">

            <strong>
                <?php echo htmlspecialchars($pharmacy["pharmacy_name"]); ?>
            </strong>

            <br>

            <small>
                <?php echo htmlspecialchars($pharmacy["address"]); ?>,
                <?php echo htmlspecialchars($pharmacy["city"]); ?>
            </small>

            <br>

            <small>
                📞 <?php echo htmlspecialchars($pharmacy["phone"]); ?>
            </small>

            <br>

            <?php if ($pharmacy["availability"] == "Available"): ?>

                <span class="badge bg-success mt-2">
                    Available
                </span>

            <?php else: ?>

                <span class="badge bg-danger mt-2">
                    Out of Stock
                </span>

            <?php endif; ?>

        </div>

    <?php endwhile; ?>

<?php else: ?>

    <p class="text-muted">
        No pharmacy information available.
    </p>

<?php endif; ?>


<?php if ($row["availability"] == "Available"): ?>

    <a href="order.php?medicine_id=<?php echo $row["medicine_id"]; ?>"
       class="btn btn-primary w-100 mt-3">

        🛒 Buy Medicine

    </a>

<?php else: ?>

    <div class="alert alert-warning mt-3">

        Medicine currently unavailable.
        Please check nearby pharmacies.

    </div>

<?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-warning">

            No medicine found for
            <strong>
                <?php echo htmlspecialchars($medicine); ?>
            </strong>

        </div>

    <?php endif; ?>

    <a href="index.php"
       class="btn btn-primary mt-4">

        ← Back to Home

    </a>

</div>

</body>

</html>