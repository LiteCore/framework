
# No Nonsense Coding

No Nonsense Coding is a provocative coding concept that will probably upset many. Used and promoted by the developer behind the e-commerce platform LiteCart.
The purpose is to make as much sense as possible with as little effort as possible when writing program code.

## No Duplicate Naming

    foreach ($myWebshopCustomers as $customer) {
      doSomethingNicelyWithThis($customer['Customer']['customerShippingAddress']['ShippingStreetName']);
    }

Better:

    foreach ($customers as $customer) {
      doThis($customer['shippingAddress']['streetName']);
    }

## No Cryptic Naming

    function fmt_CustDelAddr($c) {
      $result = fmthlp::fmtAddr($c->delAddr->identity['custFName'], $c->delAddr->identity['custLName'], $c->delAddr->identity['custAddr1'], $c->delAddr->identity['custAddr2'], $c->delAddr->identity['zip'], $c->delAddr->identity['country']);
      return $result;
    }

    fmt_CustDelAddr($custObj);

Better:

    function formatAddress(object $address) :string {
      return identifyableObject::ohThisMethod($address);
    }

    formatAddress($customer->deliveryAddress);

## No Overcomplicated Naming

    function anOverComplicatedFrameworkFunctionName() {

      $anOverComplicatedFrameworkFunctionNameResult = [];

      $anOverComplicatedFrameworkFunctionNameResult['anExtremelyLongDescriptiveNameForAnArrayNode'] = '...';
      $anOverComplicatedFrameworkFunctionNameResult['anotherExtremelyLongDescriptiveNameForAnArrayNode'] = '...';

      return $anOverComplicatedFrameworkFunctionNameResult;
    }

Better:

    function simpleFunctionName() {
      return [
        'nodeName' => '...',
        'anotherNode' => '...',
      ];
    }

## No Unnecessary Variable Duplication

Variable duplication is a challenge to backtrace. If we have no use of original values, we can just overwrite them with the sanitized ones.

    $userInput = $_POST['userInput'];
    $sanitizeduserInput = sanitize($userInput);
    $trimmedSanitizeduserInput = trim($sanitizeduserInput);

    passToFunction($trimmedSanitizeduserInput); // Wait, what is the origin of the data again?

Better:

    $_POST['userInput'] = trim(sanitize($_POST['userInput'])); // Let's sanitize it so we don't accidentally use the raw input again

    passToFunction($_POST['userInput']); // Oh we are passing something that came from a user input


## ISO codes are more reliable

Not using ISO codes you might make it too hard on yourself.

    $country = strtolower($_POST['country']);

    if (in_array($country, ['united states of america', 'united states', 'usa', 'u.s.', 'us'])) {
      doSomethingWith($_POST['country']);
    }}

Better:

    $_POST['countryCode'] = strtoupper($_POST['countryCode']);

    if ($country == 'US') {
      doSomethingWith($_POST['countryCode']);
    }

## Use codes others recognize

    $country = 'us';     // Wrong. Country codes should be uppercase
    $lang = 'EN';        // Wrong. Language codes should be lowercase
    $currencyId = 1234;  // Nonsense. No one but you recognize your internal IDs and they are hard to migrate

Better:

    $countryCode = 'US';   // ISO 3166-1 Alpha 2
    $languageCode = 'en';  // ISO 639-1
    $currencyCode = 'USD'  // ISO 4217
