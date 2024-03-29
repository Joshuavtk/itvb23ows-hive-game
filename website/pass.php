<?php

use App\Controller\WebController;

require_once '../vendor/autoload.php';

(new WebController())->handlePass();

header('Location: index.php');
