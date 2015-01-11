# Emoji

Emoji images from unicode characters and names (i.e. `:sunrise:`).
Built to work with [Twemoji images](http://twitter.github.io/twemoji/).

``` php
use HeyUpdate\Emoji\Emoji;
use HeyUpdate\Emoji\EmojiIndex;

$emoji = new Emoji(new EmojiIndex(), '//twemoji.maxcdn.com/36x36/%s.png');
$emoji->replaceEmojiWithImages('🎈 :balloon:');
```

## Install

Via Composer

``` bash
$ composer require heyupdate/emoji
```

## Requirements

The following versions of PHP are supported by this version.

* PHP 5.3
* PHP 5.4
* PHP 5.5
* PHP 5.6

## Testing

``` bash
$ phpunit
```

## Credits

- [Tom Graham](https://github.com/tompedals)

## License

The MIT License (MIT). Please see [License File](https://github.com/heyupdate/Emoji/blob/master/LICENSE) for more information.
