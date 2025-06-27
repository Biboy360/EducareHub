<?php include('insert_users.php'); ?>
<?php $old = $_POST ?? []; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>admin_dashboard</title>
  <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="css/style.css? v=1">


</head>
<body class="body">
  <div class="dashboard_main_container">
    <?php include('partials/app_topNav.php'); ?>
    <?php include('partials/app_horizontal_nav.php'); ?>

    <div class="dashboard_content">
      <div class="dashboard_content_main">
        <div class="row">
          <div class="column column-12">
            <h2 class="section_header"><i class="fa fa-list"></i>RANKING</h2>
            <div class="section_content">
                <div class="users">
                    <table>
                        <thead>
                            <tr>
                                <th>user_id</th>
                                <th>Username</th>
                                <th>Tier</th>
                                <th>Points</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $db = mysqli_connect('localhost', 'root', '', 'educarehub');
                            if (!$db) {
                              die("Connection failed: " . mysqli_connect_error());
                            }

                            $query = "SELECT * FROM users WHERE points > 0 ORDER BY points DESC";
                            $results = mysqli_query($db, $query);

                            while ($row = mysqli_fetch_assoc($results)) {
                              echo "<tr>";
                              echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['tier']) . "</td>";
                              echo "<td>" . htmlspecialchars($row['points']) . "</td>";
                            }
                            ?>
                        </tbody>
                        
                            

                    </table>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="js/script.js"></script>
</body>
</html>
