<?php
/**
 * Template Name: Reflection Gallery
 *
 * Displays the RECI Virtual Reflection Gallery with a curated grid of
 * reflection cards drawn from civil rights leaders and community voices.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reflection_cards = [
	[
		'name'        => 'August Wilson',
		'quote'       => 'Confront the dark parts of yourself, and work to banish them with illumination and forgiveness. Your willingness to wrestle with your demons will cause your angels to sing.',
		'category'    => 'Arts & Culture',
		'bg_class'    => 'bg-[#003594]',
		'text_class'  => 'text-white',
		'tag_class'   => 'bg-amber-400 text-neutral-800',
		'image_url'   => 'https://placehold.co/387x300',
		'image_alt'   => 'August Wilson',
	],
	[
		'name'        => 'Fred Hampton',
		'quote'       => 'You fight racism with solidarity. We say you fight racism with solidarity. We say you fight capitalism with socialism. We say you fight imperialism with internationalism.',
		'category'    => 'Civil Rights',
		'bg_class'    => 'bg-neutral-800',
		'text_class'  => 'text-white',
		'tag_class'   => 'bg-amber-400 text-neutral-800',
		'image_url'   => 'https://placehold.co/387x300',
		'image_alt'   => 'Fred Hampton',
	],
	[
		'name'        => 'Frederick Douglass',
		'quote'       => 'If there is no struggle, there is no progress. Those who profess to favor freedom, and yet deprecate agitation, are men who want crops without plowing up the ground.',
		'category'    => 'Abolition',
		'bg_class'    => 'bg-amber-400',
		'text_class'  => 'text-neutral-800',
		'tag_class'   => 'bg-neutral-800 text-white',
		'image_url'   => 'https://placehold.co/387x300',
		'image_alt'   => 'Frederick Douglass',
	],
	[
		'name'        => 'Franco Harris',
		'quote'       => 'The measure of a man is not where he stands in moments of comfort and convenience, but where he stands at times of challenge and controversy.',
		'category'    => 'Community',
		'bg_class'    => 'bg-slate-200',
		'text_class'  => 'text-neutral-800',
		'tag_class'   => 'bg-[#003594] text-white',
		'image_url'   => 'https://placehold.co/387x300',
		'image_alt'   => 'Franco Harris',
	],
	[
		'name'        => 'Community Voice',
		'quote'       => 'Racial equity is not a destination we arrive at, but a practice we must commit to every day — in our choices, our institutions, and our relationships.',
		'category'    => 'Equity',
		'bg_class'    => 'bg-[#003594]',
		'text_class'  => 'text-white',
		'tag_class'   => 'bg-amber-400 text-neutral-800',
		'image_url'   => 'https://placehold.co/387x300',
		'image_alt'   => 'Community Voice',
	],
	[
		'name'        => 'Voice of Resistance',
		'quote'       => 'We do not inherit the earth from our ancestors — we borrow it from our children. Let us build communities worthy of the world they deserve to inhabit.',
		'category'    => 'Justice',
		'bg_class'    => 'bg-neutral-700',
		'text_class'  => 'text-white',
		'tag_class'   => 'bg-amber-400 text-neutral-800',
		'image_url'   => 'https://placehold.co/387x300',
		'image_alt'   => 'Voice of Resistance',
	],
];

get_header();
?>

<div class="bg-slate-100 min-h-screen font-['SF_Pro_Display']">

	<!-- Hero / Page Title -->
	<div class="bg-[#003594]">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14 border-b border-zinc-600">
			<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
				<div class="flex items-center gap-3">
					<div class="w-3 h-3 bg-amber-400 rounded-sm flex-shrink-0"></div>
					<h1 class="text-white text-5xl font-medium font-['EB_Garamond'] leading-tight">
						<?php esc_html_e( 'RECI Reflection Gallery', 'reci-media-hub' ); ?>
					</h1>
				</div>
				<div class="lg:pl-10 lg:border-l lg:border-zinc-500">
					<p class="text-blue-200 text-lg font-normal leading-7 tracking-tight max-w-lg">
						<?php esc_html_e( "We're eager to see how these reflections can fuel conversations and positive change! We hope you enjoy!", 'reci-media-hub' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Gallery Introduction -->
	<div class="bg-neutral-800">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-16">
			<div class="flex flex-col lg:flex-row gap-16 items-start">
				<div class="flex-1">
					<p class="text-white text-base font-normal leading-6 tracking-tight mb-8">
						<?php esc_html_e( "Dive into the Racial Equity Consciousness Institute's Virtual Reflection Gallery \xe2\x80\x93 a unique space we've created for you to connect with the stories and perspectives of civil rights leaders and activists. This gallery, born as an extension of the RECI modules, is all about sparking deep reflection and inspiring action in our community. As you explore the artwork, reflect and record what resonates with you (you can journal, take notes in your phone etc!)", 'reci-media-hub' ); ?>
					</p>
					<p class="text-white text-base font-normal leading-6 tracking-tight mb-6">
						<?php esc_html_e( 'As you navigate the gallery below, consider leveraging the resources linked below to support building your consciousness toward racial equity:', 'reci-media-hub' ); ?>
					</p>
					<div class="flex flex-col gap-4">
						<div class="flex items-center gap-3">
							<div class="w-2 h-2 bg-amber-400 rounded-sm flex-shrink-0"></div>
							<a href="#" class="text-white text-base font-normal leading-6 tracking-tight underline">
								<?php esc_html_e( 'Cognitive-Behavioral Techniques for Racial Equity Consciousness Development', 'reci-media-hub' ); ?>
							</a>
						</div>
						<div class="flex items-center gap-3">
							<div class="w-2 h-2 bg-amber-400 rounded-sm flex-shrink-0"></div>
							<a href="#" class="text-white text-base font-normal leading-6 tracking-tight underline">
								<?php esc_html_e( 'Strategies For Developing Racial Equity Consciousness', 'reci-media-hub' ); ?>
							</a>
						</div>
						<div class="flex items-center gap-3">
							<div class="w-2 h-2 bg-amber-400 rounded-sm flex-shrink-0"></div>
							<a href="#" class="text-white text-base font-normal leading-6 tracking-tight underline">
								<?php esc_html_e( 'Racial Equity Areas of Opportunity', 'reci-media-hub' ); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Reflection Cards Grid -->
	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-16">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
			<?php foreach ( $reflection_cards as $card ) : ?>
				<div class="<?php echo esc_attr( $card['bg_class'] ); ?> rounded-lg overflow-hidden flex flex-col">
					<img
						src="<?php echo esc_url( $card['image_url'] ); ?>"
						alt="<?php echo esc_attr( $card['image_alt'] ); ?>"
						class="w-full h-56 object-cover"
					/>
					<div class="flex flex-col flex-1 p-8 gap-6">
						<!-- Quote mark -->
						<div class="w-10 h-10 bg-amber-400 rounded-sm flex items-center justify-center flex-shrink-0">
							<svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M0 16V9.6C0 7.467 0.6 5.5 1.8 3.7C3 1.9 4.8 0.6 7.2 0L8.4 1.8C6.4 2.467 5 3.433 4.2 4.7C3.4 5.967 3.067 7.2 3.2 8.4H6.4V16H0ZM11.6 16V9.6C11.6 7.467 12.2 5.5 13.4 3.7C14.6 1.9 16.4 0.6 18.8 0L20 1.8C18 2.467 16.6 3.433 15.8 4.7C15 5.967 14.667 7.2 14.8 8.4H18V16H11.6Z" fill="currentColor" class="<?php echo strpos( $card['bg_class'], 'amber' ) !== false ? 'text-neutral-800' : 'text-neutral-800'; ?>"/>
							</svg>
						</div>

						<!-- Quote text -->
						<blockquote class="<?php echo esc_attr( $card['text_class'] ); ?> text-lg font-medium font-['EB_Garamond'] leading-7 tracking-tight flex-1">
							<?php echo esc_html( $card['quote'] ); ?>
						</blockquote>

						<!-- Divider -->
						<div class="border-t border-white/20"></div>

						<!-- Author & category -->
						<div class="flex items-center justify-between">
							<span class="<?php echo esc_attr( $card['text_class'] ); ?> text-base font-semibold font-['SF_Pro_Display']">
								<?php echo esc_html( $card['name'] ); ?>
							</span>
							<span class="<?php echo esc_attr( $card['tag_class'] ); ?> text-xs font-normal font-['SF_Pro_Display'] px-2 py-1 rounded">
								<?php echo esc_html( $card['category'] ); ?>
							</span>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Load More -->
		<div class="flex justify-center mt-12">
				<button
					type="button"
					class="btn btn-primary btn-md min-w-44 hover:bg-amber-500"
				>
					<?php esc_html_e( 'Load More', 'reci-media-hub' ); ?>
				</button>
		</div>
	</div>

	<!-- Connect CTA Banner -->
	<div class="bg-neutral-800">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-20">
			<div class="flex flex-col lg:flex-row items-start gap-10">
				<img
					src="https://placehold.co/415x347"
					alt=""
					class="flex-shrink-0 rounded-lg border-b-[11px] border-amber-400 w-full lg:w-auto lg:max-w-xs"
					aria-hidden="true"
				/>
				<div class="flex flex-col gap-8">
					<div class="flex flex-col gap-3">
						<span class="px-2 py-1 bg-amber-400 rounded text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] leading-4 inline-block w-fit">
							<?php esc_html_e( 'Connect Elements', 'reci-media-hub' ); ?>
						</span>
						<h2 class="text-white text-5xl lg:text-6xl font-semibold font-['EB_Garamond'] leading-tight max-w-2xl">
							<?php esc_html_e( 'Connect your interests to improve your feed and discover more relevant content', 'reci-media-hub' ); ?>
						</h2>
					</div>
					<a
						href="#"
						class="min-w-44 px-7 py-3.5 bg-amber-400 rounded-lg text-neutral-800 text-base font-medium font-['SF_Pro_Display'] leading-6 inline-flex justify-center items-center w-fit hover:bg-amber-500 transition-colors"
					>
						<?php esc_html_e( 'Connect Now', 'reci-media-hub' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>

</div>

<?php get_footer(); ?>
