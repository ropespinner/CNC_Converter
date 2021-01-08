<?php
require_once('../wp-load.php');
$fileIncPath = getcwd();
include ($fileIncPath."/cnc_classes.php");
include ($fileIncPath."/UserCommands.php");

session_start();
	
$UserCommands[] = new ClassUserCommand(1, 'convert_M215_instructions');
$UserCommands[] = new ClassUserCommand(2, 'parse_M188_instructions');
$UserCommands[] = new ClassUserCommand(3, 'parse_M215_instructions');		

$PostCommand = $_SESSION["usrcmd"];
$UserCommand = null;

foreach ($UserCommands as $UserCommand)
{
	if ($UserCommand->PostNumber == $PostCommand) break;
}
	
if (empty ($UserCommand)) 
	
	echo "UserCommand not found<br>";

else call_user_func($UserCommand->FunctionName, $UserCommand);

echo "endgame<br>";

?>