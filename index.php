<?php
class BF
{
	/*
	 * 
	 * Developer(s): Andrew B.
	 * Description: A BrainF*ck interpreter
	 *
	 * USAGE:
	 * $bf = new BF();
	 * echo $bf->interpret('codeHere', 'optionalInputHere');
	 *
	 */
	 
	protected $pointer = 0, $validChars = array('>','<','.',',','+','-','[',']');
	protected $cell = array(), $output = '', $input = '', $inputPointer = 0;
	
	public function interpret( $code, $input = '' ) {
		$preparedCode = $this->prepare($code); // strip invalid characters and break into sections
		$this->initializeCells(); // initialize cells to 0 for 30,000 cells
		$this->input = $input;
		#return print_r($code);
		foreach($preparedCode as $line) { //loop threw the sections
			if($line[0]) { // is a loop
				$safeGuard = 0;
				// Loop until the it lands on a cell that is zeroed out.
				//Only starts if current cell is not 0
				while($this->cell[$this->pointer]) {
					// if has looped more that 10,000 times then exit script and throw an error
					if($safeGuard > 10000) {
						die(print("Error in section:<br />\n'[{$line[1]}]'"));
					}
					// loop threw and interpret each character in the section
					foreach(str_split($line[1]) as $char) {
						$this->interpretChar($char);
						$safeGuard++;
					}
				}
			} else { // not a loop
				// loop threw and interpret each character in the section
				foreach(str_split($line[1]) as $char) {
					$this->interpretChar($char);
				}
			}
		}
		return $this->output;
	}
	
	protected function initializeCells() {
		for($i=0;$i<=30000;$i++) {
			$this->cell[$i] = 0;
		}
	}
	
	protected function prepare( $code ) {
		// some needed functions
		$inBrackets = false;
		$buffer = '';
		$array = array();
		// strip invalid characters
		foreach(str_split($code) as $char) {
			if(!in_array($char, $this->validChars, true)) {
				$code = str_replace($char, '', $code);
			}
		}
		//split into sections of loops and non loops
		foreach(str_split($code) as $char) {
			if($inBrackets) {
				if($char === ']') {
					$inBrackets = false;
					$array[] = array(true, $buffer);
					$buffer = '';
				} else {
					$buffer .= $char;
				}
			} else {
				if($char === '[') {
					$inBrackets = true;
					$array[] = array(false, $buffer);
					$buffer = '';
				} else {
					$buffer .= $char;
				}
			}
		}
		//get the last lingering section if there is one
		if($buffer !== '') {
			$array[] = array(false, $buffer);
			$buffer = '';
		}
		//return the processed code
		return $array;
	}
	
	protected function interpretChar( $char ) {
		switch($char) {
			case '>':
				return $this->cellForward();
			case '<':
				return $this->cellBackward();
			case '.':
				return $this->cellPrint();
			case ',':
				return $this->cellStore();
			case '+':
				return $this->cellIncriment();
			case '-':
				return $this->cellDecriment();
		}
	}
	
	protected function cellForward() {
		if($this->pointer !== 30000) {
			$this->pointer++;
		} else {
			$this->pointer = 0;
		}
	}
	
	protected function cellBackward() {
		if($this->pointer !== 0) {
			$this->pointer--;
		} else {
			$this->pointer = 30000;
		}
	}
	
	protected function cellPrint() {
		$this->output .= chr($this->cell[$this->pointer]);
	}
	
	protected function cellStore() {
		if(isset($this->input[$this->inputPointer])) {
			$this->cell[$this->pointer] = ord($this->input[$this->inputPointer]);
			$this->inputPointer++;
		}
	}
	
	protected function cellIncriment() {
		$this->cell[$this->pointer]++;
	}
	
	protected function cellDecriment() {
		if($this->cell[$this->pointer] !== 0) {
			$this->cell[$this->pointer]--;
		}
	}
}
?>
