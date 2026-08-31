<?php

return [
	'core' => [
		'home'            => [ 'title' => 'Home', 'template' => 'templates/page/template-homepage.php' ],
		'articles'        => [ 'title' => 'Articles', 'template' => '' ],
		'about'           => [ 'title' => 'About', 'template' => 'templates/page/template-about.php' ],
		'learn'           => [ 'title' => 'Learn', 'template' => 'templates/page/template-learn.php' ],
		'framework'       => [ 'title' => 'The Six Spheres of RECI', 'template' => 'templates/page/template-spheres.php' ],
		'glossary'        => [ 'title' => 'Glossary', 'template' => 'templates/page/template-glossary.php' ],
		'sponsorship'     => [ 'title' => 'Sponsorship', 'template' => 'templates/page/template-sponsorship.php' ],
		'community'       => [ 'title' => 'Community', 'template' => '' ],
		'donate'          => [ 'title' => 'Donate', 'template' => 'templates/page/template-donate.php' ],
		'sign-in'         => [ 'title' => 'Sign In', 'template' => 'templates/page/template-sign-in.php' ],
		'sign-up'         => [ 'title' => 'Sign Up', 'template' => 'templates/page/template-sign-up.php' ],
		'become-a-collaborator' => [ 'title' => 'Become a Collaborator', 'template' => 'templates/page/template-become-a-collaborator.php' ],
		'forgot-password' => [ 'title' => 'Forgot Password', 'template' => 'templates/page/template-forgot-password.php' ],
		'reset-password'  => [ 'title' => 'Reset Password',  'template' => 'templates/page/template-reset-password.php' ],
		'verify-email'    => [ 'title' => 'Verify Email',    'template' => 'templates/page/template-verify-email.php' ],
		'submit'          => [ 'title' => 'Submit Content',  'template' => 'templates/page/template-submit-content.php' ],
	],
	'dashboard' => [
		'parent'   => [ 'slug' => 'dashboard', 'title' => 'Dashboard', 'template' => 'templates/page/dashboard/template-dashboard.php' ],
		'children' => [
			'my-content' => [ 'title' => 'Dashboard – My Content', 'template' => 'templates/page/dashboard/template-dashboard-my-content.php' ],
			'submit'     => [ 'title' => 'Dashboard – Submit', 'template' => 'templates/page/dashboard/template-dashboard-submit.php' ],
			'bookmarks'  => [ 'title' => 'Dashboard – Bookmarks', 'template' => 'templates/page/dashboard/template-dashboard-bookmarks.php' ],
			'journal'    => [ 'title' => 'Dashboard – Journal', 'template' => 'templates/page/dashboard/template-dashboard-journal.php' ],
			'comments'   => [ 'title' => 'Dashboard – Comments', 'template' => 'templates/page/dashboard/template-dashboard-comments.php' ],
			'profile'    => [ 'title' => 'Dashboard – Profile', 'template' => 'templates/page/dashboard/template-dashboard-profile.php' ],
			'settings'   => [ 'title' => 'Dashboard – Settings', 'template' => 'templates/page/dashboard/template-dashboard-settings.php' ],
		],
	],
];
