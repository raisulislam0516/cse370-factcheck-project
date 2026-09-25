<?php
require '../includes/auth_check.php';
require '../config/db.php';

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM CLAIM WHERE claim_id = ?");
$stmt->execute([$id]);
$claim = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$claim) { die("Claim পাওয়া যায়নি।"); }
if ($claim['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin') {
    die("আপনার এই Claim edit করার অনুমতি নেই।");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claim_text = trim($_POST['claim_text']);
    $category = trim($_POST['category']);
    $status = trim($_POST['status']);
    $stmt = $pdo->prepare("UPDATE CLAIM SET claim_text = ?, category = ?, status = ? WHERE claim_id = ?");
    $stmt->execute([$claim_text, $category, $status, $id]);
    header("Location: index.php");
    exit();
}
$prefix = '../';
$active = 'claims';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Edit Claim - TruthCheck</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="app-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
        <div class="container">
        <div class="page-title"><h2>Claim Edit করুন</h2></div>
        <form method="POST" class="form-box" style="margin:0;max-width:500px">
            <label>Claim এর বিবরণ</label>
            <textarea name="claim_text" required><?= htmlspecialchars($claim['claim_text']) ?></textarea>
            <label>Category</label>
            <input type="text" name="category" value="<?= htmlspecialchars($claim['category']) ?>">

            <?php if ($_SESSION['role'] === 'admin'): ?>
            <label>Status</label>
            <select name="status">
                <option value="pending" <?= $claim['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="verified" <?= $claim['status'] === 'verified' ? 'selected' : '' ?>>Verified</option>
                <option value="rejected" <?= $claim['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
            <?php else: ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($claim['status']) ?>">
            <?php endif; ?>

            <button type="submit">Update</button>
        </form>
        <p style="margin-top:14px"><a href="index.php">← ফিরে যান</a></p>
        </div>
        </div>
    </div>
</body>
</html>
