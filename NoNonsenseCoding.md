
# No Nonsense Coding

No Nonsense Coding is a provocative coding concept that probably upsets many. Used and promoted by T. Almroth - author of LiteCart and the LiteCore framework.
The purpose is to make as much sense as possible with as little effort as possible when writing program code.


## No Overcomplications

Avoid:

		function anOverComplicatedFrameworkFunctionName() {

			$anOverComplicatedFrameworkFunctionNameResult = [];

			$anExtremelyLongDescriptiveNameForAnArrayNode = '...';
			$anotherExtremelyLongDescriptiveNameForAnArrayNode = '...';

			$anOverComplicatedFrameworkFunctionNameResult['anExtremelyLongDescriptiveNameForAnArrayNode'] = $anExtremelyLongDescriptiveNameForAnArrayNode;
			$anOverComplicatedFrameworkFunctionNameResult['anotherExtremelyLongDescriptiveNameForAnArrayNode'] = $anotherExtremelyLongDescriptiveNameForAnArrayNode;

			...

			return $anOverComplicatedFrameworkFunctionNameResult;
		}

Better:

		function simpleFunctionName() {

			$result = [
				'nodeName' => '...',
				'anotherNode' => '...',
			];

			...

			return $result;
		}


## No Cryptic Naming

Cryptic naminf will just have anyone looking back at code overwhelmed, confused, or frustrated.

Avoid:

		function fmt_CustBillAddr(custObj $c) {
			$result = fmthlp::fmtAddr($c->billAddr->identity['custFName'], $c->billAddr->identity['custLName'], $c->billAddr->identity['custAddr1'], $c->delAddr->identity['custAddr2'], $c->billAddr->identity['zip'], $c->billAddr->identity['country']);
			return $result;
		}

		function fmt_CustDelAddr(custObj $c) {
			$result = fmthlp::fmtAddr($c->delAddr->identity['custFName'], $c->delAddr->identity['custLName'], $c->delAddr->identity['custAddr1'], $c->delAddr->identity['custAddr2'], $c->delAddr->identity['zip'], $c->delAddr->identity['country']);
			return $result;
		}

		echo fmt_CustBillAddr($custObj);
		echo fmt_CustDelAddr($custObj);

Better:

		function formatAddress(array $address) {
			return = '...';
		}

		echo formatAddress($customer->billingAddress);
		echo formatAddress($customer->deliveryAddress);


## No Repetitive Naming

Repetitive naming will just wear you out. More typing, longer lines to read, more noise to analyze, and it leaves a larger footprint.

Avoid:

		foreach ($webshopCustomers as $webshopCustomer) {
			printWebshopCustomerShippingStreetName($webshopCustomer['webshopCustomerShippingAddress']['webshopCustomerShippingAddressStreetName']);
		}

Better:

		foreach ($customers as $customer) {
			printStreetName($customer['shippingAddress']['streetName']);
		}


## No Variable Duplication

Variable duplication is a challenge to backtrace. If we have no use of the raw user input, we can just overwrite it with safer polished and sanitized data.

Avoid:

		$userInput = $_POST['userInput'];
		$sanitizedUserInput = sanitize($userInput);
		$trimmedSanitizedUserInput = trim($sanitizedUserInput);

		passToFunction($trimmedSanitizedUserInput); // Wait, what is the origin of the data again?

Better:

		$_POST['userInput'] = sanitize($_POST['userInput']); // Sanitize so we don't accidentally use the raw input again
		$_POST['userInput'] = polish($_POST['userInput']); // Do some polishing

		passToFunction($_POST['userInput']); // Oh we are passing something that came from a user input


## Avoid Single-Use Variables

Creating variables for one-time use should be avoided unless serving a good purpose.

Avoid:

		$array = ['foo', 'bar'];

		foreach ($array as $item) {
			echo $item;
		}

Better:

		foreach ([
			'foo',
			'bar',
		] as $item) {
			echo $item;
		}


## Use codes others recognize

Very bad:

		$country = 'us';     // Invalid. Country codes should be uppercase
		$lang = 'EN';        // Invalid. Language codes should be lowercase
		$currencyId = 1234;  // Nonsense. No one but you recognize your internal IDs and they are hard to migrate

Better:

		$countryCode = 'US';   // ISO 3166-1 Alpha 2
		$languageCode = 'en';  // ISO 639-1
		$currencyCode = 'USD'  // ISO 4217


Refusing ISO codes can be a lot of work:

		$country = $_POST['country'];

		if (in_array(strtolower($country), ['united states', 'united states of america', 'usa', 'u.s.a.', 'u.s.', 'us'])) {
			doSomethingWith('USA');
		}

		if (in_array(strtolower($country), ['great britain', 'britain', 'gb', 'united kingdom', 'united kingdom of great britain and northern ireland'])) {
			doSomethingWith('Britain');
		}

Better:

		$_POST['countryCode'] = strtoupper($_POST['countryCode']);

		doSomethingWith($_POST['countryCode']);


# No Yoda Conditions

Unless a galaxy far from, you are. Expressions like Yoda, you should not.

Avoid:

		if ('orange' == $fruit) {
			...
		}

Better:

		if ($fruit == 'orange') {
			...
		}


## No fat third party libraries for small features

Looking to cut corners with third party libraries will backfire eventually? Libraries can be performance draining. They have dependencies and can unknowingly become outdated or discontinued. They can be poorly managed, contain flaws or security problems. They can be a complete pain when you want to step up versions. One way or the other, they need to be maintained. Maintenance will take time and focus.

There is no good reason to embed a third party library if you will just utilize a small portion of it. If it's reasonable to code this part yourself it's likely a good idea in the long run to do it.

Try to stay away from third party libraries.

