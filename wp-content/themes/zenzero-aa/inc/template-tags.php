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
    $page_html = implode('', $sidebar_parts);

    return preg_replace('#</\w+>[^<]*<!--\s*\#content#s', "$sidebar_html $0", $page_html, 1);
}

function zenzero_aa_remove_unused_general_elements($html) {
    return preg_replace('#<[^>]+beforeContent.*?</[^>]+>#', '', $html);
}

function zenzero_aa_fix_detail_html($html) {
    // removes annoying inline style
    $html = preg_replace('#\bstyle=([\'"]).*?\1#', '', $html);
    // removes hardwired parens in header meeting type
    $html = preg_replace('#(<(?:span|div)[^>]+meeting_types[^>]+>)(?:<small>)?\(([^<]+)\)(?:</small>)?(</(?:span|div)>)#', '$1$2$3', $html);
    // adds a class for the otherwise anonymous container of the online meeting buttons
    $html = str_replace('<li class="list-group-item"', '<li class="list-group-item online-meeting-info"', $html);
    // in this order: back button
    $html = preg_replace('#class=".*?glyphicon-chevron-right.*?"#', 'data-feather="chevron-left" class="icon-back"', $html);
    // in this order: edit-meeting button
    $html = preg_replace('#class=".*?glyphicon-chevron-left.*?"#', 'data-feather="edit" class="icon-update"', $html);
    // adds icon for location button
    $html = preg_replace('#(<li.*?list-group-item-location.*?)(</h3>)#s', '$1 <i class="icon-location" data-feather="map-pin"></i>$2', $html, 1);
    // replace route planner icon
    $html = preg_replace('#(<a.*?tsml-directions.*?)<svg.*?</svg>(.*?</a>)#s', '$1 <i class="icon-directions" data-feather="map"></i>$2', $html, 1);
    $html = preg_replace('#class=".*?glyphicon-share-alt.*?"#', 'class="icon-directions" data-feather="map"', $html, 1);
    // replace video icon
    $html = preg_replace('#(<li.*?online-meeting-info.*?href="http.*?)<svg.*?</svg>(.*?</li>)#s', '$1 <i class="icon-video" data-feather="video"></i>$2', $html, 2);
    // replace phone icon
    $html = preg_replace('#(<li.*?online-meeting-info.*?href="tel:.*?)<svg.*?</svg>(.*?</li>)#s', '$1 <i class="icon-phone" data-feather="phone"></i>$2', $html, 2);
    // replace email icon
    $html = preg_replace('#(<a.*?(?:contact|group)-email.*?)<svg.*?</svg>(.*?</a>)#s', '$1 <i class="icon-email" data-feather="mail"></i>$2', $html, 2);
    // replace phone icon
    $html = preg_replace('#(<a.*?(?:contact|group)-phone.*?)<svg.*?</svg>(.*?</a>)#s', '$1 <i class="icon-phone" data-feather="phone"></i>$2', $html, 2);
    // replace link icon
    $html = preg_replace('#(<a.*?group-website.*?)<svg.*?</svg>(.*?</a>)#s', '$1 <i class="icon-website" data-feather="external-link"></i>$2', $html, 2);
    // removes empty contact item if public contact details are enabled but no info present
    $html = preg_replace('#<li[^>]+list-group-item-group[^>]+>[\r\n\s]*<h3[^>]+>[^<>]+</h3>[\r\n\s]*</li>#s', '', $html);

    return $html;
}

function zenzero_aa_add_timezone($html) {
    $timezone = wp_timezone()->getName();

    return preg_replace('#(<p class="meeting-time".+?)(</p>)#s', '$1 <span class="zenzero-aa-timezone"> (' . $timezone .')</span> $2', $html, 1);
}
