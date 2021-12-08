<?php

require_once 'vendor/autoload.php';
require_once '../src/parsedownMath.php';

use BenjaminHoegh\ParsedownMath;
use BenjaminHoegh\ParsedownMath\Features\Math;

$State = Math::from(new State);

$Parsedown = new Parsedown(ParsedownMath::from(new State));

$actualMarkup = $Parsedown->toHtml($markdown);

echo $actualMarkup;