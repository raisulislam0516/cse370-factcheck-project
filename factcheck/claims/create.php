<?php
require '../includes/auth_check.php';
require '../config/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claim_text = trim($_POST['claim_text']);
    $category = trim($_POST['category']);

    if ($claim_text !== "") {
        // Step 1: Create the claim itself
        $stmt = $pdo->prepare("INSERT INTO CLAIM (claim_text, category, status, user_id) VALUES (?, ?, 'pending', ?)");
        $stmt->execute([$claim_text, $category, $_SESSION['user_id']]);
        $newClaimId = $pdo->lastInsertId();

        // Step 2: If a screenshot/image was attached, upload it and link it to this claim
        if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $newName = uniqid('media_') . '.' . $ext;
                $destination = '../uploads/' . $newName;
                if (move_uploaded_file($_FILES['media_file']['tmp_name'], $destination)) {
                    $fileUrl = 'uploads/' . $newName;
                    $stmt = $pdo->prepare("INSERT INTO MEDIA (media_type, verification_status, file_url, claim_id) VALUES ('image', 'unverified', ?, ?)");
                    $stmt->execute([$fileUrl, $newClaimId]);
                }
            } else {
                $error = "Claim জমা হয়েছে, কিন্তু ছবির ফরম্যাট সঠিক না (শুধু jpg, png, gif, webp সাপোর্ট করে)।";
            }
        }

        if (!$error) {
            header("Location: view.php?id=" . $newClaimId);
            exit();
        }
    } else {
        $error = "Claim এর বিবরণ লিখুন।";
    }
}
$prefix = '../';
$active = 'new_claim';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>New Claim - TruthCheck</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="app-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
        <div class="container">
        <div class="page-title"><h2>➕ নতুন Claim জমা দিন</h2></div>
        <?php if ($error): ?><p class="error" style="max-width:500px"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="form-box" style="margin:0;max-width:500px">
            <label>Claim এর বিবরণ</label>
            <textarea name="claim_text" placeholder="Claim এর বিবরণ লিখুন..." required></textarea>

            <label>Category</label>
            <input type="text" name="category" placeholder="যেমন: Health, Politics, Science">

            <label>Screenshot / প্রমাণ (ঐচ্ছিক)</label>
            <input type="file" name="media_file" accept="image/*">
            <p style="font-size:12px;color:#888;margin:-6px 0 0">Claim সম্পর্কিত কোনো screenshot বা ছবি থাকলে সরাসরি এখানেই যোগ করতে পারেন (jpg, png, gif, webp)</p>

            <button type="submit">Submit Claim</button>
        </form>
        <p style="margin-top:14px"><a href="index.php">← ফিরে যান</a></p>
        </div>
        </div>
    </div>
</body>
</html>
