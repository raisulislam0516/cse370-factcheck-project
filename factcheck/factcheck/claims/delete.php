<?php
require '../includes/auth_check.php';
require '../config/db.php';

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM CLAIM WHERE claim_id = ?");
$stmt->execute([$id]);
$claim = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$claim) {
    die("Claim পাওয়া যায়নি।");
}

// only the owner or an admin can delete
if ($claim['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin') {
    die("আপনার এই Claim delete করার অনুমতি নেই।");
}

// DELETE
$stmt = $pdo->prepare("DELETE FROM CLAIM WHERE claim_id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit();
?>
