<?php

ob_start();
include __DIR__ . '/../zenzero/header.php';
$parent_header = ob_get_clean();

ob_start();
if (is_active_sidebar('header_teasers')):
    dynamic_sidebar('header_teasers');
endif;
$header_teasers = ob_get_clean();

ob_start();
if (current_user_can('read_private_pages')):
    wp_nav_menu(array('theme_location' => 'zenzero_aa_private_menu'));
endif;
$private_menu = ob_get_clean();

// extract relevant content of private menu and append to main nav
$private_menu = preg_replace('#^.*?<ul[^>]+>#s', '', $private_menu);
$private_menu = preg_replace('#</ul></div>.*?$#s', '', $private_menu);
$parent_header = preg_replace('#(site-navigation.*)(</ul>.*?</nav>)#s', "$1 $private_menu $2", $parent_header);

// insert the header teasers
$parent_header = preg_replace('#site-branding.*?</div>#s', "$0 $header_teasers", $parent_header);

// add a copy of the search button behind the menu
$search_button = '<button id="open-search" class="showSearch"><i data-feather="search"></i><span class="label">' . __('Search', 'zenzero-aa') . '</span></button>';
$parent_header = preg_replace('#<button[^>]+menu-toggle.*?</button>#s', "$0 $search_button", $parent_header);

// substitute menu icon with nicer feather one
$parent_header = preg_replace('#<i[^>]+?fa-align-justify[^>]+?>#s', '<i data-feather="menu">', $parent_header);

echo $parent_header;
