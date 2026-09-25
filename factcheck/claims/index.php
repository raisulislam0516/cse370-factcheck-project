<?php
require '../includes/auth_check.php';
require '../config/db.php';

// Admin: toggle flagged (means "reviewed/checked" by admin)
if (isset($_GET['toggle_flag']) && $_SESSION['role'] === 'admin') {
    try {
        $stmt = $pdo->prepare("UPDATE CLAIM SET flagged = NOT flagged WHERE claim_id = ?");
        $stmt->execute([$_GET['toggle_flag']]);
    } catch (PDOException $e) {
        die("Flag ফিচার কাজ করছে না। এর মানে database-এ এখনো 'flagged' কলাম যোগ হয়নি। phpMyAdmin এ গিয়ে migration_add_flagged.sql ফাইলের SQL টা রান করুন। (Error: " . htmlspecialchars($e->getMessage()) . ")");
    }
    header("Location: index.php" . (isset($_GET['filter']) ? "?filter=".$_GET['filter'] : ""));
    exit();
}

$filter = $_GET['filter'] ?? null;
$base = "SELECT CLAIM.*, USER.user_name FROM CLAIM JOIN USER ON CLAIM.user_id = USER.user_id";
if ($filter === 'pending') {
    $stmt = $pdo->query("$base WHERE status = 'pending' ORDER BY CLAIM.submitted_at DESC");
} elseif ($filter === 'verified') {
    $stmt = $pdo->query("$base WHERE status = 'verified' ORDER BY CLAIM.submitted_at DESC");
} elseif ($filter === 'rejected') {
    $stmt = $pdo->query("$base WHERE status = 'rejected' ORDER BY CLAIM.submitted_at DESC");
} elseif ($filter === 'flagged') {
    $stmt = $pdo->query("$base WHERE flagged = 1 ORDER BY CLAIM.submitted_at DESC");
} else {
    $stmt = $pdo->query("$base ORDER BY CLAIM.submitted_at DESC");
}
$claims = $stmt->fetchAll(PDO::FETCH_ASSOC);
$prefix = '../';
$active = ($filter === 'pending') ? 'pending' : (($filter === 'flagged') ? 'flagged' : 'claims');

function filterChip($label, $key, $current, $filter_val) {
    $activeClass = ($current === $filter_val) ? 'active' : '';
    $href = $filter_val ? "index.php?filter=$filter_val" : "index.php";
    return "<a href=\"$href\" class=\"filter-chip $activeClass\">$label</a>";
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>সব Claim - TruthCheck</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="app-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
        <div class="container">

        <div class="feed-toolbar">
            <div>
                <h2 style="margin:0;color:var(--navy)">🔍 সব Claim ঘুরে দেখুন</h2>
                <p style="margin:4px 0 0;color:var(--text-muted);font-size:13px">যে কেউ Claim submit না করেও এখানে সব Claim দেখতে পারবেন</p>
            </div>
            <a class="btn" href="create.php">+ নতুন Claim</a>
        </div>

        <div class="feed-toolbar">
            <div class="filters">
                <?= filterChip('সবগুলো', 'all', $filter, null) ?>
                <?= filterChip('⏳ Pending', 'pending', $filter, 'pending') ?>
                <?= filterChip('✅ Verified', 'verified', $filter, 'verified') ?>
                <?= filterChip('❌ Rejected', 'rejected', $filter, 'rejected') ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <?= filterChip('🚩 Flagged', 'flagged', $filter, 'flagged') ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$claims): ?>
            <div class="empty-state">
                <div class="emoji">🕵️‍♀️</div>
                <p>এই মুহূর্তে কোনো Claim পাওয়া যায়নি।</p>
            </div>
        <?php else: ?>
        <div class="claim-feed">
            <?php foreach ($claims as $c): ?>
            <div class="claim-card">
                <div class="claim-card-top">
                    <span class="claim-card-cat"><?= htmlspecialchars($c['category'] ?: 'General') ?></span>
                    <span class="status status-<?= htmlspecialchars($c['status']) ?>"><?= htmlspecialchars($c['status']) ?></span>
                </div>
                <div class="claim-card-text"><?= htmlspecialchars($c['claim_text']) ?></div>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="index.php?toggle_flag=<?= $c['claim_id'] ?><?= $filter ? '&filter='.$filter : '' ?>" style="text-decoration:none;align-self:flex-start">
                    <span class="flag-badge <?= $c['flagged'] ? 'is-flagged' : '' ?>">
                        <?= $c['flagged'] ? '🚩 Flagged' : '⬜ Mark Checked' ?>
                    </span>
                </a>
                <?php endif; ?>
                <div class="claim-card-footer">
                    <span>👤 <?= htmlspecialchars($c['user_name']) ?> · <?= date('d M, Y', strtotime($c['submitted_at'])) ?></span>
                    <div class="claim-card-actions">
                        <a href="view.php?id=<?= $c['claim_id'] ?>">Details →</a>
                    </div>
                </div>
                <?php if ($c['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] === 'admin'): ?>
                <div style="display:flex;gap:10px">
                    <a class="icon-link" href="edit.php?id=<?= $c['claim_id'] ?>">✏️ Edit</a>
                    <a class="icon-link" href="delete.php?id=<?= $c['claim_id'] ?>" onclick="return confirm('আপনি কি নিশ্চিত?')">🗑️ Delete</a>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        </div>
        </div>
    </div>
</body>
</html>
