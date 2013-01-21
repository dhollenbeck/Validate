<?php

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

	public static function is($what, $i, $opts = null){
		$what = strtolower($what);
		switch($what){
			case 'required':		return self::required($i);
			case 'state':			return self::state($i);
			case 'date':			return self::date($i, $opts);
			case 'after':			return self::after($i, $opts);
			case 'before':			return self::before($i, $opts);
			case 'time':			return self::time($i);
			case 'length':			return self::length($i, $opts);
			case 'name':			return self::name($i);
			case 'text':			return self::text($i);
			case 'email':			return self::email($i);  
			case 'alpha':			return self::alpha($i);
			case 'alphanumeric':	return self::alphanumeric($i);
			case 'integer':			return self::integer($i);
			case 'numeric':			return self::numeric($i);
			case 'positive':		return self::positive($i);
			case 'negative':		return self::negative($i);
			case 'lessthan':		return self::lessthan($i, $opts);
			case 'greaterthan':		return self::greaterthan($i, $opts);
			case 'lessthanequal':	return self::max($i, $opts);
			case 'greaterthanequal':return self::min($i, $opts);
			case 'max':				return self::max($i, $opts);
			case 'min':				return self::min($i, $opts);
			case 'phone':			return self::phone($i, $opts);
			case 'in':				return self::in($i,$opts);
			case 'equal':			return self::equal($i, $opts);
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
	 geo related
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
	  string numeric related
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
}
?>