<?php
/**
 * Template Name: Sign In
 *
 * Sign-in page: split-screen layout with a quote panel on the right
 * and a centered credential form on the left.
 *
 * All content is static placeholder — no WP_Query calls.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class=" bg-slate-100 flex">

	<!-- =========================================================
	     LEFT PANEL — Sign-in form
	     ========================================================= -->
	<div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-8 sm:px-16 xl:px-28 py-5">

		<div class="w-full max-w-md flex flex-col gap-14">

			<!-- Logo + Title -->
			<div class="flex flex-col items-center gap-10">

				<!-- Logo -->
				<div class="flex hidden items-center gap-5">
					<img
						src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/' . rawurlencode( 'reci-collab.png' ) ); ?>"
						alt="<?php echo esc_attr__( 'RECI Logo', 'reci-media-hub' ); ?>"
						class="h-9 w-auto"
					/>
				</div>

				<!-- Heading + Sub-heading -->
				<div class="flex flex-col items-center gap-3 text-center">
					<h1 class="text-3xl font-bold font-heading text-neutral-800">
						<?php echo esc_html__( 'Welcome Back', 'reci-media-hub' ); ?>
					</h1>
					<p class="text-lg font-normal text-neutral-600">
						<?php echo esc_html__( 'Kindly fill the details below to get back in!', 'reci-media-hub' ); ?>
					</p>
				</div>

			</div><!-- /Logo + Title -->

			<!-- Form -->
			<div class="flex flex-col gap-10">

				<?php
				$error_code = sanitize_text_field( $_GET['login_error'] ?? '' );
				$success    = sanitize_text_field( $_GET['reg_success'] ?? '' );
				$resetpass  = sanitize_text_field( $_GET['resetpass'] ?? '' );
				
				if ( $success === 'check_email' ) : ?>
					<div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700" role="alert">
						<?php esc_html_e( 'Note: Your registration has been received. A verification email has been sent to you. You will need to verify your email address before you can log in.', 'reci-media-hub' ); ?>
					</div>
				<?php elseif ( $success === 'email_resent' ) : ?>
					<div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700" role="alert">
						<?php esc_html_e( 'Verification email has been resent! Please check your inbox.', 'reci-media-hub' ); ?>
					</div>
				<?php elseif ( $resetpass === 'complete' ) : ?>
					<div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700" role="alert">
						<?php esc_html_e( 'Your password has been successfully reset. You can now log in with your new password.', 'reci-media-hub' ); ?>
					</div>
				<?php elseif ( $error_code ) : ?>
					<div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700" role="alert">
						<?php if ( 'unverified_email' === $error_code ) : ?>
							<?php esc_html_e( 'Please verify your email address before logging in.', 'reci-media-hub' ); ?>
							<?php if ( ! empty( $_GET['u'] ) ) : ?>
								<br>
								<a href="<?php echo esc_url( add_query_arg( [ 'action' => 'reci_resend_verify', 'u' => rawurlencode( $_GET['u'] ) ], admin_url('admin-post.php') ) ); ?>" class="underline hover:text-red-800 font-medium mt-1 inline-block">
									<?php esc_html_e( 'Click here to resend verification email.', 'reci-media-hub' ); ?>
								</a>
							<?php endif; ?>
						<?php elseif ( 'invalid_verify_link' === $error_code || 'invalid_verify_token' === $error_code ) : ?>
							<?php esc_html_e( 'The verification link is invalid or has expired. Please log in to request a new one.', 'reci-media-hub' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Incorrect email or password. Please try again.', 'reci-media-hub' ); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<form
					method="post"
					action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>"
					class="flex flex-col gap-5"
				>
					<?php wp_nonce_field( 'reci_sign_in', 'reci_sign_in_nonce' ); ?>

					<!-- Email -->
					<div class="flex flex-col gap-2">
						<label
							for="reci-email"
							class="text-sm font-medium text-neutral-800 leading-6"
						>
							<?php echo esc_html__( 'Email', 'reci-media-hub' ); ?>
						</label>
						<input
							type="email"
							id="reci-email"
							name="log"
							placeholder="<?php echo esc_attr__( 'Johndoe@email.com', 'reci-media-hub' ); ?>"
							autocomplete="email"
							class="w-full px-4 py-5 rounded-lg outline outline-[0.5px] outline-zinc-400 text-sm font-normal text-neutral-800 placeholder-zinc-400 bg-white focus:outline-[#003594] focus:outline-2 transition-all"
						/>
					</div>

					<!-- Password -->
					<div class="flex flex-col gap-2">
						<label
							for="reci-password"
							class="text-sm font-medium text-neutral-800 leading-6"
						>
							<?php echo esc_html__( 'Password', 'reci-media-hub' ); ?>
						</label>
						<div class="relative">
							<input
								type="password"
								id="reci-password"
								name="pwd"
								placeholder="<?php echo esc_attr__( 'Enter Your Password', 'reci-media-hub' ); ?>"
								autocomplete="current-password"
								class="w-full px-4 py-5 pr-12 rounded-lg outline outline-[0.5px] outline-zinc-400 text-sm font-normal text-neutral-800 placeholder-zinc-400 bg-white focus:outline-[#003594] focus:outline-2 transition-all"
							/>
							<!-- Eye toggle (static placeholder) -->
							<button
								type="button"
								aria-label="<?php echo esc_attr__( 'Toggle password visibility', 'reci-media-hub' ); ?>"
								class="absolute right-4 top-1/2 -translate-y-1/2 text-neutral-600 hover:text-neutral-700"
							>
								<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
									<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
									<path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
								</svg>
							</button>
						</div>

						<!-- Forgot password link -->
						<div class="text-right">
							<a
								href="<?php echo esc_url( wp_lostpassword_url() ); ?>"
								class="text-sm font-medium text-[#003594] hover:underline"
							>
								<?php echo esc_html__( 'Forgot Password?', 'reci-media-hub' ); ?>
							</a>
						</div>
					</div>

					<!-- Hidden redirect -->
					<input type="hidden" name="redirect_to" value="<?php echo esc_attr( home_url( '/' ) ); ?>" />

					<!-- Submit -->
						<button
							type="submit"
							class="btn btn-secondary btn-md btn-block"
						>
							<?php echo esc_html__( 'Sign In', 'reci-media-hub' ); ?>
						</button>

				</form><!-- /form -->

				<!-- Divider -->
				<div class="flex hidden items-center gap-10">
					<div class="flex-1 h-px bg-zinc-400"></div>
					<span class="text-base font-medium text-neutral-400">
						<?php echo esc_html__( 'OR', 'reci-media-hub' ); ?>
					</span>
					<div class="flex-1 h-px bg-zinc-400"></div>
				</div>

				<!-- Google SSO (placeholder — not wired) -->
				<button
					type="button"
					class="w-full hidden px-10 py-4 rounded-lg outline outline-1 outline-neutral-500 flex justify-center items-center gap-2.5 bg-white hover:bg-slate-50 transition-colors"
				>
					<svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M19.6 10.23c0-.68-.06-1.36-.18-2H10v3.79h5.39a4.6 4.6 0 01-2 3.02v2.5h3.24C18.34 15.78 19.6 13.18 19.6 10.23z" fill="#4285F4"/>
						<path d="M10 20c2.7 0 4.96-.9 6.62-2.43l-3.24-2.5c-.9.6-2.04.96-3.38.96-2.6 0-4.8-1.75-5.59-4.1H1.07v2.57A9.99 9.99 0 0010 20z" fill="#34A853"/>
						<path d="M4.41 11.93A5.98 5.98 0 014.1 10c0-.67.11-1.32.31-1.93V5.5H1.07A9.99 9.99 0 000 10c0 1.62.39 3.15 1.07 4.5l3.34-2.57z" fill="#FBBC05"/>
						<path d="M10 3.97c1.47 0 2.79.5 3.82 1.5l2.87-2.87C14.95 1 12.7 0 10 0A9.99 9.99 0 001.07 5.5l3.34 2.57C5.2 5.72 7.4 3.97 10 3.97z" fill="#EA4335"/>
					</svg>
					<span class="text-sm font-medium text-neutral-800">
						<?php echo esc_html__( 'Log in with Google', 'reci-media-hub' ); ?>
					</span>
				</button>

				<!-- Register link -->
				<p class="text-center text-base">
					<span class="text-neutral-400 font-medium">
						<?php echo esc_html__( "Don't have an account yet? ", 'reci-media-hub' ); ?>
					</span>
					<a
						href="<?php echo esc_url( reci_get_sign_up_url() ); ?>"
						class="text-neutral-800 font-bold hover:text-[#003594] transition-colors"
					>
						<?php echo esc_html__( 'Join RECI', 'reci-media-hub' ); ?>
					</a>
				</p>

			</div><!-- /form area -->

		</div><!-- /max-w-md -->

	</div><!-- /LEFT PANEL -->

	<!-- =========================================================
	     RIGHT PANEL — Quote / inspirational image panel
	     Hidden on mobile, shown from lg breakpoint.
	     ========================================================= -->
	<div class="hidden lg:flex w-1/2 relative bg-gradient-to-b from-black/0 to-black p-14 flex-col justify-center items-center overflow-hidden">

		<!-- Background image placeholder -->
		<div class="absolute inset-0 bg-[#003594] opacity-80"></div>
		<img
			src="https://placehold.co/720x1024/003594/003594"
			alt=""
			aria-hidden="true"
			class="absolute inset-0 w-full h-full object-cover mix-blend-overlay"
		/>

		<!-- Quote card -->
		<div class="relative z-10 w-full max-w-md p-10 bg-neutral-800 rounded-lg flex flex-col gap-5">

			<!-- Card header -->
			<div class="flex flex-col gap-5">
				<p class="text-center text-amber-400 text-3xl font-bold font-heading leading-[1.6]">
					<?php echo esc_html__( 'Inspiring Quotes', 'reci-media-hub' ); ?>
				</p>
				<div class="w-full h-px bg-neutral-400"></div>
			</div>

			<!-- Quote body + pagination -->
			<div class="flex flex-col gap-10">
				<blockquote class="text-center text-white text-base font-normal leading-6">
					<?php echo esc_html__( 'I for one believe that if you give people a thorough understanding of what confronts them and the basic causes that produce it, they\'ll create their own program, and when the people create a program, you get action.', 'reci-media-hub' ); ?>
				</blockquote>

				<!-- Pagination dots -->
				<div class="flex justify-center items-center gap-2.5">
					<div class="flex-1 h-0.5 bg-amber-400 max-w-[24px]"></div>
					<div class="flex-1 h-0.5 bg-zinc-400 max-w-[24px]"></div>
					<div class="flex-1 h-0.5 bg-zinc-400 max-w-[24px]"></div>
					<div class="flex-1 h-0.5 bg-zinc-400 max-w-[24px]"></div>
				</div>
			</div>

		</div><!-- /Quote card -->

	</div><!-- /RIGHT PANEL -->

</div><!-- /min-h-screen -->

<script>
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('button[aria-label="Toggle password visibility"]').forEach(function(btn) {
		btn.addEventListener('click', function() {
			var input = btn.previousElementSibling;
			if (!input || input.tagName !== 'INPUT') return;
			
			if (input.type === 'password') {
				input.type = 'text';
				btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>';
			} else {
				input.type = 'password';
				btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>';
			}
		});
	});
});
</script>

<?php get_footer(); ?>
