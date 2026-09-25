<div class="sidebar">
    <nav>
        <a href="<?= $prefix ?? '' ?>dashboard.php" class="<?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>">
            <span>🏠</span> Dashboard
        </a>
        <a href="<?= $prefix ?? '' ?>claims/index.php" class="<?= ($active ?? '') === 'claims' ? 'active' : '' ?>">
            <span>📋</span> Claim
        </a>
        <a href="<?= $prefix ?? '' ?>claims/create.php" class="<?= ($active ?? '') === 'new_claim' ? 'active' : '' ?>">
            <span>➕</span> নতুন Claim
        </a>
        <a href="<?= $prefix ?? '' ?>evidence/index.php" class="<?= ($active ?? '') === 'evidence' ? 'active' : '' ?>">
            <span>📄</span> Evidence
        </a>
        <a href="<?= $prefix ?? '' ?>media/index.php" class="<?= ($active ?? '') === 'media' ? 'active' : '' ?>">
            <span>🖼️</span> Media
        </a>
        <a href="<?= $prefix ?? '' ?>source/index.php" class="<?= ($active ?? '') === 'sources' ? 'active' : '' ?>">
            <span>🔖</span> Source
        </a>
        <a href="<?= $prefix ?? '' ?>comment/index.php" class="<?= ($active ?? '') === 'comment' ? 'active' : '' ?>">
            <span>💬</span> Comment
        </a>
        <a href="<?= $prefix ?? '' ?>factcheck/index.php" class="<?= ($active ?? '') === 'factcheck' ? 'active' : '' ?>">
            <span>✅</span> Fact-Check
        </a>

        <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'fact_checker')): ?>
        <div class="sidebar-divider"></div>
        <a href="<?= $prefix ?? '' ?>claims/index.php?filter=pending" class="<?= ($active ?? '') === 'pending' ? 'active' : '' ?>">
            <span>⏳</span> Pending Verdict
        </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="<?= $prefix ?? '' ?>claims/index.php?filter=flagged" class="<?= ($active ?? '') === 'flagged' ? 'active' : '' ?>">
            <span>🚩</span> Flagged Claims
        </a>
        <?php endif; ?>
    </nav>
</div>
