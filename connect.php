<?php

$Name 	 = $_POST['Name'];
$gender  = $_POST['gender'];
$email   = $_POST['email'];
$number  = $_POST['number'];
$address = $_POST['address'];
$data    = "";

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "Secure_Form";


function clean($string) {
   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.

   return str_replace("_"," ",preg_replace('/[^A-Za-z0-9\-\/,_]/', '', $string)); // Removes special chars.
}

if( empty($Name) || empty($gender) || empty($email) || empty($number) || empty($address)){
  echo "Please fill all the fields.";
  exit();
}

if (!preg_match("/^[a-zA-Z ]*$/",$Name)) {
  echo "Only letters and white space allowed.";
  exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo "Invalid email format.";
  exit();
}

if(!preg_match("/^[6-9]{1}[0-9]{9}$/", $number)) {
  echo "Phone number is invalid.";
  exit();
}

$address = clean($address);

if(isset($_POST['submit'])){

  $type = exif_imagetype($_FILES['photo']['tmp_name']);
  $GLOBALS['data'] = addslashes(file_get_contents($_FILES['photo']['tmp_name']));
  if(!(in_array($type , array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)))){
    echo "Invalid Image format.";
    exit();
  }
}
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  echo "$conn->connect_error";
  die("Connection failed: " . $conn->connect_error);
}
else {

  $stmt = "INSERT INTO `userinfo`(`Name`, `Gender`, `Email`, `Phone`, `Address`, `Image`) VALUES('$Name', '$gender', '$email', '$number', '$address', '$data' )";
  $query_run = mysqli_query($conn, $stmt);
  
  if($query_run){
    echo "Registration Successfull...";
  }
  else{
    echo "Registration Failed! ";
  }

}

$query = "SELECT * FROM userinfo";
if($result = mysqli_query($conn, $query)){
    if(mysqli_num_rows($result) > 0){
        echo "<table>";
            echo "<tr>";
                echo "<th>Name</th>";
                echo "<th>Gender</th>";
                echo "<th>Email</th>";
                echo "<th>Phone</th>";
		echo "<th>Address</th>";
		echo "<th>Image</th>";
            echo "</tr>";
        while($row = mysqli_fetch_array($result)){
            echo "<tr>";
                echo "<td>" . $row['Name'] . "</td>";
                echo "<td>" . $row['Gender'] . "</td>";
                echo "<td>" . $row['Email'] . "</td>";
                echo "<td>" . $row['Phone'] . "</td>";
				echo "<td>" . $row['Address'] . "</td>";
				?>
				<td> <?php echo '<img src="data:image;base64,'.base64_encode($row['Image']).'" alt="Image" style="width=100px;height=100px" >'; ?> </td>
				<?php
            echo "</tr>";
        }
        echo "</table>";
        // Free result set
        mysqli_free_result($result);
    } else{
        echo "No records matching your query were found.";
    }
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
}


$conn->close();

?>