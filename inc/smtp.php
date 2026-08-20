<?php
/**
 * SMTP mailer via ACF options.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'phpmailer_init',
	function ( $phpmailer ) {
		if ( ! p313_option( 'smtp_enabled' ) ) {
			return;
		}

		$host = p313_option( 'smtp_host' );
		if ( ! $host ) {
			return;
		}

		$phpmailer->isSMTP();
		$phpmailer->Host       = $host;
		$phpmailer->Port       = (int) ( p313_option( 'smtp_port', 587 ) ?: 587 );
		$phpmailer->SMTPAuth   = (bool) p313_option( 'smtp_auth', true );
		$phpmailer->Username   = (string) p313_option( 'smtp_user' );
		$phpmailer->Password   = (string) p313_option( 'smtp_pass' );
		$secure                = (string) p313_option( 'smtp_secure', 'tls' );
		if ( in_array( $secure, array( 'tls', 'ssl' ), true ) ) {
			$phpmailer->SMTPSecure = $secure;
		} else {
			$phpmailer->SMTPSecure = '';
			$phpmailer->SMTPAutoTLS = false;
		}

		$from_email = p313_option( 'smtp_from_email' );
		$from_name  = p313_option( 'smtp_from_name', get_bloginfo( 'name' ) );
		if ( $from_email ) {
			$phpmailer->setFrom( $from_email, $from_name, false );
		}
	}
);

add_filter(
	'wp_mail_from',
	function ( $email ) {
		$from = p313_option( 'smtp_from_email' );
		return $from ? $from : $email;
	}
);

add_filter(
	'wp_mail_from_name',
	function ( $name ) {
		$from = p313_option( 'smtp_from_name' );
		return $from ? $from : $name;
	}
);

add_action(
	'admin_post_p313_test_smtp',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Forbidden' );
		}
		check_admin_referer( 'p313_test_smtp' );
		$to = p313_option( 'notify_email', get_option( 'admin_email' ) );
		$ok = wp_mail( $to, 'Project 313 — тест SMTP', "Письмо отправлено успешно.\nВремя: " . wp_date( 'd.m.Y H:i' ) );
		wp_safe_redirect( add_query_arg( 'p313_smtp_test', $ok ? '1' : '0', admin_url( 'admin.php?page=p313-smtp' ) ) );
		exit;
	}
);
