<?php

require_once "config.php";

if (!isset($_GET["order_id"])) {
    die("Invalid order.");
}

$order_id = intval($_GET["order_id"]);

$stmt = $conn->prepare("
    SELECT
        o.order_id,
        o.quantity,
        o.delivery_address,
        o.order_status,
        o.order_date,
        m.medicine_name,
        m.generic_name,
        m.price
    FROM orders o
    JOIN medicines m
        ON o.medicine_id = m.medicine_id
    WHERE o.order_id = ?
");

$stmt->bind_param("i", $order_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Order not found.");
}

$order = $result->fetch_assoc();

$total_price = $order["price"] * $order["quantity"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Confirmed | MedicineFinder</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="style.css">

    <style>

        body {
            background: #f5f8ff;
        }

        .success-card {
            max-width: 650px;
            margin: 60px auto;
            background: white;
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .success-header {
            text-align: center;
            padding: 35px 25px;
            background: #eef7f1;
        }

        .success-icon {
            width: 70px;
            height: 70px;
            margin: auto;
            border-radius: 50%;
            background: #198754;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
        }

        .success-header h2 {
            margin-top: 18px;
            font-weight: 700;
        }

        .order-details {
            padding: 30px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 12px 0;
            border-bottom: 1px solid #eeeeee;
        }

        .detail-label {
            color: #666;
        }

        .detail-value {
            font-weight: 600;
            text-align: right;
        }

        .status {
            color: #198754;
        }

        .total {
            font-size: 20px;
            font-weight: 700;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="success-card">

        <!-- Success Header -->

        <div class="success-header">

            <div class="success-icon">
                ✓
            </div>

            <h2>
                Order Placed Successfully!
            </h2>

            <p class="text-muted mb-0">
                Thank you for ordering with MedicineFinder.
            </p>

        </div>


        <!-- Order Details -->

        <div class="order-details">

            <div class="detail-row">

                <span class="detail-label">
                    Order ID
                </span>

                <span class="detail-value">
                    #<?php echo htmlspecialchars($order["order_id"]); ?>
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Medicine
                </span>

                <span class="detail-value">
                    <?php echo htmlspecialchars($order["medicine_name"]); ?>
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Generic Name
                </span>

                <span class="detail-value">
                    <?php echo htmlspecialchars($order["generic_name"]); ?>
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Quantity
                </span>

                <span class="detail-value">
                    <?php echo htmlspecialchars($order["quantity"]); ?> Pack(s)
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Price per Pack
                </span>

                <span class="detail-value">
                    ₹<?php echo htmlspecialchars($order["price"]); ?>
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Total Amount
                </span>

                <span class="detail-value total">
                    ₹<?php echo number_format($total_price, 2); ?>
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Order Status
                </span>

                <span class="detail-value status">
                    <?php echo htmlspecialchars($order["order_status"]); ?>
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Delivery Address
                </span>

                <span class="detail-value">
                    <?php echo nl2br(htmlspecialchars($order["delivery_address"])); ?>
                </span>

            </div>


            <!-- Buttons -->

            <div class="mt-4">

                <a href="index.php"
                   class="btn btn-primary w-100">

                    Continue Shopping

                </a>

                <a href="search.php?medicine=<?php echo urlencode($order["medicine_name"]); ?>"
                   class="btn btn-outline-secondary w-100 mt-2">

                    Search Medicine Again

                </a>

            </div>

        </div>

    </div>

</div>

</body>

</html>