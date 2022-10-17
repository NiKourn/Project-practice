<h1 class="text-center"><?php echo $title; ?></h1>
<?php
//Reload this page and do the posting action on this page
//htmlentities can strip down the exploitation by hackers
?>
<form action="<?php echo htmlentities( $_SERVER[ 'PHP_SELF' ] ); ?>" method="post" enctype="multipart/form-data">
	<div class="mb-3">
		<label for="host" class="form-label">Server IP</label>
		<input type="text" name="host" class="form-control" id="host" aria-describedby="" value="<?php if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
			echo $_POST[ 'host' ];
		} ?>">
	</div>
	<div class="mb-3">
		<label for="root_username" class="form-label">Server Username</label>
		<input type="text" name="root_username" class="form-control" id="root_username" aria-describedby="emailHelp" value="<?php if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
			echo $_POST[ 'root_username' ];
		} ?>">
	</div>
	<div class="mb-3">
		<label for="root_password" class="form-label">Server Password</label>
		<input type="password" name="root_password" class="form-control" id="root_password">
	</div>
	
	
	<div class="mb-3">
		<label for="db_name" class="form-label">Database Name</label>
		<input type="text" name="db_name" class="form-control" id="db_name" aria-describedby="" value="<?php if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
			echo $_POST[ 'db_name' ];
		} ?>">
	</div>
	<div class="mb-3">
		<label for="db_username" class="form-label">Database Username</label>
		<input type="text" name="db_username" class="form-control" id="db_username" aria-describedby="emailHelp" value="<?php if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
			echo $_POST[ 'db_username' ];
		} ?>">
	</div>
	<div class="mb-3">
		<label for="db_password" class="form-label">Database Password</label>
		<input type="password" name="db_password" class="form-control" id="db_password">
	</div>
	
	<button type="submit" class="btn btn-primary" value="login">Install App</button>
	<br/>
</form>
