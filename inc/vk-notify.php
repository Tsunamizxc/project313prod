<?php
/**
 * VK community notifications for form leads.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Send message to VK community chat / callback.
 *
 * @param string $message Text.
 * @return bool|WP_Error
 */
function p313_vk_notify( $message ) {
	if ( ! p313_option( 'vk_enabled' ) ) {
		return false;
	}

	$token   = trim( (string) p313_option( 'vk_token' ) );
	$peer_id = trim( (string) p313_option( 'vk_peer_id' ) );
	$api_ver = p313_option( 'vk_api_version', '5.199' ) ?: '5.199';

	if ( ! $token || ! $peer_id ) {
		return new WP_Error( 'p313_vk', 'VK: не заданы токен или peer_id' );
	}

	$response = wp_remote_post(
		'https://api.vk.com/method/messages.send',
		array(
			'timeout' => 15,
			'body'    => array(
				'access_token' => $token,
				'v'            => $api_ver,
				'peer_id'      => $peer_id,
				'random_id'    => wp_rand( 1, PHP_INT_MAX ),
				'message'      => $message,
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! empty( $body['error'] ) ) {
		return new WP_Error( 'p313_vk', 'VK API: ' . ( $body['error']['error_msg'] ?? 'error' ) );
	}

	return true;
}

/**
 * Format lead for VK.
 *
 * @param array $data Lead fields.
 * @return string
 */
function p313_vk_format_lead( array $data ) {
	$lines = array(
		'Новая заявка с сайта Project 313',
		'Имя: ' . ( $data['name'] ?? '—' ),
		'Телефон: ' . ( $data['phone'] ?? '—' ),
		'Направление: ' . ( $data['direction'] ?: 'Не выбрано' ),
		'Комментарий: ' . ( $data['comment'] ?: '—' ),
		'Страница: ' . ( $data['page'] ?? home_url( '/' ) ),
		'Время: ' . wp_date( 'd.m.Y H:i' ),
	);
	return implode( "\n", $lines );
}
