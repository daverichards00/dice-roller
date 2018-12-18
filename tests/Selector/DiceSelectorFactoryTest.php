<?php

namespace daverichards00\DiceRollerTest\Selector;

use daverichards00\DiceRoller\Selector\DiceSelectorFactory;
use daverichards00\DiceRoller\Selector\LowestSelector;
use PHPUnit\Framework\TestCase;

class DiceSelectorFactoryTest extends TestCase
{
    public function testLowestReturnsCorrectInstance()
    {
        $sut = DiceSelectorFactory::lowest();
        $this->assertInstanceOf(LowestSelector::class, $sut);
    }
}