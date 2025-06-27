<?php
if (!function_exists('has_permission')) {
    function has_permission($required_role) {
        if (!isset($_SESSION['admins']['role'])) return false;
        $user_role = $_SESSION['admins']['role'];
        if ($user_role === 'super_admin') return true;
        switch ($required_role) {
            case 'admin': return in_array($user_role, ['admin', 'super_admin']);
            case 'rewards_only': return in_array($user_role, ['rewards_manager', 'admin', 'super_admin']);
            case 'viewer': return in_array($user_role, ['viewer', 'stock_encoder', 'purchasing_officer', 'rewards_manager', 'admin', 'super_admin']);
            default: return false;
        }
    }
}
?>
<div class="flex-column d-flex">
    <div class="d-flex align-items-center flex-row">
        <img src="icons/EducareHub.png" alt="logo" class="Main-Logo" style="width: 60px;">
        <a class="logoText">Educare<span>Hub</span></a>
    </div>
    <hr class="borderline">
    <div class="d-flex flex-column" style="width: 240px;">
        <p class="menu-title">MAIN MENU</p>
        <div class="d-flex flex-column gap-3">
            <button class="menus set-btn active" id="dashboard-btn" data-panel="dashboard.php" href="index.php?page=dashboard">
                <i class="menu-icon bi bi-clipboard-data"></i>Dashboard
            </button>
            <?php if (has_permission('admin')): ?>
            <a href="index.php?page=peer_forum">
            <button class="menus set-btn" id="peer-forum-btn" data-panel="peer_forum.php" >
                <i class="menu-icon bi bi-postcard-heart"></i>Peer Forum
            </button>
            </a>
            <button class="menus set-btn" id="task-manager-btn" data-panel="task_manager.php" href="index.php?page=task_manager">
                <i class="menu-icon bi bi-list-task"></i> Task Manager
            </button>
            <?php endif; ?>
            <?php if (has_permission('rewards_only')): ?>
            <a href="index.php?page=rewards">
            <button class="menus set-btn" id="rewards-btn" data-panel="rewards.php">
                <i class="menu-icon bi bi-trophy"></i>Rewards
            </button>
            </a>
            <?php endif; ?>
        </div>
        <p class="menu-title" style="margin-top: 20px;">ADDITIONAL</p>
        <?php if (has_permission('admin')): ?>
        <button class="menus set-btn" id="account-settings-btn" data-panel="account_settings.php">
            <i class="menu-icon bi bi-sliders2"></i> Account Settings
        </button>
        <?php endif; ?>
    </div>
</div>
<div class="d-flex flex-row align-items-center mb-4">
    <img src="icons/Export.png" alt="logo" class="Main-Logo" style="width: 20px;">
    <a href="logout.php" class="logout">Log Out</a>
</div>