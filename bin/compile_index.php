<?php

require_once __DIR__.'/../vendor/autoload.php';

use HeyUpdate\Emoji\UnicodeUtil;

$configFile = __DIR__.'/../config/index.json';

$template = <<<'TEMPLATE'
<?php

namespace HeyUpdate\Emoji\Index;

class CompiledIndex extends BaseIndex
{
    /**
     * @var array
     */
    protected $emojis = {{emojis}};

    /**
     * @var array
     */
    protected $emojiUnicodes = {{emojiUnicodes}};

    /**
     * @var array
     */
    protected $emojiNames = {{emojiNames}};

    /**
     * @var string
     */
    protected $emojiUnicodeRegex = {{emojiUnicodeRegex}};

    /**
     * @var string
     */
    protected $emojiNameRegex = {{emojiNameRegex}};
}
TEMPLATE;

if (!is_file($configFile)) {
    throw new InvalidArgumentException(sprintf('The emoji config file "%s" does not exist', $configFile));
}

$emojis = json_decode(file_get_contents($configFile), true);
if ($emojis === false) {
    throw new InvalidArgumentException(sprintf('Unable to parse the emoji config file "%s"', $configFile));
}

$emojiNames = [];
$emojiUnicodes = [];
$emojiUnicodeRegexParts = [];
foreach ($emojis as $index => $emoji) {
    if (isset($emoji['name'])) {
        // Create a map of emoji names to the hash index
        $emojiNames[$emoji['name']] = $index;
    }

    if (isset($emoji['aliases'])) {
        foreach ($emoji['aliases'] as $alias) {
            // Create a map of emoji names to the hash index
            $emojiNames[$alias] = $index;
        }
    }

    if (isset($emoji['unicode'])) {
        $string = '';
        foreach (explode('-', $emoji['unicode']) as $unicode) {
            // Get string from unicode parts
            $string .= UnicodeUtil::convertUnicodeToString($unicode);
        }

        // Create a map of unicode emoji characters to the hash index
        $emojiUnicodes[$string] = $index;
        $emojiUnicodeRegexParts[] = UnicodeUtil::formatRegexString($string);
    }
}

// Build the unicode regex
$emojiUnicodeRegex = sprintf('/%s/', implode('|', $emojiUnicodeRegexParts));

// Build the name regex
$emojiNameRegex = sprintf('/:(%s):/', implode('|', array_map(function ($name) {
    return preg_quote($name, '/');
}, array_keys($emojiNames))));

echo str_replace(
    [
        '{{emojis}}',
        '{{emojiNames}}',
        '{{emojiUnicodes}}',
        '{{emojiNameRegex}}',
        '{{emojiUnicodeRegex}}',
    ],
    [
        var_export($emojis, true),
        var_export($emojiNames, true),
        var_export($emojiUnicodes, true),
        var_export($emojiNameRegex, true),
        var_export($emojiUnicodeRegex, true),
    ],
    $template
);
