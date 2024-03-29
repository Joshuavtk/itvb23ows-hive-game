<?php

namespace Model;

use App\Model\Board;
use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{
    public function testGetPlaceablePieces()
    {
        $board = ['0,0' => [0,'Q']];
        $hand = [0 => ['Q' => 0, 'B' => 2]];
        $boardModel = new Board($board, $hand, 0);

        self::assertTrue(in_array('B', $boardModel->getPlaceablePieces()));
        self::assertFalse(in_array('Q', $boardModel->getPlaceablePieces()));
    }

    public function testGetValidPlacements()
    {
        $board = ['0,0' => [0,'Q']];
        $hand = [0 => ['Q' => 0, 'B' => 2], 1 => ['Q' => 1, 'B' => 10]];
        $boardModel = new Board($board, $hand, 1);

        $test = $boardModel->getValidPlacements();

        self::assertTrue(in_array('1,0', $boardModel->getValidPlacements()));
        self::assertTrue(in_array('-1,1', $boardModel->getValidPlacements()));
        self::assertFalse(in_array('0,0', $boardModel->getValidPlacements()));
        self::assertFalse(in_array('3,4', $boardModel->getValidPlacements()));
    }
}
