<?php
require_once('../wp-load.php');
$fileIncPath = getcwd();
include ($fileIncPath."/file_handling.php");
include ($fileIncPath."/M188_instruction_parser.php");
include ($fileIncPath."/M215_instruction_converter.php");
include ($fileIncPath."/M215_instruction_parser.php");

session_start();
												
$gMachines = array(	array( "usercmd" => 1, "task" => 'M215 to M188 CNC conversion', "routine" => 'convert_M215_instruction'),
									array( "usercmd" => 2, "task" => 'parsing M188 CNC', "routine" => 'parse_M188_instructions'),
							        array( "usercmd" => 3, "task" => 'parsing M215 CNC', "routine" => 'parse_M215_instructions')
							    );
								
Class ClassUserCommand
{
	public $PostNumber;
	public $Description;
	public $FunctionName;
}

$UserCommands = array( new ClassUserCommand(1, 'M215 to M188 CNC conversion', 'convert_M215_instruction'),
                                             new ClassUserCommand(2, 'parsing M188 CNC', 'parse_M188_instructions'),
											 new ClassUserCommand(3, 'parsing M215 CNC', 'parse_M215_instructions'),

function parse_cnc_file()
{
	global $machines;
	global $machine;
	
	echo 'Task: <i style="color:blue;">'.$machine['task'].' </i> from inputfile <i style="color:blue;">'.$_FILES["uploadFile"]["name"].'</i><br>';
	
	$input_rules = get_input_rules(); // get input as array of rules  
	
	echo "<br>Respective rules in ".$_FILES["uploadFile"]["name"]." are:<br>";
	
	foreach($input_rules as $rule) 
	{
		echo '<i style="color:blue;"><br>Rule: '.$rule.'<br></i> ';
		
		handle_cnc_rule($rule); // parse rule
	}
	
	return true;
}

function clean_cnc_rule($rule)
{
	global $gComment;
	
	$trimmed = $rule;
	$parts = explode("(",$rule);
    if (sizeof($parts) == 2)
	{
		$gComment = "(".$parts[1];
		$trimmed = $parts[0] ;
		//echo "Comment ".$comment." found in rule<br>"; 
	}
	return $trimmed;
}

function handle_cnc_rule($rule)
{
	global $machine;
	global $word_position;
	global $gComment;
	
	$gComment = null;
	
	$gCncRule = clean_cnc_rule($rule);
	
	$instructions_not_found = "Instructions not found: ";

	echo "Processing rule: ".$cnc_rule."<br>";

	$cnc_words = explode(" ",$cnc_rule);
	
	$word_position = 0;

	foreach ($cnc_words as $cnc_word) // words loop
	{
		$break_words_loop = call_user_func($machine['routine'], $cnc_word, $cnc_rule);
		
		if ($break_words_loop) break;
	}
}
/*

 CNC parser & converter

*/
	
	foreach($machines as $var)
	{
		if ($var['usercmd'] == $_SESSION["usrcmd"]) 
		{
			$machine = $var;
			break;
		}
	}
	
	if(empty($machine))  
	{
		echo "<br>Machine not defined<br>";
		exit();
	}
	
switch ($_SESSION["usrcmd"])
{
		case 1:
			if (parse_cnc_file()) echo "<br>Ready converting<br>";
			break;		
		
		case 2:
		case 3:
			if (parse_cnc_file()) echo "<br>Ready parsing<br>";
			
			else echo "<br>Machine unknown<br>";
			break;
			
		default:
			echo "<br>Function not available<br>";
			break;
}
 ?>