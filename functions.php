<?php
/**
 * Project 313 theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'P313_VERSION', '1.4.0' );
define( 'P313_DIR', get_template_directory() );
define( 'P313_URI', get_template_directory_uri() );

require_once P313_DIR . '/inc/helpers.php';
require_once P313_DIR . '/inc/setup.php';
require_once P313_DIR . '/inc/enqueue.php';
require_once P313_DIR . '/inc/cpt.php';
require_once P313_DIR . '/inc/acf-options.php';
require_once P313_DIR . '/inc/acf-fields.php';
require_once P313_DIR . '/inc/data.php';
require_once P313_DIR . '/inc/seo.php';
require_once P313_DIR . '/inc/smtp.php';
require_once P313_DIR . '/inc/vk-notify.php';
require_once P313_DIR . '/inc/forms.php';
require_once P313_DIR . '/inc/schedule-import.php';
require_once P313_DIR . '/inc/seed.php';
