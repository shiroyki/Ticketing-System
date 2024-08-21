<?php
    require __DIR__.'/auth-process.php';
    include_once('csrf-verify.php');
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet" />
    <title>Change Password</title>
</head>
<body style="background-color:white;">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title text-center mb-5 fw-light fs-5">Change Password</h1>
                <fieldset>
                    <legend>Change Password</legend>
                    <form id="change_password" method="POST" action="admin-process.php?action=change_password" enctype="multipart/form-data">
                        <div class="form-floating mb-3">
                            <label for="floatingInput">Email: </label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="user@gmail.com" autofocus>
                        </div>
                        <div class="form-floating mb-3">
                            <label for="floatingPassword">Original Password</label>
                            <input type="password" name="pw" class="form-control" id="pw" placeholder="Enter Your Original Password" required>
                        </div>
                        <div class="form-floating mb-3">
                            <label for="floatingPassword">New Password</label>
                            <input type="password" name="updated_pwd" class="form-control" id="updated_pwd" placeholder="Enter Your New Password" required>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-dark btn-login fw-bold mb-4 text-uppercase" type="submit">Change Password</button>
                            <?php if ($_SESSION["auth"] == false || ($user != 'Guest' && auth_admin()!= 1)) { ?>
                                <a class="btn btn-primary btn-login fw-bold mb-4 text-uppercase" href="index.php">Back to Main Page</a>               <?php } else { ?>
                                <a class="btn btn-secondary btn-login fw-bold mb-4 text-uppercase" href="index.php">Back to Main Page</a>
                            <?php } ?>
                        </div>
                        <input type="hidden" name="nonce" value="<?php echo csrf_getNonce('change_password'); ?>"/>
                    </form>
                </fieldset>
            </div>
        </div>
    </div>
</body>
</html>