<div class="p-4" style="background-color: #f8f9fa; border-radius: 10px; min-height: 85vh;">
    <h2>Account Settings</h2>
    <p>Manage your profile and preferences.</p>
    <form>
        <div class="mb-3">
            <label for="firstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstName" value="<?php echo htmlspecialchars($_SESSION['firstname']); ?>">
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($_SESSION['username']); // Assuming you still store username in session ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" placeholder="name@example.com">
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <button type="button" class="btn btn-secondary ms-2">Change Password</button>
    </form>
</div>