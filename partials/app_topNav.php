<div class="dashboard_topNav d-flex justify-content-between align-items-center px-3" style="height: 60px; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div class="d-flex align-items-center">
        <div class="nav-brand me-4 d-flex align-items-center">
            <img src="icons/EducareHub.png" alt="EducareHub Logo" style="width: 35px; height: 35px; margin-right: 10px;">
            <span style="font-size: 20px; font-weight: bold; color: #0F9E99;">EducareHub</span>
        </div>
        <a href="#" id="toggleBtn" class="me-3"><i class=""></i></a>
    </div>
</div>

<script>
function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}
</script>
