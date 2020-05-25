<?php

// HACK: this is dev-only code, so fixing missing ssl-awareness the easiest way
$_SERVER['HTTPS'] = 'on';

require __DIR__ . '/' . $_SERVER['ACTIVE_WP_CONFIG'];
