<?php
require_once "../../vendor/autoload.php";

use Src\Util\Loop;

$util = new Loop(10);
$util->execute(function($i) use($util) {
    if ($i == 10) {
        $util->loop = false;
    }
});