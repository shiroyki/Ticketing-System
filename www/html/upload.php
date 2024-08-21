<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);error_reporting(E_ALL);
// Initialize SQLite database connection
//$db = new SQLite3('tickets.db');
$db = new PDO('sqlite:/var/www/tickets.db');

	// enable foreign key support
	$db->query('PRAGMA foreign_keys = ON;');
require __DIR__.'/lib/db.inc.php';
include_once('auth.php');
include_once('auth-process.php');

include_once('csrf-verify.php');

session_start();
if(!empty($_SESSION["auth"]))
{
    $user = auth();
       
}
else
{
    $user = 'Guest';
}
	// FETCH_ASSOC:
	// Specifies that the fetch method shall return each row as an
	// array indexed by column name as returned in the corresponding
	// result set. If the result set contains multiple columns with
	// the same name, PDO::FETCH_ASSOC returns only a single value
	// per column name.
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Check if image was uploaded successfully
  
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $author = $_POST['author'];
    $status = "new";
    
    // Insert new ticket into database
    $query = "INSERT INTO tickets (subject, description, author, created_at, status, userid) VALUES (:subject, :description, :author, :created_at, :status, :userid)";
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':subject', $subject, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':author', $author, PDO::PARAM_STR);
    $stmt->bindValue(':created_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
    
    $lastId = $db->lastInsertId();
 echo  $lastId;
 echo $_FILES['file']['tmp_name'];
 echo $_FILES['file']['error'];
echo  $_FILES['file']['type'];
header('Location: admin.php');
      exit();

    // Upload image file
       	
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the form was submitted with a file
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {

        // Check if the uploaded file is a JPEG image
        $file_type = $_FILES['file']['type'];
        if ($file_type == 'image/jpeg') {

            // Move the uploaded file to a permanent location
            $temp_file = $_FILES['file']['tmp_name'];
            $target_file = '/var/www/html/uploads/' . basename($_FILES['file']['name']);
            if (move_uploaded_file($temp_file, $target_file)) {

                // File uploaded successfully
                echo 'File uploaded successfully.';

            } else {
                // Error moving the uploaded file
                echo 'Error moving the uploaded file.';
            }

        } else {
            // Invalid file type
            echo 'Invalid file type. Only JPEG images are allowed.';
        }

    } else {
        // No file uploaded or an error occurred
        echo 'No file uploaded or an error occurred.';
    }
}?>
