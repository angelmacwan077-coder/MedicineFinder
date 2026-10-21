
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
           category, description, uses, price, availability,image_path
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


?>

<div class="mf-results-page">
    <div class="mf-results-header">
        <div>
            <span class="mf-eyebrow">MEDICINEFINDER PHARMACY</span>
            <h2>Find your medicine</h2>
            <p>
                Search results for
                <strong>"<?php echo htmlspecialchars($medicine, ENT_QUOTES, "UTF-8"); ?>"</strong>
            </p>
        </div>
        <div class="mf-results-count">
            <i class="bi bi-capsule"></i>
            <span>Matching medicines</span>
        </div>
    </div>

    <div class="mf-product-list">

    <?php while ($row = $result->fetch_assoc()): ?>
        <?php
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
        $safe_availability = trim($row["availability"] ?? "Unknown");

        $price = is_numeric($row["price"]) ? (float)$row["price"] : 0;

        $is_available = strcasecmp($safe_availability, "Available") === 0;
        ?>

        <article class="mf-product-card">

            <div class="mf-product-visual">
                <div class="mf-product-placeholder">
                    <i class="bi bi-capsule-pill"></i>
                </div>
                <span class="mf-product-category">
                    <?php echo $safe_category ?: "Medicine"; ?>
                </span>
            </div>

            <div class="mf-product-info">
                <h3><?php echo $safe_name; ?></h3>

                <p class="mf-generic">
                    Generic name:
                    <strong><?php echo $safe_generic ?: "Not specified"; ?></strong>
                </p>

                <p class="mf-description">
                    <?php echo $safe_description ?: "Medicine details are not available yet."; ?>
                </p>

                <?php if (!empty($safe_uses)): ?>
                    <p class="mf-uses">
                        <i class="bi bi-info-circle"></i>
                        <?php echo $safe_uses; ?>
                    </p>
                <?php endif; ?>

                <div class="mf-stock-status">
                    <span class="mf-status-dot <?php echo $is_available ? 'is-available' : 'is-unavailable'; ?>"></span>
                    <?php echo $is_available ? "Listed as available" : "Catalogue: " . htmlspecialchars($safe_availability, ENT_QUOTES, "UTF-8"); ?>
                </div>
            </div>

            <div class="mf-product-purchase">
                <span class="mf-price-label">Catalogue price</span>
                <div class="mf-price">
                    ₹<?php echo number_format($price, 2); ?>
                </div>

                <?php if ($is_available): ?>
                    <a href="order.php?medicine_id=<?php echo (int)$row["medicine_id"]; ?>"
                       class="mf-buy-button">
                        <i class="bi bi-cart3"></i>
                        Buy Medicine
                    </a>
                <?php else: ?>
                    <button class="mf-buy-button mf-disabled-button" disabled>
                        Currently unavailable
                    </button>
                <?php endif; ?>
            </div>

            <div class="mf-pharmacy-section">
                <h4>
                    <i class="bi bi-shop"></i>
                    Pharmacy availability
                </h4>

                <?php if ($pharmacy_result->num_rows > 0): ?>
                    <div class="mf-pharmacy-list">
                    <?php while ($pharmacy = $pharmacy_result->fetch_assoc()): ?>
                        <?php
                        $pharmacy_available =
                            strcasecmp(trim($pharmacy["availability"] ?? ""), "Available") === 0;
                        ?>
                        <div class="mf-pharmacy-item">
                            <div class="mf-pharmacy-icon">
                                <i class="bi bi-hospital"></i>
                            </div>

                            <div class="mf-pharmacy-details">
                                <strong>
                                    <?php echo htmlspecialchars($pharmacy["pharmacy_name"] ?? "", ENT_QUOTES, "UTF-8"); ?>
                                </strong>
                                <p>
                                    <?php echo htmlspecialchars($pharmacy["address"] ?? "", ENT_QUOTES, "UTF-8"); ?>,
                                    <?php echo htmlspecialchars($pharmacy["city"] ?? "", ENT_QUOTES, "UTF-8"); ?>
                                </p>
                                <small>
                                    Phone:
                                    <?php echo htmlspecialchars($pharmacy["phone"] ?? "Not listed", ENT_QUOTES, "UTF-8"); ?>
                                </small>
                            </div>

                            <span class="mf-pharmacy-badge <?php echo $pharmacy_available ? 'available' : 'unavailable'; ?>">
                                <?php echo $pharmacy_available ? "Available" : "Out of stock"; ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="mf-no-pharmacy">
                        No pharmacy listings found for this medicine.
                    </p>
                <?php endif; ?>
            </div>

        </article>

        <?php $availability_stmt->close(); ?>
    <?php endwhile; ?>

    </div>
</div>

<?php
$stmt->close();
?>
