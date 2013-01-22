<?php
/*

Copyright (c) 2013 Dan Hollenbeck

Permission is hereby granted, free of charge, to any person obtaining a copy of this 
software and associated documentation files (the "Software"), to deal in the Software 
without restriction, including without limitation the rights to use, copy, modify, merge, 
publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to 
whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies 
or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR 
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE 
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, 
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE 
SOFTWARE.
*/


//pattern based
/**********************************
	The preg_match() function uses Perl compatable regular expressions.
	The preg_match() function parameters: preg_match('/pattern/flags', $string)
	'^' matches at the beginning of a line.
	'$' matches at the ending of a line.
	Cheat Sheet: http://www.bitcetera.com/page_attachments/0000/0030/regex_in_a_nutshell.pdf

	http://msdn.microsoft.com/en-us/library/ff650303.aspx#paght000001_commonregularexpressions

***********************************/


class validate {

	private static $version = '1.0.0';
	
	public static $states = array(
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Conneticut',
		'DE' => 'Delaware',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'DC' => 'Washington, DC',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming'
    );

	public static function is($what, $input, $opts = null){
		$what = strtolower($what);

		//check for combined
		$validates = explode(',', $what);

		if(sizeof($validates) === 1){
			switch($what){
				case 'required':		return self::required($input);
				
				case 'length':			return self::length($input, $opts);
				case 'name':			return self::name($input);
				case 'text':			return self::text($input);
				case 'email':			return self::email($input);  
				case 'alpha':			return self::alpha($input);
				case 'alphanumeric':	return self::alphanumeric($input);
				case 'integer':			return self::integer($input);
				case 'float':			return self::float($input);

				case 'numeric':			return self::numeric($input);
				case 'positive':		return self::positive($input);
				case 'negative':		return self::negative($input);
				case 'lessthan':		return self::lessthan($input, $opts);
				case 'greaterthan':		return self::greaterthan($input, $opts);
				case 'lessthanequal':	return self::max($input, $opts);
				case 'greaterthanequal':return self::min($input, $opts);
				case 'max':				return self::max($input, $opts);
				case 'min':				return self::min($input, $opts);
				case 'phone':			return self::phone($input, $opts);
				case 'in':				return self::in($input,$opts);
				case 'equal':			return self::equal($input, $opts);

				//address
				case 'address':			return self::address($input);
				case 'state':			return self::state($input);
				case 'city':			return self::city($input);
				case 'zip':				return self::zip($input);

				//date-time
				case 'date':			return self::date($input, $opts);
				case 'timestamp':		return self::timestamp($input);
				case 'after':			return self::after($input, $opts);
				case 'before':			return self::before($input, $opts);

				//transactions


				default: throw new Exception("Undefined validation rule.");
			}
		} else {
			foreach($validates as $validate){
				$validate = trim($validate);
				if(!self::$validate($input)) return false;
			}
			return true;
		}

	}

	/**
	 * Version Number
	 */
	public static function version(){
		return self::$version;
	}

	/**
	 * The input is required. All other validation methods will return if
	 * the input is missing, which allows the validation methods to support
	 * optional inputs.
	 */
	public static function required($data){
		if(is_null($data)) return false;
		if(is_array($data) AND empty($data)) return false;
		if(is_string($data)){
			if('' == $data) return false; //empty string does not satisfy as a required input
			if('' == trim($data)) return false; //all whitespace does not satisfy as a required input
		}
		return true;
	}

	public static function regex($s, $pattern, $flags = 'i'){
		$exp = '/' . $pattern . '/' . $flags;
		return preg_match($exp, $s) ? true : false;
	}

	/***************************
	 address related
	***************************/
	public static function state($abbr){
		if(!self::required($abbr)) return true;
		return (isset(self::$states[$abbr]))? true : false;
	}

	public static function zip($s){
		if(!self::required($s)) return true;

		//$pattern = '^(\d{5}-\d{4}|\d{5}|\d{9})$|^([a-z]\d[a-z] \d[a-z]\d)$'; //international
		$pattern = '^\d{5}([\-]\d{4})?$'; //usa
		return self::regex($s, $pattern);
	}

	public static function street($s){
		return self::text($s);
	}

	public static function city($s){
		return self::text($s);
	}	

	/***************
	  Date/time related
	***************/
	public static function date($dt, $format = null){
		if(!self::required($dt)) return true;
		if(is_null($format)){
			return (FALSE !== strtotime($dt)) ? true : false;
		} else {
			$ts = strtotime($dt);
			if(FALSE === $ts) return false;
			return ($dt == date($format, $ts)) ? true : false;
		}
	}
	public static function timestamp($dt){
		if(!self::required($dt)) return true;
		if('0000-00-00 00:00:00' == $dt) return true;
		return self::date($dt, 'Y-m-d H:i:s');
	}
	public static function after($dt1, $dt2){
		if(!self::required($dt1)) return true;
		if(!self::required($dt2)) return true;
		$ts1 = strtotime($dt1);
		$ts2 = strtotime($dt2);
		if(false === $ts1) return false;
		if(false === $ts2) return false;
		return ($ts1 > $ts2)? true : false;
	}
	public static function before($dt1, $dt2){
		if(!self::required($dt1)) return true;
		if(!self::required($dt2)) return true;
		$ts1 = strtotime($dt1);
		$ts2 = strtotime($dt2);
		if(false === $ts1) return false;
		if(false === $ts2) return false;
		return ($ts1 < $ts2)? true : false;
	}

	/*****************
	 Array functions
	*****************/

	public static function in($needle, $haystack){
		if(!self::required($needle)) return true;
		if(!is_array($haystack)) return false;
		return in_array($needle, $haystack);
	}

	/*****************
	  String related
	*****************/

	public static function letters($str, $length){
		if(!self::required($str)) return true;
		$pattern = '^[a-zA-Z]{'.$length.'}$';
		return self::regex($str, $pattern);
	}

	public static function digits($str, $length){
		if(!self::required($str)) return true;
				
		$pattern = '^[0-9]{'.$length.'}$';
		return self::regex($str, $pattern);
	}


	public static function length($str, $opts){
		if(!self::required($str)) return true;
		if(!is_string($str)) return false;
		$len = strlen($str);

		//use min and max options
		if(isset($opts['min']) AND isset($opts['max'])){
			return (($opts['min'] <= $len) AND ($opts['max'] >= $len)) ? true : false;
		} else if(isset($opts['min'])){
			return ($opts['min'] <= $len)? true : false;
		} else if(isset($opts['max'])){
			return ($opts['max'] >= $len)? true : false;
		}
		
		//use numeric or numeric string
		if(is_numeric($opts)){
			return ($opts == $len)? true : false;
		}
		return false;
	}

	public static function nopadding($s){
		if(!is_string($s)) return false;
		if(!self::required($s)) return true; //all validations are optional

		//leading or trailing whitespace?
		return ($s == trim($s))? true : false; 
	}

	public static function text($str){
		if(!is_string($str)) return false;
		if(!self::required($str)) return true; //all validations are optional

		$pattern = "^[-a-z0-9' \/#.,()!?@;:_\"*+=$]*$";
		return self::regex($str, $pattern);
	}

	public static function alpha($str){
		if(!is_string($str)) return false;
		if(!self::required($str)) return true; //all validations are optional

		$pattern = '^[a-z]*$';
		return self::regex($str, $pattern);
	}
	public static function alphanumeric($str){
		if(!is_string($str)) return false;
		if(!self::required($str)) return true; //all validations are optional

		$pattern = '^[a-z0-9]*$';
		return self::regex($str, $pattern);
	}


	/***************
	 idenity related
	***************/
	public static function name($str){
		if(!is_string($str)) return false;
		if(!self::required($str)) return true; //all validations are optional
		if(!self::nopadding($str)) return false; //no leading or trailing what space is allowed

		$pattern = "^[-a-z' .]{1,}$"; // only hypens, letters, apostrophes, spaces, periods
		return self::regex($str, $pattern);
	}
	
	public static function email($str){
		if(!is_string($str)) return false;
		if(!self::required($str)) return true; //all validations are optional
		if(!self::nopadding($str)) return false; //no leading or trailing what space is allowed

		$pattern = '^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'; //http://www.regular-expressions.info/email.html
		return self::regex($str, $pattern);
	}

	public static function username($str){
		if(!is_string($str)) return false;
		if(!self::required($str)) return true; //all validations are optional
		if(!self::nopadding($str)) return false; //no leading or trailing what space is allowed

		$pattern = '^[-a-z0-9.@_ ]{5,50}$'; //Usernames are required to be between 5 and 50 characters long and consist of letters, numbers, spaces, dashes,  underscores, and @ sign.
		return self::regex($str, $pattern);
	}

	public static function password($str){
		if(!is_string($str)) return false;
		if(!self::required($str)) return true;

		//require greater than 6 characters
		if(!self::length($str, array('min'=>6, 'max'=>50))) return false;
		
		//require atleast one case insensitive letter
		$pattern = '^.*[a-z]+.*$';
		if(!self::regex($str, $pattern)) return false;
		
		//require atlest one number
		$pattern = '^.*[0-9]+.*$';
		if(!self::regex($str, $pattern)) return false;

		//only allow these characters
		$pattern = '^[-a-z0-9 !@#$%^&+_()=*~]*$';
		if(!self::regex($str, $pattern)) return false;

		return true;
	}



	/****************
	  numeric related
	*****************/
	public static function integer($num){
		if(!self::required($num)) return true;
		if(!is_string($num)) return false;
		if(!is_numeric($num)) return false;
		return ((int)$num == $num)? true : false;
	}
	public static function float($num){
		if(!self::required($num)) return true;
		if(!is_string($num)) return false;
		$pattern = '^[-+]?[0-9]+\.?[0-9]+$';
		return self::regex($num, $pattern);
	}
	public static function positive($num){
		if(!self::required($num)) return true;

		if(!is_numeric($num)) return false;
		return ($num > 0)? true : false;
	}
	public static function negative($num){
		if(!self::required($num)) return true;

		if(!is_numeric($num)) return false;
		return ($num < 0)? true : false;
	}
	public static function lessthan($num, $value){
		if(!self::required($num)) return true;

		if(!is_numeric($num)) return false;
		if(!is_numeric($value)) return false;

		return ($num < $value)? true : false;
	}
	public static function greaterthan($num, $value){
		if(!self::required($num)) return true;

		if(!is_numeric($num)) return false;
		if(!is_numeric($value)) return false;

		return ($num > $value)? true : false;
	}
	public static function lessthanequal($num, $value){
		if(!self::required($num)) return true;
	
		if(!is_numeric($num)) return false;
		if(!is_numeric($value)) return false;

		return self::max($num, $value);
	}
	public static function greaterthanequal($num, $value){
		if(!self::required($num)) return true;

		if(!is_numeric($num)) return false;
		if(!is_numeric($value)) return false;

		return self::min($num, $value);
	}
	public static function max($num, $value){
		if(!self::required($num)) return true;
		if(!is_numeric($num)) return false;
		if(!is_numeric($value)) return false;

		return ($num <= $value)? true : false;
	}
	public static function min($num, $value){
		if(!self::required($num)) return true;

		if(!is_numeric($num)) return false;
		if(!is_numeric($value)) return false;

		return ($num >= $value)? true : false;
	}
	public static function key($num){
		if(!self::required($num)) return true;
		
		$pattern = '^[0-9]+$';
		return self::regex($num, $pattern);
	}

	public static function lexicon($input, $format){
		if(!is_string($input)) return false;
		if(!self::required($input)) return true;
		
		//must be same length
		if(strlen($input) !== strlen($format)) return false;

		$characters1 = str_split($input);
		$characters2 = str_split($format);

		//compare all characters
		foreach($characters2 as $key=>$target){
			$test = $characters1[$key];

			switch($target){
				case '#': 
					if(!self::digits($test, 1)) return false;	
					break;
				case '?': 
					if(!self::letters($test, 1)) return false; 
					break;
				case '*': 
					if(!(self::letters($test, 1) OR self::digits($test,1))) return false; 
					break;
				default: if($target !== $test) return false;
			}

		}
		return true;
	}
	/************************
	  phone related
	************************/
	public static function phone($phone, $format = '(###) ###-####'){
		if(!self::required($phone)) return true;

		return self::lexicon($phone, $format);
	}

	/******************
	 equality related
	******************/
	public static function equal($val1, $val2){
		if(!self::required($val1)) return true;
		return ($val1 === $val2)? true : false;
	}
	
	public static function notequal($val1, $val2){
		if(!self::required($val1)) return true;
		return ($val1 !== $val2)? true : false;
	}

	/*******************
	 transaction related
	*******************/
	public static function cvv($cvv){
		if(!self::required($cvv)) return true;
		if(!self::digits($cvv)) return false;
		if(!self::length($cvv, array('min'=>3, 'max'=>4))) return false;
		return true;
	}

	public static function expiration($expires){
		if(!self::required($expires)) return true;
		if(!self::date($expires, 'Y-m')) return false;
		if(!self::before($expires, 'now')) return false;
		if(!self::after($expires, '+15 years')) return false;
		return true;
	}

	public static function dollars($amt){
		if(!self::required($amt)) return true;
		if(!self::float($amt)) return false;
		return true;
	}

	public static function cents($amt){
		if(!self::required($amt)) return true;
		if(!self::integer($amt)) return false;
		return true;
	}

	public static function creditcard($number, $format=null){
		if(!self::required($number)) return true;
		//if(!is_null($format)){
			if(!self::lexicon($number, $format)) return false;
		//}
		if(!self::length($number, array('min' => 14, 'max' => 16))) return false;
		if(!self::luhn($number)) return false;
		return true;
	}

	public static function check_account($num){
		if(!self::required($num)) return true;
		if(!self::digits($num)) return false;
		if(!self::length($num, array('min'=>3, 'max'=>4))) return false; //todo: check number range
	}

	public static function check_routing($num){
		if(!self::required($num)) return true;
		if(!self::digits($num)) return false;
		if(!self::length($num, array('min'=>3, 'max'=>4))) return false; //todo: check number range
	}

	public static function check(){
		return false;
	}
	/**
	 * Issuer of credit card.
	 * @link http://en.wikipedia.org/wiki/Bank_card_number
	 * @link http://www.pixelenvision.com/2314/php-credit-card-validation-class-using-mod-10-luhn-more/
	 */
	public static function issuer($cc){
		
		//mastercard: 14 chars, starts with 51-55
		if (ereg('^5[1-5][0-9]{14}$', $cc)) return 'MASTERCARD';

		//visa: 
		if (ereg('^4[0-9]{12}([0-9]{3})?$', $cc)) return 'VISA';
		if (ereg('^3[47][0-9]{13}$', $cc)) return 'AMEX';
		if (ereg('^3(0[0-5]|[68][0-9])[0-9]{11}$', $cc)) return 'DINNERS';
		if (ereg('^6011[0-9]{12}$', $cc)) return 'DISCOVER';
		if (ereg('^(3[0-9]{4}|2131|1800)[0-9]{11}$', $cc)) return 'JCB';
		if (ereg('^(5[06-8]|6)[0-9]{10,17}$', $cc)) return 'MAESTRO';
		return 'UNKNOWN';
	}

	/**
	 * Luhn Alhorithm
	 * @link http://en.wikipedia.org/wiki/Luhn_algorithm
	 *
	 * Methodology:
	 * 1. Double every even (from the right) digit.
	 * 2. Split doubling if greater than 9.
	 * 3. Sum digits.
	 * 4. Valid if sum module 10 equals zero.
	 */
	public static function luhn($cc){

		//remove formating characters
		$cc = ereg_replace('[^0-9]', '', $cc); 

		//number of digits parity
		$parity = strlen($cc) % 2; //0=even, 1=odd
		$sum = 0;
		
		//array of digits
	  	$digits = str_split($cc);

	  	foreach($digits as $key => $digit) {

			//double every even (from the right) digit
		  	if (($key % 2) == $parity) $digit *= 2;

			//split double digits
		  	if ($digit > 9) {
			  	$parts = str_split($digit); //split
			  	$digit = $parts[0] + $parts[1]; //sum together
		  	}

			//sum total
			$sum += $digit;
	  	}

	  	//valid if sum mod 10 equals zero
		$valid = ($sum % 10 == 0)? true : false;

		return $valid;
	}
}
?>