<?php
require '../includes/auth_check.php';
require '../config/db.php';

$claim_id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT CLAIM.*, USER.user_name FROM CLAIM JOIN USER ON CLAIM.user_id = USER.user_id WHERE claim_id = ?");
$stmt->execute([$claim_id]);
$claim = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$claim) { die("Claim পাওয়া যায়নি।"); }

// Admin: toggle flag from claim detail page too
if (isset($_GET['toggle_flag']) && $_SESSION['role'] === 'admin') {
    try {
        $pdo->prepare("UPDATE CLAIM SET flagged = NOT flagged WHERE claim_id = ?")->execute([$claim_id]);
    } catch (PDOException $e) {
        die("Flag ফিচার কাজ করছে না। database-এ এখনো 'flagged' কলাম যোগ হয়নি। phpMyAdmin এ গিয়ে migration_add_flagged.sql রান করুন।");
    }
    header("Location: view.php?id=$claim_id"); exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_evidence'])) {
    $stmt = $pdo->prepare("INSERT INTO EVIDENCE (evidence_text, evidence_type, claim_id, source_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['evidence_text'], $_POST['evidence_type'], $claim_id, $_POST['source_id'] ?: null]);
    header("Location: view.php?id=$claim_id"); exit();
}
if (isset($_GET['delete_evidence'])) {
    $stmt = $pdo->prepare("DELETE FROM EVIDENCE WHERE evidence_id = ? AND claim_id = ?");
    $stmt->execute([$_GET['delete_evidence'], $claim_id]);
    header("Location: view.php?id=$claim_id"); exit();
}

// ---------- MEDIA: Create (real file upload, e.g. a screenshot) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_media'])) {
    $file_url = null;
    $media_type = trim($_POST['media_type']) ?: 'image';

    if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg','jpeg','png','gif','webp','mp4'];
        $ext = strtolower(pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newName = uniqid('media_') . '.' . $ext;
            $destination = '../uploads/' . $newName;
            if (move_uploaded_file($_FILES['media_file']['tmp_name'], $destination)) {
                $file_url = 'uploads/' . $newName; // stored relative to site root
            }
        }
    }
    // fallback: if no file uploaded, allow a pasted URL instead
    if (!$file_url && !empty($_POST['file_url'])) {
        $file_url = trim($_POST['file_url']);
    }

    if ($file_url) {
        $stmt = $pdo->prepare("INSERT INTO MEDIA (media_type, verification_status, file_url, claim_id) VALUES (?, 'unverified', ?, ?)");
        $stmt->execute([$media_type, $file_url, $claim_id]);
    }
    header("Location: view.php?id=$claim_id"); exit();
}
if (isset($_GET['delete_media'])) {
    $stmt = $pdo->prepare("DELETE FROM MEDIA WHERE media_id = ? AND claim_id = ?");
    $stmt->execute([$_GET['delete_media'], $claim_id]);
    header("Location: view.php?id=$claim_id"); exit();
}

// ---------- COMMENT: Create ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $stmt = $pdo->prepare("INSERT INTO COMMENT (comment_text, claim_id, user_id) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['comment_text'], $claim_id, $_SESSION['user_id']]);
    header("Location: view.php?id=$claim_id"); exit();
}
if (isset($_GET['delete_comment'])) {
    $stmt = $pdo->prepare("DELETE FROM COMMENT WHERE comment_id = ? AND (user_id = ? OR ? = 'admin')");
    $stmt->execute([$_GET['delete_comment'], $_SESSION['user_id'], $_SESSION['role']]);
    header("Location: view.php?id=$claim_id"); exit();
}

// COMMENT: React with an emoji
if (isset($_GET['react']) && isset($_GET['comment_id'])) {
    $allowedEmoji = ['👍', '❤️', '😂', '😮', '😢'];
    $emoji = $_GET['react'];
    if (in_array($emoji, $allowedEmoji)) {
        $stmt = $pdo->prepare("SELECT reactions FROM COMMENT WHERE comment_id = ?");
        $stmt->execute([$_GET['comment_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $reactions = json_decode($row['reactions'] ?? '{}', true) ?: [];
        $reactions[$emoji] = ($reactions[$emoji] ?? 0) + 1;
        $stmt = $pdo->prepare("UPDATE COMMENT SET reactions = ? WHERE comment_id = ?");
        $stmt->execute([json_encode($reactions, JSON_UNESCAPED_UNICODE), $_GET['comment_id']]);
    }
    header("Location: view.php?id=$claim_id#comments"); exit();
}

// ---------- FACT_CHECK: Create/Update ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_factcheck'])) {
    if ($_SESSION['role'] === 'fact_checker' || $_SESSION['role'] === 'admin') {
        $existing = $pdo->prepare("SELECT fact_check_id FROM FACT_CHECK WHERE claim_id = ?");
        $existing->execute([$claim_id]);
        if ($existing->fetch()) {
            $stmt = $pdo->prepare("UPDATE FACT_CHECK SET verdict=?, explanation=?, verifier_id=?, checked_at=NOW() WHERE claim_id=?");
            $stmt->execute([$_POST['verdict'], $_POST['explanation'], $_SESSION['user_id'], $claim_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO FACT_CHECK (verdict, explanation, claim_id, verifier_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['verdict'], $_POST['explanation'], $claim_id, $_SESSION['user_id']]);
        }
        $newStatus = ($_POST['verdict'] === 'True') ? 'verified' : 'rejected';
        $pdo->prepare("UPDATE CLAIM SET status = ? WHERE claim_id = ?")->execute([$newStatus, $claim_id]);
    }
    header("Location: view.php?id=$claim_id"); exit();
}
if (isset($_GET['delete_factcheck']) && $_SESSION['role'] === 'admin') {
    $pdo->prepare("DELETE FROM FACT_CHECK WHERE claim_id = ?")->execute([$claim_id]);
    header("Location: view.php?id=$claim_id"); exit();
}

// ---------- READ ----------
$evidence = $pdo->prepare("SELECT EVIDENCE.*, SOURCE.source_name FROM EVIDENCE LEFT JOIN SOURCE ON EVIDENCE.source_id = SOURCE.source_id WHERE claim_id = ?");
$evidence->execute([$claim_id]);
$evidence = $evidence->fetchAll(PDO::FETCH_ASSOC);

$media = $pdo->prepare("SELECT * FROM MEDIA WHERE claim_id = ?");
$media->execute([$claim_id]);
$media = $media->fetchAll(PDO::FETCH_ASSOC);

$comments = $pdo->prepare("SELECT COMMENT.*, USER.user_name FROM COMMENT JOIN USER ON COMMENT.user_id = USER.user_id WHERE claim_id = ? ORDER BY created_at DESC");
$comments->execute([$claim_id]);
$comments = $comments->fetchAll(PDO::FETCH_ASSOC);

$factcheck = $pdo->prepare("SELECT FACT_CHECK.*, USER.user_name AS verifier_name FROM FACT_CHECK LEFT JOIN USER ON FACT_CHECK.verifier_id = USER.user_id WHERE claim_id = ?");
$factcheck->execute([$claim_id]);
$factcheck = $factcheck->fetch(PDO::FETCH_ASSOC);

$sources = $pdo->query("SELECT * FROM SOURCE")->fetchAll(PDO::FETCH_ASSOC);
$prefix = '../';
$active = 'claims';
$quickEmojis = ['👍', '❤️', '😂', '😮', '😢'];

function isImage($path) {
    return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $path);
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Claim Detail - TruthCheck</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="app-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
        <div class="container">

        <div class="claim-hero">
            <h3><?= htmlspecialchars($claim['claim_text']) ?></h3>
            <div class="meta">
                <span>📂 <?= htmlspecialchars($claim['category']) ?></span>
                <span><span class="status status-<?= $claim['status'] ?>"><?= $claim['status'] ?></span></span>
                <span>👤 <?= htmlspecialchars($claim['user_name']) ?></span>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="view.php?id=<?= $claim_id ?>&toggle_flag=1" style="text-decoration:none">
                    <span class="flag-badge <?= $claim['flagged'] ? 'is-flagged' : '' ?>">
                        <?= $claim['flagged'] ? '🚩 Flagged (checked)' : '⬜ Mark as Checked' ?>
                    </span>
                </a>
                <?php elseif ($claim['flagged']): ?>
                    <span class="flag-badge is-flagged">🚩 Flagged</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- FACT CHECK -->
        <div class="section">
            <h3>✅ Fact-Check Verdict</h3>
            <?php if ($factcheck): ?>
                <p><b><?= htmlspecialchars($factcheck['verdict']) ?></b> — <?= htmlspecialchars($factcheck['explanation']) ?></p>
                <p style="font-size:12px;color:#888">Checked by <?= htmlspecialchars($factcheck['verifier_name']) ?> on <?= $factcheck['checked_at'] ?>
                <?php if ($_SESSION['role'] === 'admin'): ?> | <a class="del-link" href="view.php?id=<?= $claim_id ?>&delete_factcheck=1" onclick="return confirm('Delete verdict?')">Delete</a><?php endif; ?>
                </p>
            <?php else: ?>
                <p style="color:#888">এখনো কোনো verdict দেওয়া হয়নি।</p>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'fact_checker' || $_SESSION['role'] === 'admin'): ?>
            <form method="POST" class="inline-form">
                <select name="verdict" required>
                    <option value="">Select verdict</option>
                    <option value="True" <?= ($factcheck && $factcheck['verdict']==='True') ? 'selected':'' ?>>True</option>
                    <option value="False" <?= ($factcheck && $factcheck['verdict']==='False') ? 'selected':'' ?>>False</option>
                    <option value="Misleading" <?= ($factcheck && $factcheck['verdict']==='Misleading') ? 'selected':'' ?>>Misleading</option>
                </select>
                <input type="text" name="explanation" placeholder="Explanation" value="<?= $factcheck ? htmlspecialchars($factcheck['explanation']) : '' ?>">
                <button type="submit" name="save_factcheck"><?= $factcheck ? 'Update Verdict' : 'Submit Verdict' ?></button>
            </form>
            <?php endif; ?>
        </div>

        <!-- EVIDENCE -->
        <div class="section">
            <h3>📄 Evidence</h3>
            <?php foreach ($evidence as $e): ?>
                <div class="item">
                    <span><?= htmlspecialchars($e['evidence_text']) ?> (<?= htmlspecialchars($e['evidence_type']) ?>)
                    <?php if ($e['source_name']): ?> — Source: <?= htmlspecialchars($e['source_name']) ?><?php endif; ?></span>
                    <a class="del-link" href="view.php?id=<?= $claim_id ?>&delete_evidence=<?= $e['evidence_id'] ?>" onclick="return confirm('Delete?')">Delete</a>
                </div>
            <?php endforeach; ?>
            <form method="POST" class="inline-form">
                <textarea name="evidence_text" placeholder="যেমন: BBC News-এ ১৫ আগস্ট প্রকাশিত রিপোর্টে এই তথ্য পাওয়া গেছে, অথবা সরকারি ওয়েবসাইটের সার্কুলারে এই বিষয়ে উল্লেখ আছে" required style="min-height:60px"></textarea>
                <input type="text" name="evidence_type" placeholder="যেমন: News Article, Official Document, Statement">
                <select name="source_id">
                    <option value="">-- Source (optional) --</option>
                    <?php foreach ($sources as $s): ?>
                        <option value="<?= $s['source_id'] ?>"><?= htmlspecialchars($s['source_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="add_evidence">Add</button>
            </form>
            <p style="font-size:12px;margin-top:8px"><a href="../source/index.php">নতুন Source যোগ করুন →</a></p>
        </div>

        <!-- MEDIA / SCREENSHOT -->
        <div class="section">
            <h3>🖼️ Media / Screenshot</h3>
            <?php foreach ($media as $m): ?>
                <div class="item">
                    <?php if (isImage($m['file_url'])): ?>
                        <a href="../<?= htmlspecialchars($m['file_url']) ?>" target="_blank">
                            <img class="thumb" src="../<?= htmlspecialchars($m['file_url']) ?>" alt="screenshot">
                        </a>
                    <?php endif; ?>
                    <span>[<?= htmlspecialchars($m['media_type']) ?>] <?= htmlspecialchars($m['verification_status']) ?></span>
                    <a class="del-link" href="view.php?id=<?= $claim_id ?>&delete_media=<?= $m['media_id'] ?>" onclick="return confirm('Delete?')">Delete</a>
                </div>
            <?php endforeach; ?>
            <form method="POST" enctype="multipart/form-data" class="inline-form">
                <select name="media_type">
                    <option value="image">Screenshot / Image</option>
                    <option value="video">Video</option>
                    <option value="document">Document</option>
                </select>
                <input type="file" name="media_file" accept="image/*,video/mp4">
                <button type="submit" name="add_media">Upload</button>
            </form>
            <p style="font-size:12px;color:#888;margin-top:6px">সরাসরি স্ক্রিনশট/ছবি আপলোড করুন (jpg, png, gif, webp, mp4)</p>
        </div>

        <!-- COMMENT -->
        <div class="section" id="comments">
            <h3>💬 Comments</h3>
            <?php foreach ($comments as $c):
                $reactions = json_decode($c['reactions'] ?? '{}', true) ?: [];
            ?>
                <div class="item" style="display:block">
                    <div>
                        <b><?= htmlspecialchars($c['user_name']) ?>:</b> <?= htmlspecialchars($c['comment_text']) ?>
                        <span style="font-size:11px;color:#999"> (<?= $c['created_at'] ?>)</span>
                        <?php if ($c['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] === 'admin'): ?>
                            <a class="del-link" href="view.php?id=<?= $claim_id ?>&delete_comment=<?= $c['comment_id'] ?>" onclick="return confirm('Delete?')">Delete</a>
                        <?php endif; ?>
                    </div>
                    <div class="reaction-bar">
                        <?php foreach ($quickEmojis as $emoji): ?>
                            <a class="reaction-btn" href="view.php?id=<?= $claim_id ?>&react=<?= urlencode($emoji) ?>&comment_id=<?= $c['comment_id'] ?>#comments">
                                <?= $emoji ?>
                                <?php if (!empty($reactions[$emoji])): ?><span class="count"><?= $reactions[$emoji] ?></span><?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <form method="POST" class="inline-form">
                <input type="text" name="comment_text" placeholder="আপনার মন্তব্য লিখুন..." required>
                <button type="submit" name="add_comment">Post</button>
            </form>
        </div>

        <p><a href="index.php">← সব Claim-এ ফিরে যান</a></p>
        </div>
        </div>
    </div>
</body>
</html>
