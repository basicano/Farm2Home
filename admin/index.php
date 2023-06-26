<?php 

include "C:\wamp64\www\\ecommerceP3\mysql_connect.php";

function admin_options(){
	?> 
		<br><br><a href="index.php?id=1">Add Products</a>
		<br><br><a href="index.php?id=2">View Products</a>
		<br><br><a href="index.php?id=3">View All Orders</a>
		<br><br><a href="index.php?id=4">View Pending Orders</a>
		<br><br><a href="index.php?id=5">View Fulfilled Orders</a>		
	<?php
}

function add_product()
{
	global $mysqli;

	if(!empty($_POST['submit']) && $_POST['submit']=="yes")
	{
		$temp = array("Exotic"=>"", "Seasonal"=>"", "Organic"=>"", "Staple"=>"", "Fresh"=>"", "Frozen"=>"");
		foreach ($_POST['product_tags'] as $key => $set_val)
		{
			foreach ($temp as $t_key => $val) 
			{
				if($key == $t_key)
				{
					$temp[$key]=$set_val;
				}
			}
		}
		$_POST['product_tags'] = $temp;

		$error_msg="";
		if(!empty($_POST['product_name'])){
			$sql = "SELECT slno FROM products WHERE name='".$_POST['product_name']."'  ";
			$result=$mysqli->query($sql);
			if($result->num_rows>0){
				$error_msg.="Product by the same name already exists<br>";
			}
		}

		if(!is_numeric($_POST['product_mrp']) ){
			$error_msg .="MRP of the product must be valid numbers<br>";
		}
		else{
			$_POST['product_mrp']= number_format($_POST['product_mrp'],2);
		}

		$product_discount=0;
		if(!empty($_POST['product_price']) ){
			if(!is_numeric($_POST['product_price'])){
				$error_msg .="Discounted Selling Price of the product must be valid numbers<br>";
			}
			else{
				
				$_POST['product_price']= number_format($_POST['product_price'],2);
				if( $_POST['product_price'] > $_POST['product_mrp']){
					$error_msg = "Discounted price cannot be more than the actual selling price .. please enter valid value<br>";
				}
				elseif( $_POST['product_price'] < $_POST['product_mrp'] ){
					$product_discount = (($_POST['product_mrp']-$_POST['product_price'])/$_POST['product_mrp'])*100;
				}
			}
		}
		else{
			$_POST['product_price']=$_POST['product_mrp'];

		}

		if(!empty($_FILES['product_image']["name"])){
			$filename = $_FILES['product_image']["name"]; 
		    $tempname = $_FILES['product_image']["tmp_name"];  
		    $target_dir = "C:\wamp64\www\\ecommerceP3\product_img\\".basename($filename); 

			$check = getimagesize($tempname);
		  	if($check === false) {
		  		$error_msg.= "File is not an image .. please recheck";
		  	} 
		}

		

		if(empty($error_msg) )
		{

			$sql = "INSERT INTO products (name, quantity, mrp, price, discount, type, tags, description, image) values('".$_POST['product_name']."', '".$_POST['product_quantity']."', '".$_POST['product_mrp']."', '".$_POST['product_price']."', '".number_format($product_discount,2)."', '".$_POST['product_type']."', '".implode(",", array_filter($_POST['product_tags']))."', '".$_POST['product_description']."', '".$filename."') ";

			$result = $mysqli->query($sql);
			if ($result === TRUE) {
				if (move_uploaded_file($tempname, $target_dir))  
		        { 
		            echo "Image uploaded successfully"; 
		            echo "New record created successfully"; 
		            view_product($_POST['product_name']);
		            return True;
		        }
		        else
		        { 
		            echo "Failed to upload image"; 
		            return False;
			    }
			} 
			else 
			{
			  echo "Error: " . $sql . "<br>" . $mysqli->error;
			}
		}	 
		else
		{
			echo "<br>" . $error_msg;
		}
	}
	else
	{
			$_POST['product_name']="";
			$_POST['product_quantity']="";
			$_POST['product_mrp']="";
			$_POST['product_price']="";
			$_POST['product_type']="";
			$_POST['product_tags'] = array("Exotic"=>"", "Seasonal"=>"", "Organic"=>"", "Staple"=>"", "Fresh"=>"", "Frozen"=>"");
			$_POST['product_description']="";
			$_FILES['product_image']=NULL;
	}
	

		?>
			<form action="index.php?id=1" method="post" enctype="multipart/form-data">
				<p>Product Name</p>
					<input type = "text" name= "product_name" placeholder="Enter product name" value="<?php echo $_POST['product_name']; ?>" required><br><br>
				<p>Product Quantity (kg)</p>
					<input type = "text" name= "product_quantity" placeholder="Enter quantity in kg" value="<?php echo $_POST['product_quantity']; ?>"><br><br>
				<p>Product MRP per UNIT</p>
					<input type = "text" name= "product_mrp" placeholder="Enter product MRP" value="<?php echo $_POST['product_mrp']; ?>" required><br><br>
				<p>Product Discounted Selling Price per UNIT(OPTIONAL)</p>
					<input type = "text" name= "product_price" placeholder="Enter selling price" value="<?php echo $_POST['product_price']; ?>"><br><br>				
				<p>Product Type</p>
					<label><input type = "radio" name= "product_type" <?php if ($_POST['product_type']=="vegetable") echo "checked"; ?> value="vegetable" required>Vegetable<br>
					</label>
					<label><input type = "radio" name= "product_type" <?php if ($_POST['product_type']=="fruit") echo "checked"; ?> value="fruit" required>Fruit<br><br>
					</label>
				<p>Product Tags/Features</p>
				<?php 
					foreach($_POST['product_tags'] as $key=>$val){
				?>
					<label><input type = "checkbox" name="product_tags[<?php echo $key;?>]" value ="<?php echo $key?>" <?php if ($key==$val) echo "checked"; ?> ><?php echo $key;?><br>
					</label>
				<?php
					}
				?>
				<p>Product Description</p>
					<textarea name="product_description" rows="5" cols="65" placeholder="Describe the product"><?php echo $_POST['product_description'];?></textarea><br> 
				<p>Insert Product Image/s</p>
					<input type = "file" name= "product_image" ><br><br>
				<button type = "submit" name="add_product">ADD</button>
				<input type="hidden" name="submit" value="yes">
			</form>
		<?php		
}

function view_product($product_name){
	global $mysqli;

	$sql  = "SELECT name, quantity,mrp,price,discount,type,tags,description,image FROM products WHERE name='".$product_name."'";
	$result = $mysqli->query($sql);

	if($result->num_rows>0){
		$row = $result->fetch_assoc();
		?>
			<table style="width:100%">
				<tr>
					<td>CATEGORY</td>
					<td>PRODUCT INFORMATION</td>
				</tr>
				<tr>
					<td>Name</td>
					<td><?php echo $row['name'];?></td>
				</tr> 
				<tr>
					<td>Quantity (kg)</td>
					<td><?php echo $row['quantity'];?></td>
				</tr>
				<tr>
					<td>MRP</td>
					<td><?php echo $row['mrp'];?></td>
				</tr>
				<tr>
					<td>Discounted Selling Price</td>
					<td><?php echo $row['price'];?></td>
				</tr>
				<tr>
					<td>Discount</td>
					<td><?php echo $row['discount'];?></td>
				</tr>

				<tr>
					<td>Type</td>
					<td><?php  echo $row['type']; ?>
						</td>
				</tr>
				<tr>
					<td>Tags/Features</td>
					<td><?php 
						foreach (explode(',',$row['tags']) as  $val) 
						{			
								echo $val . " ";
						} 
					?></td>
				</tr>
				<tr>
					<td>Description</td>
					<td> <?php echo $row['description']; ?> </td>
				</tr>
				<tr>
					<td>Image/s</td>
					<td><img src="<?php echo baseURL; ?>/product_img/<?php echo $row['image']; ?>"  width="100" height="100"> 
					</td>
				</tr>
			</table>
			<br>
			<a href="index.php?id=1">Click here to add another product</a>
			<a href="index.php">Click here return to admin home page</a>
		<?php
		return True;
	}

}

function view_order($order_slno){
	global $mysqli;
	$sql = "SELECT slno, status, email, user_name, mobile_num, user_order,payement,address, date_time FROM orders WHERE slno=".$order_slno."";
	$result = $mysqli->query($sql);

	if($result->num_rows>0){
		$row = $result->fetch_assoc();
		?>
			<table style="width:100%">
				<tr>
					<td>CATEGORY</td>
					<td>ORDER INFORMATION</td>
				</tr>
				<tr>
					<td>Slno</td>
					<td><?php echo $row['slno'];?></td>
				</tr> 
				<tr>
					<td>Status </td>
					<td><?php if($row['status']==0) echo "Pending"; else echo "Fulfilled";?></td>
				</tr>
				<tr>
					<td>Email</td>
					<td><?php echo $row['email'];?></td>
				</tr>
				<tr>
					<td>User Name</td>
					<td><?php echo $row['user_name'];?></td>
				</tr>
				<tr>
					<td>Mobile Number</td>
					<td><?php echo $row['mobile_num'];?></td>
				</tr>

				<tr>
					<td>Order</td>
					<td><table>
							<tr>
								<td>PRODUCT SLNO</td>
								<td>PRODUCT NAME</td>
								<td>QUANTITY ORDERED</td>
							</tr>
						<?php
						 $arr = json_decode($row['user_order'],true);
							foreach ($arr as $key => $value) {
								$sql1="SELECT name FROM products WHERE slno = ".$key;
								$done = $mysqli->query($sql1);

								if($done->num_rows>0){
									$row1 = $done->fetch_assoc();
								?>
									<tr>
										<td><?php echo $key; ?></td>
										<td><?php echo $row1['name'];?></td>
										<td><?php echo $value;?></td>
									</tr> 
								<?php
								}
							}
						 ?></table>
					</td>
				</tr>
				<tr>
					<td>Payment Made</td>
					<td>Rs. <?php echo $row['payement'] ;?></td>
				</tr>
				<tr>
					<td>Address</td>
					<td> <?php echo $row['address']; ?> </td>
				</tr>
				<tr>
					<td>Date and Time</td>
					<td><?php echo $row['date_time']; ?>"</td>
				</tr>
			</table>
			<br>
			<a href="index.php?id=1">Click here to add another product</a>
			<a href="index.php">Click here return to admin home page</a>
		<?php
		return True;
	}
}


if(!empty($_REQUEST['id']))
{
	if($_REQUEST['id']==1){

		add_product();
		exit;
	}
	elseif ($_REQUEST['id']==2) 
	{
		$sql = "SELECT name, price FROM products";
		$result = $mysqli->query($sql);

		if ($result->num_rows > 0) 
		{
			?>
			<table style="width:100%">
				<tr>
					<td><?php admin_options();?></td>
					<td><h1>YOUR PRODUCTS</h1></td> 
					<td></td> 
					<td></td>
				</tr>

				<tr>
					<td></td>
					<td>PRODUCT NAME</td> 
					<td>SELLING PRICE</td> 
					<td>OPTIONS</td>
				</tr>
			<?php
			while($row=$result->fetch_assoc()) {
				?>
					<tr>
						<td></td>
						<td><?php echo $row['name']?> </td>
						<td><?php echo $row['price']?> </td>
						<td><a href="index.php?id=2&op=1&product_name=<?php echo $row['name'];?>">| View Details |</a></td>
					</tr><?php
			}?>
			</table><?php
			if(!empty($_REQUEST['op'])&&$_REQUEST['op']==1){
				echo "<br><br>";
				view_product($_REQUEST['product_name']);
				exit;
			}
			exit;	
		}
		else
		{
			admin_options();
			echo "<br><br> 	0 results  ";
		}
			
	}
	elseif ($_REQUEST['id']==3) {
		$sql = "SELECT slno, status FROM orders";
		$result = $mysqli->query($sql);

		if ($result->num_rows > 0) 
		{
		?>
		<table style="width:100%">
				<tr>
					<td><?php admin_options();?></td>
					<td><h1>ALL ORDERS</h1></td> 
					<td></td> 
					<td></td>
				</tr>

				<tr>
					<td></td>
					<td>ORDER SLNO</td> 
					<td>ORDER STATUS</td> 
					<td>OPTIONS</td>
				</tr>
			<?php
			while($row=$result->fetch_assoc()) {
				?>
					<tr>
						<td></td>
						<td><?php echo $row['slno'];?> </td>
						<td><?php echo $row['status'];?> </td>
						<td><a href="index.php?id=4&op=1&order_slno=<?php echo $row['slno'];?>">View Details</a> | <a href="index.php?id=4&op=2&order_slno=<?php echo $row['slno'];?>"> Update Status</a></td>
					</tr><?php
			}?>
			</table><?php
			if(!empty($_REQUEST['op'])&&$_REQUEST['op']==1){
				echo "<br><br>";
				view_order($_REQUEST['order_slno']);
				exit;
			}
			elseif (!empty($_REQUEST['op'])&&$_REQUEST['op']==2) {
				echo "here";
				$sql3 = "UPDATE orders SET status = 1 WHERE slno =".$_REQUEST['order_slno'];
				$result3 = $mysqli->query($sql);
				if ($mysqli->query($sql3)===True) 
				{
					header("Location:  http://localhost/ecommerceP3/admin/index.php?id=3");
				} 
				else 
				{
				  	echo "Error: " . $sql3 . "<br>" . $mysqli->error;
				}
			}
			exit;	
		}
		else
		{
			admin_options();
			echo "<br><br> 	0 results";
		}
	}
	elseif ($_REQUEST['id']==4) {
		$sql = "SELECT slno, status FROM orders WHERE status = 0";
		$result = $mysqli->query($sql);

		if ($result->num_rows > 0) 
		{
		?>
		<table style="width:100%">
				<tr>
					<td><?php admin_options();?></td>
					<td><h1>ALL ORDERS</h1></td> 
					<td></td> 
					<td></td>
				</tr>

				<tr>
					<td></td>
					<td>ORDER SLNO</td> 
					<td>ORDER STATUS</td> 
					<td>OPTIONS</td>
				</tr>
			<?php
			while($row=$result->fetch_assoc()) {
				?>
					<tr>
						<td></td>
						<td><?php echo $row['slno']?> </td>
						<td><?php echo $row['status']?> </td>
						<td><a href="index.php?id=4&op=1&order_slno=<?php echo $row['slno'];?>">View Details</a> | <a href="index.php?id=4&op=2&order_slno=<?php echo $row['slno'];?>"> Update Status</a></td>
					</tr><?php
			}?>
			</table><?php
			if(!empty($_REQUEST['op'])&&$_REQUEST['op']==1){
				echo "<br><br>";
				view_order($_REQUEST['order_slno']);
				exit;
			}
			elseif (!empty($_REQUEST['op'])&&$_REQUEST['op']==2) {
				$sql3 = "UPDATE orders SET status = 1 WHERE slno =".$_REQUEST['order_slno'];
				$result3 = $mysqli->query($sql);
				if ($mysqli->query($sql3)===True) 
				{
					header("Location:  http://localhost/ecommerceP3/admin/index.php?id=3");
				} 
				else 
				{
				  	echo "Error: " . $sql3 . "<br>" . $mysqli->error;
				}
			}
			exit;	
		}
		else
		{
			admin_options();
			echo "<br><br> 	0 results";
		}
	}
	elseif ($_REQUEST['id']==5) {
		$sql = "SELECT slno, status FROM orders WHERE status = 1";
		$result = $mysqli->query($sql);

		if ($result->num_rows > 0) 
		{
		?>
		<table style="width:100%">
				<tr>
					<td><?php admin_options();?></td>
					<td><h1>ALL ORDERS</h1></td> 
					<td></td> 
					<td></td>
				</tr>

				<tr>
					<td></td>
					<td>ORDER SLNO</td> 
					<td>ORDER STATUS</td> 
					<td>OPTIONS</td>
				</tr>
			<?php
			while($row=$result->fetch_assoc()) {
				?>
					<tr>
						<td></td>
						<td><?php echo $row['slno']?> </td>
						<td><?php echo $row['status']?> </td>
						<td><a href="index.php?id=5&op=1&order_slno=<?php echo $row['slno'];?>">View Details</a></td>
					</tr><?php
			}?>
			</table><?php
			if(!empty($_REQUEST['op'])&&$_REQUEST['op']==1){
				echo "<br><br>";
				view_order($_REQUEST['order_slno']);
				exit;
			}
			exit;	
		}
		else
		{
			admin_options();
			echo "<br><br> 	0 results";
		}
	}
}
else{
	admin_options();
	exit;
}

?>
