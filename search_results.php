
<?php
require_once "config.php";

// Get the medicine name sent by JavaScript
$medicine = trim($_GET["medicine"] ?? "");

if ($medicine === "") {
    echo '<div class="alert alert-warning">Please enter a medicine name.</div>';
    exit;
}

// Search medicines using a prepared statement
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

if ($result->num_rows === 0) {
    $allMedicines = $conn->query("
        SELECT medicine_id, medicine_name, generic_name, category, price
        FROM medicines
    ");

    $suggestions = [];
    $typedMedicine = strtolower(trim($medicine));

    while ($item = $allMedicines->fetch_assoc()) {
        $name = strtolower($item["medicine_name"] ?? "");
        $generic = strtolower($item["generic_name"] ?? "");

        // Compare without dosage text, such as 500mg
        $name = preg_replace('/\s*\d+\s*(mg|mcg|g|ml|iu)\b/i', '', $name);
        $generic = preg_replace('/\s*\d+\s*(mg|mcg|g|ml|iu)\b/i', '', $generic);

        $distance = levenshtein($typedMedicine, trim($name));

        if ($generic !== "") {
            $distance = min(
                $distance,
                levenshtein($typedMedicine, trim($generic))
            );
        }

        // Also compare after removing spaces
        $typedCompact = str_replace(' ', '', $typedMedicine);
        $nameCompact = str_replace(' ', '', trim($name));
        $compactDistance = levenshtein($typedCompact, $nameCompact);

        if ($generic !== "") {
            $genericCompact = str_replace(' ', '', trim($generic));
            $compactDistance = min(
                $compactDistance,
                levenshtein($typedCompact, $genericCompact)
            );
        }

        $distance = min($distance, $compactDistance);
        $maxDistance = strlen($typedCompact) <= 5 ? 2 : 3;

        if ($distance <= $maxDistance) {
            $item["distance"] = $distance;
            $suggestions[] = $item;
        }
    }

    usort($suggestions, function ($a, $b) {
        return $a["distance"] <=> $b["distance"];
    });

    echo '<div class="alert alert-warning">No exact match found for <strong>'
        . htmlspecialchars($medicine, ENT_QUOTES, "UTF-8")
        . '</strong>.</div>';

    if (!empty($suggestions)) {
        echo '<div class="medicine-suggestions">';
        echo '<h4>Did you mean?</h4>';

        foreach (array_slice($suggestions, 0, 3) as $item) {
            echo '<button type="button"
                class="btn btn-outline-primary d-block w-100 text-start mb-2 medicine-suggestion"
                data-medicine="'
                . htmlspecialchars($item["medicine_name"], ENT_QUOTES, "UTF-8")
                . '">';

            echo '<strong>'
                . htmlspecialchars($item["medicine_name"], ENT_QUOTES, "UTF-8")
                . '</strong>';

            echo ' — ₹' . number_format((float)$item["price"], 2);
            echo '</button>';
        }

        echo '</div>';
    } else {
        echo '<p>No close spelling suggestions found. Try another spelling.</p>';
    }

    exit;
}
?>

<div class="medicine-results-heading mb-4">
    <h3>Search Results</h3>
    <p class="text-muted mb-0">
        Medicines matching "<?php echo htmlspecialchars($medicine, ENT_QUOTES, "UTF-8"); ?>"
    </p>
</div>

<div class="row g-4">
<?php while ($row = $result->fetch_assoc()): ?>

    <?php
    // Get pharmacy information for this medicine
    $availability_stmt = $conn->prepare("
        SELECT p.pharmacy_name, p.address, p.city, p.phone,
               mp.availability
        FROM medicine_pharmacy mp
        JOIN pharmacies p
          ON mp.pharmacy_id = p.pharmacy_id
        WHERE mp.medicine_id = ?
    ");

    $availability_stmt->bind_param("i", $row["medicine_id"]);
    $availability_stmt->execute();
    $pharmacy_result = $availability_stmt->get_result();

    $safe_name = htmlspecialchars($row["medicine_name"] ?? "", ENT_QUOTES, "UTF-8");
    $safe_generic = htmlspecialchars($row["generic_name"] ?? "", ENT_QUOTES, "UTF-8");
    $safe_category = htmlspecialchars($row["category"] ?? "", ENT_QUOTES, "UTF-8");
    $safe_description = htmlspecialchars($row["description"] ?? "", ENT_QUOTES, "UTF-8");
    $safe_uses = htmlspecialchars($row["uses"] ?? "", ENT_QUOTES, "UTF-8");
    $safe_availability = htmlspecialchars($row["availability"] ?? "Unknown", ENT_QUOTES, "UTF-8");

    $price = is_numeric($row["price"]) ? (float)$row["price"] : 0;
    ?>

    <div class="col-md-6 col-xl-4">
        <div class="card medicine-result-card h-100 shadow-sm">
            <div class="card-body d-flex flex-column">

                <span class="medicine-category">
                    <?php echo $safe_category ?: "Medicine"; ?>
                </span>

                <h4 class="mt-3">
                    <?php echo $safe_name; ?>
                </h4>

                <p class="text-muted mb-2">
                    Generic: <?php echo $safe_generic ?: "Not specified"; ?>
                </p>

                <p><?php echo $safe_description ?: "No description available."; ?></p>

                <p>
                    <strong>Common uses:</strong><br>
                    <?php echo $safe_uses ?: "Not specified"; ?>
                </p>

                <div class="medicine-price mb-2">
                    ₹<?php echo number_format($price, 2); ?>
                </div>

                <p>
                    <strong>Catalogue status:</strong>
                    <?php echo $safe_availability; ?>
                </p>

                <hr>

                <h5>Pharmacy Availability</h5>

                <?php if ($pharmacy_result->num_rows > 0): ?>
                    <?php while ($pharmacy = $pharmacy_result->fetch_assoc()): ?>

                        <?php
                        $pharmacy_available =
                            strcasecmp(trim($pharmacy["availability"] ?? ""), "Available") === 0;
                        ?>

                        <div class="pharmacy-result-item mb-3">
                            <strong>
                                <?php echo htmlspecialchars($pharmacy["pharmacy_name"] ?? "", ENT_QUOTES, "UTF-8"); ?>
                            </strong>

                            <p class="small text-muted mb-1">
                                <?php echo htmlspecialchars($pharmacy["address"] ?? "", ENT_QUOTES, "UTF-8"); ?>,
                                <?php echo htmlspecialchars($pharmacy["city"] ?? "", ENT_QUOTES, "UTF-8"); ?>
                            </p>

                            <p class="small mb-2">
                                Phone:
                                <?php echo htmlspecialchars($pharmacy["phone"] ?? "Not listed", ENT_QUOTES, "UTF-8"); ?>
                            </p>

                            <?php if ($pharmacy_available): ?>
                                <span class="badge bg-success">Listed as available</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Listed as out of stock</span>
                            <?php endif; ?>
                        </div>

                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">
                        No pharmacy listings found for this medicine.
                    </p>
                <?php endif; ?>

                <?php if (strcasecmp(trim($row["availability"] ?? ""), "Available") === 0): ?>
                    <a
                        href="order.php?medicine_id=<?php echo (int)$row["medicine_id"]; ?>"
                        class="btn btn-primary w-100 mt-auto"
                    >
                        🛒 Buy Medicine
                    </a>
                <?php else: ?>
                    <div class="alert alert-warning mt-auto mb-0">
                        This medicine is marked unavailable in the catalogue.
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <?php $availability_stmt->close(); ?>
<?php endwhile; ?>
</div>

<?php
$stmt->close();
?>
