<?php
	function msg_show() {
		/*
		$sql= "SELECT * FROM message";

		$result = mysqli_query($link, $sql);

		$row = @mysqli_fetch_array($result);

		for ($i=0; $i<sizeof($row); $i++) {


			print("Message: ".$row[$i]['msg']);
			print("<br>Name: ".$row[$i]['name']);
			print("<br>Time: ".$row[$i]['time']."<br>");
		}
		*/
		$sql= "SELECT * FROM message";

		$result = mysqli_query($link, $sql);

	    while(($row = @mysqli_fetch_array($result))) {
	            $rows[] = $row;
	    }
		var_dump($rows);
	}


	function msga_add() {
		$sql = "INSERT INTO message (name, msg, time, sn) VALUES ()";
		if(mysqli_query($link, $sql)){
			echo ("<script>window.alert('Message has been added!')
			location.href='index.php';</script>");
			exit();
		}
		else {
			echo "Error";
		}
	}

	function test() {
		$sql = $pdo->prepare('SELECT User,Host FROM mysql.user');
		$sql->execute();
		$row = $sql->fetch();
		$sql->closeCursor();
		//$result = mysqli_query($link, $sql);

		var_dump($row);
	}


?>