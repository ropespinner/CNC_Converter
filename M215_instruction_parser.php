<?php
												
function handle_M215_word($cinputFile, $cnc_words)
{
		$wordcounter = $cinputFile->crules[$cinputFile->rulecounter]->wordcounter;
		
		if(!isset($wordcounter)) $wordcounter = 0;
		
		else $wordcounter +=1;
		
		$cinputFile->crules[$cinputFile->rulecounter]->wordcounter = $wordcounter;
		
		$break_instructions_loop  = false;
		
		$cword = new ClassWord($cnc_words[$wordcounter]);

		foreach($cinputFile->cuserCommand->cinstructionSet as $cinstruction) // instructions loop
		{
			//echo "<br>test";
     		//echo "<br>                  word: ".$cword->word;
			//echo "<br>instruction code: ".$cinstruction->Code;
			
			$position = strpos($cword->word, $cinstruction->Code); 
			
			//echo "<br>position: ".$position;

			if ($position !== false) 
			{
				// word found in instructionset
				$cword->unknown = false;
				$break_instructions_loop = true;
				
				switch ($cinstruction->ParseCase)  // instruction typical handling
				{
					case 0: // one-character instruction followed by variable
						
						$parts = explode($cinstruction->Code, $cword->word);
						//echo "<br>one-char instruction: ";
						//echo "<br>     syntax: ".$cinstruction->Code;
						//echo "<br>parameter: ".$parts[1];
						$cword->syntax = $cinstruction->Code;
						$cword->parameter_1 = $parts[1];
						
					break;
					
					case 1: // plane-positioning having 2 coordinates (all rule instruction)
						
						$parts = explode(" ", $cinputFile->crules[$cinputFile->rulecounter]->parseRule);
						$offset = $cinputFile->cuserCommand->ruleNumberOffset;
						$cword->syntax = $parts[0+$offset];
						$cword->parameter_1 = $parts[1+$offset];
						$cword->parameter_2 = $parts[2+$offset];	
						$cinputFile->crules[$cinputFile->rulecounter]->break_words_loop = true;
						
					break;
					
					case 3: // the M188 start-instruction (all rule instruction)
					
						$cword->syntax = $cword->word;
						$cinputFile->crules[$cinputFile->rulecounter]->break_words_loop = true;
						
					break;
					
					case 4: // the M188 rule number
					
						$cword->ruleNumber = $cword->word;
 
					break;
					
					default:
					
						$cword->unknown = true;
						
					break;
				}
			}
			
			if ($break_instructions_loop) break;
		}
		
		$cinputFile->crules[$cinputFile->rulecounter]->cwords[$wordcounter] = $cword;
		
		return $cinputFile;
}
?>