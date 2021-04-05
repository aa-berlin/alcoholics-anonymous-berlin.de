<?php

$css = file_get_contents(__DIR__ . '/../../12-step-meeting-list/assets/css/public.css');

$icons = [];

preg_replace_callback('#(?<=\.)glyphicon-([a-z0-9_-]+)#', function ($match) use (&$icons) {
    list($class, $name) = $match;
    $icons[] = "<span class='glyphicon $class' title='$name'></span>";
}, $css);

$icons = array_unique($icons);

echo implode('', $icons);
