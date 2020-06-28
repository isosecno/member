<?php
/*
Dette er en utvidelse av Sidney Theme for å hente inn Bootstrap css.
Linjen uner legges inn på slutten av functions.php

require get_template_directory() . '/isosec-functions.php';

*/
function wpbootstrap_enqueue_styles() {
    wp_enqueue_style( 'bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' );
    //wp_enqueue_style( 'my-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'wpbootstrap_enqueue_styles');

