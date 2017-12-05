<?php

require_once 'ClassImageResizer.php';

if (version_compare(PHP_VERSION, '7.0.0') >= 0 && !class_exists('PHPUnit_Framework_TestCase')) {
    class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}

class imageResizerTest extends PHPUnit_Framework_TestCase
{

}
