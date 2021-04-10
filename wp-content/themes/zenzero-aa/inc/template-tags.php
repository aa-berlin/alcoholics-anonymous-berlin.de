<?php

function zenzero_aa_remove_author($html) {
    return preg_replace('#<span\s+class="byline">.*?</span>.*?</span>#', '', $html);
}

function zenzero_aa_insert_sidebar($html) {
    if (!is_active_sidebar( 'sidebar-1' )) {
        return $html;
    }

    $sidebar_parts = explode(ZENZERO_AA_SIDEBAR_DELIMITER, $html);
    $sidebar_html = array_splice($sidebar_parts, 1, 1)[0];
    $html = implode('', $sidebar_parts);

    $button_html = '<button class="show-sidebar" title="' . __('Show Schedule & Announcements', 'zenzero-aa') . '"><i class="fa fa-angle-up"></i></button>';

    $html = preg_replace('#<(?:div|header)[^>]+(?:entry|page)-header.*?</h1>#s', "$0 $button_html", $html, 1);
    $html = preg_replace('#<div[^>]+id="content"[^>]+>#s', "$0 $sidebar_html", $html, 1);

    return $html;
}

function zenzero_aa_remove_unused_general_elements($html) {
    return preg_replace('#<[^>]+beforeContent.*?</[^>]+>#', '', $html);
}

function zenzero_aa_fix_detail_html($html) {
    // removes annoying inline style
    $html = preg_replace('#\bstyle=([\'"]).*?\1#', '', $html);
    // adds a class for online meeting buttons
    $html = str_replace('<li class="list-group-item"', '<li class="list-group-item online-meeting-info"', $html);
    // in this order: email button, back button, email button (placeholder during substitution)
    $html = str_replace(['glyphicon-chevron-left', 'glyphicon-chevron-right', 'glyphicon-chevron-temp-right'], ['glyphicon-chevron-temp-right', 'glyphicon-chevron-left', 'glyphicon-edit'], $html);
    // adds icon for location button
    $html = preg_replace('#(<li.*?list-group-item-location.*?)(</h3>)#s', '$1 <span class="glyphicon glyphicon-chevron-right"></span>$2', $html, 1);
    // removes empty contact item if public contact details are enabled but no info present
    $html = preg_replace('#<li[^>]+list-group-item-group[^>]+>[\r\n\s]*<h3[^>]+>[^<>]+</h3>[\r\n\s]*</li>#s', '', $html);

    return $html;
}

function zenzero_aa_add_timezone($html) {
    $timezone = wp_timezone()->getName();

    return preg_replace('#(<p class="meeting-time".+?)(</p>)#s', '$1 <span class="zenzero-aa-timezone"> (' . $timezone .')</span> $2', $html, 1);
}
