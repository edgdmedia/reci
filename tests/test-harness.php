<?php
/**
 * The harness must be able to fail. A runner that always passes is worse
 * than no runner, so prove both directions here.
 */

reci_assert_same( 'a', 'a', 'harness: identical strings are equal' );
reci_assert( true, 'harness: true is truthy' );

// Prove failure detection without failing the suite: run the comparison the
// runner uses and check it returns false.
reci_assert_same( false, ( 'a' === 'b' ), 'harness: differing strings are not equal' );

reci_test_stub_options( [ 'reci_demo' => 'seeded' ] );
reci_assert_same( 'seeded', get_option( 'reci_demo' ), 'harness: option stub reads back' );
reci_assert_same( 'fallback', get_option( 'reci_missing', 'fallback' ), 'harness: option stub falls back' );
