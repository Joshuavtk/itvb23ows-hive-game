<?php

namespace App\Controller;

use App\Model\Database;

class WebController
{
    public Database $database;
    public HiveController $hiveController;

    public function __construct()
    {
        session_start();

        $this->database = new Database();

        if (!isset($_SESSION['board'])) {
            $this->resetBoardState();
        }
        $this->hiveController = new HiveController($this->database);
    }

    public function resetBoardState(): void
    {
        $_SESSION['board'] = [];
        $_SESSION['hand'] = [
            0 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]
        ];
        $_SESSION['player'] = 0;

        $stmt = $this->database->prepare('INSERT INTO games VALUES ()');
        $stmt->execute();
        $_SESSION['game_id'] = $this->database->lastInsertId();
    }

    public function handleUndo(): void
    {
        $stmt = $this->database->prepare('SELECT * FROM moves WHERE id = ' . $_SESSION['last_move']);

        $stmt->execute();
        $result = $stmt->fetchAll();

        $_SESSION['last_move'] = $result[5];
        $this->database->setState($result[6]);

    }

    public function handleIndex(): string|false
    {
        // TODO: Make index.php a loaded in file...
        // example 1
//        ob_start();
//        require_once __DIR__ . '/../View/board.php';
//        return ob_get_clean();
        // example 2
//        return file_get_contents(__DIR__ . '/../View/board.php');
    }

    public function handleRestart(): void
    {
        $this->resetBoardState();
    }

    public function handlePass(): void
    {
        $_SESSION['last_move'] = $this->database->saveMove(
            $_SESSION['game_id'],
            'pass', null, null,
            key_exists('last_move', $_SESSION) ? $_SESSION['last_move'] : null,
            $this->database->getState()
        );

        $_SESSION['player'] = 1 - $_SESSION['player'];
    }

    public function handlePlay(): void
    {
        $piece = $_POST['piece'];
        $to = $_POST['to'];

        $player = $_SESSION['player'];
        $board = $_SESSION['board'];
        $hand = $_SESSION['hand'][$player];

        if (!$hand[$piece]) {
            $_SESSION['error'] = 'Player does not have tile';
        } elseif (isset($board[$to])) {
            $_SESSION['error'] = 'Board position is not empty';
        } elseif (count($board) && !$this->hiveController->hasNeighbour($to, $board)) {
            $_SESSION['error'] = 'board position has no neighbour';
        } elseif (array_sum($hand) < 11 && !$this->hiveController->neighboursAreSameColor($player, $to, $board)) {
            $_SESSION['error'] = 'Board position has opposing neighbour';
        } elseif (array_sum($hand) <= 8 && $hand['Q']) {
            $_SESSION['error'] = 'Must play queen bee';
        } else {
            $_SESSION['board'][$to] = [[$_SESSION['player'], $piece]];
            $_SESSION['hand'][$player][$piece]--;
            $_SESSION['player'] = 1 - $_SESSION['player'];

            $_SESSION['last_move'] = $this->database->saveMove(
                $_SESSION['game_id'],
                'play', $piece, $to,
                key_exists('last_move', $_SESSION) ? $_SESSION['last_move'] : null,
                $this->database->getState()
            );
        }

    }

    public function getMoveValues()
    {
        $from = $_POST['from'];
        $to = $_POST['to'];

        $player = $_SESSION['player'];
        $board = $_SESSION['board'];
        $hand = $_SESSION['hand'][$player];

        return [$from, $to, $player, $board, $hand];
    }

    public function handleMove(): void
    {
        list($from, $to, $player, $board, $hand) = $this->getMoveValues();
        unset($_SESSION['error']);

        if ($this->hiveController->checkIfIllegalMove($from, $board, $player, $hand)) {
            return;
        }

        $tile = array_pop($board[$from]);

        $this->hiveController->checkIfValidMove($board, $from, $to, $tile);

        $this->hiveController->processMoveOnBoard($board, $from, $to, $tile);
    }
}
