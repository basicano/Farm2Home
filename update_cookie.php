<?php

	// include "mysql_connect.php";

	function update_cookie($product_slno, $product_quantity="")
	{
		$product_quantity=(int)$product_quantity;
		$product_slno=(int)$product_slno;
		// echo "<br>".$product_slno." ".$product_quantity;

		global $mysqli;
		$sql = "SELECT price FROM products WHERE slno = ".$product_slno."";
		$result = $mysqli->query($sql);
		if($result->num_rows>0){
			$row=$result->fetch_assoc();
			$price = (float)$row['price'];
		}
		else{
			echo "Error: in update_cookie...slno missing" . $sql . "<br>" . $mysqli->error;
			exit;
		}

		
		
		if(empty($_COOKIE["user_cart"]))
		{
			$order = array($product_slno => 1 );
			setcookie("user_cart", json_encode($order));
			setcookie("user_cart_price","$price");

		}
		else
		{
			if(empty($product_quantity)){
				$c_price=$_COOKIE['user_cart_price'];
				$price=(float)$c_price+$price;
				setcookie("user_cart_price","$price");

				$arr = json_decode($_COOKIE['user_cart'],true);
				if (array_key_exists($product_slno,$arr)) {
					$c_quantity = (int)$arr[$product_slno]+ 1;
					$arr[$product_slno] = $c_quantity;
				}
			
				else{
					$arr[$product_slno]=1;
				}
				setcookie("user_cart", json_encode($arr));
			}
			else{

					// if (array_key_exists($product_slno,$arr)) {
					// 	$prev_quantity = (int)$arr[$product_slno];
					// 	$arr[$product_slno] = $product_quantity;
					// }
					// setcookie("user_cart", json_encode($arr));
				
					$arr = json_decode($_COOKIE['user_cart'],true);
				// echo print_r($arr);
					$prev_quantity = (int)$arr[$product_slno];
					$arr[$product_slno] = $product_quantity;
				// echo print_r($arr);	
				$new_arr=$arr;
					setcookie("user_cart", json_encode($new_arr));
					$_COOKIE['user_cart']=json_encode($new_arr);
				// $arr = json_decode($_COOKIE['user_cart'],true);
				// echo print_r($arr);

				$c_price=(float)$_COOKIE['user_cart_price'];
					$price = ($product_quantity-$prev_quantity)*$price;
					$price=$c_price+$price;
					setcookie("user_cart_price","$price");
					$_COOKIE['user_cart_price']=$price;
			}
		}
		return True;
	}
	
?>

