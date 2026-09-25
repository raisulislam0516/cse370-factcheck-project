<?php
require '../includes/auth_check.php';
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_source'])) {
    $name = trim($_POST['source_name']);
    $url = trim($_POST['url']);
    $type = trim($_POST['source_type']);
    if ($name !== "") {
        $stmt = $pdo->prepare("INSERT INTO SOURCE (source_name, url, source_type) VALUES (?, ?, ?)");
        $stmt->execute([$name, $url, $type]);
    }
    header("Location: index.php");
    exit();
}
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM SOURCE WHERE source_id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: index.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $stmt = $pdo->prepare("UPDATE SOURCE SET source_name=?, url=?, source_type=? WHERE source_id=?");
    $stmt->execute([$_POST['source_name'], $_POST['url'], $_POST['source_type'], $_POST['edit_id']]);
    header("Location: index.php");
    exit();
}

$sources = $pdo->query("SELECT * FROM SOURCE ORDER BY source_id DESC")->fetchAll(PDO::FETCH_ASSOC);
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM SOURCE WHERE source_id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch(PDO::FETCH_ASSOC);
}
$prefix = '../';
$active = 'sources';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Sources - TruthCheck</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="app-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
        <div class="container">
        <div class="page-title"><h2>Source Management</h2></div>

        <form method="POST" class="form-box" style="margin:0 0 24px;max-width:500px">
            <input type="hidden" name="edit_id" value="<?= $editing ? $editing['source_id'] : '' ?>">
            <input type="text" name="source_name" placeholder="Source name (e.g. BBC News)" value="<?= $editing ? htmlspecialchars($editing['source_name']) : '' ?>" required>
            <input type="text" name="url" placeholder="URL" value="<?= $editing ? htmlspecialchars($editing['url']) : '' ?>">
            <input type="text" name="source_type" placeholder="Type (e.g. news, government, social media)" value="<?= $editing ? htmlspecialchars($editing['source_type']) : '' ?>">
            <button type="submit" name="<?= $editing ? '' : 'add_source' ?>"><?= $editing ? 'Update Source' : 'Add Source' ?></button>
        </form>

        <table class="data-table">
            <tr><th>Name</th><th>URL</th><th>Type</th><th>Action</th></tr>
            <?php foreach ($sources as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['source_name']) ?></td>
                <td><a href="<?= htmlspecialchars($s['url']) ?>" target="_blank"><?= htmlspecialchars($s['url']) ?></a></td>
                <td><?= htmlspecialchars($s['source_type']) ?></td>
                <td>
                    <a href="index.php?edit=<?= $s['source_id'] ?>">Edit</a> |
                    <a href="index.php?delete=<?= $s['source_id'] ?>" onclick="return confirm('Delete this source?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        </div>
        </div>
    </div>
</body>
</html>
