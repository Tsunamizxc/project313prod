<?php
/**
 * Schedule CSV / Excel import.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Parse uploaded spreadsheet into rows.
 *
 * @param string $file_path Absolute file path.
 * @param string $ext       File extension.
 * @return array<int, array<int, string>>|WP_Error
 */
function p313_parse_spreadsheet( $file_path, $ext ) {
	$ext = strtolower( (string) $ext );
	if ( 'csv' === $ext ) {
		return p313_parse_csv_file( $file_path );
	}
	if ( in_array( $ext, array( 'xlsx', 'xls' ), true ) ) {
		return p313_parse_xlsx_file( $file_path );
	}
	return new WP_Error( 'p313_bad_format', 'Поддерживаются файлы CSV и XLSX.' );
}

/**
 * @param string $file_path File path.
 * @return array<int, array<int, string>>|WP_Error
 */
function p313_parse_csv_file( $file_path ) {
	$handle = fopen( $file_path, 'rb' );
	if ( ! $handle ) {
		return new WP_Error( 'p313_csv_open', 'Не удалось прочитать CSV-файл.' );
	}

	$first = fgets( $handle );
	if ( false === $first ) {
		fclose( $handle );
		return new WP_Error( 'p313_csv_empty', 'Файл пустой.' );
	}

	$delimiter = substr_count( $first, ';' ) > substr_count( $first, ',' ) ? ';' : ',';
	rewind( $handle );

	$rows = array();
	while ( ( $row = fgetcsv( $handle, 0, $delimiter ) ) !== false ) {
		$rows[] = array_map(
			static function ( $cell ) {
				$cell = (string) $cell;
				if ( 0 === strpos( $cell, "\xEF\xBB\xBF" ) ) {
					$cell = substr( $cell, 3 );
				}
				return trim( $cell );
			},
			$row
		);
	}
	fclose( $handle );

	return $rows;
}

/**
 * Minimal XLSX reader for the first worksheet.
 *
 * @param string $file_path File path.
 * @return array<int, array<int, string>>|WP_Error
 */
function p313_parse_xlsx_file( $file_path ) {
	if ( ! class_exists( 'ZipArchive' ) ) {
		return new WP_Error( 'p313_no_zip', 'На сервере недоступен ZipArchive. Сохраните файл как CSV.' );
	}

	$zip = new ZipArchive();
	if ( true !== $zip->open( $file_path ) ) {
		return new WP_Error( 'p313_xlsx_open', 'Не удалось открыть XLSX-файл.' );
	}

	$shared = array();
	$shared_xml = $zip->getFromName( 'xl/sharedStrings.xml' );
	if ( $shared_xml ) {
		$xml = simplexml_load_string( $shared_xml );
		if ( $xml ) {
			foreach ( $xml->si as $si ) {
				if ( isset( $si->t ) ) {
					$shared[] = (string) $si->t;
				} else {
					$text = '';
					foreach ( $si->r as $run ) {
						$text .= (string) $run->t;
					}
					$shared[] = $text;
				}
			}
		}
	}

	$sheet_xml = $zip->getFromName( 'xl/worksheets/sheet1.xml' );
	$zip->close();

	if ( ! $sheet_xml ) {
		return new WP_Error( 'p313_xlsx_sheet', 'В файле Excel не найден первый лист.' );
	}

	$sheet = simplexml_load_string( $sheet_xml );
	if ( ! $sheet || ! isset( $sheet->sheetData->row ) ) {
		return new WP_Error( 'p313_xlsx_empty', 'Лист Excel пустой.' );
	}

	$rows = array();
	foreach ( $sheet->sheetData->row as $row ) {
		$cells = array();
		$col   = 0;
		foreach ( $row->c as $cell ) {
			$ref = (string) $cell['r'];
			if ( preg_match( '/([A-Z]+)/', $ref, $match ) ) {
				$index = p313_xlsx_column_index( $match[1] );
				while ( count( $cells ) < $index ) {
					$cells[] = '';
				}
			} else {
				$index = $col;
			}

			$value = '';
			if ( isset( $cell->v ) ) {
				$value = (string) $cell->v;
				if ( isset( $cell['t'] ) && 's' === (string) $cell['t'] ) {
					$value = $shared[ (int) $value ] ?? '';
				}
			}
			$cells[ $index ] = trim( $value );
			$col             = $index + 1;
		}
		if ( array_filter( $cells ) ) {
			$rows[] = array_values( $cells );
		}
	}

	return $rows;
}

/**
 * @param string $letters Column letters, e.g. A, B, AA.
 * @return int Zero-based index.
 */
function p313_xlsx_column_index( $letters ) {
	$letters = strtoupper( $letters );
	$index   = 0;
	$len     = strlen( $letters );
	for ( $i = 0; $i < $len; $i++ ) {
		$index = $index * 26 + ( ord( $letters[ $i ] ) - 64 );
	}
	return max( 0, $index - 1 );
}

/**
 * Normalize header map.
 *
 * @param array<int, string> $header Header row.
 * @return array<string, int>
 */
function p313_schedule_import_header_map( $header ) {
	$aliases = array(
		'time'      => array( 'время', 'time' ),
		'day'       => array( 'день', 'день недели', 'day' ),
		'direction' => array( 'направление', 'услуга', 'direction', 'service' ),
		'group'     => array( 'группа', 'group' ),
		'teacher'   => array( 'педагог', 'teacher' ),
		'branch'    => array( 'филиал', 'branch', 'зал', 'hall' ),
	);

	$map = array();
	foreach ( $header as $index => $label ) {
		$key = mb_strtolower( trim( (string) $label ) );
		foreach ( $aliases as $field => $names ) {
			if ( in_array( $key, $names, true ) ) {
				$map[ $field ] = (int) $index;
			}
		}
	}

	return $map;
}

/**
 * Import one schedule row.
 *
 * @param array<string, string> $row Parsed row.
 * @return int|WP_Error Post ID or error.
 */
function p313_schedule_import_row( $row ) {
	$time      = trim( (string) ( $row['time'] ?? '' ) );
	$day       = trim( (string) ( $row['day'] ?? '' ) );
	$direction = trim( (string) ( $row['direction'] ?? '' ) );
	$group     = trim( (string) ( $row['group'] ?? '' ) );
	$teacher   = trim( (string) ( $row['teacher'] ?? '' ) );
	$branch    = trim( (string) ( $row['branch'] ?? '' ) );

	if ( '' === $time && '' === $day && '' === $direction ) {
		return new WP_Error( 'p313_row_empty', 'Пустая строка.' );
	}

	$service_id = p313_find_post_by_title( 'p313_service', $direction );
	$group_id   = $group ? p313_find_post_by_title( 'p313_kids', $group ) : 0;
	$teacher_id = $teacher ? p313_find_post_by_title( 'p313_teacher', $teacher ) : 0;
	$branch_id  = $branch ? p313_find_post_by_title( 'p313_branch', $branch ) : 0;

	$title_parts = array_filter( array( $direction, $day, $time ) );
	$post_id     = wp_insert_post(
		array(
			'post_type'   => 'p313_schedule',
			'post_status' => 'publish',
			'post_title'  => implode( ' — ', $title_parts ),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	$fields = array(
		'time'        => $time,
		'day'         => $day,
		'service'     => $service_id ?: '',
		'group'       => $group_id ?: '',
		'teacher_ref' => $teacher_id ?: '',
		'branch'      => $branch_id ?: '',
	);

	if ( function_exists( 'update_field' ) ) {
		foreach ( $fields as $key => $value ) {
			update_field( $key, $value, $post_id );
		}
	} else {
		foreach ( $fields as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
	}

	return (int) $post_id;
}

/**
 * Import spreadsheet rows.
 *
 * @param array<int, array<int, string>> $rows Parsed rows.
 * @param bool                           $replace Delete existing schedule first.
 * @return array{created:int, skipped:int, errors:array<int,string>}
 */
function p313_schedule_import_rows( $rows, $replace = false ) {
	$result = array(
		'created' => 0,
		'skipped' => 0,
		'errors'  => array(),
	);

	if ( $replace ) {
		$existing = get_posts(
			array(
				'post_type'      => 'p313_schedule',
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'fields'         => 'ids',
			)
		);
		foreach ( $existing as $post_id ) {
			wp_delete_post( (int) $post_id, true );
		}
	}

	if ( ! $rows ) {
		$result['errors'][] = 'Файл не содержит строк.';
		return $result;
	}

	$header_map = p313_schedule_import_header_map( $rows[0] );
	$start      = 0;
	if ( isset( $header_map['time'] ) || isset( $header_map['direction'] ) ) {
		$start = 1;
	} else {
		$header_map = array(
			'time'      => 0,
			'day'       => 1,
			'direction' => 2,
			'group'     => 3,
			'teacher'   => 4,
			'branch'    => 5,
		);
	}

	for ( $i = $start; $i < count( $rows ); $i++ ) {
		$cells = $rows[ $i ];
		$row   = array(
			'time'      => $cells[ $header_map['time'] ?? -1 ] ?? '',
			'day'       => $cells[ $header_map['day'] ?? -1 ] ?? '',
			'direction' => $cells[ $header_map['direction'] ?? -1 ] ?? '',
			'group'     => $cells[ $header_map['group'] ?? -1 ] ?? '',
			'teacher'   => $cells[ $header_map['teacher'] ?? -1 ] ?? '',
			'branch'    => $cells[ $header_map['branch'] ?? -1 ] ?? '',
		);

		$created = p313_schedule_import_row( $row );
		if ( is_wp_error( $created ) ) {
			if ( 'p313_row_empty' === $created->get_error_code() ) {
				++$result['skipped'];
				continue;
			}
			$result['errors'][] = sprintf( 'Строка %d: %s', $i + 1, $created->get_error_message() );
			continue;
		}
		++$result['created'];
	}

	return $result;
}

add_action(
	'admin_menu',
	function () {
		add_submenu_page(
			'edit.php?post_type=p313_schedule',
			'Импорт расписания',
			'Импорт Excel/CSV',
			'manage_options',
			'p313-schedule-import',
			'p313_schedule_import_page'
		);
	}
);

/**
 * Admin import page.
 */
function p313_schedule_import_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$notice = '';
	if ( ! empty( $_GET['p313_import_done'] ) ) {
		$created = isset( $_GET['created'] ) ? (int) $_GET['created'] : 0;
		$skipped = isset( $_GET['skipped'] ) ? (int) $_GET['skipped'] : 0;
		$notice  = sprintf(
			'<div class="notice notice-success"><p>Импорт завершён: добавлено %d, пропущено %d.</p></div>',
			$created,
			$skipped
		);
		if ( ! empty( $_GET['errors'] ) ) {
			$errors = array_filter( explode( '|', sanitize_text_field( wp_unslash( $_GET['errors'] ) ) ) );
			if ( $errors ) {
				$notice .= '<div class="notice notice-warning"><p>' . esc_html( implode( ' ', $errors ) ) . '</p></div>';
			}
		}
	}

	$sample_url = add_query_arg(
		array(
			'action'   => 'p313_schedule_sample',
			'_wpnonce' => wp_create_nonce( 'p313_schedule_sample' ),
		),
		admin_url( 'admin-post.php' )
	);

	echo '<div class="wrap">';
	echo '<h1>Импорт расписания</h1>';
	echo wp_kses_post( $notice );
	echo '<p>Загрузите CSV или XLSX. Колонки: <strong>Время, День, Направление, Группа, Педагог, Филиал</strong>.</p>';
	echo '<p>Направления, группы, педагоги и филиалы подбираются по точному названию из админки.</p>';
	echo '<p><a href="' . esc_url( $sample_url ) . '">Скачать шаблон CSV</a></p>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" enctype="multipart/form-data">';
	wp_nonce_field( 'p313_schedule_import' );
	echo '<input type="hidden" name="action" value="p313_schedule_import">';
	echo '<table class="form-table"><tbody>';
	echo '<tr><th scope="row"><label for="p313_schedule_file">Файл</label></th><td><input type="file" id="p313_schedule_file" name="schedule_file" accept=".csv,.xlsx,.xls" required></td></tr>';
	echo '<tr><th scope="row">Режим</th><td><label><input type="checkbox" name="replace_existing" value="1"> Заменить текущее расписание</label></td></tr>';
	echo '</tbody></table>';
	submit_button( 'Импортировать' );
	echo '</form></div>';
}

add_action(
	'admin_post_p313_schedule_sample',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Недостаточно прав.' );
		}
		check_admin_referer( 'p313_schedule_sample' );

		$csv = "Время;День;Направление;Группа;Педагог;Филиал\n17:00;Понедельник;Modern;«Крылья»;Анна Волкова;Красный Путь\n18:00;Вторник;Jazz-modern;;Дмитрий Орлов;Химик\n";
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=schedule-template.csv' );
		echo "\xEF\xBB\xBF" . $csv;
		exit;
	}
);

add_action(
	'admin_post_p313_schedule_import',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Недостаточно прав.' );
		}
		check_admin_referer( 'p313_schedule_import' );

		if ( empty( $_FILES['schedule_file']['tmp_name'] ) ) {
			wp_die( 'Файл не загружен.' );
		}

		$file     = $_FILES['schedule_file'];
		$filename = sanitize_file_name( wp_unslash( $file['name'] ) );
		$ext      = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
		$rows     = p313_parse_spreadsheet( $file['tmp_name'], $ext );

		if ( is_wp_error( $rows ) ) {
			wp_die( esc_html( $rows->get_error_message() ) );
		}

		$result  = p313_schedule_import_rows( $rows, ! empty( $_POST['replace_existing'] ) );
		$redirect = add_query_arg(
			array(
				'page'             => 'p313-schedule-import',
				'p313_import_done' => 1,
				'created'          => $result['created'],
				'skipped'          => $result['skipped'],
				'errors'           => rawurlencode( implode( '|', $result['errors'] ) ),
			),
			admin_url( 'edit.php?post_type=p313_schedule' )
		);
		wp_safe_redirect( $redirect );
		exit;
	}
);

add_filter(
	'manage_p313_schedule_posts_columns',
	function ( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['p313_time'] = 'Время';
				$new['p313_day'] = 'День';
				$new['p313_dir'] = 'Направление';
				$new['p313_group'] = 'Группа';
				$new['p313_teacher'] = 'Педагог';
				$new['p313_branch'] = 'Филиал';
			}
		}
		return $new;
	}
);

add_action(
	'manage_p313_schedule_posts_custom_column',
	function ( $column, $post_id ) {
		switch ( $column ) {
			case 'p313_time':
				echo esc_html( p313_field( 'time', '—', $post_id ) );
				break;
			case 'p313_day':
				echo esc_html( p313_field( 'day', '—', $post_id ) );
				break;
			case 'p313_dir':
				echo esc_html( p313_post_title( p313_field( 'service', '', $post_id ) ) ?: '—' );
				break;
			case 'p313_group':
				echo esc_html( p313_post_title( p313_field( 'group', '', $post_id ) ) ?: '—' );
				break;
			case 'p313_teacher':
				echo esc_html( p313_post_title( p313_field( 'teacher_ref', '', $post_id ) ) ?: '—' );
				break;
			case 'p313_branch':
				echo esc_html( p313_post_title( p313_field( 'branch', '', $post_id ) ) ?: '—' );
				break;
		}
	},
	10,
	2
);
