
<?php
session_start();

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: pages/prescription.html");
    exit;
}

if (!isset($_FILES["prescription"]) ||
    $_FILES["prescription"]["error"] !== UPLOAD_ERR_OK) {
    $message = "Please select a file and try again.";
} else {
    $file = $_FILES["prescription"];
    $maxSize = 5 * 1024 * 1024; // 5 MB

    if ($file["size"] <= 0 || $file["size"] > $maxSize) {
        $message = "File must be larger than 0 bytes and no bigger than 5 MB.";
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file["tmp_name"]);

        $allowedTypes = [
            "image/jpeg" => "jpg",
            "image/png"  => "png",
            "application/pdf" => "pdf"
        ];

        if (!isset($allowedTypes[$mime])) {
            $message = "Only JPG, PNG, and PDF files are allowed.";
        } else {
            // Store outside htdocs so files are not directly public.
            $uploadDir = dirname(__DIR__,3) . DIRECTORY_SEPARATOR
                       . "medicinefinder_private_uploads";

            if (!is_dir($uploadDir) &&
                !mkdir($uploadDir, 0700, true)) {
                $message = "Upload folder could not be created.";
            } else {
                $fileName = bin2hex(random_bytes(16))
                          . "." . $allowedTypes[$mime];

                $destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

                if (move_uploaded_file($file["tmp_name"], $destination)) {
                    $message = "Prescription uploaded successfully. "
                             . "Pharmacist review is not connected yet.";
                    $success = true;
                } else {
                    $message = "Upload failed. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Upload | MedicineFinder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card border-0 shadow-sm rounded-4 mx-auto p-4"
             style="max-width: 600px;">
            <h1 class="h3 mb-3">Prescription Upload</h1>

            <div class="alert <?php echo $success ? 'alert-success' : 'alert-warning'; ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
            </div>

            <a href="pages/prescription.html" class="btn btn-success rounded-pill">
                Back to Prescription Help
            </a>
            <a href="index.php" class="btn btn-outline-secondary rounded-pill mt-2">
                Back to Home
            </a>
        </div>
    </div>
</body>
</html>
