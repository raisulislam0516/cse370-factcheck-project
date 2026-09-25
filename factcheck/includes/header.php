<div class="site-header">
    <a href="<?= $home_link ?? (($prefix ?? '') . 'dashboard.php') ?>" class="brand">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="10.5" cy="10.5" r="6.5" stroke="currentColor" stroke-width="2"/>
            <line x1="15.4" y1="15.4" x2="21" y2="21" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
            <path d="M7.5 10.5L9.5 12.5L13.5 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>TruthCheck</span>
    </a>
    <?php if (isset($_SESSION['user_name'])): ?>
    <div class="header-right">
        <span class="user-chip"><?= htmlspecialchars($_SESSION['user_name']) ?> · <?= htmlspecialchars($_SESSION['role']) ?></span>
        <a href="<?= $prefix ?? '' ?>logout.php" class="logout-link">Logout</a>
    </div>
    <?php endif; ?>
</div>
