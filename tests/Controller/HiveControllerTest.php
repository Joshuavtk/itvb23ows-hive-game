<?php

namespace Controller;

use App\Controller\HiveController;
use PHPUnit\Framework\TestCase;

class HiveControllerTest extends TestCase
{
    public HiveController $hiveController;

    public function __construct(string $name) {
        $this->hiveController = new HiveController();
        parent::__construct($name);
    }

    public function testIsNeighbour()
    {
        // Horizontal
        $this->assertTrue($this->hiveController->isNeighbour('0,0', '1,0'));
        $this->assertTrue($this->hiveController->isNeighbour('0,0', '-1,0'));
        $this->assertFalse($this->hiveController->isNeighbour('0,0', '2,0'));
        $this->assertFalse($this->hiveController->isNeighbour('0,0', '-2,0'));

        // NW to SE
        $this->assertTrue($this->hiveController->isNeighbour('0,0', '0,-1'));
        $this->assertTrue($this->hiveController->isNeighbour('0,0', '0,1'));
        $this->assertFalse($this->hiveController->isNeighbour('0,0', '0,-2'));
        $this->assertFalse($this->hiveController->isNeighbour('0,0', '0,2'));

        // NE to SW
        $this->assertTrue($this->hiveController->isNeighbour('0,0', '1,-1'));
        $this->assertTrue($this->hiveController->isNeighbour('0,0', '-1,1'));
        $this->assertFalse($this->hiveController->isNeighbour('0,0', '2,-2'));
        $this->assertFalse($this->hiveController->isNeighbour('0,0', '-2,2'));
    }

    public function testNeighboursAreSameColor()
    {
    }

    public function testSlide()
    {
    }

    public function testHasNeighbour()
    {
    }

    public function testLen()
    {
    }
}
