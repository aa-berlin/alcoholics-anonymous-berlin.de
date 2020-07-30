<?php

function zenzero_aa_remove_author($html) {
    return preg_replace('#<span\s+class="byline">.*?</span>.*?</span>#', '', $html);
}
