<?php

namespace App\Controller;

use App\Model\Database;

class HiveController
{
    public const OFFSETS = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];
    public Database $database;

    public function __construct($database = null)
    {
        if ($database === null) {
            $database = new Database();
        }
        $this->database = $database;
    }

    public function neighboursAreSameColor($player, $a, $board): bool
    {
        foreach ($board as $b => $st) {
            if (!$st) {
                continue;
            }
            $c = $st[count($st) - 1][0];
            if ($c != $player && $this->isNeighbour($a, $b)) {
                return false;
            }
        }
        return true;
    }

    public function isNeighbour($a, $b): bool
    {
        $a = explode(',', $a);
        $b = explode(',', $b);

        /** @var int[] $a */
        /** @var int[] $b */
        return ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) ||
            ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1) ||
            (abs($a[1] - $b[1]) == 1 && $a[0] + $a[1] == $b[0] + $b[1]);
    }

    public function slide($board, $from, $to): bool
    {
        if (!$this->hasNeighbour($to, $board) || !$this->isNeighbour($from, $to)) {
            return false;
        }

        /** @var int[] $b */
        $b = explode(',', $to);
        $common = [];
        foreach (self::OFFSETS as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($this->isNeighbour($from, $p . ',' . $q)) {
                $common[] = $p . ',' . $q;
            }
        }

        if (!$board[$common[0]] && !$board[$common[1]] && !$board[$from] && !$board[$to]) {
            return false;
        }

        return min(
                self::len($board[$common[0]]),
                self::len($board[$common[1]])
            ) <= max(
                self::len($board[$from]),
                self::len($board[$to])
            );
    }

    public function hasNeighbour($a, $board): bool
    {
        foreach (array_keys($board) as $b) {
            if ($this->isNeighbour($a, $b)) {
                return true;
            }
        }
        return false;
    }

    public function len($tile)
    {
        return $tile ? count($tile) : 0;
    }

    public function checkIfIllegalMove($from, $board, $player, $hand)
    {
        if (!isset($board[$from])) {
            $_SESSION['error'] = 'Board position is empty';
        } elseif ($board[$from][count($board[$from]) - 1][0] != $player) {
            $_SESSION['error'] = 'Tile is not owned by player';
        } elseif ($hand['Q']) {
            $_SESSION['error'] = 'Queen bee is not played';
        } else {
            return false;
        }
        return true;
    }

    public function checkIfValidMove($board, $from, $to, $tile)
    {
        if (!$this->hasNeighbour($to, $board)) {
            $_SESSION['error'] = 'Move would split hive';
            return;
        }

        $all = array_keys($board);
        $queue = [array_shift($all)];
        while ($queue) {
            $next = explode(',', array_shift($queue));
            foreach (self::OFFSETS as $pq) {
                list($p, $q) = $pq;
                $p += $next[0];
                $q += $next[1];
                if (in_array("$p,$q", $all)) {
                    $queue[] = "$p,$q";
                    $all = array_diff($all, ["$p,$q"]);
                }
            }
        }

        if ($all) {
            $_SESSION['error'] = 'Move would split hive';
            return;
        }

        if ($from == $to) {
            $_SESSION['error'] = 'Tile must move';
        } elseif (isset($board[$to]) && $tile[1] != 'B') {
            $_SESSION['error'] = 'Tile not empty';
        } elseif ($tile[1] == 'Q' || $tile[1] == 'B') {
            if (!$this->slide($board, $from, $to)) {
                $_SESSION['error'] = 'Tile must slide';
            }
        }
    }

    public function processMoveOnBoard($board, $from, $to, $tile): void
    {
        if (isset($_SESSION['error'])) {
            if (isset($board[$from])) {
                array_push($board[$from], $tile);
            } else {
                $board[$from] = [$tile];
            }
        } else {
            if (isset($board[$to])) {
                array_push($board[$to], $tile);
            } else {
                $board[$to] = [$tile];
            }
            $_SESSION['player'] = 1 - $_SESSION['player'];

            $_SESSION['last_move'] = $this->database->saveMove(
                $_SESSION['game_id'],
                'move', $from, $to,
                key_exists('last_move', $_SESSION) ? $_SESSION['last_move'] : null,
                $this->database->getState()
            );
        }
        $_SESSION['board'] = $board;
    }
}
