<?php
require '../includes/auth_check.php';
require '../config/db.php';

$rows = $pdo->query("
    SELECT EVIDENCE.*, CLAIM.claim_text, SOURCE.source_name
    FROM EVIDENCE
    JOIN CLAIM ON EVIDENCE.claim_id = CLAIM.claim_id
    LEFT JOIN SOURCE ON EVIDENCE.source_id = SOURCE.source_id
    ORDER BY EVIDENCE.evidence_id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$prefix = '../';
$active = 'evidence';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Evidence - TruthCheck</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="app-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
        <div class="container">
        <div class="page-title"><h2>📄 সব Evidence</h2></div>
        <table class="data-table">
            <tr><th>Evidence</th><th>Type</th><th>Source</th><th>Claim</th><th>Action</th></tr>
            <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['evidence_text']) ?></td>
                <td><?= htmlspecialchars($r['evidence_type']) ?></td>
                <td><?= htmlspecialchars($r['source_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars(mb_strimwidth($r['claim_text'], 0, 40, '...')) ?></td>
                <td><a href="../claims/view.php?id=<?= $r['claim_id'] ?>">Open Claim</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?><tr><td colspan="5" style="text-align:center;color:#999">এখনো কোনো evidence যোগ করা হয়নি</td></tr><?php endif; ?>
        </table>
        <p style="font-size:13px;color:#888;margin-top:12px">নতুন evidence যোগ করতে হলে কোনো claim-এর Details পেজে যান।</p>
        </div>
        </div>
    </div>
</body>
</html>
