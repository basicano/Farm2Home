<?php
	include "C:\wamp64\www\\ecommerceP3\mysql_connect.php";
	include "C:\wamp64\www\\ecommerceP3\update_cookie.php";

	// if the 'add' parameter is present in the request and not empty. If it is, it calls the update_cookie() function from the included "update_cookie.php" file, passing the value of the 'add' parameter.
	if(!empty($_REQUEST['add'])){
		update_cookie($_REQUEST['add']);
	}
?>



<!-- heading "Fruits & Vegetable Shop" and a navigation menu -->
<table width=95% align="center">
<tr><td><font size="5"><b>Fruits & Vegitable Shop</b></font></td><td align="right">
	<!-- If the 'login' cookie is empty, it displays a "Login" link. -->
	<?php if (empty($_COOKIE['login']))
			{
				echo"<a href=\"login_register.php?id=1\">Login</a>";
			}  ?>
			| <a href="index.php">Home</a> | <a href="fruits.php">Fruits</a>  | <a href="vegetables.php">Vegetables</a>  | <a href="user_cart.php">Cart [Rs. <?php if (!empty($_COOKIE['user_cart_price'])) { echo $_COOKIE['user_cart_price'];} else{
	echo "0.00";
} ?>]</a> </td></tr>
</table><br>

<!-- table that contains product information retrieved from the database. -->
<table width=95% align="center" border="0" cellpadding="5">

	<?php
		$sql = "SELECT slno, name, mrp, price, image FROM products WHERE status= 1";
		// query result is stored in the $result variable using the $mysqli->query() method.
		$result = $mysqli->query($sql);
		if($result->num_rows>0){
			// there are rows returned by the query (i.e., $result->num_rows is greater than 0), the code enters a loop
			$counter=0;
			while($row=$result->fetch_assoc()){
				// Every three products, it starts a new row.
				if($counter%3==0){
					echo "<tr>";
				}
				?>
				<td width="33%">
				<table width=100% align="center" border="0">
<!-- 					For each product, it displays an image, name, price, and a "BUY NOW" link -->
				<tr><td align="center"><img src="<?php echo baseURL; ?>/product_img/<?php echo $row['image']; ?>" width="200" alt="<?php echo $row['name']; ?>"></td></tr>
				<tr><td align="center"><b><?php echo $row['name'];?></b></td></tr>
				<tr><td align="center"><?php if ($row['price']!=$row['mrp']) {
					echo "<strike>Rs.".$row['mrp']."</strike> ";
				} echo "Rs.".$row['price'];?> / kg</td></tr>
				<tr><td align="center"><a href="index.php?add=<?php echo $row['slno'];?>"><b>BUY NOW</b></a></td></tr>
				</table>
				</td>
				<?php

				$counter++;
				if($counter%3==0){
					echo "<tr><td colspan=\"3\"><br><br></td></tr>";
					echo "</tr>";
					
				}
				// only 6 items on home page
				if($counter==6){
					break;
				}

			}
		}
		else{
			echo "Error: " . $sql . "<br>" . $mysqli->error;
		} ?>
</table><br>

