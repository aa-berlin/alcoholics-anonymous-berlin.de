<?php

function zenzero_aa_remove_author($html) {
    return preg_replace('#<span\s+class="byline">.*?</span>.*?</span>#', '', $html);
}

function zenzero_aa_add_timezone($html) {
    $timezone = wp_timezone()->getName();

    return preg_replace('#(<p class="meeting-time".+?)(</p>)#s', '$1 <span class="zenzero-aa-timezone"> (' . $timezone .')</span> $2', $html);
}
