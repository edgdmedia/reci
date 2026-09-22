<?php
/**
 * Dependency-free test runner.
 *
 * Usage: php tests/run.php
 * Exit code 0 when everything passes, 1 otherwise.
 */

require __DIR__ . '/bootstrap.php';

$GLOBALS['reci_test_results'] = [
	'pass'     => 0,
	'fail'     => 0,
	'failures' => [],
];

/**
 * Assert a condition holds.
 */
function reci_assert( bool $condition, string $message ): void {
	if ( $condition ) {
		$GLOBALS['reci_test_results']['pass']++;
		return;
	}

	$GLOBALS['reci_test_results']['fail']++;
	$GLOBALS['reci_test_results']['failures'][] = $message;
}

/**
 * Assert strict equality, reporting both sides on failure.
 */
function reci_assert_same( $expected, $actual, string $message ): void {
	reci_assert(
		$expected === $actual,
		sprintf(
			'%s — expected %s, got %s',
			$message,
			var_export( $expected, true ),
			var_export( $actual, true )
		)
	);
}

foreach ( glob( __DIR__ . '/test-*.php' ) as $test_file ) {
	require $test_file;
}

$results = $GLOBALS['reci_test_results'];

foreach ( $results['failures'] as $failure ) {
	fwrite( STDERR, "FAIL: {$failure}\n" );
}

printf( "%d passed, %d failed\n", $results['pass'], $results['fail'] );

exit( $results['fail'] > 0 ? 1 : 0 );
