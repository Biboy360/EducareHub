<?php
// Enable error reporting for debugging - IMPORTANT: Disable or comment out in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- MySQLi Database Connection ---
$host = 'localhost';
$db_name = 'educarehub'; // The name of your database
$user = 'root';        // Your database username (default for XAMPP is 'root')
$pass = '';            // Your database password (default for XAMPP is empty '')

// Create connection using MySQLi
$conn = new mysqli($host, $user, $pass, $db_name);

// Check connection
if ($conn->connect_error) {
    // If connection fails, stop script execution and display the error
    die("Database connection failed: " . $conn->connect_error);
}

// Initialize rewards array to prevent 'undefined variable' warnings if query fails
$rewards = [];

// Fetch products with their details and available stock
// Make sure these table and column names exactly match your database schema
$query = "
    SELECT
        p.product_id,
        p.product_name,
        p.img,
        p.description,
        p.price,
        s.quantity AS stock_quantity
    FROM
        products p
    JOIN
        stocks s ON p.product_id = s.product_id
    WHERE
        s.quantity >= 0
    ORDER BY
        p.product_name ASC
";

// Execute the query
$result = $conn->query($query);

// Check if the query executed successfully
if ($result) {
    // Fetch all results as an associative array
    // MYSQLI_ASSOC ensures columns are returned as associative strings (e.g., $row['product_name'])
    $rewards = $result->fetch_all(MYSQLI_ASSOC);
    $result->free(); // Free the result set to release memory
} else {
    // If query fails, print the MySQLi error. This is crucial for debugging.
    echo "<p style='color: red; font-weight: bold;'>Error executing query: " . $conn->error . "</p>";
    // In a production environment, you would log this error and display a user-friendly message
    // For now, we allow the rest of the HTML to render, showing "No rewards currently available."
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../style.css">

    <style>
/* Your existing CSS styles */
.page-title {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
    font-size: 2.5em;
}
.reward-container { /* Renamed from .product-container for clarity */
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px; /* Increased gap for better spacing */
    padding: 20px;
}
.reward-card { /* Renamed from .card for clarity */
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    width: 280px; /* Adjusted width for better fit */
    text-align: center;
    overflow: hidden;
    transition: transform 0.2s ease-in-out;
    display: flex;
    flex-direction: column;
}
.reward-card:hover {
    transform: translateY(-5px);
}
.reward-card img {
    width: 100%;
    height: 180px; /* Adjusted height */
    object-fit: cover;
    border-bottom: 1px solid #eee;
}
.reward-card-content {
    padding: 20px;
    flex-grow: 1; /* Allows content to push button to bottom */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.reward-card h3 {
    color: #0056b3;
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 1.5em;
}
.reward-card p.description {
    color: #666;
    font-size: 0.9em;
    line-height: 1.5;
    margin-bottom: 15px;
    flex-grow: 1; /* Ensures description takes available space */
}
.reward-card p.price {
    font-size: 1.2em;
    color: #28a745;
    font-weight: bold;
    margin-bottom: 10px;
}
.reward-card p.stock {
    font-size: 0.9em;
    color: #555;
    margin-bottom: 20px;
}
.claim-btn {
    background: #007bff;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1em;
    font-weight: bold;
    transition: background 0.3s ease;
    width: 100%; /* Make button full width of card content */
}
.claim-btn:hover {
    background: #0056b3;
}
.claim-btn:disabled {
    background: #cccccc;
    cursor: not-allowed;
}
    </style>
</head>
<body>

    <div class="p-4" style="background-color: #f8f9fa; border-radius: 10px; min-height: 85vh;">
        <h2 class="page-title">Rewards</h2>
        <p>See your earned rewards and how to get more!</p>

        <div class="reward-container mt-4">
            <?php if (empty($rewards)): ?>
                <p>No rewards currently available.</p>
            <?php else: ?>
                <?php foreach ($rewards as $reward): ?>
                    <div class="reward-card">
                        <img src="../images/<?= htmlspecialchars($reward['img'] ?? 'placeholder.jpg') ?>" alt="<?= htmlspecialchars($reward['product_name']) ?>">
                        <div class="reward-card-content">
                            <h3><?= htmlspecialchars($reward['product_name']) ?></h3>
                            <p class="description"><?= htmlspecialchars($reward['description']) ?></p>
                            <p class="price">Price: $<?= number_format($reward['price'], 2) ?></p>
                            <p class="stock">In Stock: <?= (int)$reward['stock_quantity'] ?></p>

                            <?php if ((int)$reward['stock_quantity'] > 0): ?>
                                <button class="claim-btn">Claim Now</button>
                            <?php else: ?>
                                <button class="claim-btn" disabled>Out of Stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="alert alert-success mt-4" role="alert">
            Keep using EducareHub to earn more exciting rewards!
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>