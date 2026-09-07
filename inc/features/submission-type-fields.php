<?php
/**
 * Per-content-type submission fields.
 *
 * The wizard's later steps ask only what the chosen type actually needs. Before
 * this, every type collected the same generic set, so a submitted podcast had no
 * audio URL and a submitted video had no video URL — staff filled those in by
 * hand in wp-admin, which is the dependency the dashboard work is removing.
 *
 * Meta keys here are the same ones the wp-admin metaboxes read and write
 * (inc/content/meta-fields.php), so a submission and a staff edit populate the
 * same fields rather than two parallel sets.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'reci_submission_type_fields' ) ) {
	/**
	 * Fields for one front-end content type, or all of them when $type is ''.
	 *
	 * Each field: key (meta key), label, type, and optional placeholder/help/
	 * options/required.
	 *
	 * @return array<string,array<int,array<string,mixed>>>|array<int,array<string,mixed>>
	 */
	function reci_submission_type_fields( string $type = '' ) {
		$source = [
			[ 'key' => '_post_source_name',    'label' => __( 'Original Publication', 'reci-media-hub' ), 'type' => 'text', 'placeholder' => 'e.g. The Atlantic', 'help' => __( 'If this appeared somewhere else first.', 'reci-media-hub' ) ],
			[ 'key' => '_post_source_url',     'label' => __( 'Original URL', 'reci-media-hub' ),         'type' => 'url',  'placeholder' => 'https://' ],
			[ 'key' => '_post_canonical_url',  'label' => __( 'Canonical URL', 'reci-media-hub' ),        'type' => 'url',  'placeholder' => 'https://', 'help' => __( 'Tells search engines which copy is authoritative. Usually the original URL.', 'reci-media-hub' ) ],
		];

		$all = [
			'article'  => $source,
			'blog'     => $source,
			'exhibit'  => $source,
			'other'    => $source,

			'video'    => [
				[ 'key' => '_reci_video_url',            'label' => __( 'Video URL', 'reci-media-hub' ),      'type' => 'url',    'placeholder' => 'https://youtube.com/watch?v=…', 'required' => true, 'help' => __( 'YouTube, Vimeo, or a direct link to the file.', 'reci-media-hub' ) ],
				[ 'key' => '_reci_video_duration_label', 'label' => __( 'Duration', 'reci-media-hub' ),       'type' => 'text',   'placeholder' => 'e.g. 12:45' ],
			],

			'podcast'  => [
				[ 'key' => '_reci_podcast_audio_url',      'label' => __( 'Audio URL', 'reci-media-hub' ),      'type' => 'url',    'placeholder' => 'https://…/episode.mp3', 'required' => true, 'help' => __( 'A direct link to the audio file, or upload it above.', 'reci-media-hub' ) ],
				[ 'key' => '_reci_podcast_duration_label', 'label' => __( 'Duration', 'reci-media-hub' ),       'type' => 'text',   'placeholder' => 'e.g. 42:10' ],
				[ 'key' => '_reci_podcast_episode_number', 'label' => __( 'Episode Number', 'reci-media-hub' ), 'type' => 'number', 'placeholder' => 'e.g. 12' ],
				[ 'key' => '_reci_podcast_season_number',  'label' => __( 'Season Number', 'reci-media-hub' ),  'type' => 'number', 'placeholder' => 'e.g. 2' ],
				[ 'key' => '_reci_podcast_transcript_url', 'label' => __( 'Transcript URL', 'reci-media-hub' ), 'type' => 'url',    'placeholder' => 'https://' ],
			],

			// A resource's payload is the upload itself, which the shared step
			// already collects. Nothing type-specific to ask for.
			'document' => [],
		];

		if ( '' === $type ) {
			return $all;
		}

		return $all[ $type ] ?? [];
	}
}

if ( ! function_exists( 'reci_save_submission_type_fields' ) ) {
	/**
	 * Persist the type-specific fields for one submission.
	 *
	 * Only keys declared for that type are written, so a crafted POST cannot set
	 * arbitrary meta.
	 */
	function reci_save_submission_type_fields( int $post_id, string $type ): void {
		foreach ( reci_submission_type_fields( $type ) as $field ) {
			$key = (string) $field['key'];
			$raw = $_POST[ $key ] ?? '';

			if ( ! is_string( $raw ) ) {
				continue;
			}

			$raw = wp_unslash( $raw );

			switch ( $field['type'] ) {
				case 'url':
					$value = esc_url_raw( $raw );
					break;
				case 'number':
					$value = '' === trim( $raw ) ? '' : (string) absint( $raw );
					break;
				default:
					$value = sanitize_text_field( $raw );
			}

			if ( '' !== $value ) {
				update_post_meta( $post_id, $key, $value );
			}
		}

		// Platform and external id are derivable from the URL, so the form does
		// not ask for them.
		if ( 'video' === $type ) {
			$url = (string) get_post_meta( $post_id, '_reci_video_url', true );
			if ( '' !== $url ) {
				update_post_meta( $post_id, '_reci_video_platform', reci_detect_video_platform( $url ) );

				$external_id = reci_detect_video_id( $url );
				if ( '' !== $external_id ) {
					update_post_meta( $post_id, '_reci_video_external_id', $external_id );
				}
			}
		}
	}
}

if ( ! function_exists( 'reci_detect_video_platform' ) ) {
	/**
	 * Platform slug from a video URL, matching the metabox's own options.
	 */
	function reci_detect_video_platform( string $url ): string {
		$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );

		if ( str_contains( $host, 'youtube' ) || str_contains( $host, 'youtu.be' ) ) {
			return 'youtube';
		}
		if ( str_contains( $host, 'vimeo' ) ) {
			return 'vimeo';
		}

		return 'file';
	}
}

if ( ! function_exists( 'reci_detect_video_id' ) ) {
	/**
	 * YouTube or Vimeo id from a URL, empty when it is neither.
	 */
	function reci_detect_video_id( string $url ): string {
		if ( preg_match( '#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})#', $url, $m ) ) {
			return $m[1];
		}
		if ( preg_match( '#vimeo\.com/(?:video/)?(\d+)#', $url, $m ) ) {
			return $m[1];
		}

		return '';
	}
}
