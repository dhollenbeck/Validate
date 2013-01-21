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

//or, validate by combination of rules
if(!validate::is('required,integer,positive', $input)) throw new Exception('Input must be a positive integer!');

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
validate::letters('abc', 3)				//letters and fixed length
validate::digits('123', 3)				//digits and fixed length

//length
validate::length('Some string', 11)							//fixed length
validate::length('some string', array('min'=>5, 'max'=>15))	//varible length

validate::nopadding('some string')							//no whitespace allowed
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


## Similar Projects ##
https://github.com/Respect/Validation


## Legal (MIT License) ##

Copyright (c) 2013 Dan Hollenbeck

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.