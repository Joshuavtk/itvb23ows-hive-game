<?php

namespace App\Model;

use PDO;

class Database extends PDO
{
    public function __construct()
    {
        $host = $_ENV['MYSQL_HOST'];
        $database = $_ENV['MYSQL_DB_NAME'];
        $user = $_ENV['MYSQL_USER'];
        $password = $_ENV['MYSQL_PASSWORD'];

        $dsn = "mysql:host=$host;dbname=$database;charset=UTF8";

        return parent::__construct($dsn, $user, $password);
    }


    public function getState(): string
    {
        return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
    }


    public function setState($state): void
    {
        list($a, $b, $c) = unserialize($state);
        $_SESSION['hand'] = $a;
        $_SESSION['board'] = $b;
        $_SESSION['player'] = $c;
    }

    public function saveMove($gameId, $type, $move_from, $move_to, $previous_id, $state): string|false
    {
        $stmt = $this->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state)
                values (:game_id, :type, :move_from, :move_to, :previous_id, :state)');

        $stmt->bindValue(':game_id', $gameId, self::PARAM_INT);
        $stmt->bindValue(':type', $type);
        $stmt->bindValue(':move_from', $move_from);
        $stmt->bindValue(':move_to', $move_to);
        $stmt->bindValue(':previous_id', $previous_id, self::PARAM_INT);
        $stmt->bindValue(':state', $state);

        $stmt->execute();

        return $this->lastInsertId();
    }

    public function getHistory(): false|array
    {
        $stmt = $this->prepare('SELECT * FROM moves WHERE game_id = ' . $_SESSION['game_id']);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
