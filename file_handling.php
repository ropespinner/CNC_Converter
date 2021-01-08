<?php

function get_input_rules($rif)
{
		
	if ($_FILES["uploadFile"]["error"] > 0)
	{
		echo "Error Return Code: " . $_FILES["uploadFile"]["error"] . "<br>";
	}         
	else               
	{                
		$rif->fileName = $_FILES["uploadFile"]["name"] ;
		
		$handle = fopen($_FILES["uploadFile"]["tmp_name"],"r");
	
		$c = "";
		
		while(($c = fgets($handle))!==false)
		{
			$ruleOK = true;
			
			$c = trim($c);
			
			// echo "<br>c: ".$c;
			
			// if (empty($c) || $c==='' || $c===' ' || $c==='/\R+/' || $c==="\n" || $c===' /\R+/' || $c===" \n" || strcmp($c,'') ==0 ) $ruleOK = false;
		
			if (empty($c)) $ruleOK = false;
			
			if ($ruleOK)
			{
				if(!isset($linecounter)) $linecounter = 0;
				
				$rif->crules[$linecounter] = new ClassRule(trim($c));
				
				$linecounter++;
			}
		}
		fclose($handle);	

		//return $cinputFile;
	}
}	

function display_M188_parse_result($cinputFile)
{
  echo "Inputfile ".$cinputFile->fileName."<br><br>";
  
  foreach ($cinputFile->crules as $crule)
  {
	  echo "<br>Rule: ".$crule->fileRule."<br>Comment: ";
	  
	  if (empty($crule->comment)) echo "none<br>"; else echo $crule->comment."<br>";
	  
	  echo "Parsing rule: ".$crule->parseRule."<br>";
	  
	  foreach ($crule->cwords as $cword)
	  {
		if ($cword->unknown) echo $cword->word." unknown<br>";

		else
		{
			if (!empty($cword->ruleNumber))
			{
				echo $cword->word." is the rule number<br>";
			}
			else
			{
				echo $cword->word." breaks up in instruction ".$cword->syntax;
			
				if(!empty($cword->parameter_1)) echo " with parameter ".$cword->parameter_1;
			
				if(!empty($cword->parameter_2)) echo " and parameter ".$cword->parameter_2;
			
				echo "<br>";
			}
		}
	  }
  }
}

?>