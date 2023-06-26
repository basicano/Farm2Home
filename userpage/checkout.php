<?php  
include "C:\wamp64\www\\ecommerceP3\mysql_connect.php";


if ( !empty($_REQUEST['action']) && $_REQUEST['action']=="register"){
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
			echo "Registered successfully";	
		} 
		else 
		{
		  echo "Error: " . $sql . "<br>" . $mysqli->error;
		  exit;
		}
				
		$sql = "INSERT INTO orders (email, user_name, mobile_num, user_order, payement, address) values('".$_REQUEST['email']."', '".$_REQUEST['user_name']."', '".$_REQUEST['mobile_num']."', '".$_COOKIE['user_cart']."', '".$_COOKIE['user_cart_price']."', '".$_REQUEST['address']."')" ;
		$result = $mysqli->query($sql);
		if ($result===True) {
			setcookie("user_cart", "");
			setcookie("user_cart_price", 0.00);
			echo "<br>Order successfully placed<br>";
			exit;
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
elseif( !empty($_REQUEST['action']) && $_REQUEST['action']=="login"){

	// echo $_REQUEST['login_email'];
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
				$sql1 = "INSERT INTO orders (email, user_order, payement) values('".$_REQUEST['login_email']."', '".$_COOKIE['user_cart']."', '".$_COOKIE['user_cart_price']."')" ;
				$result1 = $mysqli->query($sql1);
				if ($result1===True) {
					setcookie("user_cart", "");
					setcookie("user_cart_price", 0.00);
					echo "Order successfully placed<br>";
					exit;
				} 
				else 
				{
				  echo "Error: " . $sql . "<br>" . $mysqli->error;
				  exit;
				}
			}
		}
		else
		{
			$error_msg .= "This email is not registered with us<br>";
		}
	}

	echo $error_msg;
	if ($error_msg=="This email is not registered with us"){
		?><a href="checkout.php?id=2" title="register"><br>Click here to register now</a> <br>
		<?php
	echo "<br> Or enter correct email to login<br><br>";
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


<!-- VISIBLE USER CHECKOUT PAGE -->

<table width=95% align="center">

	<!-- HEADER -->
<tr><td><font size="5"><b>Fruits & Vegitable Shop</b></font></td><td align="right"><a href="index.php">Home</a> | <a href="fruits.php">Fruits</a>  | <a href="vegetables.php">Vegetables</a>  | <a href="user_cart.php">Cart [Rs. <?php if (!empty($_COOKIE['user_cart_price'])) {
	echo $_COOKIE['user_cart_price'];
}else{
	echo "0.00";
} ?>]</a> </td></tr>
</table><br>


 <table width=95% align="center" border="0" cellpadding="3">
 	<tr>
 		<td><h3>User must register/login for placing order</h3></td>
 		<td><h3>Your Order Summary</h3></td></tr>
 		<tr>
 		<td>	
 			<!-- User not logged in, give register or loign option -->
 			<?php if(empty($_REQUEST['id'])){
 			?>
 			<a href="checkout.php?id=1">Click here to login</a><br><br>
 			<a href="checkout.php?id=2">Click here to register</a>
 		<?php } 
 			elseif($_REQUEST['id']==2){
 				?>
 				<!-- User selected to register -->
 				<form action="checkout.php" method="post">
			
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
				<button type = "submit" name="place_order">PLACE ORDER</button>
				<input type="hidden" name="action" value="register">
				</form>
			<?php
 			}
 			elseif ($_REQUEST['id']==1) {
 				?>
 				<!-- User selected to login -->
 				<form action="checkout.php" method="post">
					<p>Enter your registered email id</p>
						<input type = "text" name= "login_email" placeholder="Enter Email" value="<?php if (!empty($_REQUEST['login']))echo $_REQUEST['login_email']; ?>" required><br><br>
					<p>Enter your password</p>
					<input type = "password" name= "login_pass" placeholder="Enter Password" value="<?php if (!empty($_POST['login']))echo $_POST['login_pass']; ?>" required><br><br>
					<input type="hidden" name="action" value="login">
					<button type = "submit" name="submit">SUBMIT</button>
				</form>
			<?php
 			}?>
			
		</td>
		<td><table width=95% align="center" border="0" cellpadding="3"><?php
			$arr = json_decode($_COOKIE['user_cart'],true);
			foreach ($arr as $key => $value) {
				$sql = "SELECT name, price, image FROM products WHERE slno=".$key;
				$result=$mysqli->query($sql);
				if($result->num_rows>0){
					$row=$result->fetch_assoc();
				?>
				
					<tr><td><img src="<?php echo baseURL; ?>/product_img/<?php echo $row['image']; ?>" width="50" alt="<?php echo $row['name']; ?>" valign="top"></td><td align="left"><?php echo $row['name'];?> </td>
						<td align="right">Rs.<?php echo $row['price']*$value;?></td>
					</tr>
					
				<?php
				}
				else{
					echo "Error: " . $sql . "<br>" . $mysqli->error;
					// exit;
				}
			}
			?>
			<tr><br></tr>
			<tr><td><b>Total Payable Amount :</b></td><td></td><td>Rs. <?php echo $_COOKIE['user_cart_price']; ?></td></tr>
		</table>
		</td>
	</tr>
</table>


<!-- 
$sql1 = "INSERT INTO orders (email, user_name, mobile_num, user_order, payment, address) values('".$_REQUEST['email']."', '".$_REQUEST['user_name']."', '".$_REQUEST['mobile_num']."', '".$_COOKIE['user_cart']."', '".$_COOKIE['user_cart_price']."', '".$_REQUEST['address']."')" ;

$result1 = $mysqli->query($sql1);
if ($result1===True) 
{
	setcookie("user_cart", "");
	setcookie("user_cart_price", 0.00);
	echo "Order successfully placed<br>";
	exit;
} 
else 
{
  echo "Error: " . $sql . "<br>" . $mysqli->error;
  exit;
}
 -->