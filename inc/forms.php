<?php
/**
 * Signup form AJAX handler.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function p313_handle_signup() {
	check_ajax_referer( 'p313_signup', 'nonce' );

	$name      = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$phone     = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$direction = sanitize_text_field( wp_unslash( $_POST['direction'] ?? '' ) );
	$comment   = sanitize_textarea_field( wp_unslash( $_POST['comment'] ?? '' ) );
	$page      = esc_url_raw( wp_unslash( $_POST['page'] ?? home_url( '/' ) ) );

	$digits = preg_replace( '/\D+/', '', $phone );
	if ( strlen( (string) $digits ) < 11 ) {
		wp_send_json_error( array( 'message' => 'Укажите корректный номер телефона' ), 400 );
	}

	$lead = compact( 'name', 'phone', 'direction', 'comment', 'page' );

	$to      = p313_option( 'notify_email', get_option( 'admin_email' ) );
	$subject = p313_option( 'notify_subject', 'Новая заявка — Project 313' );
	$body    = "Имя: {$name}\nТелефон: {$phone}\nНаправление: " . ( $direction ?: 'Не выбрано' ) . "\nКомментарий: " . ( $comment ?: '—' ) . "\nСтраница: {$page}\n";

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	$mailed  = wp_mail( $to, $subject, $body, $headers );

	$vk = p313_vk_notify( p313_vk_format_lead( $lead ) );

	wp_send_json_success(
		array(
			'mail'    => (bool) $mailed,
			'vk'      => ! is_wp_error( $vk ) && $vk,
			'message' => p313_option( 'form_success_text', 'Спасибо! Мы позвоним вам в течение дня и подберём удобное время для просмотра.' ),
		)
	);
}

add_action( 'wp_ajax_p313_signup', 'p313_handle_signup' );
add_action( 'wp_ajax_nopriv_p313_signup', 'p313_handle_signup' );
