# dice-roller

- [Dice](#dice)
- [DiceShaker](#diceshaker)
- [Examples](#examples)
- [Contribution](#contribution)

## Dice

A single dice can be created and rolled multiple times.

### Creating a Dice

Create a standard numeric `Dice` by passing a single number into the constructor:
```php
<?php

use daverichards00\DiceRoller\Dice;

// Create a d6 (Number range 1 - 6)
$d6 = new Dice(6);

// Create a d100 (Number range 1 - 100)
$d100 = new Dice(100);

// Or use a predefined constant
$d10 = new Dice(Dice::D10);
```

Create a `Dice` with custom sides by passing an array of numbers or strings: 
```php
<?php

use daverichards00\DiceRoller\Dice;

// Create a Dice with custom selection of numbers:
$evenDice = new Dice([2, 2, 4, 4, 6, 6]);

// Create a Dice of strings, such as colours:
$colourDice = new Dice(['Red', 'Blue', 'Green']);

// ...or shapes:
$shapeDice = new Dice(['Triangle', 'Square', 'Circle', 'Cross']);
```

### Rolling

You can roll a `Dice` and get the value that was rolled:
```php
<?php

use daverichards00\DiceRoller\Dice;

$dice = new Dice(6);

// Separate calls:
$dice->roll();
$firstValue = $dice->getValue();

// ...or chained calls:
$secondValue = $dice->roll()->getValue();
```

### Custom Rollers

You can customise the random number generation by injecting an implementation of 
`daverichards00\DiceRoller\Roller\RollerInterface`.

There are two preset rollers:
```php
<?php

use daverichards00\DiceRoller\Dice;
use daverichards00\DiceRoller\Roller;

// Create a d6 with the QuickRoller
// NOTE: QuickRoller is the default roller when one isn't specified
$dice = new Dice(6, new Roller\QuickRoller);

// ...is the same as:
$dice = new Dice(6);

// Create a d20 with the StrongRoller
// The StrongRoller could be used for cryptographic purposes
$dice = new Dice(20, new Roller\StrongRoller);
```

#### Creating your own Roller

You can create your own roller by implementing the RollerInterface. This requires you to implement the method `public 
function roll(int $min, int $max): int`, which takes two integers, a min and a max, and returns a random int within the 
range (inclusive). Below is an example:
```php
<?php

use daverichards00\DiceRoller\Roller\RollerInterface;

class MyCustomRoller implements RollerInterface
{
    /**
     * @param int $min
     * @param int $max
     * @return int
     */
    public function roll(int $min, int $max): int
    {
        // NOTE: This is how the QuickRoller is implemented.
        return mt_rand($min, $max);
    }
}

// ...

// You can then inject this roller into the Dice object when instantiating:

use daverichards00\DiceRoller\Dice;

$dice = new Dice(6, new MyCustomRoller);

$dice->roll(); // This will now be using MyCustomRoller->roll() to select a random value.
```

### History

You can see the history of rolls:
```php
<?php

use daverichards00\DiceRoller\Dice;

$dice = new Dice(6);

// Enable the history (History is disabled by default)
$dice->enableHistory();

$dice->roll(5); // Roll the Dice 5 times

$values = $dice->getHistory(); // e.g. [3, 2, 5, 6, 2]

// History can be disabled
$dice->disableHistory();

// History can be cleared
$dice->clearHistory();
```

### Exceptions

The most common exceptions that are thrown are `\InvalidArgumentException` and 
`daverichards00\DiceRoller\Exception\DiceException`. `DiceException` extends `\RuntimeException`, and will be thrown 
when you would expect a runtime exception to be thrown.

## DiceShaker

A collection of `Dice` can be created by using a `DiceShaker`.

### Creating a DiceShaker

When creating a `DiceShaker` you need to specify the `Dice` that will be used. This can be done by passing an array of 
`Dice` objects:
```php
<?php

use daverichards00\DiceRoller\Dice;
use daverichards00\DiceRoller\DiceShaker;

$diceShaker = new DiceShaker([
    new Dice(6),
    new Dice(20),
    new Dice('red'),
]);
```

It can also be done by passing an array of scalar values, that will be used to create the instances of `Dice`:
```php
<?php

use daverichards00\DiceRoller\DiceShaker;

$diceShaker = new DiceShaker([6, 20, 'red']);
```

Or, you can pass a single `Dice`/scalar and a quantity:
```php
<?php

use daverichards00\DiceRoller\Dice;
use daverichards00\DiceRoller\DiceShaker;

$diceShaker = new DiceShaker(6, 5); // Creates 5 instances of 6-sided Dice.

$diceShaker = new DiceShaker(new Dice(6), 5); // Creates 5 instances of 6-sided Dice.
```

**Note:** When passing an instance of `Dice` and a quantity, the passed instance will be added as the first dice, then 
subsequent dice will be clones of the passed instance.

### Rolling

All the `Dice` in the `DiceShaker` can be rolled:
```php
<?php

use daverichards00\DiceRoller\DiceShaker;

$diceShaker = new DiceShaker(6, 5);

$diceShaker->roll(); // All 5 Dice will be rolled.
```

### Getters

There are numerous ways to get and interact with the values of the rolled Dice:
```php
<?php

use daverichards00\DiceRoller\DiceShaker;

$diceShaker = new DiceShaker(6, 5);
$diceShaker->roll();

// Get the number of Dice in the DiceShaker
echo $diceShaker->getDiceQuantity() . PHP_EOL;

// An alias of getDiceQuantity()
echo $diceShaker->getNumberOfDice() . PHP_EOL;

// Get an array of all the rolled Dice values.
var_dump($diceShaker->getValues());

// Get the value of a single rolled Dice. Will throw an exception if more than 1 Dice is selected.
echo $diceShaker->getValue() . PHP_EOL;

// Get the highest rolled value. Uses the max() function to select this.
echo $diceShaker->getHighestValue() . PHP_EOL;

// Get the lowest rolled value. Uses the min() function to select this.
echo $diceShaker->getLowestValue() . PHP_EOL;

//
// The below can only used when ALL the Dice in the DiceShaker are numeric, an exception would be thrown otherwise.
//

// Get the sum of all the Dice.
echo $diceShaker->getSumValue() . PHP_EOL;

// An alias of getSumValue()
echo $diceShaker->getTotalValue() . PHP_EOL;

// Get the mean of all the Dice.
echo $diceShaker->getMeanValue() . PHP_EOL;

// An alias of getMeanValue()
echo $diceShaker->getAverageValue() . PHP_EOL;

// Get the median of all the Dice.
echo $diceShaker->getMedianValue() . PHP_EOL;
```

### Selectors

Selectors can be used to perform actions on, or get values from, a subset of the `Dice` in the `DiceShaker`. A 
selector can be created by the factory and passed to the action/getter:
```php
<?php

use daverichards00\DiceRoller\DiceShaker;
use daverichards00\DiceRoller\Selector\DiceSelectorFactory as Select;

$diceShaker = new DiceShaker(6, 5);
$diceShaker->roll();

// Re-roll the highest 3 Dice:
$diceShaker->roll(
    Select::highest(3)
);

// Get the sum of the Dice with values less than 4:
echo $diceShaker->getSumValue(
    Select::lessThan(4)
);

// Get the quantity of 6s rolled:
echo $diceShaker->getNumberOfDice(
    Select::equalTo(6)
);
```

There are various selectors available:
```php
<?php

use daverichards00\DiceRoller\Selector\DiceSelectorFactory as Select;

// Highest:
Select::highest(); // Select the Dice with the highest value.
Select::highest(3); // Select the 3 Dice with the highest values.

// Lowest:
Select::lowest(); // Select the Dice with the lowest value.
Select::lowest(3); // Select the 3 Dice with the lowest values.

// EqualTo:
Select::equalTo(6); // Select all Dice equal to 6.
Select::equalTo(6, true); // Select all dice strictly equal to 6; i.e. return values equal to int(6) but not string(6).

// greaterThan / greaterThanOrEqualTo:
Select::greaterThan(2); // Select all Dice with values > 2.
Select::greaterThanOrEqualTo(3); // Select all Dice with values >= 3.

// LessThan / lessThanOrEqualTo:
Select::lessThan(4); // Select all Dice with values < 4.
Select::lessThanOrEqualTo(3); // Select all Dice with values <= 3.

// In
Select::in([1, 3, 5]); // Select all Dice with values equal to 1, 3 or 5.
Select::in([1, 3, 5], true); // Select all Dice with values strictly equal to 1, 3 or 5.

// These
Select::these([$diceA, $diceB]); // Array of specific Dice instances to select.

// Random
Select::random(); // Select a Dice at random.
Select::random(3); // Select 3 Dice at random.

// All
Select::all(); // Select all Dice.
```

Custom selectors can also be used, as long as they implement the interface: 
`daverichards00\DiceRoller\Selector\DiceSelectorInterface`:
```php
<?php

use daverichards00\DiceRoller\Selector\DiceSelectorInterface;

class MyCustomSelector implements DiceSelectorInterface
{
    // ...
}

use daverichards00\DiceRoller\DiceShaker;

$diceShaker = new DiceShaker(6, 5);
$diceShaker
    ->roll()
    ->getAverageValue(
        new MyCustomSelector
    );
```

### Actions

There are some actions that can be performed on the `Dice` in the `DiceShaker`:
```php
<?php

use daverichards00\DiceRoller\DiceShaker;
use daverichards00\DiceRoller\Selector\DiceSelectorFactory as Select;

$diceShaker = new DiceShaker(6, 5);

// Dice can be rolled.
$diceShaker->roll(); // Roll all Dice by default.
$diceShaker->roll(Select::random()); // Roll Dice selected by a selector (In this case, a Dice chosen at random).
$diceShaker->roll(Select::all(), 2); // Roll all the Dice twice.

// Dice can be kept.
$diceShaker->keep(Select::greaterThan(3)); // Only Dice with a value greater than 3 will be left in the DiceShaker after this.

// Dice can be discarded.
$diceShaker->discard(Select::Lowest()); // The lowest Dice will be removed from the DiceShaker after this.
```

### Exceptions

`\daverichards00\DiceRoller\Exception\DiceShakerException` extends `\RuntimeException` and will be thrown if trying to 
perform an operation when there are no Dice in the DiceShaker, or trying to use a numeric-only getter when non-numeric 
Dice are present.

## Examples

### 2d6
```php
<?php

use daverichards00\DiceRoller\Dice;
use daverichards00\DiceRoller\DiceShaker;

// In a single line:
$value = (new DiceShaker(Dice::D6, 2))->roll()->getTotalValue();

// Or, reuse the shaker multiple times:
$shaker = new DiceShaker(Dice::D6, 2);

$value1 = $shaker->roll()->getTotalValue();
$value2 = $shaker->roll()->getTotalValue();
$value3 = $shaker->roll()->getTotalValue();
```

### 2d6 + 1d8
```php
<?php

use daverichards00\DiceRoller\DiceShaker;

$shaker = new DiceShaker([6, 6, 8]);

$value = $shaker->roll()->getTotalValue();
```

### 4d6-L
```php
<?php

use daverichards00\DiceRoller\Dice;
use daverichards00\DiceRoller\DiceShaker;
use daverichards00\DiceRoller\Selector\DiceSelectorFactory as Select;

$shaker = new DiceShaker(Dice::D6, 4);

// Calculating the total of the highest 3 Dice
$value = $shaker
    ->roll()
    ->getTotalValue(Select::highest(3));

// Or, Remove the lowest Dice from the Shaker before calculating the total.
// NB: This will remove the lowest Dice from this DiceShaker instance permanently.
$value = $shaker
    ->roll()
    ->discard(Select::lowest())
    ->getTotalValue();
```

### d% (Percentile)
```php
<?php

use daverichards00\DiceRoller\Dice;
use daverichards00\DiceRoller\DiceShaker;

// Use a d100
$dice = new Dice(100);
$value = $dice->roll()->getValue();

// Or, simulate using a "tens" d10 and a standard d10 dice.
$shaker = new DiceShaker([Dice::TENS_D10, Dice::D10]);
$value = $shaker->roll()->getTotalValue() + 1;

// Or, simulate using 2d10
$shaker = new DiceShaker(Dice::D10, 2);
$shaker->roll();
$value = ($shaker->getValues()[0] * 10) + $shaker->getValues()[1] + 1;
```
**Note:** In the last 2 examples we're adding 1 to the result to alter the range from 0-99 to 1-100. Programmatically, 
this is easier than treating 0 as 100 and still results in an evenly balanced random number between 1 and 100.

### 3 x (2d6+4)
```php
<?php

use daverichards00\DiceRoller\Dice;
use daverichards00\DiceRoller\DiceShaker;

$shaker = new DiceShaker(Dice::D6, 2);

$value  = $shaker->roll()->getTotalValue() + 4;
$value += $shaker->roll()->getTotalValue() + 4;
$value += $shaker->roll()->getTotalValue() + 4;
```

### 8d10k6 or 8d10+H6 or 8d10-L2
```php
<?php

use daverichards00\DiceRoller\Dice;
use daverichards00\DiceRoller\DiceShaker;
use daverichards00\DiceRoller\Selector\DiceSelectorFactory as Select;

$shaker = new DiceShaker(Dice::D10, 8);

$value = $shaker->roll()->getTotalValue(Select::highest(6));
```

### Number of results >= 8 from 6d10
```php
<?php

use daverichards00\DiceRoller\Dice;
use daverichards00\DiceRoller\DiceShaker;
use daverichards00\DiceRoller\Selector\DiceSelectorFactory as Select;

$shaker = new DiceShaker(Dice::D10, 6);

$value = $shaker->roll()->getNumberOfDice(Select::greaterThanOrEqualTo(8));
```

### 4dF (Fudge)
```php
<?php

use daverichards00\DiceRoller\Dice;
use daverichards00\DiceRoller\DiceShaker;

// Numerical:
$shaker = new DiceShaker(Dice::DF, 4);
$value = $shaker->roll()->getTotalValue();

// Or, for visual representation:
$shaker = new DiceShaker(["-", "", "+"], 4);
$values = $shaker->roll()->getValues();
```

### D66 or d6x10+d6
```php
<?php

use daverichards00\DiceRoller\Dice;
use daverichards00\DiceRoller\DiceShaker;

$shaker = new DiceShaker(Dice::D6, 2);
$shaker->roll();

$value = ($shaker->getValues()[0] * 10) + $shaker->getValues()[1];
```

## Contribution

Please feel free to let me know if you think there is anything that I have missed or any way to improve usability or 
functionality. It would be great to understand how people use, or would like to use, this package. I'm more than happy
to update and expand this project if I think it will be useful :) 