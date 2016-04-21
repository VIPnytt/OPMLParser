[![Build Status](https://travis-ci.org/VIPnytt/OPMLParser.svg?branch=master)](https://travis-ci.org/VIPnytt/OPMLParser)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/VIPnytt/OPMLParser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/VIPnytt/OPMLParser/?branch=master)
[![Code Climate](https://codeclimate.com/github/VIPnytt/OPMLParser/badges/gpa.svg)](https://codeclimate.com/github/VIPnytt/OPMLParser)
[![Test Coverage](https://codeclimate.com/github/VIPnytt/OPMLParser/badges/coverage.svg)](https://codeclimate.com/github/VIPnytt/OPMLParser/coverage)
[![License](https://poser.pugx.org/VIPnytt/OPMLParser/license)](https://github.com/VIPnytt/OPMLParser/blob/master/LICENSE)
[![Packagist](https://img.shields.io/packagist/v/vipnytt/opmlparser.svg)](https://packagist.org/packages/vipnytt/opmlparser)
[![Join the chat at https://gitter.im/VIPnytt/OPMLParser](https://badges.gitter.im/VIPnytt/OPMLParser.svg)](https://gitter.im/VIPnytt/OPMLParser)

# OPML parser
PHP class to parse OPML documents according to [OPML 1.0](http://dev.opml.org/spec1.html) and [OPML 2.0 specifications](http://dev.opml.org/spec2.html).

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7393a662-7988-4a2a-820b-ebac01a1a91f/big.png)](https://insight.sensiolabs.com/projects/7393a662-7988-4a2a-820b-ebac01a1a91f)

#### Requirements:
- PHP [>=5.6](http://php.net/supported-versions.php)

Note: HHVM support is planned once [facebook/hhvm#4277](https://github.com/facebook/hhvm/issues/4277) is fixed.

## Installation
The recommended way to install the robots.txt parser is through [Composer](http://getcomposer.org). Add this to your `composer.json` file:

```json
{
    "require": {
        "vipnytt/opmlparser": "1.0.*"
    }
}
```
Then run: ```php composer.phar update```

## Getting Started
### Basic usage example
```php
$parser = new vipnytt\OPMLParser($xml);

// Result as Array
$array = $parser->getResult()

// Validate the result
$object = $parser->validate()  // \SimpleXMLElement on success | false on failure
```

### Array rendering example
```php
$render = new vipnytt\OPMLParser\Render($array, $version = '2.0');

// as SimpleXMLElement object
$object = $render->asXMLObject(); // \SimpleXMLElement

// as XML string
$string = $render->asXMLObject()->asXML(); // string
```

Note: OPML version 2.0 is used by default, unless you have specified otherwise.
The difference between version 2.0 and 1.0 is the "text" attribute, witch is optional in version 1.0.
