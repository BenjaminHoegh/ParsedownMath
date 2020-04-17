<?php
if (class_exists('ParsedownExtra')) {
    class DynamicParent extends ParsedownExtra
    {
        public function __construct()
        {
            if (version_compare(parent::version, '0.8.1') < 0) {
                throw new Exception('ParsedownMath requires a later version of ParsedownExtra');
            }
            parent::__construct();
        }
    }
} else {
    class DynamicParent extends Parsedown
    {
        public function __construct()
        {
            if (version_compare(parent::version, '1.7.3') < 0) {
                throw new Exception('ParsedownMath requires a later version of Parsedown');
            }
        }
    }
}


class ParsedownMath extends DynamicParent
{
    const VERSION = '1.1.1';

    public function __construct()
    {
        parent::__construct();
        // // Blocks
        $this->BlockTypes['\\'][] = 'Math';
        $this->BlockTypes['$'][] = 'Math';

        // Inline
        $this->InlineTypes['\\'][] = 'Math';
        $this->inlineMarkerList .= '\\';

    }

    // Setters

    protected $mathMode = true;

    public function enableMath($input = true)
    {
        $this->mathMode = $input;

        if ($input == false) {
            return $this;
        }

        return $this;
    }



    protected function element(array $Element)
    {
        if ($this->safeMode)
        {
            $Element = $this->sanitiseElement($Element);
        }

        if(isset($Element['name'])) {
            $markup = '<'.$Element['name'];
        } else {
            $markup = '';
        }

        if (isset($Element['attributes']))
        {
            foreach ($Element['attributes'] as $name => $value)
            {
                if ($value === null)
                {
                    continue;
                }

                $markup .= ' '.$name.'="'.self::escape($value).'"';
            }
        }

        if (isset($Element['text']))
        {
            if(isset($Element['name'])) {
                $markup .= '>';
            }

            if (!isset($Element['nonNestables']))
            {
                $Element['nonNestables'] = array();
            }

            if (isset($Element['handler']))
            {
                $markup .= $this->{$Element['handler']}($Element['text'], $Element['nonNestables']);
            }
            else
            {
                $markup .= self::escape($Element['text'], true);
            }

            if(isset($Element['name'])) {
                $markup .= '</'.$Element['name'].'>';
            }
        }
        else
        {
            if(isset($Element['name'])) {
                $markup .= ' />';
            }
        }

        return $markup;
    }





    // -------------------------------------------------------------------------
    // -----------------------         Inline         --------------------------
    // -------------------------------------------------------------------------


    //
    // Inline Math
    // -------------------------------------------------------------------------

    protected function inlineMath($Excerpt)
    {
        if (!$this->mathMode) {
            return;
        }

        // if (preg_match('/^(?<!\\\\)((?<!\\\\\()\\\\\((?!\\\\\())(.*?)(?<!\\\\)(?<!\\\\\()((?<!\\\\\))\\\\\)(?!\\\\\)))(?!\\\\\()/s', $Excerpt['text'], $matches)) {
        if (preg_match('/^(?<!\\\\)(?<!\\\\\()\\\\\((.*?)(?<!\\\\\()\\\\\)(?!\\\\\))/s', $Excerpt['text'], $matches)) {
            return array(
                'extent' => strlen($matches[0]),
                'element' => array(
                    'text' =>  $matches[0],
                ),
            );
        }
    }

    protected $specialCharacters = array(
        '\\', '`', '*', '_', '{', '}', '[', ']', '(', ')', '<', '>', '#', '+', '-', '.', '!', '|', '~', '^', '='
    );


    //
    // Inline Escape
    // -------------------------------------------------------------------------

    protected function inlineEscapeSequence($Excerpt)
    {
        $Element = array(
            'element' => array(
                'rawHtml' => $Excerpt['text'][1],
            ),
            'extent' => 2,
        );

        if ($this->mathMode) {
            if (isset($Excerpt['text'][1]) && in_array($Excerpt['text'][1], $this->specialCharacters) && !preg_match('/(?<!\\\\)((?<!\\\\\()\\\\\((?!\\\\\())(.*?)(?<!\\\\)(?<!\\\\\()((?<!\\\\\))\\\\\)(?!\\\\\)))(?!\\\\\()/s', $Excerpt['text'])) {
                return $Element;
            }
        } else {
            if (isset($Excerpt['text'][1]) && in_array($Excerpt['text'][1], $this->specialCharacters)) {
                return $Element;
            }
        }
    }



    // -------------------------------------------------------------------------
    // -----------------------         Blocks         --------------------------
    // -------------------------------------------------------------------------


    //
    // Block Math
    // --------------------------------------------------------------------------

    protected function blockMath($Line)
    {
        $Block = array(
            'element' => array(
                'text' => '',
            ),
        );

        if (preg_match('/^(?<!\\\\)(\\\\\[)(?!.)$/', $Line['text'])) {
            $Block['end'] = '\]';
            return $Block;
        } elseif (preg_match('/^(?<!\\\\)(\$\$)(?!.)$/', $Line['text'])) {
            $Block['end'] = '$$';
            return $Block;
        }
    }

    // ~

    protected function blockMathContinue($Line, $Block)
    {
        if (isset($Block['complete'])) {
            return;
        }

        if (isset($Block['interrupted'])) {
            $Block['element']['text'] .= str_repeat("\n", $Block['interrupted']);

            unset($Block['interrupted']);
        }

        if (preg_match('/^(?<!\\\\)(\\\\\])$/', $Line['text']) && $Block['end'] === '\]') {
            $Block['complete'] = true;
            $Block['latex'] = true;
            $Block['element']['text'] = "\\[".$Block['element']['text']."\\]";
            return $Block;
        } elseif (preg_match('/^(?<!\\\\)(\$\$)$/', $Line['text']) && $Block['end'] === '$$') {
            $Block['complete'] = true;
            $Block['latex'] = true;
            $Block['element']['text'] = "$$".$Block['element']['text']."$$";
            return $Block;
        }


        $Block['element']['text'] .= "\n" . $Line['body'];

        // ~

        return $Block;
    }

    // ~

    protected function blockMathComplete($Block)
    {
        return $Block;
    }
}
