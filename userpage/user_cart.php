<?php  
include "C:\wamp64\www\\ecommerceP3\mysql_connect.php";
include "C:\wamp64\www\\ecommerceP3\update_cookie.php";

if(!empty($_POST['action']) && $_REQUEST['action']=="yes"){
	if (!empty($_POST['update_quantity'])) {
		// echo print_r($_POST['update_quantity']);
		// exit;
		foreach ($_POST['update_quantity'] as $key => $value) {
			// echo "here";
			if(update_cookie($key, $value))	;
		}
	}
}

?>

<html>
<head>
</head>
<body>


<table width=95% align="center">
<tr><td><font size="5"><b>Fruits & Vegitable Shop</b></font></td><td align="right"><a href="index.php">Home</a> | <a href="fruits.php">Fruits</a>  | <a href="vegetables.php">Vegetables</a>  | <a href="user_cart.php">Cart [Rs. <?php if (!empty($_COOKIE['user_cart_price'])) {
	echo $_COOKIE['user_cart_price'];
}else{
	echo "0.00";
} ?>]</a> </td></tr>
</table><br>

<form action="user_cart.php" method="post">
<table width=95% align="center" border="0" cellpadding="5">
<tr><td colspan="3"><h3> Your Cart</h3></td></tr>

<?php 
	if (isset($_COOKIE['user_cart'])) {
		$arr = json_decode($_COOKIE['user_cart'],true);
		// echo print_r($arr);
		// exit;
		foreach ($arr as $key => $value) {
			$sql = "SELECT name, price, image FROM products WHERE slno=".$key;
			$result=$mysqli->query($sql);
			if($result->num_rows>0){
				$row=$result->fetch_assoc();
			?>
				<tr><td><img src="<?php echo baseURL; ?>/product_img/<?php echo $row['image']; ?>" width="60" alt="<?php echo $row['name']; ?>" valign="top"><?php echo $row['name'];?> </td>
					<td>Rs.<?php echo $row['price'];?> / kg</td>
					<td> <input type="text" name="update_quantity[<?php echo $key; ?>]" value="<?php echo $value; ?>" size="4"></td>
					<td align="right">Rs.<?php echo $row['price']*$value;?></td></tr>
			<?php
			}
			else{
				echo "Error: " . $sql . "<br>" . $mysqli->error;
				// exit;
			}
		}
	}
	else{
		// echo "Cart Empty";
		?>  <tr><td>Cart Empty <a href="index.php">Click here to go to shop</a></td></tr>
	</table>
		<?php
		exit;
	}
	
?>

<input type="hidden" name="action" value="yes">
<tr><td colspan="3"><input type="submit" name="update_card" value="Update Cart"><br><br> </form> 
	<form action="checkout.php"><input type="submit" name="CheckOut" value="Proceed To CheckOut"></form></td><td align="right">Total : Rs.<?php echo $_COOKIE['user_cart_price'];?></td></tr>

</table><br>



</body>
</html>