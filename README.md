# Acme Widget Co

This is an implementation of a checkout Basket for the Acme Widget Co exercise, written in PHP.

---

## Setup
- Setup consists of cloning the repository and installing the necessary dependencies through composer
1. `git clone git@github.com:mdacme/acme-widget-co.git`
2. `cd acme-widget-co`
3. `composer install`

---

## Testing
- Testing is done via PHPUnit and behind the scenes, it runs the `./vendor/bin/phpunit tests/ --testdox`
- To run the tests, simply run the command `composer test`

---

## Analysis
- Code analysis is done via PHPStan, and is currently passing level 5 check
- To run the analysis, simply run the command `composer analysis`

---

## Usage
The `Basket` class in the `Mario\AcmeWidget` namespace provides a lightweight shopping cart system. It supports:

- Adding products by code
- Promotional pricing rules (e.g., Buy One Get One Half Off)
- Delivery fee calculation based on basket total

### Constructor

```php
public function __construct(
    array $productCatalogue,
    array $deliveryChargeRules,
    array $offers
)
```

#### Constructor Parameters

- `$productCatalogue`: Associative array of product codes and product details.
- The key must be the product code, the `price` should be a two-decimal-digits float, and the `product` should be a string with the name of the product.
    - Example:
      ```php
      [
      'R01' => ['price' => 32.95, 'product' => 'Red Widget'],
      'G01' => ['price' => 24.95, 'product' => 'Green Widget'],
      'B01' => ['price' => 7.95,  'product' => 'Blue Widget']
      ]
      ```
- `$deliveryChargeRules`: Rules defining delivery costs based on total basket value.
    - Example:
  ```php
      [
        50 => 4.95, 
        90 => 2.95
      ]
  ```
  
- `offers`: Promotional pricing rules (value) applied to specific products (key).
    - Example:
  ```php
    [
    'R01' => 'bogohalfoff'
    ]
  ```

### Example Usage
```php
use Mario\AcmeWidget\Basket;

// build productCatalogue, deliveryChargeRules, and offers arrays
$productCatalogue = [
    'R01' => ['price' => 32.95, 'product' => 'Red Widget'],
    'G01' => ['price' => 24.95, 'product' => 'Green Widget'],
    'B01' => ['price' => 7.95,  'product' => 'Blue Widget']
];

$deliveryChargeRules = [
    50 => 4.95,
    90 => 2.95
];

$offers = [
    'R01' => 'bogohalfoff'
];

// instantiate the Basket class initialized with the arrays above
$basket = new Basket($productCatalogue, $deliveryChargeRules, $offers);

// add items to our basket
$basket->addItem('R01');
$basket->addItem('R01');

// calculate and print the total
$total = $basket->calculateTotal();
echo "Total: $" . $total;

```

### Notes
#### Assumptions
- I assumed that we can cover a buy one get one half off as the first promo. The promos in the future will need a code update depending on what the parameters for promotions will be.
  - Adding promotions should be as easy as implementing a switch statement inside of the `getTotalProductPrice` method and writing a private method for each of the new promotions. Then use the switch to call different calculation methods.
- I assumed that no only the 2nd instance of the same product will be half off, but every even instance, since someone could buy 6 of the same item. I wanted to make sure that every other item was marked down 50%.
  - The reason behind this is that it would remove the incentive for the user to make 3 separate orders to purchase 6 of the same item.
- I assumed that PHPStan level 5 is sufficient for this case, considering trying to stay within the reason in relation to time constraints.
- I assumed "buy one red widget, get the second half price" meant literally the red widget when I wrote my tests
- I assumed that throwing exceptions is reasonable instead of returning false or something else when adding invalid items, since there wasn't a specified requirement regarding that

#### Possible Improvements
While trying to deliver the result for the project, I also tried to stay within the reason when it comes to time constraints.
With that being said, I skipped over building some things that I believe would be an important part of the system design.
The list is as follows, and only goes into what would still fall under the scope of the task (not adding any new features):
- Build a BasketInterface which would serve as a set of guidelines to expand on Baskets if we were to have to implement a different basket class in the future.
- Separate Item, Offer, DeliveryChargeRule, Product, and Catalogue as their own classes
  - This would allow us to:
    - have a clean set of validation rules for each of the classes
    - call methods to get things like product names, prices, and so on, instead of referring to indexes in the arrays
    - have an actual object oriented approach to the basket implementation
- Review through PHPStan results for possibly the next couple of levels of checks, and make the code less error-prone
