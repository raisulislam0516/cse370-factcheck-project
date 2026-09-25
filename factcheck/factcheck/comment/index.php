<?php
require '../includes/auth_check.php';
require '../config/db.php';

$rows = $pdo->query("
    SELECT COMMENT.*, USER.user_name, CLAIM.claim_text
    FROM COMMENT
    JOIN USER ON COMMENT.user_id = USER.user_id
    JOIN CLAIM ON COMMENT.claim_id = CLAIM.claim_id
    ORDER BY COMMENT.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$prefix = '../';
$active = 'comment';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Comments - TruthCheck</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="app-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
        <div class="container">
        <div class="page-title"><h2>💬 সব Comment</h2></div>
        <table class="data-table">
            <tr><th>Comment</th><th>By</th><th>On Claim</th><th>Date</th><th>Action</th></tr>
            <?php foreach ($rows as $r):
                $reactions = json_decode($r['reactions'] ?? '{}', true) ?: [];
            ?>
            <tr>
                <td><?= htmlspecialchars($r['comment_text']) ?>
                    <?php if ($reactions): ?>
                        <div style="font-size:12px;color:#888;margin-top:4px">
                            <?php foreach ($reactions as $e => $c): ?><?= $e ?> <?= $c ?> &nbsp;<?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['user_name']) ?></td>
                <td><?= htmlspecialchars(mb_strimwidth($r['claim_text'], 0, 35, '...')) ?></td>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
                <td><a href="../claims/view.php?id=<?= $r['claim_id'] ?>#comments">Open Claim</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?><tr><td colspan="5" style="text-align:center;color:#999">এখনো কোনো comment নেই</td></tr><?php endif; ?>
        </table>
        </div>
        </div>
    </div>
</body>
</html>
