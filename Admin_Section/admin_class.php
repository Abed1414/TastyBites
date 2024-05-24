<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
		include("DB_Connect.php");
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
		$stmt = $this->db->prepare("SELECT * FROM admins WHERE Admin_email = ? AND Admin_password = ?");
		$stmt->bind_param("ss", $email, $password);
		$stmt->execute();
		$result = $stmt->get_result();
	
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			foreach ($row as $key => $value) {
				$_SESSION['login_'.$key] = $value; 
			}
			return 1;
		} else {
			return 3;
		}
	}
	
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	function save_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
				if($k =='password')
					$v = md5($v);
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM Admins where Admin_email ='$email' ".(!empty($id) ? " and Admin_ID != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO Admins set $data");
		}else{
			$save = $this->db->query("UPDATE Admins set $data where Admin_ID = $id");
		}

		if($save){
			return 1;
		}
	}

	function update_user() {
		extract($_POST);
		$data = "";
		$id = $_POST['Admin_ID'];
		$number = $_POST['Admin_number'];
		$fullname = $_POST['Admin_fullname'];
		$email = $_POST['Admin_email'];
		$password = $_POST['Admin_password'];
		$address = $_POST['Admin_address'];
	
		if (
			$fullname == "" || $email == "" || $password == "" || $address == "" || $number == ""
		) {
			echo 2; 
			exit;
		}
	
		if (strpos($email, "@") === false || strpos($email, ".") === false) {
			echo 3; 
			exit;
		}
	
		if (strlen($password) < 8 || (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password))) {
			echo 4; 
			exit;
		}
	
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('Admin_ID', 'table')) && !is_numeric($k)) {
				if ($k == 'Admin_password') {

				}
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM Admins WHERE Admin_email ='$email' AND Admin_ID != $id")->num_rows;
		if ($check > 0) {
			echo 5; 
			exit;
		}
		else {
			// Perform the database update
			$update_query = $this->db->query("UPDATE Admins SET $data WHERE Admin_ID = $id");
			if ($update_query) {
				echo 1; // Success
				exit;
			} else {
				echo 2; // General error
				exit;
			}
		}
	}
	function save_category(){
		extract($_POST);
		$data = " Category = '$name' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO Categories set ".$data);
		}else{
			$save = $this->db->query("UPDATE Categories set ".$data." where Category_ID=".$id);
		}
		if($save)
			return 1;
	}
	function delete_category(){
		extract($_POST);
		$update = $this->db->query("UPDATE Platters SET Category_ID = 1, Platter_status = 0 WHERE Category_ID = ".$id);
		if ($update) {
		$delete = $this->db->query("DELETE FROM Categories WHERE Category_ID = ".$id);
		if ($delete) {
			return 1;
		} else {
			return 0;
		}
		}
	}
	function save_timing() {
		extract($_POST);
		$data = "Timing = '$name'";
		
		if (empty($id)) {
			// Insert new record
			$save = $this->db->query("INSERT INTO Timings (Timing, Timing_status) VALUES ('$name', 1)");
		} else {
			// Update existing record
			$save = $this->db->query("UPDATE Timings SET $data, Timing_status = 1 WHERE Timing_ID = $id");
		}
		
		if ($save)
			return 1;
	}
	
	function flipp_timing_state() {
		extract($_POST);
		$update = $this->db->query("UPDATE Timings SET Timing_status = 1 - Timing_status WHERE Timing_ID = $id");
		if ($update) {
			return 1;
		} else {
			return 0;
		}
	}
	
	function save_menu(){
		extract($_POST);
		$data = " Platter = '$name' ";
		$data .= ", Platter_price = '$price' ";
		$data .= ", Platter_discount = '$discount' ";
		$data .= ", Category_ID = '$category_id' ";
		$data .= ", Timing_ID = '$timing_id' ";
		$data .= ", Platter_small_description = '$description' ";
		if(isset($status) && $status  == 'on')
		$data .= ", Platter_status = 1 ";
			else
		$data .= ", Platter_status = 0 ";

		if($_FILES['img']['tmp_name'] != ''){
					$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
					$move = move_uploaded_file($_FILES['img']['tmp_name'],'../Public_Section/assets/images/platter/'. $fname);
					$data .= ", Platter_image = '$fname' "; 

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO Platters set ".$data);
		}else{
			$save = $this->db->query("UPDATE Platters set ".$data." where Platter_ID=".$id);
		}
		if($save)
			return 1;
	}

	function delete_menu(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM Platters where Platter_ID = ".$id);
		if($delete)
			return 1;
	}

	function edit_reservations() {
		extract($_POST);

		$stmt = $this->db->prepare("UPDATE Reservation_settings SET Max_reservations = ? WHERE Reservation_settings = ?");
		if ($stmt) {
			$stmt->bind_param('is', $name, $id); 
			if ($stmt->execute()) {
				return 1;
			}
		} 
	
	}
	
	function save_pages(){
		extract($_POST);
		$ID = $pageId;                    
		if (isset($_POST['Update']) && $_POST['Update'] === 'Update') {
				$new_title = $_POST['title']; 
				$updateQuery = "UPDATE Pages SET Title = ? WHERE Page_ID = ?";
				$stmt = mysqli_prepare($conn, $updateQuery);
				mysqli_stmt_bind_param($stmt, "si", $new_title, $ID);
				mysqli_stmt_execute($stmt);

				if ($_SERVER["REQUEST_METHOD"] == "POST") {
					if (isset($_POST["state"])) {
						$newState = $_POST["state"];
						if ($newState === "activate") {
							$sql = "UPDATE Pages SET Active = 1 WHERE Page_ID = ?";
						} elseif ($newState === "inactivate") {
							$sql = "UPDATE Pages SET Active = 0 WHERE Page_ID = ?";
						}
				
						// Prepare and execute the SQL statement
						$stmtUpdateState = $conn->prepare($sql);
						$stmtUpdateState->bind_param("i", $ID);
						$stmtUpdateState->execute();
						$stmtUpdateState->close();
					}
				}
				

			$i = 1;
			while($i < $counter){
				$bodyId = $i;
				$bsid = "summernote" . $i;
				$newBodySection = $_POST[$bsid];
				$sqlUpdateBody = "UPDATE Pages_Body_Sections SET Body_Section = ? WHERE Page_ID = ? AND Body_Section_ID = ?";
				$stmtUpdateBody = mysqli_prepare($conn, $sqlUpdateBody);
				mysqli_stmt_bind_param($stmtUpdateBody, "sii", $newBodySection, $ID, $bodyId);
				mysqli_stmt_execute($stmtUpdateBody);
				mysqli_stmt_close($stmtUpdateBody);
				$i++;
			}
		}
	}


}