<?php

Class ClassUserCommand
{
	public $PostNumber;
	public $FunctionName = "";
	public $CounterCNC;
	public $cinstructionSet = array();
	public $ruleNumberOffset;
	
	public function  __construct($PostNumber, $FunctionName)
	{
		$this->PostNumber = $PostNumber;
		$this->FunctionName = $FunctionName;
	
		if ($PostNumber !== 1) // command requires M215 instructionset
		{  
	        $this->ruleNumberOffset = 0;  //  word ix offset for picking up instructions in rule
			$this->CounterCNC = 1;  //  ix in UserCommands array to M188 instructionset
			
             // populate with M215 instructionset
			 //  Code - ParseCase - PlaceInFile - PlaceInRule - ConversionCase - ConversionCode - Description
			 //
			 // ParseCase 0: LBL 0 = End of a group of instructions marker
			 //                                      Only instruction in the rule
			 //                                      Stop collecting instructions 
			 // ParseCase 1: LBL = Start of a group of instructions marker
			 //                                    Is followed by LBL-index is the 2nd and last word in rule
			 //                                    Start collection instructions with name 'index' untill 'LBL 0' 
			 // ParseCase 2: CALL LBL = Call to execute instructions
			 //                                              Followed by LBL-index 
			 //                                              Can further be followed by REP instruction
			 // ParseCase 3: REP = Repeat instruction for a labeled group
			 //                                     Can be followed by 2nd word being repetition number
			 // ParseCase 4: TOOL DEF = Tool definition marker
			 //                                                Followed by a TOOL-DEF-index 
			 //                                                Followed by a tool definition
			 //												   Can be repeated in following rules
			 // ParseCase 5: TOOL CALL =  Tool selection 
			//                                                    Followed by a TOOL-DEF-index
			//                                                    Can be followed by other instructions in rule
			//                                                    X has no value in the rule
			// ParseCase 6: CYCLE DEF = Cycle definition
			//                                                  Followed by 2 dot-separated indexes defining the cycle and definition
			//                                                  1st CYCLE DEF is followed by textual explanation
			//                                                  Repeated in following rules with incremented definition index
			//													 Following CYCLE DEF contain instruction
			// ParseCase 7: CYCLE CALL = Call to last defined CYCLE
			//                                                    Can be followed by other instructions
			// ParseCase 8: X = Displacement in X-plane 
			//                                Preceded by instruction A or I
			//                                Followed by negative or positive value
			//                                Varying space between instruction and value
			// ParseCase 9: B0 = Table front plane positioning 
			//                                  Default value, can be omitted
			// ParseCase 10: B+90 = Table left plane positioning 
			//                                         
			// ParseCase 11: B-90 = Table right plane positioning 
			//                                 
			// ParseCase 12: B+180 = Table back plane positioning 
			//                                          Manual handling on M188 
			// ParseCase 13: B-180 = Table back plane positioning 
			//                                         Manual handling on M188 - N()
			// ParseCase 14: R0 F9999 M = tool change instructions?
			//
			// ParseCase 15: L = Tool length
			//                                 Part of CYCL DEF rule
            //			                        Next rule usually R
			//                                  Manual handling on M188 - N()
			// ParseCase 16: R = Tool radius
			//                                  Part of CYCL DEF rule
			//                                  Previous rule usually L
			//                                  Manual handling on M188 - N() 
			// ParseCase 17: TIEFBOHREN = Start of CYCL DEF
			//                                                       Stand alone
			//                                                       Followed by next 5 parse cases
			// ParseCase 18: ABST = Distance 
			//                                         Followed by a positive or negative number
			// ParseCase 19: TIEFE = Depth 
			//                                         Followed by a positive or negative number
			// ParseCase 20: ZUSTLG = Safety distance 
			//                                              Followed by a positive or negative number
			// ParseCase 21: V.ZEIT = Timeout 
			//                                           Followed by a positive or negative number
			// ParseCase 22: S = Speed in RPM
			//                                  Followed by a positive number
			// ParseCase 23: F = Feed in mm/min
			//                                 Followed by a positive number
			// ParseCase 24: M = Move in mm
			//                                 Followed by a positive number
			// ParseCase 25: POLAR-KOORD = Starting point definition
			//                                                           Stand alone
			//                                                           Followed by sub-ix defines
			// ParseCase 26: A = Absolute displacement
			//                                 Followed by displacement instruction
			// ParseCase 27: I = Incremental displacement
			//                                 Followed by displacement instruction
			// ParseCase 28: P.R. = Radial displacement 
			//                                      Followed by a value
			// ParseCase 29: P.W. = Angular displacement
			//                                       Followed by a value
			// ParseCase 30: STOP = Machine stop
			// ParseCase 31: Y = Displacement in Y-plane 
			//                                 Preceded by instruction A or I
			//                                 Followed by negative or positive value			
            //                                 Varying space between instruction and value			
			// ParseCase 32: Z = Displacement in Z-plane 
			//                                Preceded by instruction A or I
			//                                Followed by negative or positive value
			//                                Varying space between instruction and value
			//
			 // ConvertCase 0: End grouping and saving collected rules 
			 // ConvertCase 1: Stop previous grouping if any
			 //                            Start grouping and save following rules until new LBL definition 
			 // ConvertCase 
			 //
			 // Notes: M215 X-Y-Z = M188 X-Z-Y
			 //
			$this->cinstructionSet[] = new ClassInstruction(  "LBL",  0, null, 1, 0, null, "Start or end of a group of instructions<br>"); // LBL as word 1
			$this->cinstructionSet[] = new ClassInstruction(  "LBL",  1,  null,  2, 1, null, "part of call of a group of instructions <br>"); // LBL as word 2
			$this->cinstructionSet[] = new ClassInstruction(  "CALL",  2,  null,  1,  0, null," Call for a group of instructions<br>"); // CALL as word 1
			$this->cinstructionSet[] = new ClassInstruction(  "CALL",  2,  null,  2,  0, null," Call for a collection<br>"); // CALL as word 2
			$this->cinstructionSet[] = new ClassInstruction(  "REP",  3,  null,  null,  0, null,"Repeat instruction<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "TOOL",  4,  null,  1,  0, null,"Tool reference<br>"); // tool as word 1
			$this->cinstructionSet[] = new ClassInstruction(  "DEF", 5,  null,  2,  0, null	,"Definition <br>");
			$this->cinstructionSet[] = new ClassInstruction(  "CYCLE", 5,  null,  null,  0, null	,"Cycle marker <br>");
			//$this->cinstructionSet[] = new ClassInstruction( "CYCL DEF",  6,  null,  null,  0, null,"Cycle definition <br>");
			//$this->cinstructionSet[] = new ClassInstruction(  "CYCL CALL",  7,  null,  null,  0, null,"Cycle call <br>"); 
			$this->cinstructionSet[] = new ClassInstruction(  "X",  8,  null,  null,  0, "X","Displacement in X-plane <br>");
			$this->cinstructionSet[] = new ClassInstruction(  "Y",  31,  null,  null,  0, "Z","Displacement in Y-plane <br>");
			$this->cinstructionSet[] = new ClassInstruction(  "Z",  32,  null,  null,  0, "Y","Displacement in Z-plane <br>");
			$this->cinstructionSet[] = new ClassInstruction(  "B0",  9,  null,  null,  0, "PCXZ CFY CLY+","Table front plane positioning (default)<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "B+90",  10,  null,  null,  0, "PCYZ CFX CLX+","Table left plane positioning <br>");
			$this->cinstructionSet[] = new ClassInstruction(  "B-90",  11,  null,  null,  0, "PCYZ CFX CLX-","Table right plane positioning <br>");
			$this->cinstructionSet[] = new ClassInstruction(  "B+180",  12,  null,  null,  0, null	,"Table back plane positioning<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "B-180",  13,  null,  null,  0, null	,"Table back plane positioning <br>");
			$this->cinstructionSet[] = new ClassInstruction(  "R0 F9999 M",  14,  null,  null, 0, null	,"Tool change instructions? <br>");
			$this->cinstructionSet[] = new ClassInstruction(  "L",  15,  null,  null, 0, null	,"Tool length<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "R",  16,  null,  null, 0, null	,"Tool radius <br>");
			$this->cinstructionSet[] = new ClassInstruction(  "TIEFBOHREN",  17,  null,  null, 0, "G83"	,"Start of CYCL DEF<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "ABST",  18,  null,  null, 0, "R"	,"Distance<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "TIEFE",  19,  null,  null, 0, "E"	,"Depth<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "ZUSTLG",  20,  null,  null, 0, "RA"	,"Safety distance<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "V.ZEIT",  21,  null,  null, 0, "G82"	,"Distance<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "S",  22,  null,  null, 0, "S"	,"Speed in RPM<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "F",  23,  null,  null, 0, null	,"Feed in mm/min<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "M",  24,  null,  null, 0, null	,"Move<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "POLAR-KOORD",  25,  null,  null, 0, null	,"Starting point definition<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "A",  26,  null,  null, 0, null	,"Absolute displacement<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "I",  27,  null,  null, 0, null	,"Incremental displacement<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "P.R.",  28,  null,  null, 0, null	,"Radial displacement<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "P.W.",  29,  null,  null, 0, null	,"Angular displacement<br>");
			$this->cinstructionSet[] = new ClassInstruction(  "STOP",  30,  null,  null, 0, null	,"Machine stop<br>");
		}
		else // command requires M188 instructionset
		{    
			$this->ruleNumberOffset = 1;  // word ix offset for picking up instructions in rule
			$this->CounterCNC = 2;  // ix in UserCommands array to M215 instructionset
			
             // populate with M188 instructionset
			 //  Code - ParseCase - PlaceInFile - PlaceInRule - ConversionCase - ConversionCode
			 //
			 // ParseCase 0: <> = One-character instruction without interruption followed by variable
			 // ParseCase 1: PC<><> = Plane-positioning having 2 coordinates (all rule instruction)
			 // ParseCase 2: M06 = Tool change with last encountered T-instruction
			 // ParseCase 3: %N0 = The M188 start-instruction (all rule instruction)
			 // ParseCase 4: The M188 rule number
			 // ParseCase 5: One-character instruction without interruption followed by variable
			 //                         Can be recalled with M06
			 // ParseCase 6: R = Distance?
			 //
			 // Notes: M188 X-Y-Z = M215 X-Z-Y

			$this->cinstructionSet[] = new ClassInstruction(  "PCXZ",  1, null, null, 0, "PCXZ"	," <br>");
			$this->cinstructionSet[] = new ClassInstruction(  "PCXY",  1,  null,  null,  0, "PCXY"," <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "PCYZ",  1,  null,  null,  0, "PCYZ"," <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "T",  5,  null,  null,  0, "T"," Tool change<br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "S",  0,  null,  null,  0, "S"," <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "F", 0,  null,  null,  0, "F"," <br>");
			$this->cinstructionSet[]  = new ClassInstruction( "G",  0,  null,  null,  0, "G"	," <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "M05",  0,  null,  null,  0, null	,"Spindle stop<br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "M06",  2,  null,  null,  0, null	,"Tool change <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "M09",  0,  null,  null,  0, null	,"Stop cooling <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "M",  0,  null,  null,  0, "M"	," <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "X",  0,  null,  null,  0, "X"	,"Absolute displacement in the X-plane <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "Y",  0,  null,  null,  0, "Z"	,"Absolute displacement in the Y-plane <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "Z",  0,  null,  null,  0, "Y"	,"Absolute displacement in the Z-plane <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "OX",  2,  null,  null,  0, "OX"," <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "OY",  2,  null,  null,  0, "OY"," <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "OZ",  2,  null,  null,  0, "OZ"	," <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "%N0",  3,  null,  null,  0, "%N00"	," <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "N",  4,  null,  null,  0, null," <br>");
			$this->cinstructionSet[]  = new ClassInstruction(  "R",  6,  null,  null,  0, "ABST","Distance?<br>");
		}
    }
}

Class	ClassInstruction
{	
		public $Code;
		public $ParseCase;
		public $PlaceInFile;
		public $PlaceInRule;
		public $ConversionCase;
		public $ConversionCode;
		public $Description;
		
	public function  __construct($Code, $ParseCase, $PlaceInFile, $PlaceInRule,$ConversionCase,$ConversionCode,$Description)
	{
		$this->Code = $Code;
		$this->Casus = $ParseCase;
		$this->PlaceInFile = $PlaceInFile;
		$this->PlaceInRule = $PlaceInRule;
		$this->ConversionCase = $ConversionCase;
		$this->ConversionCode = $ConversionCode; 
		$this->Description = $Description;
    }
	
		public function ParseInstruction()
		{
			echo "ParseInstruction<br>";
		}
		
		public function DisplayParse()
		{
			echo "DisplayParse<br>";
		}
		
		public function ConvertInstruction()
		{
			echo "ConvertInstruction<br>";
		}
		
		public function DisplayConvert()
		{
			echo "DisplayConvert<br>";
		}
		
}

Class ClassFile
{
	public $cuserCommand;
	public $crules = array();
	public $rulecounter;
	public $fileName;
	
	public function  __construct($cuserCommand)
	{
		$this->cuserCommand = $cuserCommand;
		$this->fileName = null;
		$this->rulecounter = null;
    }
}	

Class ClassRule
{
	public $fileRule;
	public $parseRule;
	public $convertedRule;
	public $comment ;
	public $cwords = array();
	public $wordcounter;
	public $break_words_loop;
	public $label = null;
	public $labeldef = false;
	public $labelend = false;
	public $labelcall = false;
	public $tool = null;
	public $tooldef = false;
	public $toolcall = false;
	public $cycle = null;
	public $cycledef = false;
	public $cyclecall = false;
	public $error = null;
	
	public function  __construct($fileRule)
	{
		$this->fileRule = $fileRule;
		$this->parseRule = null;
		$this->convertedRule = "";
		$this->comment = null;
		$this->wordcounter = null;
		$this->break_words_loop = false;

    }
	
}

Class ClassWord
{
	public $word = "";
	public $syntax;
	public $parameter_1;
	public $parameter_2;
	public $converter;
	public $ruleNumber;
	public $marker = 9;// default unknown word
	public $NrParams;
	public $ops;
	
	public function  __construct($word)
	{
		$this->word = $word;
		$this->syntax = "";
		$this->parameter_1 = null;
		$this->parameter_2 = null;
		$this->NrParams = 0;
		$this->converter = null;
		$this->ops = false;
		$this->ruleNumber = null;
    }
}

?>