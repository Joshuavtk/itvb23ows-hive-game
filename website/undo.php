<?php

use App\Controller\WebController;

require_once '../vendor/autoload.php';

(new WebController())->handleUndo();

header('Location: index.php');
