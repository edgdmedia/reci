<?php
/**
 * Branded transactional email.
 *
 * Every message the theme sends goes through reci_send_email(), which wraps a
 * small block vocabulary in a branded HTML shell and posts a plain-text
 * alternative alongside it. Content type is set per message rather than with a
 * global filter, so a plugin's own mail is never reformatted by ours.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'reci_email_palette' ) ) {
	/**
	 * Brand values, kept in step with tailwind.config.js and theme.json.
	 *
	 * @return array<string,string>
	 */
	function reci_email_palette(): array {
		// Primary and accent follow the Branding settings, so the email cannot
		// drift from the site the way a second hardcoded palette would.
		$primary = function_exists( 'reci_setting' ) ? (string) reci_setting( 'branding_primary_color', '#003594' ) : '#003594';
		$accent  = function_exists( 'reci_setting' ) ? (string) reci_setting( 'branding_accent_color', '#FFB81C' ) : '#FFB81C';

		return [
			'navy'    => $primary,
			'yellow'  => $accent,
			'ink'     => '#2B2B2B',
			'muted'   => '#6A6D70',
			'line'    => '#BABBBD',
			'ground'  => '#F3F5F9',
			'surface' => '#FFFFFF',
		];
	}
}

if ( ! function_exists( 'reci_email_from_name' ) ) {
	function reci_email_from_name(): string {
		$name = function_exists( 'reci_setting' )
			? trim( (string) reci_setting( 'email_from_name', '' ) )
			: '';

		if ( '' === $name ) {
			$name = get_bloginfo( 'name' ) ?: 'RECI Media Hub';
		}

		return (string) apply_filters( 'reci_email_from_name', $name );
	}
}

if ( ! function_exists( 'reci_email_from_address' ) ) {
	/**
	 * Sender address for transactional email.
	 *
	 * Set under Settings → Email. Falls back to admin_email only if that is
	 * cleared, since a From address off the site's domain fails SPF and DKIM and
	 * gets the mail filed as spam.
	 */
	function reci_email_from_address(): string {
		$address = function_exists( 'reci_setting' )
			? sanitize_email( (string) reci_setting( 'email_from_address', '' ) )
			: '';

		if ( '' === $address ) {
			$address = (string) get_option( 'admin_email' );
		}

		return (string) apply_filters( 'reci_email_from_address', $address );
	}
}

if ( ! function_exists( 'reci_email_render' ) ) {
	/**
	 * Render the branded HTML shell around a list of content blocks.
	 *
	 * Table-based with inline styles: email clients have no cascade to rely on.
	 *
	 * @param array<int,array<string,mixed>> $blocks
	 */
	function reci_email_render( string $heading, array $blocks, string $preheader = '' ): string {
		$c    = reci_email_palette();
		$site = esc_html( get_bloginfo( 'name' ) ?: 'RECI Media Hub' );
		$home = esc_url( home_url( '/' ) );

		$body = '';
		foreach ( $blocks as $block ) {
			$body .= reci_email_render_block( (array) $block, $c );
		}

		$year    = esc_html( (string) gmdate( 'Y' ) );
		$contact = function_exists( 'reci_setting' ) ? (string) reci_setting( 'footer_email', '' ) : '';

		$logo_id  = function_exists( 'reci_setting' ) ? (int) reci_setting( 'branding_reci_logo', 0 ) : 0;
		$logo_url = $logo_id > 0 ? (string) wp_get_attachment_image_url( $logo_id, 'medium' ) : '';

		$masthead = '' !== $logo_url
			? '<img src="' . esc_url( $logo_url ) . '" alt="' . $site . '" width="200" style="display:block;border:0;max-width:200px;height:auto;" />'
			: '<span style="color:' . $c['navy'] . ';font-family:\'Arial Narrow\',Arial,sans-serif;font-size:22px;font-weight:bold;letter-spacing:.04em;text-transform:uppercase;">' . $site . '</span>';

		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>' . esc_html( $heading ) . '</title>
</head>
<body style="margin:0;padding:0;background:' . $c['ground'] . ';">
<div style="display:none;font-size:1px;color:' . $c['ground'] . ';max-height:0;overflow:hidden;">' . esc_html( $preheader ) . '</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:' . $c['ground'] . ';padding:24px 12px;">
<tr><td align="center">
  <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background:' . $c['surface'] . ';">
    <tr><td style="background:' . $c['navy'] . ';padding:10px 32px;font-family:Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#c9d4e8;">' . $site . '</td></tr>
    <tr><td style="background:#ffffff;padding:22px 32px;">
      <a href="' . $home . '" style="text-decoration:none;">' . $masthead . '</a>
    </td></tr>
    <tr><td style="height:4px;background:' . $c['yellow'] . ';font-size:0;line-height:0;">&nbsp;</td></tr>
    <tr><td style="padding:34px 32px 30px;font-family:Arial,Helvetica,sans-serif;">
      <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 18px;"><tr>
        <td width="12" style="width:12px;padding:0 12px 0 0;vertical-align:middle;">
          <div style="width:12px;height:12px;background:' . $c['yellow'] . ';font-size:0;line-height:0;">&nbsp;</div>
        </td>
        <td style="vertical-align:middle;">
          <h1 style="margin:0;font-family:\'Arial Narrow\',Arial,sans-serif;font-size:30px;line-height:1.1;color:' . $c['ink'] . ';font-weight:bold;">' . esc_html( $heading ) . '</h1>
        </td>
      </tr></table>
      ' . $body . '
    </td></tr>
    <tr><td style="background:' . $c['navy'] . ';padding:26px 32px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.7;color:#c9d4e8;">
      <p style="margin:0 0 8px;color:#ffffff;font-weight:bold;font-size:13px;">' . $site . '</p>
      <p style="margin:0 0 4px;"><a href="' . $home . '" style="color:' . $c['yellow'] . ';text-decoration:none;">' . esc_html( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ) . '</a></p>
      ' . ( '' !== $contact ? '<p style="margin:0 0 4px;">' . esc_html( $contact ) . '</p>' : '' ) . '
      <p style="margin:10px 0 0;color:#8ea3c9;">&copy; ' . $year . ' ' . $site . '. Sent because of activity on your account.</p>
    </td></tr>
  </table>
</td></tr>
</table>
</body></html>';
	}
}

if ( ! function_exists( 'reci_email_render_block' ) ) {
	/**
	 * Render one content block.
	 *
	 * Supported: text, button, list, note, details.
	 *
	 * @param array<string,mixed>  $block
	 * @param array<string,string> $c
	 */
	function reci_email_render_block( array $block, array $c ): string {
		$type = (string) ( $block['type'] ?? 'text' );

		if ( 'button' === $type ) {
			// Mirrors .btn-primary on the site: yellow ground, dark text.
			return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:8px 0 24px;"><tr><td style="background:' . $c['yellow'] . ';border-radius:6px;">
				<a href="' . esc_url( (string) ( $block['url'] ?? '#' ) ) . '" style="display:inline-block;padding:14px 28px;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;color:' . $c['ink'] . ';text-decoration:none;">' . esc_html( (string) ( $block['label'] ?? 'Open' ) ) . '</a>
			</td></tr></table>';
		}

		if ( 'list' === $type ) {
			$items = '';
			foreach ( (array) ( $block['items'] ?? [] ) as $item ) {
				$items .= '<li style="margin:0 0 7px;">' . esc_html( (string) $item ) . '</li>';
			}
			return '<ul style="margin:0 0 18px;padding-left:20px;font-size:15px;line-height:1.65;color:' . $c['ink'] . ';">' . $items . '</ul>';
		}

		if ( 'details' === $type ) {
			$rows = '';
			foreach ( (array) ( $block['rows'] ?? [] ) as $label => $value ) {
				$rows .= '<tr>
					<td style="padding:7px 14px 7px 0;font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:' . $c['muted'] . ';white-space:nowrap;vertical-align:top;">' . esc_html( (string) $label ) . '</td>
					<td style="padding:7px 0;font-size:15px;color:' . $c['ink'] . ';">' . esc_html( (string) $value ) . '</td>
				</tr>';
			}
			return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;width:100%;font-family:Arial,Helvetica,sans-serif;border-top:1px solid ' . $c['line'] . ';border-bottom:1px solid ' . $c['line'] . ';">' . $rows . '</table>';
		}

		if ( 'note' === $type ) {
			return '<p style="margin:0 0 18px;padding:13px 16px;background:' . $c['ground'] . ';border-left:3px solid ' . $c['yellow'] . ';font-size:14px;line-height:1.6;color:' . $c['ink'] . ';">' . esc_html( (string) ( $block['text'] ?? '' ) ) . '</p>';
		}

		// Plain paragraph. A raw URL is linked so the fallback stays clickable.
		$text = (string) ( $block['text'] ?? '' );
		return '<p style="margin:0 0 16px;font-size:15px;line-height:1.65;color:' . $c['ink'] . ';">' . esc_html( $text ) . '</p>';
	}
}

if ( ! function_exists( 'reci_email_plain_text' ) ) {
	/**
	 * Plain-text alternative built from the same blocks.
	 *
	 * @param array<int,array<string,mixed>> $blocks
	 */
	function reci_email_plain_text( string $heading, array $blocks ): string {
		$lines = [ $heading, str_repeat( '=', min( 60, strlen( $heading ) ) ), '' ];

		foreach ( $blocks as $block ) {
			$type = (string) ( $block['type'] ?? 'text' );

			if ( 'button' === $type ) {
				$lines[] = (string) ( $block['label'] ?? 'Open' ) . ': ' . (string) ( $block['url'] ?? '' );
			} elseif ( 'list' === $type ) {
				foreach ( (array) ( $block['items'] ?? [] ) as $item ) {
					$lines[] = '  - ' . (string) $item;
				}
			} elseif ( 'details' === $type ) {
				foreach ( (array) ( $block['rows'] ?? [] ) as $label => $value ) {
					$lines[] = $label . ': ' . $value;
				}
			} else {
				$lines[] = (string) ( $block['text'] ?? '' );
			}

			$lines[] = '';
		}

		$lines[] = '--';
		$lines[] = (string) ( get_bloginfo( 'name' ) ?: 'RECI Media Hub' ) . ' — ' . home_url( '/' );

		return implode( "\n", $lines );
	}
}

if ( ! function_exists( 'reci_send_email' ) ) {
	/**
	 * Send a branded transactional email.
	 *
	 * @param array<int,array<string,mixed>> $blocks
	 */
	function reci_send_email( string $to, string $subject, string $heading, array $blocks, string $preheader = '' ): bool {
		if ( ! is_email( $to ) ) {
			return false;
		}

		$html = reci_email_render( $heading, $blocks, $preheader );
		$text = reci_email_plain_text( $heading, $blocks );

		$from_name  = reci_email_from_name();
		$from_email = reci_email_from_address();

		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			sprintf( 'From: %s <%s>', $from_name, $from_email ),
		];

		// Attach the text/plain part so clients that prefer it get real copy
		// rather than a tag-stripped approximation of the HTML.
		$attach_alt = static function ( $phpmailer ) use ( $text ) {
			$phpmailer->AltBody = $text;
		};
		add_action( 'phpmailer_init', $attach_alt );

		// Capture the transport's own error so the log says why, not just that.
		$error   = '';
		$capture = static function ( $wp_error ) use ( &$error ) {
			$error = $wp_error instanceof WP_Error ? $wp_error->get_error_message() : '';
		};
		add_action( 'wp_mail_failed', $capture );

		$sent = wp_mail( $to, $subject, $html, $headers );

		remove_action( 'wp_mail_failed', $capture );
		remove_action( 'phpmailer_init', $attach_alt );

		reci_log_email( $to, $subject, $heading, (bool) $sent, $error );

		return (bool) $sent;
	}
}

if ( ! function_exists( 'reci_send_verification_email' ) ) {
	/**
	 * Email-verification message.
	 *
	 * Registration, resend, and the collaborator application all created this
	 * message separately with slightly different wording. One copy now.
	 */
	function reci_send_verification_email( int $user_id, string $email, string $name, string $token ): bool {
		$verify_url = add_query_arg(
			[
				'action' => 'reci_verify_email',
				'u'      => $user_id,
				't'      => $token,
			],
			admin_url( 'admin-post.php' )
		);

		$name = trim( $name );

		return reci_send_email(
			$email,
			__( 'Verify your email address', 'reci-media-hub' ),
			__( 'Confirm your email address', 'reci-media-hub' ),
			[
				[
					'type' => 'text',
					'text' => $name !== ''
						? sprintf( __( 'Hello %s, welcome to RECI.', 'reci-media-hub' ), $name )
						: __( 'Welcome to RECI.', 'reci-media-hub' ),
				],
				[
					'type' => 'text',
					'text' => __( 'Confirm this address to activate your account. The link signs you in, so you can pick up right where you left off.', 'reci-media-hub' ),
				],
				[
					'type'  => 'button',
					'label' => __( 'Verify my email', 'reci-media-hub' ),
					'url'   => $verify_url,
				],
				[
					'type' => 'note',
					'text' => __( 'If you did not create a RECI account, you can ignore this email and nothing will happen.', 'reci-media-hub' ),
				],
			],
			__( 'Confirm your email address to activate your RECI account.', 'reci-media-hub' )
		);
	}
}

if ( ! function_exists( 'reci_smtp_config' ) ) {
	/**
	 * SMTP transport settings.
	 *
	 * All of it comes from Settings → Email, including the password. Defining
	 * RECI_SMTP_PASSWORD in wp-config.php overrides the stored value, which keeps
	 * the secret out of the database and therefore out of database backups.
	 *
	 * @return array<string,mixed>
	 */
	function reci_smtp_config(): array {
		return [
			'host'       => function_exists( 'reci_setting' ) ? trim( (string) reci_setting( 'email_smtp_host', '' ) ) : '',
			'port'       => function_exists( 'reci_setting' ) ? (int) reci_setting( 'email_smtp_port', 587 ) : 587,
			'encryption' => function_exists( 'reci_setting' ) ? (string) reci_setting( 'email_smtp_encryption', 'tls' ) : 'tls',
			'username'   => function_exists( 'reci_setting' ) ? trim( (string) reci_setting( 'email_smtp_username', '' ) ) : '',
			// The wp-config constant wins when present; otherwise the stored
			// setting is used, which is how the site is configured by default.
			'password'   => defined( 'RECI_SMTP_PASSWORD' ) && '' !== (string) RECI_SMTP_PASSWORD
				? (string) RECI_SMTP_PASSWORD
				: ( function_exists( 'reci_setting' ) ? (string) reci_setting( 'email_smtp_password', '' ) : '' ),
		];
	}
}

if ( ! function_exists( 'reci_configure_smtp' ) ) {
	/**
	 * Route mail through SMTP when a host is configured.
	 *
	 * With no host set this does nothing, so the site keeps using the server's
	 * own mail() exactly as before — configuring SMTP is opt-in.
	 *
	 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer
	 */
	function reci_configure_smtp( $phpmailer ): void {
		$c = reci_smtp_config();

		if ( '' === $c['host'] ) {
			return;
		}

		$phpmailer->isSMTP();
		$phpmailer->Host = $c['host'];
		$phpmailer->Port = $c['port'] > 0 ? $c['port'] : 587;

		if ( in_array( $c['encryption'], [ 'tls', 'ssl' ], true ) ) {
			$phpmailer->SMTPSecure = $c['encryption'];
		} else {
			$phpmailer->SMTPSecure  = '';
			$phpmailer->SMTPAutoTLS = false;
		}

		// Only authenticate when a username exists; some relays accept the host
		// unauthenticated and offering empty credentials makes them reject it.
		if ( '' !== $c['username'] ) {
			$phpmailer->SMTPAuth = true;
			$phpmailer->Username = $c['username'];
			$phpmailer->Password = $c['password'];
		} else {
			// WordPress reuses one PHPMailer instance per request, so credentials
			// from an earlier send would otherwise linger on the object.
			$phpmailer->SMTPAuth = false;
			$phpmailer->Username = '';
			$phpmailer->Password = '';
		}
	}
}

add_action( 'phpmailer_init', 'reci_configure_smtp' );

if ( ! function_exists( 'reci_handle_test_email' ) ) {
	/**
	 * Send a real test message and report what actually happened.
	 *
	 * "It didn't work" is useless when diagnosing mail, so the transport's own
	 * error is carried back to the settings screen.
	 */
	function reci_handle_test_email(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do that.', 'reci-media-hub' ), '', [ 'response' => 403 ] );
		}

		check_admin_referer( 'reci_send_test_email' );

		$back = add_query_arg( [ 'page' => 'reci-settings', 'tab' => 'email' ], admin_url( 'admin.php' ) );
		$user = wp_get_current_user();
		$to   = (string) $user->user_email;

		$error = '';
		$capture = static function ( $wp_error ) use ( &$error ) {
			$error = $wp_error instanceof WP_Error ? $wp_error->get_error_message() : '';
		};
		add_action( 'wp_mail_failed', $capture );

		$sent = reci_send_email(
			$to,
			__( 'RECI test email', 'reci-media-hub' ),
			__( 'Mail is working', 'reci-media-hub' ),
			[
				[ 'type' => 'text', 'text' => __( 'This is a test message from your RECI settings screen. If you are reading it, transactional email is reaching real inboxes.', 'reci-media-hub' ) ],
				[
					'type' => 'details',
					'rows' => [
						__( 'Sent to', 'reci-media-hub' )   => $to,
						__( 'Transport', 'reci-media-hub' ) => '' !== reci_smtp_config()['host'] ? sprintf( 'SMTP (%s)', reci_smtp_config()['host'] ) : __( 'PHP mail()', 'reci-media-hub' ),
						__( 'From', 'reci-media-hub' )      => reci_email_from_address(),
					],
				],
			],
			__( 'Test message from your RECI settings screen.', 'reci-media-hub' )
		);

		remove_action( 'wp_mail_failed', $capture );

		$args = $sent
			? [ 'reci_mail_test' => 'sent' ]
			: [ 'reci_mail_test' => 'failed', 'reci_mail_error' => rawurlencode( $error ?: __( 'Unknown transport error.', 'reci-media-hub' ) ) ];

		wp_safe_redirect( add_query_arg( $args, $back ) );
		exit;
	}
}

add_action( 'admin_post_reci_send_test_email', 'reci_handle_test_email' );

if ( ! function_exists( 'reci_render_test_email_notice' ) ) {
	/**
	 * Report the result of a test send on the settings screen.
	 */
	function reci_render_test_email_notice(): void {
		if ( ! isset( $_GET['reci_mail_test'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if ( 'sent' === $_GET['reci_mail_test'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				esc_html__( 'Test email sent. If it does not arrive, the transport accepted it and the problem is downstream — check SPF and DKIM for the sending domain.', 'reci-media-hub' )
			);
			return;
		}

		$error = isset( $_GET['reci_mail_error'] ) ? sanitize_text_field( rawurldecode( wp_unslash( $_GET['reci_mail_error'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html__( 'Test email failed:', 'reci-media-hub' ),
			esc_html( $error )
		);
	}
}

add_action( 'admin_notices', 'reci_render_test_email_notice' );

if ( ! function_exists( 'reci_email_log_table' ) ) {
	function reci_email_log_table(): string {
		global $wpdb;
		return $wpdb->prefix . 'reci_email_log';
	}
}

if ( ! function_exists( 'reci_log_email' ) ) {
	/**
	 * Record one send attempt.
	 *
	 * The rendered body is deliberately not stored. It is bulky, and verification
	 * messages contain a working sign-in link — a log holding those is a
	 * credential store wearing a friendlier name.
	 */
	function reci_log_email( string $to, string $subject, string $heading, bool $sent, string $error = '' ): void {
		global $wpdb;

		$user = get_user_by( 'email', $to );
		$host = reci_smtp_config()['host'];

		$wpdb->insert(
			reci_email_log_table(),
			[
				'user_id'    => $user instanceof WP_User ? (int) $user->ID : 0,
				'recipient'  => $to,
				'subject'    => $subject,
				'heading'    => $heading,
				'transport'  => '' !== $host ? 'smtp' : 'mail()',
				'status'     => $sent ? 'sent' : 'failed',
				'error'      => $error,
				'created_at' => current_time( 'mysql' ),
			],
			[ '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
		);
	}
}

if ( ! function_exists( 'reci_email_log_retention_days' ) ) {
	/**
	 * How long log rows are kept. Zero keeps them indefinitely.
	 */
	function reci_email_log_retention_days(): int {
		$days = function_exists( 'reci_setting' ) ? (int) reci_setting( 'email_log_retention', 30 ) : 30;
		return max( 0, $days );
	}
}

if ( ! function_exists( 'reci_prune_email_log' ) ) {
	/**
	 * Delete log rows past the retention window.
	 */
	function reci_prune_email_log(): int {
		$days = reci_email_log_retention_days();
		if ( 0 === $days ) {
			return 0;
		}

		global $wpdb;
		$table  = reci_email_log_table();
		$cutoff = gmdate( 'Y-m-d H:i:s', strtotime( sprintf( '-%d days', $days ), (int) current_time( 'timestamp' ) ) );

		return (int) $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE created_at < %s", $cutoff ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}

add_action( 'reci_prune_email_log', 'reci_prune_email_log' );

if ( ! function_exists( 'reci_schedule_email_log_pruning' ) ) {
	function reci_schedule_email_log_pruning(): void {
		if ( ! wp_next_scheduled( 'reci_prune_email_log' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'reci_prune_email_log' );
		}
	}
}

add_action( 'init', 'reci_schedule_email_log_pruning' );

if ( ! function_exists( 'reci_get_email_log' ) ) {
	/**
	 * Recent log rows, newest first.
	 *
	 * @return array<int,object>
	 */
	function reci_get_email_log( int $limit = 100 ): array {
		global $wpdb;
		$table = reci_email_log_table();

		return (array) $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} ORDER BY created_at DESC, id DESC LIMIT %d", $limit ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}
