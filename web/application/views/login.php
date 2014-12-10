<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/common.css'); ?>">
		<!--<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/normalize.css'); ?>">-->				
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/signin.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/responsive.css'); ?>">
		<link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">		
		 
		<title>FireDesk Login</title>
	</head>
	<body>
	
	<div class="container">

      <form class="form-signin" role="form" action="<?php echo base_url('index.php/desk');?>">
        <h2 class="form-signin-heading">FireDesk Login</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>

    </div> <!-- /container -->
	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js'); ?>"></script>
	</body>
</html>