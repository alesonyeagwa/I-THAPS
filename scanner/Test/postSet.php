<?php
$plugins_to_install = array();
if ( is_array( $_POST['plugin'] ) ) {
    $plugins_to_install = (array) $_POST['plugin'];
} elseif ( is_string( $_POST['plugin'] ) ) {
    // Received via Filesystem page - un-flatten array (WP bug #19643).
    $plugins_to_install = explode( ',', $_POST['plugin'] );
}
$_POST['plugin'] = implode( ',', $plugins_to_install ); // Work around for WP bug #19643.

$method = ''; // Leave blank so WP_Filesystem can populate it as necessary.
$fields = array_keys( $_POST ); // Extra fields to pass to WP_Filesystem.