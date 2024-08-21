<?php
// Start the session and check if the user is logged in
require __DIR__.'/lib/db.inc.php';
include_once('auth.php');
include_once('auth-process.php');

include_once('csrf-verify.php');

session_start();
if (!isset($_SESSION['auth'])) {
  header('Location: login.php');
  exit;
}

// Connect to the database
require_once 'db.php';
$db = db_connect();

// Retrieve the tickets data for the current user
$user_id = $_SESSION['auth']['user_id'];
$query = "SELECT * FROM tickets WHERE user_id=:user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Tickets</title>
</head>
<body>
  <h1>View Tickets</h1>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Subject</th>
        <th>Status</th>
        <th>Num Comments</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($tickets as $ticket): ?>
        <tr>
          <td><?= $ticket['TID'] ?></td>
          <td><?= $ticket['SUBJECT'] ?></td>
          <td><?= $ticket['STATUS'] ?></td>
          <td><?= $ticket['NUM_COMMENTS'] ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</body>
</html>