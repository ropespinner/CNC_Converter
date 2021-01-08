<?php
$fileIncPath = getcwd();

include ($fileIncPath."/M188_instruction_parser.php");


function parse_M188_instructions($cuserCommand)
{
	echo "function parse_M188_instructions<br>";
	
	$cinputFile = parse_M188_input_file($cuserCommand);
	
	display_M188_parse_result($cinputFile);
}

function parse_M188_input_file($cuserCommand)
{
	$cinputFile = new ClassFile($cuserCommand);
	
	$cinputFile = get_input_rules($cinputFile); // get input as array of classrules  
	
	foreach($cinputFile->crules as $crule) 
	{
		 if(!isset($cinputFile->rulecounter)) $cinputFile->rulecounter = 0;
		 
		$cinputFile = handle_M188_rule($cinputFile); // parse rule
		
		$cinputFile->rulecounter++;		
	}
	
	return $cinputFile;
}

function handle_M188_Rule($cinputFile)
{
	$cinputFile = getParseRuleAndComment($cinputFile);
	
	$cnc_words = explode( " ",  $cinputFile->crules[$cinputFile->rulecounter]->parseRule);
	
	foreach ($cnc_words as $word) // words loop
	{		
		$cinputFile = handle_M188_word($cinputFile, $cnc_words);
		
		if ($cinputFile->crules[$cinputFile->rulecounter]->break_words_loop) break;
	}
	
	return $cinputFile;
}

function getParseRuleAndComment($cinputFile)
{
	$trimmed = $cinputFile->crules[$cinputFile->rulecounter]->fileRule;
	
	$parts = explode( "(" , $trimmed );
	
	$cinputFile->crules[$cinputFile->rulecounter]->parseRule = trim($parts[0]) ;
	
    if (count($parts) == 2)
	{
		$cinputFile->crules[$cinputFile->rulecounter]->comment = "(".$parts[1];		
	}
    /*
	echo $cinputFile->crules[$rulecounter]->parseRule;
	echo "<br>"; 
	echo $cinputFile->crules[$rulecounter]->comment;
	echo "<br><br>"; 
	*/
	return $cinputFile;
}
?>