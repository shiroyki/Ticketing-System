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
header('Location: index.php');
      exit();

    // Upload image file
       	
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["file"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["file"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    echo "The file ". htmlspecialchars( basename( $_FILES["file"]["name"])). " has been uploaded.";
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}

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

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Ticketing System - Create Ticket</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css"><style> .dropzone { border: 2px dashed #007bff; padding: 20px; text-align: center; cursor: pointer; }
Copy
    .dropzone.highlight {
        background-color: #e6f7ff;
    }

    .dropzone input[type="file"] {
        display: none;
    }
</style>
</head>

<body>
  <!-- Navigation bar -->
   <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="index.php">Ticketing System</a>
      </div>
      <ul class="nav navbar-nav">
        <li><a href="index.php">Home</a></li>
        <li><a href="create_ticket.ph	p">Create Ticket</a></li>
        <li><a href="index.php">All Tickets</a></li>
        <li><a href="user_tickets.php">My Tickets</a></li>      </ul>
      <ul class="nav navbar-nav navbar-right">
       
<ul class="navbar-nav ml-auto">
  <?php if ($_SESSION["auth"] == false || ($user != 'Guest' && auth_admin()!= 1)) { ?>
    <li class="nav-item">
      <form class="form-inline my-1 my-lg-0 pr-3" id="logout" method="POST" action="admin-process.php?action=<?php echo($action ='logout'); ?>" enctype="multipart/form-data">
        <button class="nav-btn btn btn-outline-dark btn-light my-2 my-sm-0 btn-lg" type="submit" value="Submit">
          <?php if ($_SESSION["auth"] == false) {
            echo '<i class="bi bi-key-fill"></i><span class="glyphicon glyphicon-log-in"></span> Login';
          } elseif ($user != 'Guest' && auth_admin()!= 1) {
            echo '<span class="glyphicon glyphicon-log-out"></span> Logout';
          } ?>
        </button>
        <input type="hidden" name="nonce" value="<?php if( auth_admin()!= 1) echo csrf_getNonce($action); ?>"/>
      </form>
    </li>
  <?php } ?>

  <?php if($user != 'Guest' ) { ?>
    <li class="nav-item">
      <button class="nav-btn btn btn-outline-dark btn-light my-2 my-sm-0 btn-lg" type="button">
        <i class="bi bi-key-fill"></i> <a href="changePassword.php"><span class="glyphicon glyphicon-lock"></span> Change password</a>
      </button>
    </li>
  <?php } ?>

  <?php if ($user != 'Guest' && auth_admin()== 1) { ?>
    <li class="nav-item">
      <form class="d-inline my-2 my-lg-0 pr-3">
        <a class="btn btn-outline-dark btn-light my-2 my-sm-0 btn-lg" href="admin.php">
          <i class="bi bi-terminal"></i>
          <span class="glyphicon glyphicon-cog"></span>
          Admin Panel
        </a>
      </form>
    </li>
  <?php } ?>  <li class="nav-item ml-auto">
    <!-- add any additional menu items here --><h5><text class="my-2 my-sm-0">Welcome, <?php echo htmlspecialchars($user); ?> </text></h5>

  </li>
</ul>
    </div>

 
  </nav>

  <div class="container">
    <h2>Create Ticket</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="subject">Subject</label>
        <input type="text" class="form-control" name="subject" required>
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea class="form-control" name="description" required></textarea>
      </div>
      <!-- <div class="form-group">
              <label for="image">Image</label>
            <div class="dropzone" id="dropzone">
                <span>Drag and drop or click to select image</span>
                <input type="file" name="file" accept="image/jpeg" id="fileinput">
            </div>      </div> -->
      <div class="form-group">
        <label for="author">Author</label>
        <input type="text" class="form-control" name="author" required>
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>

  <!-- jQuery and Bootstrap JS -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    function handleDropzoneClick(e) {
        var fileinput = document.getElementById("fileinput");
        fileinput.click();
    }

    function handleDropzoneDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        e.dataTransfer.dropEffect = "copy";
        var dropzone = document.getElementById("dropzone");
        dropzone.classList.add("highlight");
    }

    function handleDropzoneDragLeave(e) {
        var dropzone = document.getElementById("dropzone");
        dropzone.classList.remove("highlight");
    }

    function handleDropzoneDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        var files = e.dataTransfer.files;
        var fileinput = document.getElementById("fileinput");
        fileinput.files = files;
        handleFileInputChange();
    }

    function handleFileInputChange() {
        var fileinput = document.getElementById("fileinput");
        var files = fileinput.files;
        var dropzone = document.getElementById("dropzone");
        if (files.length > 0) {
            dropzone.innerHTML = files[0].name;
        } else {
            dropzone.innerHTML = '<span>Drag and drop or click to select image</span>';
        }
    }

    var dropzone = document.getElementById("dropzone");
    dropzone.addEventListener("click", handleDropzoneClick);
    dropzone.addEventListener("dragover", handleDropzoneDragOver);
    dropzone.addEventListener("dragleave", handleDropzoneDragLeave);
    dropzone.addEventListener("drop", handleDropzoneDrop);

    var fileinput = document.getElementById("fileinput");
    fileinput.addEventListener("change", handleFileInputChange);
</script>
</body>

</html>