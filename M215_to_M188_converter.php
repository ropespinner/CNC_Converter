<?php
/*
* converter converts M215 CNC to M188 CNC
*
*  Input is object $cinputfile containing parsed input file
*
*  = First 3 M188 instructions should be respectively: %N0 ()
*                                                                                     N1 ( <...>)
*                                                                                     N2 PCXZ CFY CLY+
*  = M188 CNC requires an even number of characters per rule
*  = M188 parameter follows cmd syntax without space inbetween
*
* rule markers : 		labeldef
*								labelend
*								labelcall					=> converts and inserts all label labeled rules
*								label
*								tooldef
*								toolcall						=> converts and inserts all tool labeled rules
*								tool
*								cycledef
*								cyclecall					=> converts and inserts all cycle labeled rules 
*								cycle
*  
* word->marker		0 = rule number
*                   	    	1 = meta command
*						  		2 = ops command		=> processed as ops command
*								3 = value
*								9 = not processed
*
* word->converter 	function to convert M215 ops command to M188 ops command
*/
define('MONITOR', 1);
define('FILE', 2);

$display = MONITOR;

$rulenumber = 0;
$outfile = null;
$cif = null;

function convert(&$cinputfile)
{
	global $display;
	global $outfile;
	global $cif;
	
	$cif = $cinputfile; 
	
	$outname = getcwd()."/uploads/converted ".$cif->fileName;
	
	$outfile = fopen($outname, 'wb');
	
	displayM215_summary($cinputfile);
	
	if ($display == MONITOR)
	{
		echo "<br><br>Result of converting file ".$cif->fileName.":<br>";
		echo "<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspN0% (".date('d-m-Y').")";
	}
	
	if ($display == FILE) display("N0% (".date('d-m-Y').")");
	
	display("PCXZ CFY CLY+", "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
	
	for ($ix=0; $ix < count($cif->crules); $ix++)
	{
		$crule = $cinputfile->crules[$ix]; 
 
		if ($crule->tooldef || $crule->cycledef) 
		{
			// skip definitions
		}
		else
		{
			if ($crule->labelcall) labelcall($crule);
 
			else
			{
				if ($crule->cyclecall) cyclecall($crule);
			
				if ($crule->toolcall) toolcall($crule);
			
				// ops commands in ops rules
				// and additional ops commands in cycle/tool-calling rules
			
				foreach($crule->cwords as $cword)
				{
					if($cword->marker == 2)
					{
						$crule->convertedRule = $crule->convertedRule . call_user_func($cword->converter, $cword);
					}
				}
				if (strcmp($crule->convertedRule,"") !==0) 
				{
					display($crule->convertedRule, red($crule->cwords[0]->word, " O&nbsp"));
					$crule->convertedRule = "";
				}
			}
		}
	}
	fclose($outfile);
}

function labeldef($crule)
{
	display("( label start ".$rule->label." )");
}

function labelend(&$crule)
{
	display("( label end ".$rule->label." )");
}

function labelcall($crule)
{
		global $cif;
		
		foreach($cif->crules as $potrule)
		{
				if($potrule->label == $crule->label)
				{
					foreach($potrule->cwords as $cword)
					{
						if($cword->marker == 2)
						{
							$potrule->convertedRule = $potrule->convertedRule . call_user_func($cword->converter, $cword);
						}
					}
				}
			if (strcmp($potrule->convertedRule,"") !==0) 
			{
				display($potrule->convertedRule, red($potrule->cwords[0]->word, "L".$crule->label));
				$potrule->convertedRule = "";
			}
		}
}

function tooldef($crule)
{
	display("( tool def ".$rule->tool." )");
}

function toolcall(&$crule)
{
		global $cif;
		
		foreach($cif->crules as $potrule)
		{
				if($potrule->tooldef && $potrule->tool == $crule->tool)
				{
					foreach($potrule->cwords as $cword)
					{
						if($cword->marker == 2)
						{
							$potrule->convertedRule = $potrule->convertedRule . call_user_func($cword->converter, $cword);
						}
					}
				}
			if (strcmp($potrule->convertedRule,"") !==0) 
			{
				display($potrule->convertedRule, red($potrule->cwords[0]->word, "T".$crule->tool));
				$potrule->convertedRule = "";
			}
		}
}

function cycledef(&$crule)
{
	// action?
}

function cyclecall($crule)
{
		global $cif;
		
		$cycledone = false;
		
		foreach($cif->crules as $potrule)
		{
				if($potrule->cycledef && $potrule->cycle == $crule->cycle)
				{
					$cycledone = true;
					
					foreach($potrule->cwords as $cword)
					{
						if($cword->marker == 2)
						{
							$potrule->convertedRule = $potrule->convertedRule . call_user_func($cword->converter, $cword);
						}
					}
				}
				else
				{
					if ($cycledone) break;
				}
				
				if (strcmp($potrule->convertedRule,"") !==0) 
				{
					display($potrule->convertedRule, red($potrule->cwords[0]->word, "C".$crule->cycle));
					$potrule->convertedRule = "";
				}
		}
}

function run($pretext)
{
	global $rulenumber;
	
	$rulenumber +=1;
	return "<br>".$pretext."N".strval($rulenumber)." ";
}

function display($text, $pretext)
{
	global $display;
	global $outfile;
	
	if($display == MONITOR)
	{
		echo run($pretext).$text;
	}
	
	if ($display == FILE)
	{
		fwrite($outputfile, $text . "\n");
	}
}

// ops word call back functions:

function manual($cword)
{
	$conv = "( set manual: ". $cword->syntax;
	
	if ($cword->NrParams == 1 && isset($cword->parameter_1))
	{
		$conv = $conv . "   " . $cword->parameter_1 ;
	}	
	
	$conv = $conv . ")";
	
	return $conv;
}

function ignore($cword)
{
	return "";
}

function r_command($cword)
{
	return "R ".$cword->parameter_1;
}

function e_command($cword)
{
	return "E ".$cword->parameter_1;
}

function x_displacement($cword)
{
	return "X ".$cword->parameter_1;
}

function z_displacement($cword)
{
	return "Z ".$cword->parameter_1;
}

function y_displacement($cword)
{
	return "Y ".$cword->parameter_1;
}

function left_plane($cword)
{
	return "PCYZ CFX CLX+";
}

function right_plane($cword)
{
	return "PCYZ CFX CLX-";
}

function yetunknown($cword)
{
	$conv = '<span style="color:#FF0000;">  [</span>'.$cword->syntax;
	
	if ($cword->NrParams == 1 && isset($cword->parameter_1))
	{
		$conv = $conv . '    '.$cword->parameter_1;
	}	
	
$conv = $conv . '<span style="color:#FF0000;">]how? </span>';
	
	return $conv;
}

function displayM215_summary(&$cif)
{
	
	echo "<br><br> meta view:<br>";
	
	$template = '<span style="color:#FF0000;">L  T  C  </span> ';
	
	echo '<br>' . $template.  '<span style="color:#FF0000;">M215 CNC:</span>';
	
	foreach($cif->crules as $crule)
	{
			//echo "<br>start<br>";
			//var_dump($crule);
			
			$rule = $template;
			
			//echo "<br>".$rule;
			
			if (isset($crule->label)) $rule = str_replace("L", $crule->label, $rule); else $rule = str_replace("L", "&nbsp-", $rule); 
			//echo "<br>".$rule;
			
			if (isset($crule->tool)) $rule = str_replace("T", $crule->tool, $rule); else $rule = str_replace("T", "&nbsp-", $rule); 
			//echo "<br>".$rule;
			
			if (isset($crule->cycle)) $rule = str_replace("C", $crule->cycle, $rule); else $rule = str_replace("C", "&nbsp-", $rule); 
			//echo "<br>".$rule;
			
			$rule = $rule ."    ". $crule->fileRule;
			echo "<br>".$rule;
			
			//echo $rule;
			//echo "<br>stop<br>";
	}
}

function red($linenumber, $marker)
{
	$nr; 
	
	if (strval($linenumber) <= 9) $nr = "00" . $linenumber;
	
	else if (strval($linenumber) <= 99) $nr = "0" . $linenumber;
	
	return  '<span style="color:#FF0000;">' .$nr. '&nbsp'.$marker.': </span> ';
}

?>