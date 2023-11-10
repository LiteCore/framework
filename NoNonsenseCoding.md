
# No Nonsense Coding

No Nonsense Coding is a provocative coding concept that will probably upset many. Used and promoted by T. Almroth - author of the e-commerce platform LiteCart.
The purpose is to make as much sense as possible with as little effort as possible when writing program code.


## No Duplicate Naming

This will just take longer to type, longer to read, longer to analyze, and leave a bigger footprint:

    foreach ($webshopCustomers as $webshopCustomer) {
      echo $webshopCustomer['webshopCustomerShippingAddress']['webshopCustomerShippingAddressStreetName'];
    }

Better:

    foreach ($customers as $customer) {
      ech $customer['shippingAddress']['street'];
    }


## No Cryptic Naming

This will just have anyone looking back at code confused and frustrated:

    function fmt_CustBillAddr(custObj $c) {
      $result = fmthlp::fmtAddr($c->billAddr->identity['custFName'], $c->billAddr->identity['custLName'], $c->billAddr->identity['custAddr1'], $c->delAddr->identity['custAddr2'], $c->billAddr->identity['zip'], $c->billAddr->identity['country']);
      return $result;
    }

    function fmt_CustDelAddr(custObj $c) {
      $result = fmthlp::fmtAddr($c->delAddr->identity['custFName'], $c->delAddr->identity['custLName'], $c->delAddr->identity['custAddr1'], $c->delAddr->identity['custAddr2'], $c->delAddr->identity['zip'], $c->delAddr->identity['country']);
      return $result;
    }

    fmt_CustBillAddr($custObj);
    fmt_CustDelAddr($custObj);

Better:

    function formatAddress(array $address) :string {
      $result = '...';
      return $result;
    }

    formatAddress($customer->billingAddress);
    formatAddress($customer->deliveryAddress);


## No Overcomplications

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


## No Unnecessary Variable Duplication

Variable duplication is a challenge to backtrace. If we have no use of the original user input, we can just overwrite it with the safer polished and sanitized data.

    $userInput = $_POST['userInput'];
    $sanitizeduserInput = sanitize($userInput);
    $trimmedSanitizeduserInput = trim($sanitizeduserInput);

    passToFunction($trimmedSanitizeduserInput); // Wait, what is the origin of the data again?

Better:

    $_POST['userInput'] = trim(sanitize($_POST['userInput'])); // Let's sanitize it so we don't accidentally use the raw input again

    passToFunction($_POST['userInput']); // Oh we are passing something that came from a user input


## Use codes others recognize

    $country = 'us';     // Wrong. Country codes should be uppercase
    $lang = 'EN';        // Wrong. Language codes should be lowercase
    $currencyId = 1234;  // Nonsense. No one but you recognize your internal IDs and they are hard to migrate

Better:

    $countryCode = 'US';   // ISO 3166-1 Alpha 2
    $languageCode = 'en';  // ISO 639-1
    $currencyCode = 'USD'  // ISO 4217


Not using ISO codes you might make it too hard on yourself.

    $country = $_POST['country'];

    if (in_array(strtolower($country), ['united states', 'united states of america', 'usa', 'u.s.a.', 'u.s.', 'us'])) {
      doSomethingWith($_POST['country']);
    }

Better:

    $_POST['countryCode'] = strtoupper($_POST['countryCode']);

    if ($_POST['countryCode'] == 'US') {
      doSomethingWith($_POST['countryCode']);
    }


## No fat third party libraries for small features

Looking to cut corners with third party libraries will backfire eventually. Libraries can be performance draining. They have dependencies and can unknowingly become outdated or discontinued. They can persist of poor management, contain flaws or security problems. Or they can be a complete pain when you want to step up PHP versions. One way or the other, they need to be maintained. Maintenance will take time and focus.

There is no good reason to embed a third party library if you will just utilize a small portion of it. If it's reasonable to code this part yourself it's likely a good idea to do it.

Try to stay away from third party libraries.
