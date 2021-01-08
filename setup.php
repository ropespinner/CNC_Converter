<?php
/*
Receives user input with submit from index.php
*/

require_once('../wp-load.php');

$fileIncPath = getcwd();

include ($fileIncPath."/file_handling.php");
include ($fileIncPath."/cnc_parser.php");

session_start();

switch ($_SESSION["usrcmd"])
{
		case 1:
			echo "<br>M215 CNC conversion to M188 not yet available<br>";
			break;		
		
		case 2:
		case 3:
			if (parse_cnc_file())
			{
				echo "<br>Ready parsing<br>";
			}
			else
			{
				echo "<br>Machine unknown<br>";
			}
			break;
			
		default:
			echo "<br>Function not available<br>";
			break;
}
?>
