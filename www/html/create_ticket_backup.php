<?php
// Initialize SQLite database connection
//$db = new SQLite3('tickets.db');
$db = new PDO('sqlite:/var/www/tickets.db');

	// enable foreign key support
	$db->query('PRAGMA foreign_keys = ON;');
  session_start();
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
  if ($_FILES["image"]["error"] == 0
        && $_FILES["image"]["type"] == "image/jpeg"
        && mime_content_type($_FILES["image"]["tmp_name"]) == "image/jpeg"
        && $_FILES["image"]["size"] < 700000000) {
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];
    $tmp_image = $_FILES['image']['tmp_name'];
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

    // Upload image file
    if (move_uploaded_file($tmp_image, "/var/www/html/lib/images/" . $lastId . ".jpg")) {
      // Redirect to admin page
      header('Location: admin.php');
      exit();
    } else {
      // Error uploading image file
      echo 'Error uploading image file';echo $lastId;
    }
  } else {
    // Invalid file
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to create ticket.</a>'; echo var_dump($_FILES["image"]["error"]);
    echo var_dump($_FILES["image"]["type"]);echo var_dump(mime_content_type($_FILES["image"]["tmp_name"]));
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
        <li><a href="user_tickets.php">My Tickets</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
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
      <div class="form-group">
        <label for="image">Image</label>
            <div class="dropzone" id="dropzone">
                <span>Drag and drop or click to select image</span>
                <input type="file" name="image"accept="image/jpeg" id="fileinput">
            </div>      </div>
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