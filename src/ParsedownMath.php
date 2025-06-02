<?php

if (class_exists('ParsedownExtra')) {
    class_alias('ParsedownExtra', 'ParsedownMathParentAlias');
} else {
    class_alias('Parsedown', 'ParsedownMathParentAlias');
}

class ParsedownMath extends ParsedownMathParentAlias
{
    public const VERSION = '1.2.4';
    public const MIN_PHP_VERSION = '7.4';
    public const VERSION_PARSEDOWN_REQUIRED = '1.7.4';
    public const VERSION_PARSEDOWN_EXTRA_REQUIRED = '0.8.1';

    private bool $legacyMode = false;

    protected $options = [];

    public function __construct($options = '')
    {
        // Check if the current PHP version meets the minimum requirement
        $this->checkVersion('PHP', PHP_VERSION, self::MIN_PHP_VERSION);

        // Check if the installed Parsedown version meets the minimum requirement
        $this->checkVersion('Parsedown', \Parsedown::version, self::VERSION_PARSEDOWN_REQUIRED);

        if (class_exists('ParsedownExtra')) {
            // Ensure ParsedownExtra meets the version requirement
            $this->checkVersion('ParsedownExtra', \ParsedownExtra::version, self::VERSION_PARSEDOWN_EXTRA_REQUIRED);
            parent::__construct();
        }

        $this->setLegacyMode();

        // Blocks
        $this->BlockTypes['\\'][] = 'Math';
        $this->BlockTypes['$'][] = 'Math';

        // Inline
        $this->InlineTypes['\\'][] = 'Math';
        $this->inlineMarkerList .= '\\';

        $this->InlineTypes['$'][] = 'Math';
        $this->inlineMarkerList .= '$';

        $this->options['math']['enabled'] = ($options['math']['enabled'] ?? false);
        $this->options['math']['inline']['enabled'] = ($options['math']['inline']['enabled'] ?? true);
        $this->options['math']['block']['enabled'] = ($options['math']['block']['enabled'] ?? true);
        $this->options['math']['matchSingleDollar'] = ($options['math']['matchSingleDollar'] ?? false);
    }

    private function checkVersion(string $component, string $currentVersion, string $requiredVersion): void
    {
        // Compare the current version with the required version
        if (version_compare($currentVersion, $requiredVersion) < 0) {
            // Prepare an error message indicating version incompatibility
            $msg_error  = 'Version Error.' . PHP_EOL;
            $msg_error .= "  ParsedownMath requires a later version of $component." . PHP_EOL;
            $msg_error .= "  - Current version : $currentVersion" . PHP_EOL;
            $msg_error .= "  - Required version: $requiredVersion and later" . PHP_EOL;

            // Throw an exception with the version error message
            throw new \Exception($msg_error);
        }
    }

    private function setLegacyMode(): void
    {
        $parsedownVersion = preg_replace('/-.*$/', '', \Parsedown::version);

        // Enable legacy mode if Parsedown version is between 1.7.4 and below 1.8.0
        if (version_compare($parsedownVersion, '1.8.0') < 0 && version_compare($parsedownVersion, '1.7.4') >= 0) {
            $this->legacyMode = true;
        }
    }

    // #[Override]
    protected function element(array $Element)
    {
        if ($this->legacyMode) {
            // If the element's name is empty, return the text attribute
            if (empty($Element['name'])) {
                return $Element['text'] ?? '';
            }
        }

        // Use the original element method from the parent
        return parent::element($Element);
    }

    // -------------------------------------------------------------------------
    // -----------------------         Inline         --------------------------
    // -------------------------------------------------------------------------

    //
    // Inline Math
    // -------------------------------------------------------------------------

    protected function inlineMath($Excerpt)
    {
        if (!$this->options['math']['enabled'] && !$this->options['math']['inline']['enabled']) {
            return null;
        }

        $matchSignleDollar = $this->options['math']['matchSingleDollar'] ?? false;
        $mathMatch = '';

        // Using inline detection to detect Block single-line math.
        if (preg_match('/^(?<!\\\\)(?<!\$)\${2}(?!\$)[^$]*?(?<!\$)\${2}(?!\$)$/', $Excerpt['text'], $matches)) {
            $Block = [
                'element' => [
                    'text' => '',
                ],
            ];

            $Block['end'] = '$$';
            $Block['complete'] = true;
            $Block['latex'] = true;
            $Block['element']['text'] = $matches[0];
            $Block['extent'] = strlen($Block['element']['text']);
            return $Block;
        }

        // Inline Matches
        if ($matchSignleDollar === true) {
            // Experimental
            if (preg_match('/^(?<!\\\\)((?<!\$)\$(?!\$)(.*?)(?<!\$)\$(?!\$)|(?<!\\\\\()\\\\\((.*?)(?<!\\\\\()\\\\\)(?!\\\\\)))/s', $Excerpt['text'], $matches)) {
                $mathMatch = $matches[0];
            }
        } elseif (preg_match('/^(?<!\\\\)(?<!\\\\\()\\\\\((.*?)(?<!\\\\\()\\\\\)(?!\\\\\))/s', $Excerpt['text'], $matches)) {
            $mathMatch = $matches[0];
        }

        if ($mathMatch !== '') {
            return [
                'extent' => strlen($mathMatch),
                'element' => [
                    'text' => $mathMatch,
                ],
            ];
        }
        
        return null;
    }

    protected $specialCharacters = [
        '\\', '`', '*', '_', '{', '}', '[', ']', '(', ')', '<', '>', '#', '+', '-', '.', '!', '|', '~', '^', '=',
    ];

    //
    // Inline Escape
    // -------------------------------------------------------------------------

    // #[Override]
    protected function inlineEscapeSequence($Excerpt)
    {
        $Element = [
            'element' => [
                'rawHtml' => $Excerpt['text'][1],
            ],
            'extent' => 2,
        ];

        if ($this->options['math']['enabled'] === true) {
            if (isset($Excerpt['text'][1]) && in_array($Excerpt['text'][1], $this->specialCharacters) && !preg_match('/^(?<!\\\\)((?<!\\\\\()\\\\\((?!\\\\\())(.*?)(?<!\\\\)(?<!\\\\\()((?<!\\\\\))\\\\\)(?!\\\\\)))(?!\\\\\()/s', $Excerpt['text'])) {
                return $Element;
            } elseif (isset($Excerpt['text'][1]) && in_array($Excerpt['text'][1], $this->specialCharacters) && !preg_match('/^(?<!\\\\)(?<!\\\\\()\\\\\((.+?)(?<!\\\\\()\\\\\)(?!\\\\\))/s', $Excerpt['text'])) {
                return $Element;
            }
        } elseif (isset($Excerpt['text'][1]) && in_array($Excerpt['text'][1], $this->specialCharacters)) {
            return $Element;
        }
        return null;
    }

    // -------------------------------------------------------------------------
    // -----------------------         Blocks         --------------------------
    // -------------------------------------------------------------------------

    //
    // Block Math
    // --------------------------------------------------------------------------

    protected function blockMath($Line)
    {
        if (!$this->options['math']['enabled'] && !$this->options['math']['block']['enabled']) {
            return null;
        }

        $Block = [
            'element' => [
                'text' => '',
            ],
        ];

        if (preg_match('/^(?<!\\\\)(\\\\\[)(?!.)$/', $Line['text'])) {
            $Block['end'] = '\]';
            return $Block;
        } elseif (preg_match('/^(?<!\\\\)(\$\$)(?!.)$/', $Line['text'])) {
            $Block['end'] = '$$';
            return $Block;
        }
        return null;
    }

    // ~

    protected function blockMathContinue($Line, $Block)
    {
        if (isset($Block['complete'])) {
            return null;
        }

        if (isset($Block['interrupted'])) {
            $Block['element']['text'] .= str_repeat("\n", $Block['interrupted']);

            unset($Block['interrupted']);
        }

        if (preg_match('/^(?<!\\\\)(\\\\\])$/', $Line['text']) && $Block['end'] === '\]') {
            $Block['complete'] = true;
            $Block['latex'] = true;
            $Block['element']['text'] = '\\['.$Block['element']['text'].'\\]';
            return $Block;
        } elseif (preg_match('/^(?<!\\\\)(\$\$)$/', $Line['text']) && $Block['end'] === '$$') {
            $Block['complete'] = true;
            $Block['latex'] = true;
            $Block['element']['text'] = '$$'.$Block['element']['text'].'$$';
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
