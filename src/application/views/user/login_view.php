<!DOCTYPE html>
<html>
<head>
	<title>Absensi Penerbit Duta </title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/custom.css">

	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-4"></div>
			<div class="col-md-4">
				<div id="login-box">
				    <br>
				    <br>
				    <br>
				    <br>
				    <br>
				    <br>
				    <br>
				    <br>
				    
					<h3 class="header-login">Absensi Penerbit Duta</h3>
					<?php
						echo validation_errors();
						if($this->session->flashdata('login_failed')){
							echo $this->session->flashdata('login_failed');
						}
					?>
					<form class="form-horizontal" role="form" method="POST" action="">
						<div class="form-group">
							<div class="col-sm-12">
								<input type="text" name="username" id="username" class="form-control" placeholder="Username" required="true">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<input type="text" name="password" id="password" class="form-control" placeholder="Password" required="true">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<input type="submit" name="login" id="login" value="Login" class="btn btn-success">&nbsp;
								<!--<input type="reset" name="reset" id="reset" value="Cancel" class="btn">-->
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="col-md-4"></div>
		</div>
	</div>
	<script type="text/javascript">
	var base_url = '<?php echo base_url(); ?>';
	</script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/custom.js"></script>
</body>
</html>