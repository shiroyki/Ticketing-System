<?php
    require __DIR__.'/auth-process.php';
    include_once('auth.php');
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
	<title>Login Page</title>
</head>
<body style="background-color:white;">
	<div class="container">
		<div class="card">
			<div class="card-body">
				<h1 class="card-title text-center mb-5 fw-light fs-5">Login Panel</h1>
				<fieldset>
					<legend>Login</legend>
					<form id="login" method="POST" action="admin-process.php?action=<?php echo ($action = 'login')?>" enctype="multipart/form-data">
						<div class="form-floating mb-3">
							<label for="floatingInput">Email address</label>
							<input type="email" name="email" class="form-control" id="email" placeholder="user@gmail.com" autofocus>
						</div>
						<div class="form-floating mb-3">
							<label for="floatingPassword">Password</label>
							<input type="password" name="pw" class="form-control" id="pw" placeholder="Enter Your Password" required>
						</div>
						<div class="d-grid">
							<button class="btn btn-dark btn-login fw-bold mb-4 text-uppercase" type="submit">Sign in</button>
							<a class="btn btn-primary btn-login fw-bold mb-4 text-uppercase" href="index.php">Continue as guest</a>
						</div>
						<input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>"/>
					</form>
					<a href="changePassword.php" class="forgot-password-link">Forgot password?</a>
                                        <a href="register.php" class="register-password-link">New user?</a>
				</fieldset>
			</div>
		</div>
	</div>
</body>
</html>
