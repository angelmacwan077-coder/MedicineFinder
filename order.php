<?php

require_once "config.php";

if (!isset($_GET["medicine_id"])) {
    die("Medicine not selected.");
}

$medicine_id = intval($_GET["medicine_id"]);

$stmt = $conn->prepare("
    SELECT medicine_id, medicine_name, generic_name, price, availability
    FROM medicines
    WHERE medicine_id = ?
");

$stmt->bind_param("i", $medicine_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Medicine not found.");
}

$medicine = $result->fetch_assoc();

/* Get pharmacies where this medicine is available */
$pharmacy_stmt = $conn->prepare("
    SELECT p.pharmacy_id,
           p.pharmacy_name,
           p.address,
           p.city,
           p.phone
    FROM medicine_pharmacy mp
    JOIN pharmacies p
        ON mp.pharmacy_id = p.pharmacy_id
    WHERE mp.medicine_id = ?
      AND mp.availability = 'Available'
");

$pharmacy_stmt->bind_param("i", $medicine_id);
$pharmacy_stmt->execute();

$pharmacy_result = $pharmacy_stmt->get_result();

$order_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $quantity = intval($_POST["quantity"]);
    $delivery_address = trim($_POST["delivery_address"]);
    $pharmacy_id = intval($_POST["pharmacy_id"]);

    if ($quantity < 1) {

        $order_message = "Please enter a valid quantity.";

    } elseif ($delivery_address == "") {

    $order_message = "Please enter your delivery address.";

} elseif ($pharmacy_id < 1) {

    $order_message = "Please select a pharmacy.";

} else {
        $order_stmt = $conn->prepare("
    INSERT INTO orders
    (medicine_id, quantity, delivery_address, pharmacy_id)
    VALUES (?, ?, ?, ?)
");

$order_stmt->bind_param(
    "iisi",
    $medicine_id,
    $quantity,
    $delivery_address,
    $pharmacy_id
);
        if ($order_stmt->execute()) {

    $order_id = $conn->insert_id;

    header(
        "Location: order_success.php?order_id=" . $order_id
    );

    exit;

} else {

    $order_message = "Unable to place order.";

}
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Medicine | MedicineFinder</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="style.css">

</head>

<body>

<div class="container py-5">

    <div class="card shadow-sm mx-auto"
         style="max-width: 600px;">

        <div class="card-body p-4">

            <h2 class="mb-4">
                Order Medicine
            </h2>

            <h4>
                <?php echo htmlspecialchars($medicine["medicine_name"]); ?>
            </h4>

            <p>
                <strong>Generic Name:</strong>
                <?php echo htmlspecialchars($medicine["generic_name"]); ?>
            </p>

            <p>
                <strong>Price:</strong>
                ₹<?php echo htmlspecialchars($medicine["price"]); ?>
            </p>

            <p>
                <strong>Availability:</strong>

                <span class="badge bg-success">
                    <?php echo htmlspecialchars($medicine["availability"]); ?>
                </span>

            </p>

            <hr>

            <h5 class="mb-3">
                Order Details
            </h5>

            <?php if ($order_message != ""): ?>

    <div class="alert alert-info">
        <?php echo htmlspecialchars($order_message); ?>
    </div>

<?php endif; ?>

           <form method="POST">

                <div class="mb-3">

                    <label class="form-label">
                        Quantity (Number of Packs/Strips)
                    </label>

                    <input type="number"
                           name="quantity"
                           class="form-control"
                           value="1"
                           min="1">

                </div>

                <div class="mb-3">

    <label class="form-label">
        Select Pharmacy
    </label>

    <select name="pharmacy_id"
            class="form-select"
            required>

        <option value="">
            -- Select Pharmacy --
        </option>

        <?php while ($pharmacy = $pharmacy_result->fetch_assoc()): ?>

            <option value="<?php echo $pharmacy["pharmacy_id"]; ?>">

                <?php echo htmlspecialchars($pharmacy["pharmacy_name"]); ?>
                -
                <?php echo htmlspecialchars($pharmacy["address"]); ?>,
                <?php echo htmlspecialchars($pharmacy["city"]); ?>

            </option>

        <?php endwhile; ?>

    </select>

    <small class="text-muted">
        Select the pharmacy from which your order will be processed.
    </small>

</div>

                <div class="mb-3">

                    <label class="form-label">
                        Delivery Address
                    </label>

                    <textarea name="delivery_address"
                              class="form-control"
                              rows="3"
                              placeholder="Enter your delivery address"></textarea>

                </div>

                <button type="submit"
                        class="btn btn-primary w-100">

                    Place Order

                </button>

            </form>

            <a href="index.php"
               class="btn btn-outline-secondary w-100 mt-3">

                ← Back to Home

            </a>

        </div>

    </div>

</div>

</body>

</html>