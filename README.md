# PHP Template based random string generator

## Installation

Install via [composer](https://getcomposer.org/download/):

```bash
$ composer require skgroup/php-std-dynamic-string
```

### How to use

```php
$dynamicString = new \sKGroup\Std\String\DynamicString('{Hi|Hello},{My {friend|name} is a {Bob|Mark|Jon}}!');
echo $dynamicString->generate(); // e.g. - Hello, My name is a Jon!
echo $dynamicString->generate(); // e.g. - Hello, My friend is a Mark!
echo $dynamicString->generate(); // e.g. - Hi, My name is a Bob!
```