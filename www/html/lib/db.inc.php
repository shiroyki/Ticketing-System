<?php
function ierg4210_DB() {
	// connect to the database
	// TODO: change the following path if needed
	// Warning: NEVER put your db in a publicly accessible location
	$db = new PDO('sqlite:/var/www/cart.db');

	// enable foreign key support
	$db->query('PRAGMA foreign_keys = ON;');

	// FETCH_ASSOC:
	// Specifies that the fetch method shall return each row as an
	// array indexed by column name as returned in the corresponding
	// result set. If the result set contains multiple columns with
	// the same name, PDO::FETCH_ASSOC returns only a single value
	// per column name.
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    //$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	return $db;
}
function ierg4210_cat_fetchOne_name($cid){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM categories WHERE CID = (?) LIMIT 100;");
    $q->bindParam(1, $cid);
    if ($q->execute())
        return $q->fetchAll();
}

function ierg4210_cat_fetchall() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM categories LIMIT 100;");
    if ($q->execute())
        return $q->fetchAll();
}
function ierg4210_cat_fetchOne($cid){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM products WHERE CID = (?) LIMIT 100;");
    $q->bindParam(1, $cid);
    if ($q->execute())
        return $q->fetchAll();
}

// Since this form will take file upload, we use the tranditional (simpler) rather than AJAX form submission.
// Therefore, after handling the request (DB insert and file copy), this function then redirects back to admin.html
function ierg4210_prod_insert() {
    // input validation or sanitization

    // DB manipulation
    

    // TODO: complete the rest of the INSERT command
    if (!preg_match('/^\d*$/', $_POST['cid']))
        throw new Exception("invalid-cid");
    $_POST['cid'] = (int) $_POST['cid'];
    if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
    
    if (!preg_match('/^[\d\.]+$/', $_POST['price']))
        throw new Exception("invalid-price");
    //if (!preg_match('/^[\w\- ]+$/', $_POST['description']))
        //throw new Exception("invalid-text");
    
    //if (!preg_match('/^[\w\- ]+$/', $_POST['info']))
        //throw new Exception("invalid-text");
    $_POST['info'] = $_POST['info'];
    //if (!preg_match('/^[\w\- ]+$/', $_POST['stock']))
        //throw new Exception("invalid-text");
    

    

    global $db;
    $db = ierg4210_DB();
    
    $cid = $_POST["cid"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $desc = $_POST["description"];
    $info = $_POST["info"];
    $stock = $_POST["stock"];

    
    $sql="INSERT INTO products (cid, name, price, description, info, stock) VALUES (?, ?, ?, ?, ?, ?)";
    $q = $db->prepare($sql);

    
    

    // Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
    if ($_FILES["file"]["error"] == 0
        && $_FILES["file"]["type"] == "image/jpeg"
        && mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg"
        && $_FILES["file"]["size"] < 5000000) {
        
        
        $cid = $_POST["cid"];
        $name = $_POST["name"];
        $price = $_POST["price"];
        $desc = $_POST["description"];
        $info = $_POST["info"];
        $stock = $_POST["stock"];
                $sql="INSERT INTO products (CID, NAME, PRICE, DESCRIPTION, INFO, STOCK) VALUES (?, ?, ?, ?, ?, ?);";
        $q = $db->prepare($sql);
        $q->bindParam(1, $cid);
        $q->bindParam(2, $name);
        $q->bindParam(3, $price);
        $q->bindParam(4, $desc);
        $q->bindParam(5, $info);
        $q->bindParam(6, $stock);

        $q->execute();
        $lastId = $db->lastInsertId();

        // Note: Take care of the permission of destination folder (hints: current user is apache)
        if (move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/lib/images/" . $lastId . ".jpg")) {
            // redirect back to original page; you may comment it during debug
            header('Location: admin.php');
            echo '<img src="/lib/images/" . $lastId . ".jpg" width="239" height="380" />';
            exit();
        }
    }
    // Only an invalid file will result in the execution below
    // To replace the content-type header which was json and output an error message
    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

// TODO: add other functions here to make the whole application complete
function ierg4210_cat_insert() {
    
    
    if (!preg_match('/^[\w\-, ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
    $name = $_POST['name'];
    global $db;
    $db = ierg4210_DB();
    
    htmlspecialchars($name);
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $q = $db->prepare($sql);
    $q->bindParam('1', $name);
    //return $q->execute(array($_POST['name']));
    
    
    if ($q->execute()) {
        header('Location: admin.php');
        exit();
}






}
function ierg4210_cat_edit(){
    if (!preg_match('/^\d*$/', $_POST['cid']))
        throw new Exception("invalid-cid");
    $_POST['cid'] = (int) $_POST['cid'];
    if (!preg_match('/^[\w\-, ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
    

    global $db;
    $db = ierg4210_DB();

    //ierg4210_prod_delete_by_cid();

    //to do
    $cid = $_POST['cid'];
    $name = $_POST['name'];
    $q = $db->prepare('UPDATE categories SET NAME =? WHERE CID=?');
    $q->bindParam(1, $name);
    $q->bindParam(2, $cid);
    if ($q->execute()) {
        header('Location: admin.php');
        exit();
    }

    

}
function ierg4210_cat_delete(){
    if (!preg_match('/^\d*$/', $_POST['cid']))
        throw new Exception("invalid-cid");
    $_POST['cid'] = (int) $_POST['cid'];
    

    global $db;
    $db = ierg4210_DB();
    $cid = $_POST['cid'];

    ierg4210_prod_delete_by_cid($cid);
    
    
    
    
    $q = $db->prepare('DELETE FROM categories WHERE CID=?');
    
    $q->bindParam(1, $cid);
    if ($q->execute()) {
        header('Location: admin.php');
        exit();
    }
}
function ierg4210_prod_delete_by_cid($cid){
    
    

    global $db;
    $db = ierg4210_DB();

    
    
    $q = $db->prepare('DELETE FROM products WHERE CID=(?)');
    $q->bindParam(1, $cid);
    $q->execute();
    
}
function ierg4210_prod_fetchAll(){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM products LIMIT 200;");
    if ($q->execute())
        return $q->fetchAll();
}
function ierg4210_prod_fetchOne($pid){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM products WHERE PID = (?) LIMIT 100;");
    $q->bindParam(1, $pid);
    if ($q->execute())
        return $q->fetchAll();





}

function ierg4210_prod_edit(){
    if (!preg_match('/^[\w\-, ]+$/', $_POST['pid']))
        throw new Exception("invalid-pid");
    $_POST['pid'] = (int) $_POST['pid'];
    
    if (!preg_match('/^[\w\-, ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
    $_POST['name'] = $_POST['name'];
    if (!preg_match('/^[\d\.]+$/', $_POST['price']))
        throw new Exception("invalid-price");
    $_POST['price'] = $_POST['price'];
    

    global $db;
    $db = ierg4210_DB();

    $pid = $_POST["pid"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $desc = $_POST["description"];
    $info = $_POST["info"];
    $stock = $_POST["stock"];



    //$q = $db->prepare('UPDATE categories SET NAME =? WHERE CID=?');
    
    //return $q->execute(array($_POST['name']), $_POST['cid']);

    $sql="UPDATE products SET NAME=(?), PRICE=(?), DESCRIPTION=(?), INFO=(?), STOCK=(?) WHERE PID=(?);";
    $q = $db->prepare($sql);
    
    $q->bindParam(1, $name);
    $q->bindParam(2, $price);
    $q->bindParam(3, $desc);
    $q->bindParam(4, $info);
    $q->bindParam(5, $stock);$q->bindParam(6, $pid);

    if ($q->execute()) {
        header('Location: admin.php');
        exit();
    }




}
function ierg4210_prod_delete(){
        
    

    global $db;
    $db = ierg4210_DB();
    $pid = $_POST['pid'];

    
    
    $q = $db->prepare('DELETE FROM products WHERE PID=(?)');
    $q->bindParam(1, $pid);
    
    if ($q->execute()) {
        header('Location: admin.php');
        exit();
    }


}
