<?php 
	include "C:\wamp64\www\\ecommerceP3\mysql_connect.php";
	if( !empty($_REQUEST['action']))
	{
		if($_REQUEST['action']=="login")
		{
			$error_msg = "";
			if (!stristr($_REQUEST['login_email'],".") || !stristr($_REQUEST['login_email'],"@") )
			{
				$error_msg .= "INVALID EMAIL ID<br>";
			}
			else
			{	
				$sql = "SELECT slno, email, password FROM registeration WHERE email = '".$_REQUEST['login_email']."' ";
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

						if(!empty($_REQUEST['checkout'])&&$_REQUEST('checkout')=="True"){
							header("Location: http://localhost/ecommerceP3/checkout.php");
						}
						echo "LOGIN SUCCESSFUL";
					}

				}
				else
				{
					$error_msg .= "This email is not registered with us<br>";
				}
			}

			echo $error_msg;
			if ($error_msg=="This email is not registered with us")
			{
				?><a href="login_register.php?id=2" title="register"><br>Click here to register now</a> <br><?php
				echo "<br> Or enter correct email to login<br><br>";
			}
		}
		else if($_REQUEST['action']=="register")
		{
			$error_msg = "";
			if (!stristr($_REQUEST['email'],".") || !stristr($_REQUEST['email'],"@") )
			{
				$error_msg = "INVALID EMAIL ID<br>";
			}
			else
			{			
				$sql = "SELECT slno, email FROM registered_users WHERE email = '".$_REQUEST['email']."' ";
				$result = $mysqli->query($sql);

				if ($result->num_rows > 0)
				{
					$error_msg .= "Email id already exists<br>";
				}
			}

			if (!preg_match('/^[0-9]{10}+$/', $_REQUEST['mobile_num']) && !preg_match('/^\+[0-9]{1,2}-[0-9]{3}-[0-9]{3}-[0-9]{4}$/', $_REQUEST['mobile_num']) )
			{
					$error_msg .= "INVALID MOBILE NUMBER<br>";
			}

			if (empty($error_msg))
			{
				$sql = "INSERT INTO registered_users (email, password, user_name, mobile_num, address) values('".$_REQUEST['email']."', '".$_REQUEST['password']."', '".$_REQUEST['user_name']."', '".$_REQUEST['mobile_num']."', '".$_REQUEST['address']."')" ;
				$result = $mysqli->query($sql);
				if ($result===True) {

					if(!empty($_REQUEST['checkout'])&&$_REQUEST('checkout')=="True"){
						header("Location: http://localhost/ecommerceP3/checkout.php?login_register=1");
					}
					echo "Registered successfully";	
				} 
				else 
				{
				  echo "Error: " . $sql . "<br>" . $mysqli->error;
				  exit;
				}
			}	
			else{
				echo $error_msg;
			}
		}
		else
		{
			echo "error";
		}
	}	
	else{
		$_POST['user_name']="";
		$_POST['mobile_num']="";
		$_POST['email']="";
		$_POST['address']="";
		$_POST['password']="";
	}	
?>

<table width=95% align="center">
	<tr>
		<td><font size="4"><b>Fruits & Vegitable Shop</b></font></td>
		<!-- <td align="center"></td> -->
	</tr>
	<tr> 
		<td> <font size= "5" align="center"><?php if (empty($_POST['registered'])) echo "LOGIN"; else echo "REGISTER" ?></font></td>			 
	</tr>
	<tr>
		<td><font size="2"><?php if (!empty($_REQUEST['id']) && $_REQUEST['id']=="1") echo "Not a member?"; else echo "Already a member?"; ?> </font><font size="3"><?php if (!empty($_REQUEST['id']) && $_REQUEST['id']=="1") echo "<a href=\"login_register.php?id=2\">REGISTER</a>"; else echo "<a href=\"login_register.php?id=1\">LOGIN</a>"; ?> </font></td>
	</tr>
</table><br>
<?php 
if( !empty($_REQUEST['id']) && empty($_REQUEST['action'])){
	if( $_REQUEST['id']=="1" ){
	?>
	<table width=95% align="center" cellpadding="3">
	 	<tr>
	 		<td><form action="login_register.php" method="post">
					<p>Email</p>
					<input type = "text" name= "login_email" placeholder="Enter your email id" value="<?php if (!empty($_REQUEST['login']))echo $_REQUEST['login_email']; ?>" required><br><br>
					<p>Password</p>
					<input type = "password" name= "login_pass" placeholder="Enter your password" value="<?php if (!empty($_POST['login']))echo $_POST['login_pass']; ?>" required><br><br>
					<input type="hidden" name="action" value="login">
					<input type="hidden" name="id" value=1>
					<button type ="submit" name="login">SUBMIT</button>
				</form>
	 		</td> 	
		</tr> 
	</table>
	<?php
	}
	else if($_REQUEST['id']=="2"){
		?>
		<table width=95% align="center" cellpadding="3">
		 	<tr><td><form action="login_register.php">			
				<p>Enter your name</p>
				<input type = "text" name= "user_name" placeholder="Enter name" value="<?php echo $_POST['user_name']; ?>" required><br><br>

				<p>Enter you email id</p>
				<input type = "text" name="email" placeholder ="Email" value="<?php echo $_POST['email']; ?>" required><br><br>

				<p>Set account password</p>
				<input type = "password" name="password" placeholder ="Password" value="<?php echo $_POST['password']; ?>" required><br><br>

				<p>Enter your mobile number</p>
				<input type = "text" name= "mobile_num" placeholder ="Mobile number" value="<?php echo $_POST['mobile_num']; ?>" maxlength="10" required><br><br>

				<p>Enter your delivery address</p>
				<textarea name="address"  rows="5" cols="65" placeholder="enter delivery address" required><?php echo $_POST['address']; ?> </textarea><br>  
				<button type = "submit" name="register">REGISTER</button>
				<input type="hidden" name="action" value="register">
				<input type="hidden" name="id" value=2></form></td>
			</tr>
		</table><?php
	}
}
else{
	// echo "error";
}
?>