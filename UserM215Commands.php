<?php
$fileIncPath = getcwd();

include ($fileIncPath."/M215_instruction_parser.php");
include ($fileIncPath."/M215_to_M188_converter.php");

$instructions = array (

 array ("cmd"  => "LBL",  "PlaceInRule" => 1, "NrParams" => 1, "Description" => "Start or end of a group of instructions<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "LBL",  "PlaceInRule" => 2, "NrParams" => 1, "Description" => "Start or end of a group of instructions<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "CALL",  "PlaceInRule" => 1, "NrParams" => 1, "Description" => "call for a group of instructions<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "REP",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Repeat instruction<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "TOOL",  "PlaceInRule" => 1, "NrParams" => 0, "Description" => "Tool reference<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "DEF",  "PlaceInRule" => 2, "NrParams" => 1, "Description" => "Definition marker<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "P.R.",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Radial displacement<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "P.W.",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Angular displacement<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "R0",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "???<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "F9999",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "???<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "CYCL",  "PlaceInRule" => 1, "NrParams" => 1, "Description" => "part of a cycle<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "CALL",  "PlaceInRule" => 2, "NrParams" => 1, "Description" => "call for a cycle<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "POLAR-KOORD",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "Cycle type<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "POL",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "Cycle type<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "TIEFBOHREN",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "Cycle type<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "ABST",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Distance<br>", "Converter" => "r_command"),
 array ("cmd"  => "TIEFE",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Depth<br>", "Converter" => "e_command"),
 array ("cmd"  => "ZUSTLG",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Safety margin<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "V.ZEIT",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Delay<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "F100",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "Feed<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "M03",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "???<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "M",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "???<br>", "Converter" => "yetunknown"), // should not precede M03
 array ("cmd"  => "L",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Set tool length<br>", "Converter" => "manual"),
 array ("cmd"  => "R",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Set tool radius<br>", "Converter" => "manual"),
 array ("cmd"  => "A",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "Absolute values<br>", "Converter" => "ignore"),
 array ("cmd"  => "I",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "Incremental values<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "X",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Displacement in X plane<br>", "Converter" => "x_displacement"),
 array ("cmd"  => "Y",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Displacement in Y plane<br>", "Converter" => "z_displacement"),
 array ("cmd"  => "Z",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Displacement in Z plane<br>", "Converter" => "y_displacement"),
 array ("cmd"  => "/",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "???<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "STOP",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "Stop the machine<br>", "Converter" => "yetunknown"),
 array ("cmd"  => "B+90",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "Stop the machine<br>", "Converter" => "left_plane" ),
 array ("cmd"  => "B-90",  "PlaceInRule" => 0, "NrParams" => 0, "Description" => "Stop the machine<br>", "Converter" => "right_plane" ),
 array ("cmd"  => "S",  "PlaceInRule" => 0, "NrParams" => 1, "Description" => "Smear<br>", "Converter" => "yetunknown")); // should not precede STOP
 
 $cinputFile; 
 $lastcycle = null;

function parse_M215_instructions($cuserCommand)
{
	global  $cinputFile; 
	
	parse_M215_input_file($cuserCommand);
	
	displayM215($cinputFile);

}

function convert_M215_instructions($cuserCommand)
{
	global  $cinputFile; 
	
	parse_M215_input_file($cuserCommand);
	
	displayM215($cinputFile);

	convert($cinputFile);

}

function parse_M215_input_file($cuserCommand)
{
	global  $cinputFile;
	
	$cinputFile = new ClassFile($cuserCommand);
	
	get_input_rules($cinputFile); // get input as array of classrules  
	
	foreach($cinputFile->crules as $crule) 
	{
		 if(!isset($cinputFile->rulecounter)) $cinputFile->rulecounter = 0;
		 
		handle_M215_rule($cinputFile); // parse rule
		
		$cinputFile->rulecounter++;		
	}
}

function handle_M215_Rule(&$cif)
{	
	// get all words in rule 
	
	global $instructions;
	global $lastcycle;
	
	$rast  = $cif->crules[$cif->rulecounter]->fileRule; // rule as string
	
	//echo "<br>rule: ".$rast."<br>"; // TST

	$wei = strpos($rast," "); // word end index
	
	//$ix = 0;
	
	while ($wei !== false)
	{			
			$word = substr($rast, 0, $wei);
			
			if ($word) $cif->crules[$cif->rulecounter]->cwords[] = new ClassWord($word);
			
			$rast = substr ($rast, $wei+1);
			
			$wei = strpos($rast," "); 
			
			//$ix++;
	}
	
	if (strlen($rast) != 0) $cif->crules[$cif->rulecounter]->cwords[] = new ClassWord($rast); // last word !!!
	
	// get and set meta data
	
	$cif->crules[$cif->rulecounter]->cwords[0]->marker = 0; // marks rulenumber
	
	$cwords = &$cif->crules[$cif->rulecounter]->cwords;
	
	$nrWords = count($cwords);
	
	for ($cwix = 1; $cwix < $nrWords; $cwix++) // new
	{
		$mw1 = null;
		$mw2 = null;

		foreach ($instructions as $instruction)
		{
				if($instruction['PlaceInRule'] == 1 && !empty($cwords[1]) && !isset($mw1) && strcmp($instruction['cmd'], $cwords[1]->word) == 0) $mw1 = $cwords[1]->word;
				if($instruction['PlaceInRule'] == 2 && !empty($cwords[2]) && !isset($mw2) && strcmp($instruction['cmd'], $cwords[2]->word) == 0) $mw2 = $cwords[2]->word;
				
				if (isset($mw1) && is_numeric($cwords[2]->word)) $mw2 = " ix"; // label start rule
				
				if (isset($mw1) && isset($mw2))
				{
					$metacase = $mw1.$mw2;
					
					//echo "<br>Found in rule ".$cif->crules[$cif->rulecounter]->cwords[0]->word." metacase ".$metacase;
					
					switch ($metacase)
					{
						case "LBL ix": 
						
							$cwords[1]->marker = 1; // marks meta cmd
							$cwords[2]->marker = 1; // marks meta cmd

							if(intval($cwords[2]->word) !== 0)
							{
								$cif->crules[$cif->rulecounter]->labeldef = true;
								$cif->crules[$cif->rulecounter]->label = $cwords[2]->word;
							}
							else
							{
								$cif->crules[$cif->rulecounter]->labelend = true;
							}
						break;
				
						case "CALLLBL":
				
							if(!empty($cwords[3]) && is_numeric($cwords[3]->word))
							{
								$cif->crules[$cif->rulecounter]->labelcall = true;
								$cif->crules[$cif->rulecounter]->label = $cwords[3]->word;
								
								$cwords[1]->marker = 1; // marks meta cmd
								$cwords[2]->marker = 1; // marks meta cmd
								$cwords[3]->marker = 1; // marks meta cmd

							}
							else
							{
								echo "<br>Wrong label definition in call"; 
							}
							
						break;
				
						case "TOOLDEF":
						
								if(!empty($cwords[3]) && is_numeric($cwords[3]->word))
								{
										$cif->crules[$cif->rulecounter]->tooldef = true;
										$cif->crules[$cif->rulecounter]->tool = $cwords[3]->word;	
										
										$cwords[1]->marker = 1; // marks meta cmd
										$cwords[2]->marker = 1; // marks meta cmd
										$cwords[3]->marker = 1; // marks meta cmd

								}
								else
								{
									$cif->crules[$cif->rulecounter]->error = "<br>Wrong tool definition in rule";
								}
								
						break;
				
						case "TOOLCALL":
				
								if(!empty($cwords[3]) && is_numeric($cwords[3]->word))
								{
										
										$cif->crules[$cif->rulecounter]->toolcall= true;
										$cif->crules[$cif->rulecounter]->tool = $cwords[3]->word;
										
										$cwords[1]->marker = 1; // marks meta cmd
										$cwords[2]->marker = 1; // marks meta cmd
										$cwords[3]->marker = 1; // marks meta cmd
	
								}
								else
								{
										$cif->crules[$cif->rulecounter]->error = "<br>Wrong tool call in rule";
								}

						break;
				
						case "CYCLDEF":
							
								if(!empty($cwords[3]))
								{
									$cif->crules[$cif->rulecounter]->cycledef = true; 
									$parts = explode(".",$cwords[3]->word);
									$cif->crules[$cif->rulecounter]->cycle = $parts[0];
									$lastcycle = $parts[0];
									
									$cwords[1]->marker = 1; // marks meta cmd
									$cwords[2]->marker = 1; // marks meta cmd
									$cwords[3]->marker = 1; // marks meta cmd

								}
								else
								{
									$cif->crules[$cif->rulecounter]->error = "<br>wrong cycle definition in rule";
								}

						break;
				
						case "CYCLCALL": // find the last defined cycle
						
								$cif->crules[$cif->rulecounter]->cyclecall = true; 
								$cwords[1]->marker = 1; // marks meta cmd
								$cwords[2]->marker = 1; // marks meta cmd

								if(!empty($cwords[3]) && is_numeric($cwords[3]))
								{
									$parts = explode(".", $cwords[3]->word);
									$cif->crules[$cif->rulecounter]->cycle = $parts[0];
									$lastcylce  = $parts[0];
									$cwords[3]->marker = 1; // marks meta cmd

								}
								else if (!empty($lastcycle))
								{
									$cif->crules[$cif->rulecounter]->cycle = $lastcycle;
								}
								else
								{
									$cif->crules[$cif->rulecounter]->error = "<br>Wrong cycle call in rule";
								}
								
						break;
				
						default:
					}
					
					break; // break instruction loop after meta command
				}
		}
	}	
	
	// handle resting ops commands
	
	reset($cwords);
	
	while ($current = current($cwords) )
	{
		if ($current->marker == 9) // unprocessed word
				{
					foreach ($instructions as $instruction)
					{
						if (strcmp($instruction['cmd'], $current->word) == 0)
						{
							$current->syntax = $instruction['cmd'];
							$current->marker = 2; // mark ops command
							if ($instruction['Converter']) $current->converter = $instruction['Converter'];
							
							if ($instruction['NrParams'] == 1)
							{
								$next = next($cwords);
								$current = prev($cwords);
								
								if (false !== $next && is_numeric(str_replace(",",".",$next->word)))
								{
									//echo "<br>pick up parameter here!";
									$current->parameter_1 = $next->word;
									$current->NrParams = $instruction['NrParams'] ;
									$next->marker = 3; // marks value
								}
								else
								{
									$cif->crules[$cif->rulecounter]->error = "<br>ops command in rule misses a parameter"; 	
								}
							}
							
							break; // exit instruction loop
						}
						
						else // check if instruction and parameter are unseparated
						{
							$parts = explode($instruction['cmd'], $current->word);

							if (count($parts) == 2) 
							{
								$current->syntax = $instruction['cmd'];
								$current->marker = 2; // mark ops command
								if ($instruction['Converter']) $current->converter = $instruction['Converter'];
								$current->parameter_1 = $parts[1]; 
								$current->NrParams = 1; 
								
								break; // exit instruction loop
							}
						}
						
					} // instruction loop
					
				} // unprocessed word check
				
				if (false === next($cwords)) break;
	}	// foreach word loop
}


function displayM215(&$cif)
{
		$labelon = false;
		$label = null;
		$cycleon = false;
		$toolon = false;
		

		echo "<br>Result of parsing file ".$cif->fileName.":";
		
		foreach($cif->crules as $crule)
		{
			echo '<br><span style="color:#FF0000;"><br>Rule: '.$crule->fileRule.'</span>';
			
			//foreach ($crule->cwords as $cword) echo "<br>word: ".$cword->word;
					
			if ($crule->labeldef) 
			{
				$labelon = true;
				$label = $crule->label;
				echo "<br>Rule start label ".$label;
			}	
			else if ($crule->labelend) 
			{
				$labelon = false;
				echo "<br>Rule end label ";
			}
			else if($labelon)
			{
				echo "<br>Rule is part of label ".$label;
				 $crule->label = $label;                              // fix for summary
			}
					
			if ($crule->labelcall) echo "<br>Rule calls label ".$crule->label;
					
			if ($crule->tooldef) echo "<br>Rule defines tool ".$crule->tool;
					
			if ($crule->toolcall) echo "<br>Rule calls tool ".$crule->tool;

			if ($crule->cycledef) echo "<br>Rule is part of cycle ".$crule->cycle;
					
			if ($crule->cyclecall) echo "<br>Rule calls cycle ".$crule->cycle;
								
			foreach($crule->cwords as $cword)
			{
				if (!empty($cword) && isset($cword->marker) && $cword->marker == 2) 
				{
					echo "<br>cmd ". $cword->syntax;

					if($cword->NrParams == 1 ) echo " with value ". $cword->parameter_1;
				}
						
			}
		}
}
?>
