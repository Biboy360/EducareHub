<div class="uppertab d-flex justify-content-between align-items-center" style="margin-right: 20px;">
    <div class="d-flex flex-row align-items-center justify-content-center gap-3">
        <p class="hello" style="margin: 0;padding-left:25px;">Hello, <span class="userName"><?php
            if (isset($_SESSION['firstname'])) {
                echo htmlspecialchars($_SESSION['firstname']);
            } else {
                echo "Guest"; // Or redirect to login page if user is not logged in
            }
            ?></span></p>
        <div class="clock d-flex align-items-center gap-2"> <img src="icons/clock.png" alt="clock" width="15px" height="15px"><span id="realTimeClockDisplay"></span></div>
    </div>
    <div class="d-flex align-items-center">
        <div class="notification">
            <i class="far fa-bell"></i>
            <a href="" style="color: #000000;"><span>Don't miss your <span style="color:#109799a6;">tasks Today!</span></span></a>
        </div>
        <div class="profile-pic">
            <i class="fas fa-user"></i>
        </div>
    </div>
</div>