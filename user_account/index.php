<?php

	include "C:\wamp64\www\\ecommerceP3\mysql_connect.php";

	if(!empty($_REQUEST['logout']) && $_REQUEST['logout']=="yes"){
		echo "LOGOUT SUCCESSFUL";
	}

	if(!empty($_REQUEST['login']) && $_REQUEST['login']=="true")
	{
		$error_msg = "";
		if (!stristr($_REQUEST['login_email'],".") || !stristr($_REQUEST['login_email'],"@") ){
			$error_msg .= "INVALID EMAIL ID<br>";
		}
		else
		{	
			$sql = "SELECT slno, email, password FROM registered_users WHERE email = '".$_REQUEST['login_email']."' ";
			$result = $mysqli->query($sql);

			if ($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();
				if($_REQUEST['login_pass']!=$row['password'])
				{
					$error_msg .= "Password is incorrect<br>";
				}
				else
				{
					echo "LOGIN SUCCESSFUL";
					setcookie("my_login", $row['slno']);
					header("Location: http://localhost/ecommerceP3/userpage/index.php");
					exit;
				}
			}
			else
			{
				$error_msg .= "This email is not registered with us<br>";
			}
		}

		echo $error_msg;
		if ($error_msg=="This email is not registered with us"){
			?><a href="form.php" title="register"><br>Click here to register now</a> <br>
			<?php
		echo "<br> Or enter correct email to login<br><br>";}
	}
?>

<html>
<body>
	<form action="login.php" method="post">
		<p>Enter your registered email id</p>
			<input type = "text" name= "login_email" placeholder="Enter Email" value="<?php if (!empty($_POST['login']))echo $_POST['login_email']; ?>" required><br><br>
		<p>Enter your password</p>
		<input type = "password" name= "login_pass" placeholder="Enter Password" value="<?php if (!empty($_POST['login']))echo $_POST['login_pass']; ?>" required><br><br>
		<input type="hidden" name="login" value="true">
		<button type = "submit" name="submit">SUBMIT</button>
	</form>
</body>
</html>