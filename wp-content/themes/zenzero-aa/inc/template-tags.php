<?php

function zenzero_aa_remove_author($html) {
    return preg_replace('#<span\s+class="byline">.*?</span>.*?</span>#', '', $html);
}

function zenzero_aa_fix_detail_html($html) {
    // removes annoying inline style
    $html = preg_replace('#\bstyle=([\'"]).*?\1#', '', $html);
    // adds a class for online meeting buttons
    $html = str_replace('<li class="list-group-item"', '<li class="list-group-item online-meeting-info"', $html);
    // in this order: email button, back button, email button (placeholder during substitution)
    $html = str_replace(['glyphicon-chevron-left', 'glyphicon-chevron-right', 'glyphicon-chevron-temp-right'], ['glyphicon-chevron-temp-right', 'glyphicon-chevron-left', 'glyphicon-edit'], $html);
    // adds icon for location button
    $html = preg_replace('#(<li.*?list-group-item-location.*?)(</h3>)#s', '$1 <span class="glyphicon glyphicon-chevron-right"></span>$2', $html);
    // removes empty contact item if public contact details are enabled but no info present
    $html = preg_replace('#<li[^>]+list-group-item-group[^>]+>[\r\n\s]*<h3[^>]+>[^<>]+</h3>[\r\n\s]*</li>#s', '', $html);

    return $html;
}

function zenzero_aa_add_timezone($html) {
    $timezone = wp_timezone()->getName();

    return preg_replace('#(<p class="meeting-time".+?)(</p>)#s', '$1 <span class="zenzero-aa-timezone"> (' . $timezone .')</span> $2', $html);
}
