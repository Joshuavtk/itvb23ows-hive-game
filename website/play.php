<?php

use App\Controller\WebController;

require_once '../vendor/autoload.php';

(new WebController())->handlePlay();

header('Location: index.php');
