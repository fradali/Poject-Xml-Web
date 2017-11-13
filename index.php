<?php
	/**
	 * XML Project
	 * @version		1.0.0
	 * @authors 		Frad Ali and Jmal Chadly	 	 
	 */
	require_once('xml.class.php');
	require_once('users.class.php');
	$users = new users();
	// DELETE A USER
	if (isset($_GET['del'])) {
		$users->remove('user', $_GET['del']);
		$users->save(); // SAVE CHANGES
	}
	// TRY LOGIN
	if (isset($_POST['doLogin'])) {
		$logOk = $users->connect($_POST['login'], $_POST['password']);		
	}
	// EDIT A USER
	if (isset($_POST['doEdit'])) {
		$user = $users->user[$_POST['id']];
		$user->name = $_POST['edit_name'];
		$user->login = $_POST['edit_login'];
		$user->email = $_POST['edit_email'];
		$user->password = $_POST['edit_password'];
		$users->save();
	}
	// ADD A USER
	if (isset($_POST['doAdd'])) {
		$user = new user();
		$user->name = $_POST['add_name'];
		$user->login = $_POST['add_login'];
		$user->email = $_POST['add_email'];
		$user->password = $_POST['add_password'];	
		$users->add('user', $user);
		$users->save();	
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Test XML Map</title>
</head>
<body>
	<h1>List of users</h1>
	<table border="1">
		<thead>
			<tr>
				<th>Name</th>
				<th>Login</th>
				<th>Email</th>
				<th>Password</th>
				<th>Date</th>
				<th colspan="2">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
			// READ XML LIST
			foreach($users->user as $id => $u) {
				echo '<tr>';
				echo '<td>'.$u->name.'</td>';
				echo '<td>'.$u->login.'</td>';
				echo '<td>'.$u->email.'</td>';
				echo '<td>'.$u->password.'</td>';
				echo '<td>'.$u->getLastLogin().'</td>';
				echo '<td><a href="?edit='.$id.'">Edit</a></td>';
				echo '<td><a href="?del='.$id.'">Delete</a></td>';
				echo '</tr>';
			}
		?>
		</tbody>
	</table>
	<?php	if (isset($_GET['edit'])) {	$user = $users->user[$_GET['edit']]; ?>
	<h1>Edit an user</h1>
	<form method="post" action="index.php">
		<div>
			Name :
		</div><input type="text" name="edit_name" value="<?php echo $user->name ?>" />
		<div>
			Login :
		</div><input type="text" name="edit_login" value="<?php echo $user->login ?>" />
		<div>
			Email :
		</div><input type="text" name="edit_email" value="<?php echo $user->email ?>" />
		<div>
			Password :
		</div><input type="text" name="edit_password" value="<?php echo $user->password ?>" />
		<div>
			<input type="hidden" name="id" value="<?php echo $_GET['edit']; ?>" />
			<input type="submit" name="doEdit" />
		</div>			
	<?php	}	?>
	<div style="display: block; float: left; padding-right: 20px;">
	<h1>Try to login</h1>
	<?php
	if (isset($logOk)) {
		echo '<h2>';
		if ($logOk) {
			echo 'Was connected !';
		} else echo 'Login error';
		echo '</h2>';
	}
	?>
	<form method="post" action="index.php">
		<div>
			Login : 
		</div>
		<input type="text" name="login" />
		<div>
			Password : 
		</div>
		<input type="password" name="password" />
		<div>
			<input type="submit" name="doLogin" />
		</div>
	</form>	
	</div>
	<div style="display: block; float: left; padding-left: 20px; border-left: dotted 1px #999999;">
	<h1>Add a new user</h1>
	<form method="post" action="index.php">
		<div>
			Name :
		</div><input type="text" name="add_name" />
		<div>
			Login :
		</div><input type="text" name="add_login" />
		<div>
			Email :
		</div><input type="text" name="add_email" />
		<div>
			Password :
		</div><input type="text" name="add_password" />
		<div>
			<input type="submit" name="doAdd" />
		</div>		
	</form>
	</div>
</body>
</html>