<?php
/*
 Title: Hello World Example
 Tagline: Let's say hello!
 Tags: basic
 Requires: PHP >= 5.3
 Description: Basic hello world example to get started with Restler 3.
 Example 1: GET say/hello returns "Hello world!"
 Example 2: GET say/hello?to=R.Arul%20Kumaran returns "Hello R.Arul Kumaran!"
 Example 3: GET say/hi?to=restler3.0 returns "Hi restler3.0!"
 Content: > **Note:-** If you have used Restler 2 before, you will wonder why
 the generated routes are lesser with Restler 3.
 Look at [Routes](../_006_routing/readme.html) example to understand.
 */
require_once '../../vendor/restler.php';
use Luracast\Restler\Restler;
use Luracast\Restler\Format\HtmlFormat;

$r = new Restler();
$r->setSupportedFormats('JsonFormat', 'HtmlFormat');
$r->addAPIClass('DemoConnector');
$r->addAPIClass('Downloader');
$r->addAPIClass('DownloaderFromRinggold');
$r->handle();
