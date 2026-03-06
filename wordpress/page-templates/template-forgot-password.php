<?php
/**
 * Template Name: Forgot Password
 *
 * Password-reset request page: split-screen layout, single email input.
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
	     LEFT PANEL — Forgot-password form
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
					<h1 class="text-3xl font-semibold font-['EB_Garamond'] text-neutral-800">
						<?php echo esc_html__( 'Forgot Password', 'reci-media-hub' ); ?>
					</h1>
					<p class="text-lg font-normal font-['SF_Pro_Display'] text-neutral-500">
						<?php echo esc_html__( "Enter your email below and we'll send you a link to reset it.", 'reci-media-hub' ); ?>
					</p>
				</div>

			</div><!-- /Logo + Title -->

			<!-- Form -->
			<div class="flex flex-col gap-10">

				<form
					method="post"
					action="<?php echo esc_url( wp_lostpassword_url() ); ?>"
					class="flex flex-col gap-5"
				>
					<?php wp_nonce_field( 'retrieve_password', 'reci_forgot_nonce' ); ?>

					<!-- Email -->
					<div class="flex flex-col gap-2">
						<label
							for="reci-reset-email"
							class="text-sm font-medium font-['SF_Pro_Display'] text-neutral-800 leading-6"
						>
							<?php echo esc_html__( 'Email', 'reci-media-hub' ); ?>
						</label>
						<input
							type="email"
							id="reci-reset-email"
							name="user_login"
							placeholder="<?php echo esc_attr__( 'Johndoe@email.com', 'reci-media-hub' ); ?>"
							autocomplete="email"
							class="w-full px-4 py-5 rounded-lg outline outline-[0.5px] outline-zinc-400 text-sm font-normal font-['SF_Pro_Display'] text-neutral-800 placeholder-zinc-400 bg-white focus:outline-[#003594] focus:outline-2 transition-all"
						/>
					</div>

					<!-- Submit -->
						<button
							type="submit"
							class="btn btn-secondary btn-md btn-block"
						>
							<?php echo esc_html__( 'Send Reset Link', 'reci-media-hub' ); ?>
						</button>

				</form><!-- /form -->

				<!-- Back to login -->
				<p class="text-center text-base font-['SF_Pro_Display']">
					<span class="text-neutral-400 font-medium">
						<?php echo esc_html__( 'Remember your password? ', 'reci-media-hub' ); ?>
					</span>
					<a
						href="<?php echo esc_url( wp_login_url() ); ?>"
						class="text-neutral-800 font-bold hover:text-[#003594] transition-colors"
					>
						<?php echo esc_html__( 'Back to Login', 'reci-media-hub' ); ?>
					</a>
				</p>

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
				<p class="text-center text-amber-400 text-3xl font-semibold font-['EB_Garamond'] leading-[1.6]">
					<?php echo esc_html__( 'Inspiring Quotes', 'reci-media-hub' ); ?>
				</p>
				<div class="w-full h-px bg-neutral-400"></div>
			</div>

			<div class="flex flex-col gap-10">
				<blockquote class="text-center text-white text-base font-normal font-['SF_Pro_Display'] leading-6">
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

<?php get_footer(); ?>
