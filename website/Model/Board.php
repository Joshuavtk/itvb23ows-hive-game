<?php

namespace App\Model;

use App\Controller\HiveController;

class Board
{
    public array $hand;
    public int $player;
    public array $board;
    public array $to;

    public HiveController $hiveController;

    public function __construct($board, $hand, $player) {
        $this->hiveController = new HiveController();

        $this->hand = $hand;
        $this->player = $player;
        $this->board = $board;
        $this->to = $this->getTos();
    }

    public function getTos()
    {
        $to = [];
        foreach ($this->hiveController::OFFSETS as $pq) {
            foreach (array_keys($this->board) as $pos) {
                $pq2 = explode(',', $pos);
                $to[] = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
            }
        }
        $to = array_unique($to);
        if (!count($to)) {
            $to[] = '0,0';
        }
        return $to;
    }

    public function getPlaceablePieces(): array
    {
        $placeablePieces = [];

        foreach ($this->hand[$this->player] as $tile => $ct) {
            if ($ct > 0) {
                $placeablePieces[] = $tile;
            }
        }

        return $placeablePieces;
    }

    public function getValidPlacements(): array
    {
        $validPlacements = [];

        foreach ($this->to as $pos) {
            if (isset($this->board[$pos])) {continue;}
            if (array_sum($this->hand[$this->player]) < 11 &&
                !$this->hiveController->neighboursAreSameColor($this->player, $pos, $this->board)) {
                continue;
            }

            $validPlacements[] = $pos;
        }

        return $validPlacements;
    }
}
