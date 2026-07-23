<?php
/**
 * Template Name: Reset Password
 *
 * Password reset page: split-screen layout, new password inputs.
 *
 * All content is static placeholder — no WP_Query calls.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="min-h-screen bg-slate-100 flex">

	<!-- =========================================================
	     LEFT PANEL — Reset-password form
	     ========================================================= -->
	<div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-8 sm:px-16 xl:px-28 py-20">

		<div class="w-full max-w-md flex flex-col gap-14">

			<!-- Logo + Title -->
			<div class="flex flex-col items-center gap-10">

				<!-- Logo -->
				<div class="flex items-center gap-5">
					<img
						src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/' . rawurlencode( 'RECI Logo - Version 2 1.png' ) ); ?>"
						alt="<?php echo esc_attr__( 'RECI Logo', 'reci-media-hub' ); ?>"
						class="h-9 w-auto"
					/>
				</div>

				<!-- Heading + Sub-heading -->
				<div class="flex flex-col items-center gap-3 text-center">
					<h1 class="text-3xl font-bold font-heading text-neutral-800">
						<?php echo esc_html__( 'Create New Password', 'reci-media-hub' ); ?>
					</h1>
					<p class="text-lg font-normal text-neutral-600">
						<?php echo esc_html__( "Your new password must be different from previous used passwords.", 'reci-media-hub' ); ?>
					</p>
				</div>

			</div><!-- /Logo + Title -->

			<!-- Form -->
			<div class="flex flex-col gap-10">

				<?php
				$error_code = sanitize_text_field( $_GET['error'] ?? '' );
				if ( $error_code ) : ?>
					<div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700" role="alert">
						<?php if ( 'expiredkey' === $error_code || 'invalidkey' === $error_code ) : ?>
							<?php esc_html_e( 'Your password reset link is invalid or has expired. Please request a new one.', 'reci-media-hub' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'An error occurred while trying to reset your password. Please try again.', 'reci-media-hub' ); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<form
					method="post"
					action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
					class="flex flex-col gap-5"
				>
					<input type="hidden" name="action" value="reci_reset_password" />
					<?php wp_nonce_field( 'reci_reset_password', 'reci_reset_nonce' ); ?>
					<input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( wp_unslash( $_GET['login'] ?? '' ) ); ?>" autocomplete="off" />
					<input type="hidden" name="rp_key" value="<?php echo esc_attr( wp_unslash( $_GET['key'] ?? '' ) ); ?>" />

					<!-- Password -->
					<div class="flex flex-col gap-2">
						<label
							for="pass1"
							class="text-sm font-medium text-neutral-800 leading-6"
						>
							<?php echo esc_html__( 'Password', 'reci-media-hub' ); ?>
						</label>
						<div class="relative">
							<input
								type="password"
								id="pass1"
								name="pass1"
								placeholder="<?php echo esc_attr__( 'Enter New Password', 'reci-media-hub' ); ?>"
								autocomplete="new-password"
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
					</div>

					<!-- Confirm Password -->
					<div class="flex flex-col gap-2">
						<label
							for="pass2"
							class="text-sm font-medium text-neutral-800 leading-6"
						>
							<?php echo esc_html__( 'Confirm Password', 'reci-media-hub' ); ?>
						</label>
						<div class="relative">
							<input
								type="password"
								id="pass2"
								name="pass2"
								placeholder="<?php echo esc_attr__( 'Confirm New Password', 'reci-media-hub' ); ?>"
								autocomplete="new-password"
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
					</div>

					<!-- Submit -->
					<button
						type="submit"
						class="btn btn-secondary btn-md btn-block"
					>
						<?php echo esc_html__( 'Reset Password', 'reci-media-hub' ); ?>
					</button>

				</form><!-- /form -->

			</div><!-- /form area -->

		</div><!-- /max-w-md -->

	</div><!-- /LEFT PANEL -->

	<!-- =========================================================
	     RIGHT PANEL — Quote / inspirational image panel
	     ========================================================= -->
	<div class="hidden lg:flex w-1/2 relative bg-gradient-to-b from-black/0 to-black p-14 flex-col justify-end items-center overflow-hidden">

		<!-- Background colour overlay -->
		<div class="absolute inset-0 bg-[#003594] opacity-80"></div>
		<img
			src="https://placehold.co/720x1024/003594/003594"
			alt=""
			aria-hidden="true"
			class="absolute inset-0 w-full h-full object-cover mix-blend-overlay"
		/>

		<!-- Quote card -->
		<div class="relative z-10 w-full max-w-md p-10 bg-neutral-800 rounded-lg flex flex-col gap-5">

			<div class="flex flex-col gap-5">
				<p class="text-center text-amber-400 text-3xl font-bold font-heading leading-[1.6]">
					<?php echo esc_html__( 'Inspiring Quotes', 'reci-media-hub' ); ?>
				</p>
				<div class="w-full h-px bg-neutral-400"></div>
			</div>

			<div class="flex flex-col gap-10">
				<blockquote class="text-center text-white text-base font-normal leading-6">
					<?php echo esc_html__( 'I for one believe that if you give people a thorough understanding of what confronts them and the basic causes that produce it, they\'ll create their own program, and when the people create a program, you get action.', 'reci-media-hub' ); ?>
				</blockquote>

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
