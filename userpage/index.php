<?php
	include "C:\wamp64\www\\ecommerceP3\mysql_connect.php";
	include "C:\wamp64\www\\ecommerceP3\update_cookie.php";


	if(!empty($_REQUEST['add'])){
		update_cookie($_REQUEST['add']);
	}
?>




<table width=95% align="center">
<tr><td><font size="5"><b>Fruits & Vegitable Shop</b></font></td><td align="right">
	<?php if (empty($_COOKIE['login']))
			{
				echo"<a href=\"login_register.php?id=1\">Login</a>";
			}  ?>
			| <a href="index.php">Home</a> | <a href="fruits.php">Fruits</a>  | <a href="vegetables.php">Vegetables</a>  | <a href="user_cart.php">Cart [Rs. <?php if (!empty($_COOKIE['user_cart_price'])) { echo $_COOKIE['user_cart_price'];} else{
	echo "0.00";
} ?>]</a> </td></tr>
</table><br>


<table width=95% align="center" border="0" cellpadding="5">

	<?php
		$sql = "SELECT slno, name, mrp, price, image FROM products WHERE status= 1";
		$result = $mysqli->query($sql);
		if($result->num_rows>0){
			$counter=0;
			while($row=$result->fetch_assoc()){
				
				if($counter%3==0){
					echo "<tr>";
				}
				?>
				<td width="33%">
				<table width=100% align="center" border="0">
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
				if($counter==6){
					break;
				}

			}
		}
		else{
			echo "Error: " . $sql . "<br>" . $mysqli->error;
		} ?>
</table><br>

