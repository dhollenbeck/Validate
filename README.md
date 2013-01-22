Validate
========

PHP Validation class - dead simple validation of user inputs. Unit tests included.

## Usage ##
```php
require_once '/path/to/validate.php';
$input = $_GET['field1'];

//validate on individual rules
if(!validate::required($input)) throw new Exception('Missing required input!');
if(!validate::integer($input)) throw new Exception('Input must be an integer!');
if(!validate::positive($input)) throw new Exception('Input must be positive!');

//or, validate by combination
if(!validate::is('required, integer, positive', $input)) throw new Exception('Input must be a positive integer!');
if(!validate::is('required, dollars, positive', $input)) throw new Exception('Input must be a positive dollar amount!');
```
### Address Validation ###
```php
validate::street('123 main street')	//street address
validate::city('Austin')			//city
validate::state('TX')				//state
validate::zip('78733')				//zip code 5 or 9 digit
```

### Date/Time Validation ###
```php

//see PHP date format for options
validate::date('2012-10-10', 'Y-m-d')	//ISO date
validate::date('Jan 23, 2012')			//free format date
validate::date('January', 'F')			//textual month
validate::date('12', 'm')				//numeric month
validate::date('2003', 'Y')				//year
validate::date('31', 'd')				//day of month
validate::date('Mon', 'D')				//day of week

validate::timestamp('2010-10-10 14:00:00')	//timestamp

validate::after('2010-01-02', '-100 years')	//comparison after
validate::before('2010-01-02', 'now') //comparison before
```

### String Validation ###
```php
//letters and digits
validate::alpha('abcABC')				//letters
validate::alphanumeric('abc123')		//letters and numbers
validate::letters('abc')				//letters and fixed length
validate::digits('123')				//digits and fixed length

//length
validate::length('Some string', 11)							//fixed length
validate::length('some string', array('min'=>5, 'max'=>15))	//range length

//comments
validate::text('some string')								//text input like a comment

//lexicon (#=digit, ?=letter, *=either)
validate::lexicon('Some phrase: 123', 'Some phrase: ###')

```

### Person/Account Validation ###
```php
validate::name('Dan Hollenbeck')					//name
validate::email('dan@hollenbecks.com')				//email
validate::username('my secret username 123')		//username
validate::password('134#$$#ABC')					//password strength
validate::phone('512-555-1234', '###-###-####')		//phone with strick format
validate::phone('(512) 555-5555', '(###) ###-####') //phone with strick format
```

### Numeric Validation ###
```php
validate::integer('123')			//integer
validate::float('1.2')				//float
validate::positive('1.2')			//positive
validate::negative('-1.2')			//negative
validate::lessthan('1.0', '2.0')	//less than
validate::lessthanequal(1, 2)		//less than equal
validate::greaterthan('2', '1')			//greather than
validate::greaterthanequal('2', '2')	//greater than equal
validate::min(1, 0)						//min allowed value
validate::max(1, 2)						//max allowed value
```

### Transaction Validation ###

Credit card validating.

```php
//credit cards
$cc = '4242 4242 4242 4242';
validate::billname('Dan Hollenbeck');			//name on account
validate::creditcard($cc, 'visa, mastercard') 	//uses luhn alogrithm and accepted issuers (card types)
validate::expires('2014-12');					//expiration year and month
validate::cvv('123');							//credit card cvv
```

Electronic check validating.
```php
//bank check
validate::billname('Dan Hollenbeck');	//name on account
validate::check('1234567-575757')		//if single input containing both routing and account numbers
validate::bank_routing('0123456789')	//if seperate routing input
validate::bank_account('0123456789012') //if seperate account input
```

Transaction amounts validating.
```php
//amounts
validate::dollars('123.00');			//if you transact in dollar amounts
validate::cents('12300');				//if you transact in cents amount
```

Misc transactions methods used to make more friendly error messages.
```php
//credit card issuer detection
validate::issuer($cc) 		//returns card type
validate::mastercard($cc)	//true or false
validate::visa($cc)
validate::amex($cc)
validate::dinners($cc)
validate::jcb($cc)
validate::dinners($cc)
validate::maestro($cc)

//misc
validate::luhn($cc) 		//luhn algorithm w/o spaces in card numbers

```

## Todo ##
How do we use issuer detection to restrict transactions to a subset of cards?

option 1:
 if(
 	!validate::mastercard($cc) AND
 	!validate::visa($cc) AND
 	!validate::amex($cc) AND
 	!validate::dinners($cc)
 	) throw new Exception('We only accept mastercards, visa, amex and dinners cards.');

option 2:
 if(!validate::creditcard($cc, array('mastercard', 'visa', 'amex', 'dinners'))
 	throw new Exception('We only accept mastercards, visa, amex and dinners cards.');

option 3:
if(!validate::creditcard($cc, 'mastercard, visa, amex, dinners')
 	throw new Exception('We only accept mastercards, visa, amex and dinners cards.');


## Similar Projects ##
https://github.com/Respect/Validation



## Legal (MIT License) ##

Copyright (c) 2013 Dan Hollenbeck

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.