<!DOCTYPE html>
<html>
<head>
<script>
function validateForm() {
	
  var y = document.forms["FunctionForm"]["func"].value;
  if (y == "") {
    alert("Function must be selected");
    return false;
	
 }

}
</script>
</head>
<body>

<h3>CNC Conversion Program  </h3>
 <form name='FunctionForm' action="<?php echo $_SERVER['PHP_SELF'];?>" method='post' onsubmit='return validateForm()' >
Select  required function: <br>
<select multiple name='func'>
<option value=1>M215 to M188 CNC conversion</option>
<option value=2>M188 CNC parsing</option>
<option value=3>M215 CNC parsing</option></select><br>
<input type='submit' value='Submit'>
</form>

<?php
session_start();
 
 $request_inputfile = false;
 $usrcmd = null;

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$usrcmd =  $_POST['func'];
  
	if (empty($usrcmd)) 
	{
		echo "No function selected";
		exit();
	} 
	else if($usrcmd ==1)
	{
		echo "<br>Function selected is M215 to M188 CNC conversion<br><br>";
		$request_inputfile = true;
	}
	else if($usrcmd ==2)
	{
		echo "<br>Function selected is M188 CNC parsing <br><br>";
		$request_inputfile = true;
	}
	else if($usrcmd ==3)
	{
		echo "<br>Function selected is M215 CNC parsing <br><br>";
		$request_inputfile = true;
	}
  
	$_SESSION["usrcmd"] = $usrcmd;
	
	unset($_POST);	

	if ($request_inputfile)
	{	
		echo '<h3>Select Input File:  </h3>
				<form name="FileInputForm" action="usrcmd.php" method="post" enctype="multipart/form-data">
				<input type="file" name="uploadFile"><br>
				<input type="submit" value="Submit">
				</form>';
	}
}
?>
</body>
</html>


