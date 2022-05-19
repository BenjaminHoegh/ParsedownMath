<?php

namespace BenjaminHoegh\ParsedownMath;

use Erusev\Parsedown\State;
use Erusev\Parsedown\StateBearer;

use BenjaminHoegh\ParsedownMath\Features\Maths;

final class ParsedownMath implements StateBearer
{
    /** @var State */
    private $State;

    public function __construct(StateBearer $StateBearer = null)
    {
        $StateBearer = Maths::from($StateBearer ?? new State());

        $this->State = $StateBearer->state();
    }

    public function state(): State
    {
        return $this->State;
    }

    /** @return self */
    public static function from(StateBearer $StateBearer)
    {
        return new self($StateBearer);
    }
}
