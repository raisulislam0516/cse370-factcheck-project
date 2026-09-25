<?php
require 'includes/auth_check.php';
require 'config/db.php';

$total = $pdo->query("SELECT COUNT(*) FROM CLAIM")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM CLAIM WHERE status='pending'")->fetchColumn();
$verified = $pdo->query("SELECT COUNT(*) FROM CLAIM WHERE status='verified'")->fetchColumn();
$rejected = $pdo->query("SELECT COUNT(*) FROM CLAIM WHERE status='rejected'")->fetchColumn();
$mySubmitted = $pdo->prepare("SELECT COUNT(*) FROM CLAIM WHERE user_id = ?");
$mySubmitted->execute([$_SESSION['user_id']]);
$mySubmitted = $mySubmitted->fetchColumn();

$recent = $pdo->query("
    SELECT CLAIM.*, USER.user_name FROM CLAIM
    JOIN USER ON CLAIM.user_id = USER.user_id
    ORDER BY submitted_at DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$prefix = '';
$active = 'dashboard';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - TruthCheck</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="app-layout">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <div class="container">
                <div class="hero-banner">
                    <h2>👋 স্বাগতম, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
                    <p>Claim submit করুন, অথবা এমনিতেই সবার জমা দেওয়া Claim গুলো ঘুরে দেখুন — Claim submit করা বাধ্যতামূলক না।</p>
                    <div class="hero-actions">
                        <a href="claims/index.php" class="hero-btn">🔍 সব Claim দেখুন</a>
                        <a href="claims/create.php" class="hero-btn ghost">➕ নতুন Claim জমা দিন</a>
                    </div>
                </div>

                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="num"><?= $total ?></div>
                        <div class="label">মোট Claim</div>
                    </div>
                    <div class="stat-card pending">
                        <div class="num"><?= $pending ?></div>
                        <div class="label">⏳ Pending</div>
                    </div>
                    <div class="stat-card verified">
                        <div class="num"><?= $verified ?></div>
                        <div class="label">✅ Verified</div>
                    </div>
                    <div class="stat-card rejected">
                        <div class="num"><?= $rejected ?></div>
                        <div class="label">❌ Rejected</div>
                    </div>
                    <div class="stat-card">
                        <div class="num"><?= $mySubmitted ?></div>
                        <div class="label">👤 আপনার জমা দেওয়া</div>
                    </div>
                </div>

                <div class="dash-section">
                    <h3>দ্রুত অপশন</h3>
                    <div class="quick-links">
                        <a href="claims/create.php">➕ নতুন Claim জমা দিন</a>
                        <a href="claims/index.php">📋 সব Claim দেখুন</a>
                        <a href="source/index.php">🔖 Source Manage করুন</a>
                    </div>
                </div>

                <div class="dash-section">
                    <h3>সাম্প্রতিক Claim</h3>
                    <table class="data-table">
                        <tr><th>Claim</th><th>Status</th><th>By</th><th></th></tr>
                        <?php foreach ($recent as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars(mb_strimwidth($c['claim_text'], 0, 60, '...')) ?></td>
                            <td><span class="status status-<?= $c['status'] ?>"><?= $c['status'] ?></span></td>
                            <td><?= htmlspecialchars($c['user_name']) ?></td>
                            <td><a href="claims/view.php?id=<?= $c['claim_id'] ?>">Details</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
