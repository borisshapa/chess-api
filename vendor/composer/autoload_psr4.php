<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'utils\\' => array($baseDir . '/src/utils'),
    'tests\\' => array($baseDir . '/tests'),
    'phpDocumentor\\Reflection\\' => array($vendorDir . '/phpdocumentor/reflection-common/src', $vendorDir . '/phpdocumentor/type-resolver/src', $vendorDir . '/phpdocumentor/reflection-docblock/src'),
    'app\\' => array($baseDir . '/src/app'),
    'api\\' => array($baseDir . '/src/api'),
    'Webmozart\\Assert\\' => array($vendorDir . '/webmozart/assert/src'),
    'Symfony\\Polyfill\\Php72\\' => array($vendorDir . '/symfony/polyfill-php72'),
    'Symfony\\Polyfill\\Mbstring\\' => array($vendorDir . '/symfony/polyfill-mbstring'),
    'Symfony\\Polyfill\\Intl\\Idn\\' => array($vendorDir . '/symfony/polyfill-intl-idn'),
    'Symfony\\Polyfill\\Ctype\\' => array($vendorDir . '/symfony/polyfill-ctype'),
    'Psr\\Http\\Message\\' => array($vendorDir . '/psr/http-message/src'),
    'Prophecy\\' => array($vendorDir . '/phpspec/prophecy/src/Prophecy'),
    'GuzzleHttp\\Psr7\\' => array($vendorDir . '/guzzlehttp/psr7/src'),
    'GuzzleHttp\\Promise\\' => array($vendorDir . '/guzzlehttp/promises/src'),
    'GuzzleHttp\\' => array($vendorDir . '/guzzlehttp/guzzle/src'),
    'Doctrine\\Instantiator\\' => array($vendorDir . '/doctrine/instantiator/src/Doctrine/Instantiator'),
    'DeepCopy\\' => array($vendorDir . '/myclabs/deep-copy/src/DeepCopy'),
);
