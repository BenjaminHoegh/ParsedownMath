<?php

require_once 'vendor/autoload.php';

use Erusev\Parsedown\Parsedown;
use Erusev\ParsedownExtra\ParsedownExtra;
use BenjaminHoegh\ParsedownMath\ParsedownMath;


$Parsedown = new Parsedown(ParsedownExtra::from(new ParsedownMath()));

$markdown = file_get_contents('test.md');

$actualMarkup = $Parsedown->toHtml($markdown);

echo $actualMarkup;