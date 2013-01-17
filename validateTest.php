<?php

require_once '../settings.php';
//require_once '../validate.php';

use validate as v;


class validationTest extends PHPUnit_Framework_TestCase {

	public function setUp(){}
	public function tearDown(){}


	public function test_version(){
		$this->assertEquals(v::version(), '1.0.0', 'test version');
	}

	public function test_required(){

		//true
		$this->assertTrue(v::required('.'), 'non-empty string');
		$this->assertTrue(v::required('x'), 'non-empty string');
		$this->assertTrue(v::required('0'), 'zero numeric string');
		$this->assertTrue(v::required(array('x')), 'non-empty array');
		$this->assertTrue(v::required(0), 'zero numeric');
		$this->assertTrue(v::required(1), 'positive numeric');
		$this->assertTrue(v::required(0.1), 'small float numeric');
		
		//false
		$this->assertFalse(v::required(''), 'empty string');
		$this->assertFalse(v::required(null), 'null object');
		$this->assertFalse(v::required(array()), 'empty array');

		$this->assertFalse(v::required(' '), 'whitespace string');
		$this->assertFalse(v::required("\n"), 'newline string');
		$this->assertFalse(v::required("\t"), 'tab string');
		$this->assertFalse(v::required("\r"), 'carriage return string');
		$this->assertFalse(v::required("\0"), 'NUL-byte string');
		$this->assertFalse(v::required("\x0B"), 'vertical tab string');

	}

	public function test_state(){

		//true
		$this->assertTrue(v::state(''), 'not required');
		$this->assertTrue(v::state('TX'), 'valid state abbr');

		//false
		$this->assertFalse(v::state('XX'), 'invalid state abbr');
		$this->assertFalse(v::state('Texas'), 'invalid state abbr');
	}

	public function test_zip(){
		//true
		$this->assertTrue(v::zip(''), 'not required');
		$this->assertTrue(v::zip('12345'), 'normal 5 zip');
		$this->assertTrue(v::zip('12345-1234'), 'normal 5-4 zip');

		//false
		$this->assertFalse(v::zip('1234'), 'short zip');
		$this->assertFalse(v::zip('123456'), 'long zip');
		$this->assertFalse(v::zip('12345-'), 'invalid zip');
		$this->assertFalse(v::zip('12345-12345'), 'too long 5-4 zip');
	}

	public function test_address(){
		//true
		$this->assertTrue(v::address(''), 'not required');
		$this->assertTrue(v::address('123 main street'), 'not required');

		//false
	}

	public function test_date(){

		//true
		$this->assertTrue(v::date(''), 'not required');
		$this->assertTrue(v::date('', 'Y-m-d'), 'not required');
		$this->assertTrue(v::date('1968-01-01', 'Y-m-d'), 'normal date');

		//false
		$this->assertFalse(v::date('1968-01-', 'Y-m-d'), 'partial format');
		$this->assertFalse(v::date('68-01-01', 'Y-m-d'), 'partial format');
		$this->assertFalse(v::date('1/1/1968', 'Y-m-d'), 'date does not match specified format');
		$this->assertFalse(v::date('0000-00-00', 'Y-m-d'), 'zero date');
		//$this->assertFalse(v::date('0/0/0'), 'zero date');
	}

	public function test_timestamp(){
		
		//true
		$this->assertTrue(v::timestamp(''), 'not required');
		$this->assertTrue(v::timestamp('0000-00-00 00:00:00'), 'zero timestamp not required');
		$this->assertTrue(v::timestamp('1968-01-01 00:00:00'), 'normal timestamp');

		//false
		$this->assertFalse(v::timestamp('1968-01-01'), 'date format');
		$this->assertFalse(v::timestamp('1968-01-01 00:00:0'), 'non-timestamp format');
	}

	public function test_after(){
		//true
		$this->assertTrue(v::after('', '1968-01-01'), 'not required');
		$this->assertTrue(v::after('1968-01-01', ''), 'not required');
		$this->assertTrue(v::after('1970-01-01', '1968-01-01'), 'two dates');

		//fail
		$this->assertFalse(v::after('1968-01-01', '1970-01-01'), 'two dates');
		$this->assertFalse(v::after('1970-01-', '1968-01-01'), 'invalid date input 1');
		$this->assertFalse(v::after('1970-01-01', '1968-01-'), 'invalid date input 2');
		$this->assertFalse(v::after('1970-01-', '1968-01-'), 'invalid date both inputs');
	}

	public function test_before(){
		//true
		$this->assertTrue(v::before('', '1968-01-01'), 'not required');
		$this->assertTrue(v::before('1968-01-01', ''), 'not required');
		$this->assertTrue(v::before('1968-01-01', '1970-01-01'), 'two dates');

		//fail
		$this->assertFalse(v::before('1970-01-01', '1968-01-01'), 'two dates');
		$this->assertFalse(v::before('1968-01-', '1970-01-01'), 'invalid date input 1');
		$this->assertFalse(v::before('1968-01-01', '1970-01-'), 'invalid date input 2');
		$this->assertFalse(v::before('1968-01-', '1970-01-'), 'invalid date both inputs');
	}

	public function test_in(){
		//true
		$this->assertTrue(v::in('', array('TX')), 'not required');
		$this->assertTrue(v::in('TX', array('TX')), 'in array');
		
		//false
		$this->assertFalse(v::in('TXX', array('TX')), 'not in array');
		$this->assertFalse(v::in('TX', 0), 'haystack not array');
	}

	public function test_length(){
		
		//true
		$this->assertTrue(v::length('', 3), 'not required');
		$this->assertTrue(v::length('abc', 3), 'fixed length');
		$this->assertTrue(v::length('abc', array('min'=>2, 'max'=>3)), 'min and max length');
		$this->assertTrue(v::length('abc', array('min'=>3, 'max'=>3)), 'min and max length');
		
		//false
		$this->assertFalse(v::length('abc', 4), 'not fixed length');
		$this->assertFalse(v::length('abc', array('min'=>4, 'max'=>5)), 'too short for min and max');
		$this->assertFalse(v::length('abc', array('min'=>4, 'max'=>5)), 'too short for min and max');
		$this->assertFalse(v::length(array('abc'), array('min'=>3, 'max'=>3)), 'non-string input');
	}

	public function test_nopadding(){
		
		//true
		$this->assertTrue(v::nopadding(''), 'blank string is not required');
		$this->assertTrue(v::nopadding('abc'), 'no whitespace string');
		$this->assertTrue(v::nopadding('ab cd'), 'middle whitespace string');

		//false
		$this->assertFalse(v::nopadding(' abc'), 'leading white space');
		$this->assertFalse(v::nopadding('abc '), 'trailing white space');
		$this->assertFalse(v::nopadding(' abc '), 'leading and trailing white space');

	}

	public function test_text(){
		
		//true
		$this->assertTrue(v::text(''), 'not required');
		$this->assertTrue(v::text("abcdefghijklmnopqrstuvwxyz"), 'lower case letters');
		$this->assertTrue(v::text("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 'upper case letters');
		$this->assertTrue(v::text("0123456789"), 'numbers');
		$this->assertTrue(v::text("let's go"), 'single quote and space');
		$this->assertTrue(v::text('"'), 'double quote');
		$this->assertTrue(v::text("1/4th"), 'forward slash');
		$this->assertTrue(v::text("Unit #23"), 'pound');
		$this->assertTrue(v::text("."), 'period');
		$this->assertTrue(v::text(","), 'comma');
		$this->assertTrue(v::text("()"), 'parentheses');
		$this->assertTrue(v::text("!"), 'exclamation point');
		$this->assertTrue(v::text("?"), 'question mark');
		$this->assertTrue(v::text("@"), 'at sign');
		$this->assertTrue(v::text(";:"), 'colons');
		$this->assertTrue(v::text("-_+="), 'hypen and underscore and plus and equals');
		$this->assertTrue(v::text("*"), 'asterisk');
		$this->assertTrue(v::text("$"), 'dollar sign');

		//false
		$this->assertFalse(v::text("1\4th"), 'backward slash');
	}

	public function test_alpha(){
		
		//true
		$this->assertTrue(v::alpha(''), 'not required');
		$this->assertTrue(v::alpha("abcdefghijklmnopqrstuvwxyz"), 'lower case letters');
		$this->assertTrue(v::alpha("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 'upper case letters');

		//false
		$this->assertFalse(v::alpha("1"), 'number');
	}

	public function test_alphanumeric(){
		
		//true
		$this->assertTrue(v::alphanumeric(''), 'not required');
		$this->assertTrue(v::alphanumeric("abcdefghijklmnopqrstuvwxyz"), 'lower case letters');
		$this->assertTrue(v::alphanumeric("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 'upper case letters');
		$this->assertTrue(v::alphanumeric("0123456789"), 'numbers');
		
		//false
		$this->assertFalse(v::alphanumeric("$"), 'number');
		
	}
	
	public function test_name(){
		
		//true
		$this->assertTrue(v::name(''), 'blank string is not required');
		$this->assertTrue(v::name("dan hollenbeck"), 'space in name is allowed');
		$this->assertTrue(v::name("abcdefghijklmnopqrstuvwxyz"), 'lower case letters');
		$this->assertTrue(v::name("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 'upper case letters');
		$this->assertTrue(v::name("Johnson-Smith"), 'hypen');
		$this->assertTrue(v::name("O'reily"), 'single apostrophe');
		$this->assertTrue(v::name("Mr. Hollenbeck"), 'period');
		
		//false
		$this->assertFalse(v::name(' hollenbeck'), 'leading whitespace is not allowed');
		$this->assertFalse(v::name(' hollenbeck'), 'trailing whitespace is not allowed');
		$this->assertFalse(v::name("dan\nhollenbeck"), 'do not allow newline in name');
	}

	public function test_email(){
		//true
		$this->assertTrue(v::email(''), 'not required');
		$this->assertTrue(v::email('me@example.com'), 'standard email');

		//false
		$this->assertFalse(v::email("me at example.com"), 'invalid email');
		$this->assertFalse(v::email('me@work.com, me@personal.com'), 'multiple emails seperated by space and coma');
		$this->assertFalse(v::email('me@work.com; me@personal.com'), 'multiple emails seperated by space and semi-colon');
		$this->assertFalse(v::email('me@work.com;me@personal.com'), 'multiple emails seperated by just semi-colon');
	}

	public function test_username(){
		 //Usernames are required to be between 5 and 50 characters long and consist of letters, numbers, spaces, dashes,  underscores.

		//true
		$this->assertTrue(v::username(''), 'not required');
		$this->assertTrue(v::username('DOGGY'), 'normal username');
		$this->assertTrue(v::username('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 'all upper case letters');
		$this->assertTrue(v::username('abcdefghijklmnopqrstuvwxyz'), 'all lower case letters');
		$this->assertTrue(v::username('0123456789'), 'numbers');
		$this->assertTrue(v::username('DAN-O'), 'hypen');
		$this->assertTrue(v::username('DAN_O'), 'underscore');
		$this->assertTrue(v::username('me@example.com'), 'email');

		//false
		$this->assertFalse(v::username('DOG'), 'too short');
		$this->assertFalse(v::username('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 'too long');
	}

	public function test_password(){
		//between 6 and 50
		//one or more letters
		//one or more numbers
		//consisting of these '^[-a-z0-9 !@#$%^&+_()=*~]*$' characters.

		//true
		$this->assertTrue(v::password(''), 'not required');
		$this->assertTrue(v::password('Y0 mamma!'), 'valid password: letter, number, length');
		$this->assertTrue(v::password('Y0 mamma~'), 'tilda');
		$this->assertTrue(v::password('Y0 mamma@'), 'at sign');
		$this->assertTrue(v::password('Y0 mamma#'), 'pound');
		$this->assertTrue(v::password('Y0 mamma$'), 'dollar sign');
		$this->assertTrue(v::password('Y0 mamma%'), 'percent');
		$this->assertTrue(v::password('Y0 mamma^'), 'hat');
		$this->assertTrue(v::password('Y0 mamma&'), 'ampersand');
		$this->assertTrue(v::password('Y0 mamma*'), 'asterisk');
		$this->assertTrue(v::password('Y0 mamma*'), 'asterisk');
		$this->assertTrue(v::password('Y0 mamma)('), ')(');
		$this->assertTrue(v::password('Y0 mamma+'), '+');
		$this->assertTrue(v::password('Y0 mamma-'), '-');
		$this->assertTrue(v::password('Y0 mamma_'), '_');
		$this->assertTrue(v::password('Y0 mamma='), '=');

		//false
		$this->assertFalse(v::password('Yo mamma!'), 'no number');
		$this->assertFalse(v::password('1234567'), 'no letter');
		$this->assertFalse(v::password('1234Y'), 'too short');
		$this->assertFalse(v::password("\0abcdefhi7"), 'null byte');
	}

	public function test_integer(){

		//true
		$this->assertTrue(v::integer(''), 'not required');
		$this->assertTrue(v::integer('0'), 'string zero');
		$this->assertTrue(v::integer('01'), 'string zero one');
		$this->assertTrue(v::integer('-1'), 'string neg one');
		
		//false
		$this->assertFalse(v::integer('abc'), 'letters');
		$this->assertFalse(v::integer(0.1), 'float');
		$this->assertFalse(v::integer('1.1'), 'float string');
		$this->assertFalse(v::integer(0), 'int zero');
		$this->assertFalse(v::integer(1), 'int one');
		$this->assertFalse(v::integer(-1), 'int neg one');
	}

	public function test_float(){
		
		//true
		$this->assertTrue(v::float(''), 'not required');
		$this->assertTrue(v::float('1.1'), 'float string');
		$this->assertTrue(v::float('0.0'), 'zero float string');
		$this->assertTrue(v::float('+1.1'), 'pos float string');
		$this->assertTrue(v::float('-1.1'), 'neg float string');

		//false
		$this->assertFalse(v::float(0.1), 'non-zero float');
		$this->assertFalse(v::float(0.0), 'zero float');
		$this->assertFalse(v::float('abc'), 'letters');
		$this->assertFalse(v::float(0), 'int zero');
		$this->assertFalse(v::float(1), 'int one');
		$this->assertFalse(v::float('0'), 'string zero');
		$this->assertFalse(v::float('-1'), 'string neg one');
		$this->assertFalse(v::float(-1), 'int neg one');
	}

	public function test_positive(){
		
		//true
		$this->assertTrue(v::positive(''), 'not required');
		$this->assertTrue(v::positive('1'), 'str int one');
		$this->assertTrue(v::positive('1.0'), 'str flt one');

		//false
		$this->assertFalse(v::positive('0'), 'str int one');
		$this->assertFalse(v::positive('-1'), 'str neg int one');
		$this->assertFalse(v::positive('-1.0'), 'str neg flt one');
		$this->assertFalse(v::positive('abc'), 'str letters');
	}

	public function test_negative(){

		//true
		$this->assertTrue(v::negative(''), 'not required');
		$this->assertTrue(v::negative('-1'), 'str neg int one');
		$this->assertTrue(v::negative('-1.0'), 'str neg flt one');

		//flase
		$this->assertFalse(v::negative('+1'), 'str pos int one');
		$this->assertFalse(v::negative('+1.0'), 'str pos flt one');
		$this->assertFalse(v::negative('abc'), 'str letters');
	}

	public function test_lessthan(){

		//true
		$this->assertTrue(v::lessthan('', 1), 'not required');
		$this->assertTrue(v::lessthan('0', 1), '0 < 1');
		$this->assertTrue(v::lessthan('0.0', 1.0), '0.0 < 1.0');

		//false
		$this->assertFalse(v::lessthan('1', 0), '1 < 0');
		$this->assertFalse(v::lessthan('1.0', 0.0), '1.0 < 0.0');
		$this->assertFalse(v::lessthan('0.0', 0.0), '0.0 < 0.0');
	}

	public function test_greaterthan(){

		//true
		$this->assertTrue(v::greaterthan('', 1), 'not required');
		$this->assertTrue(v::greaterthan('1', 0), '1 > 0');
		$this->assertTrue(v::greaterthan('1.0', 0.0), '1.0 > 0.0');

		//false
		$this->assertFalse(v::greaterthan('0', 1), '0 > 1');
		$this->assertFalse(v::greaterthan('0.0', 1.0), '0.0 > 1.0');
		$this->assertFalse(v::greaterthan('0.0', 0.0), '0.0 > 0.0');
	}

	public function test_lessthanequal(){

		//true
		$this->assertTrue(v::lessthanequal('', 1), 'not required');
		$this->assertTrue(v::lessthanequal('0', 1), '0 <= 1');
		$this->assertTrue(v::lessthanequal('0.0', 1.0), '0.0 <= 1.0');
		$this->assertTrue(v::lessthanequal('1.0', 1.0), '1.0 <= 1.0');

		//false
		$this->assertFalse(v::lessthanequal('1', 0), '1 <= 0');
		$this->assertFalse(v::lessthanequal('1.0', 0.0), '1.0 <= 0.0');
	}

	public function test_greaterthanequal(){

		//true
		$this->assertTrue(v::greaterthanequal('', 1), 'not required');
		$this->assertTrue(v::greaterthanequal('1', 0), '1 >= 0');
		$this->assertTrue(v::greaterthanequal('1.0', 0.0), '1.0 >= 0.0');
		$this->assertTrue(v::greaterthanequal('1.0', 1.0), '1.0 >= 1.0');

		//false
		$this->assertFalse(v::greaterthanequal('0', 1), '0 >= 1');
		$this->assertFalse(v::greaterthanequal('0.0', 1.0), '0.0 >= 1.0');
	}

	public function test_max(){

		//true
		$this->assertTrue(v::max('', 1), 'not required');
		$this->assertTrue(v::max('0', 1), '1 max 0');
		$this->assertTrue(v::max('0.0', 1.0), '1.0 max 0.0');
		$this->assertTrue(v::max('1.0', 1.0), '1.0 max 1.0');

		//false
		$this->assertFalse(v::max('1', 0), '1 max 0');
		$this->assertFalse(v::max('1.0', 0.0), '1.0 max 0.0');
		$this->assertFalse(v::max('1.1', 1.0), '1.1 max 1.0');
	}

	public function test_min(){

		//true
		$this->assertTrue(v::min('', 1), 'not required');
		$this->assertTrue(v::min('1', 0), '1 min 0');
		$this->assertTrue(v::min('1.0', 0.0), '1.0 min 0.0');
		$this->assertTrue(v::min('1.0', 1.0), '1.0 min 1.0');

		//false
		$this->assertFalse(v::min('0', 1), '0 min 1');
		$this->assertFalse(v::min('0.0', 1.0), '1.0 min 0.0');
		$this->assertFalse(v::min('1.0', 1.1), '1.0 min 1.1');
	}

	public function test_key(){
		
		//true
		$this->assertTrue(v::key(''), 'not required');
		$this->assertTrue(v::key('0'), 'zero');
		$this->assertTrue(v::key('1'), 'one');

		//false
		$this->assertFalse(v::key('0.0'), 'float zero');
		$this->assertFalse(v::key('-1'), 'neg one');
		$this->assertFalse(v::key('abc'), 'letters');
	}

	public function test_phone_weak(){

		//true weak
		$this->assertTrue(v::phone('', 'weak'), 'not required');
		$this->assertTrue(v::phone('8001231234', 'weak'), '800 number');

		//false weak
		$this->assertFalse(v::phone('1231234', 'weak'), 'too short');
		$this->assertFalse(v::phone('51212312341', 'weak'), 'too long');
	}

	public function test_phone_literal(){
		//true literal
		$this->assertTrue(v::phone('1-512-373-1234'), '512 number');
		$this->assertTrue(v::phone('512-373-1234'), '512 number');

		//false literal
		$this->assertFalse(v::phone('800-555-0199'), 'reserved number');
	}

	public function test_equal(){

		//true
		$this->assertTrue(v::equal('', 'x'), 'not required');
		$this->assertTrue(v::equal('x', 'x'), 'equal');

		//false
		$this->assertFalse(v::equal('y', 'x'), 'not equal');
	}

	public function test_notequal(){

		//true
		$this->assertTrue(v::notequal('', 'x'), 'not required');
		$this->assertTrue(v::notequal('y', 'x'), 'not equal');

		//false
		$this->assertFalse(v::notequal('x', 'x'), 'not equal');

	}
}
?>