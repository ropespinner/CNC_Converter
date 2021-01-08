<?php
require_once('../wp-load.php');
$fileIncPath = getcwd();
include ($fileIncPath."/file_handling.php");
include ($fileIncPath."/cnc_classes.php");
include ($fileIncPath."/UserM188Commands.php");
include ($fileIncPath."/UserM215Commands.php");

session_start();

$M215_parseCaseComments = array(

"<br>ParseCase 0: LBL 0 = 
<br>End of a group of instructions marker
<br>Only instruction in the rule
<br>Stop collecting instructions",

"<br>ParseCase 1: LBL = Start of a group of instructions marker
<br>Is followed by LBL-index is the 2nd and last word in rule
<br>Start collection instructions with name 'index' untill 'LBL 0'",

"<br>ParseCase 2: CALL LBL = Call to execute instructions
<br>Followed by LBL-index 
<br>Can further be followed by REP instruction",

"<br>ParseCase 3: REP = Repeat instruction for a labeled group
<br>Can be followed by 2nd word being repetition number",

"<br>ParseCase 4: TOOL DEF = Tool definition marker
<br>Followed by a TOOL-DEF-index 
<br>Followed by a tool definition
<br>Can be repeated in following rules",

"<br>ParseCase 5: TOOL CALL =  Tool selection 
<br>Followed by a TOOL-DEF-index
<br>Can be followed by other instructions in rule
<br>X has no value in the rule",

"<br>ParseCase 6: CYCLE DEF = Cycle definition
<br>Followed by 2 dot-separated indexes defining the cycle and definition
<br>1st CYCLE DEF is followed by textual explanation
<br>Repeated in following rules with incremented definition index
<br>Following CYCLE DEF contain instruction",

"<br>ParseCase 7: CYCLE CALL = Call to last defined CYCLE
<br>Can be followed by other instructions",

"<br>ParseCase 8: X = Displacement in X-plane 
<br>Preceded by instruction A or I
<br>Followed by negative or positive value
<br>Varying space between instruction and value",

"<br>ParseCase 9: B0 = Table front plane positioning 
<br>Default value, can be omitted",

"<br>ParseCase 10: B+90 = Table left plane positioning ",  

"<br>ParseCase 11: B-90 = Table right plane positioning",      

"<br>ParseCase 12: B+180 = Table back plane positioning 
<br>Manual handling on M188 - N()", 

"<br>ParseCase 13: B-180 = Table back plane positioning 
<br>Manual handling on M188 - N()",

"<br>ParseCase 14: R0 F9999 M = tool change instructions?",

"<br>ParseCase 15: L = Tool length
<br>Part of CYCL DEF rule
 <br>Next rule usually R
<br>Manual handling on M188 - N()",

"<br>ParseCase 16: R = Tool radius
<br>Part of CYCL DEF rule
<br>Previous rule usually L
<br>Manual handling on M188 - N()",

"<br>ParseCase 17: TIEFBOHREN = Start of CYCL DEF
<br>Stand alone
<br>Followed by next 5 parse cases",

"<br>ParseCase 18: ABST = Distance 
<br>Followed by a positive or negative number",

"<br>ParseCase 19: TIEFE = Depth 
<br>Followed by a positive or negative number",

"<br>ParseCase 20: ZUSTLG = Safety distance 
<br>Followed by a positive or negative number",

"<br>ParseCase 21: V.ZEIT = Timeout 
<br>Followed by a positive or negative number",

"<br>ParseCase 22: S = Speed in RPM
<br>Followed by a positive number",

"<br>ParseCase 23: F = Feed in mm/min
<br>Followed by a positive number",

"<br>ParseCase 24: M = Move in mm
<br>Followed by a positive number",

"<br>ParseCase 25: POLAR-KOORD = Starting point definition
<br>Stand alone
<br>Followed by sub-ix defines",

"<br>ParseCase 26: A = Absolute displacement
<br>Followed by displacement instruction",

"<br>ParseCase 27: I = Incremental displacement
<br>Followed by displacement instruction",

"<br>ParseCase 28: P.R. = Radial displacement 
<br>Followed by a value",

"<br>ParseCase 29: P.W. = Angular displacement
<br>Followed by a value",

"<br>ParseCase 30: STOP = Machine stop",

"<br>ParseCase 31: Y = Displacement in Y-plane 
<br>Preceded by instruction A or I
<br>Followed by negative or positive value			
<br>Varying space between instruction and value",	

"<br>ParseCase 32: Z = Displacement in Z-plane 
<br>Preceded by instruction A or I
<br>Followed by negative or positive value
<br>Varying space between instruction and value"

); 
	
$UserCommands[] = new ClassUserCommand(1,"convert_M215_instructions"); // convert M215 CNC M188 CNC
$UserCommands[] = new ClassUserCommand(2,"parse_M188_instructions"); // parse M188 CNC
$UserCommands[] = new ClassUserCommand(3,"parse_M215_instructions"); // parse M215 CNC		

$PostCommand = $_SESSION["usrcmd"];
$UserCommand = null;

foreach ($UserCommands as $UserCommand)
{
	if ($UserCommand->PostNumber == $PostCommand) break;
}
	
if (empty ($UserCommand)) echo "UserCommand not found<br>";

else call_user_func($UserCommand->FunctionName, $UserCommand);



?>