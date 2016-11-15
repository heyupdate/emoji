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

$data = json_decode(file_get_contents($configFile), true);
if ($data === false) {
    throw new InvalidArgumentException(sprintf('Unable to parse the emoji config file "%s"', $configFile));
}

$index = 0;
$emojis = [];
$emojiNames = [];
$emojiUnicodes = [];
$emojiUnicodeRegexParts = [];

$skinTones = [];
foreach ($data as $emoji) {
    if ($emoji['category'] === 'Skin Tones') {
        $skinTones[$emoji['unified']] = $emoji;
    }
}

foreach ($data as $emoji) {
    if ($emoji['has_img_twitter']) {
        if (isset($emoji['skin_variations'])) {
            foreach ($emoji['skin_variations'] as $variation) {
                if ($variation['has_img_twitter']) {
                    $unicodeParts = explode('-', $variation['unified']);
                    $skinTone = $skinTones[$unicodeParts[count($unicodeParts) - 1]];

                    $emojis[$index] = [
                        'unicode' => strtolower($variation['unified']),
                        'name' => $emoji['short_name'].' '.$skinTone['short_name'],
                        'description' => strtolower($emoji['name']).' '.strtolower($skinTone['name']),
                    ];

                    foreach ($emoji['short_names'] as $shortName) {
                        // Create a map of emoji names to the hash index
                        $emojiNames[$shortName.' '.$skinTone['short_name']] = $index;
                        $emojiNameRegexParts[] = preg_quote(':'.$shortName.'::'.$skinTone['short_name'].':', '/');
                    }

                    $string = '';
                    foreach ($unicodeParts as $unicode) {
                        // Get string from unicode parts
                        $string .= UnicodeUtil::convertUnicodeToString($unicode);
                    }

                    // Create a map of unicode emoji characters to the hash index
                    $emojiUnicodes[$string] = $index;
                    $emojiUnicodeRegexParts[] = UnicodeUtil::formatRegexString($string);

                    ++$index;
                }
            }
        }

        $emojis[$index] = [
            'unicode' => strtolower($emoji['unified']),
            'name' => $emoji['short_name'],
            'description' => strtolower($emoji['name']),
        ];

        foreach ($emoji['short_names'] as $shortName) {
            // Create a map of emoji names to the hash index
            $emojiNames[$shortName] = $index;
            $emojiNameRegexParts[] = preg_quote(':'.$shortName.':', '/');
        }

        $string = '';
        foreach (explode('-', $emoji['unified']) as $unicode) {
            // Get string from unicode parts
            $string .= UnicodeUtil::convertUnicodeToString($unicode);
        }

        // Create a map of unicode emoji characters to the hash index
        $emojiUnicodes[$string] = $index;
        $emojiUnicodeRegexParts[] = UnicodeUtil::formatRegexString($string);

        ++$index;
    }
}

// Build the unicode regex
$emojiUnicodeRegex = '/'.implode('|', $emojiUnicodeRegexParts).'/';

// Build the name regex
$emojiNameRegex = '/'.implode('|', $emojiNameRegexParts).'/';

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
