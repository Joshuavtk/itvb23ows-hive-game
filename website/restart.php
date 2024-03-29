<?php

use App\Controller\WebController;

require_once '../vendor/autoload.php';

(new WebController())->handleRestart();

header('Location: index.php');
