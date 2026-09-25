<?php
require '../includes/auth_check.php';
require '../config/db.php';

$rows = $pdo->query("
    SELECT MEDIA.*, CLAIM.claim_text
    FROM MEDIA
    JOIN CLAIM ON MEDIA.claim_id = CLAIM.claim_id
    ORDER BY MEDIA.media_id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$prefix = '../';
$active = 'media';

function isImg($p) { return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $p); }
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Media - TruthCheck</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="app-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
        <div class="container">
        <div class="page-title"><h2>🖼️ সব Media / Screenshot</h2></div>
        <div class="stat-grid">
            <?php foreach ($rows as $r): ?>
            <div class="stat-card" style="border-top-color: var(--navy)">
                <?php if (isImg($r['file_url'])): ?>
                    <a href="../<?= htmlspecialchars($r['file_url']) ?>" target="_blank">
                        <img src="../<?= htmlspecialchars($r['file_url']) ?>" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:8px">
                    </a>
                <?php endif; ?>
                <div style="font-size:13px;font-weight:600"><?= htmlspecialchars($r['media_type']) ?></div>
                <div class="label"><?= htmlspecialchars(mb_strimwidth($r['claim_text'], 0, 40, '...')) ?></div>
                <a href="../claims/view.php?id=<?= $r['claim_id'] ?>" style="font-size:12px">Open Claim →</a>
            </div>
            <?php endforeach; ?>
            <?php if (!$rows): ?><p style="color:#999">এখনো কোনো media আপলোড করা হয়নি</p><?php endif; ?>
        </div>
        <p style="font-size:13px;color:#888">নতুন media/screenshot আপলোড করতে হলে কোনো claim-এর Details পেজে যান।</p>
        </div>
        </div>
    </div>
</body>
</html>
