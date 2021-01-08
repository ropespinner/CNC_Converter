<?php
												
function convert_M215_instructions($cnc_word, $cnc_rule)
{
		global $M188_instructionset;
		global $M215_instructionset;
		global $word_position;
		
		$break_words_loop = false;
		$break_instructions_loop  = false;
		$word_unknown = true;

		foreach($M215_instructionset as $M215_instruction) // instructions loop
		{
			$position = strpos($cnc_word, $$M215_instruction['Code']); 

			if ($position !== false) 
			{
				// word = instruction
				$word_unknown = false;
				$break_instructions_loop = true;
				$word_position +=1;
				
				foreach ($M188_instructionset as $M188_instruction)
				{
					if strcmp($M188_instruction['Code'], 
				}
				
				switch ($M215_instruction['Casus'])  // instruction typical handling
				{
					case 0: // one-character instruction followed by variable
						$parts = explode($M215_instruction['Code'],$cnc_word);
						echo  "Conversion rules required for instruction: ".$$M215_instruction['Code']." with value ".$parts[1]." at position ".$word_position."<br>"; 
					break;
					
					case 1: // plane-positioning having 2 coordinates (all rule instruction)
						$parts = explode(" ",$cnc_rule);
						echo "Conversion rules required for instruction: ".$parts[1]." with values ".$parts[2]." and ".$parts[3]." at position ".$word_position."<br>"; 
						$break_words_loop = true;
					break;
					
					case 3: // the M215 start-instruction (all rule instruction)
						echo "Conversion rules required for the M215 start-instruction<br>";
						$break_words_loop = true;
					break;
					
					default:
						echo "No handling yet for ".$cnc_word."<br>"; 
					break;
				}
			}
			
			if ($break_instructions_loop) break;
		}
		
		if ($word_unknown && strcmp("",$cnc_word) != 0) echo 'Found '.$cnc_word.' which is a <i style="color:red;">unknown instruction</i><br>';
		
		return $break_words_loop;
}
?>