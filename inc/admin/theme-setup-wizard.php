<?php
/**
 * RECI Media Hub — Theme Setup Wizard.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'reci_register_setup_wizard_page' );

function reci_register_setup_wizard_page(): void {
	// Register as a submenu under RECI Settings
	add_submenu_page(
		'reci-settings',
		'Setup Wizard',
		'Setup Wizard',
		'manage_options',
		'reci-setup-wizard',
		'reci_render_setup_wizard_page'
	);
}

add_action( 'admin_enqueue_scripts', 'reci_setup_wizard_assets' );

function reci_setup_wizard_assets( string $hook_suffix ): void {
	if ( 'reci-settings_page_reci-setup-wizard' !== $hook_suffix && 'appearance_page_reci-setup-wizard' !== $hook_suffix ) {
		return;
	}

	// Enqueue Alpine.js for lightweight state management
	wp_enqueue_script( 'alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', [], null, true );
}

function reci_render_setup_wizard_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$theme_version = wp_get_theme()->get( 'Version' );
	$php_version   = phpversion();
	$wp_version    = get_bloginfo( 'version' );

	$php_ok = version_compare( $php_version, '8.0.0', '>=' );
	$wp_ok  = version_compare( $wp_version, '6.0', '>=' );
    
    // Media Upload Limit
    $max_upload_bytes = wp_max_upload_size();
    $max_upload_mb    = $max_upload_bytes / MB_IN_BYTES;
    $upload_ok        = $max_upload_mb >= 8;
    $upload_display   = size_format( $max_upload_bytes );
    
    // Check if demo is installed
    $demo_installed = get_option( 'reci_demo_installed', false );

	?>
	<!-- Load Tailwind via CDN with preflight disabled to avoid breaking WP admin styles -->
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		tailwind.config = {
			corePlugins: {
				preflight: false,
			}
		}
	</script>

	<div class="wrap" style="margin-top:20px;">
		<h1 class="wp-heading-inline" style="display:none;">Theme Setup Wizard</h1>

		<div class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex" x-data="reciSetupWizard()" style="min-height: 600px;">
			
			<!-- Sidebar -->
			<div class="w-64 bg-gray-50 border-r border-gray-200 p-6 flex flex-col hidden md:flex">
				<div class="mb-8">
					<div class="text-xl font-bold text-gray-900 tracking-tight">RECI Media Hub</div>
					<div class="text-sm text-gray-500 mt-1">Setup Wizard</div>
				</div>

				<nav class="flex-1 space-y-1">
					<template x-for="(stepData, index) in steps" :key="index">
						<button 
							class="w-full text-left px-3 py-2.5 text-sm font-medium rounded-lg flex items-center transition-colors"
							:class="{
								'bg-blue-50 text-blue-700': step === index + 1,
								'text-gray-900 hover:bg-gray-100': step !== index + 1 && step > index + 1,
								'text-gray-400 cursor-not-allowed': step < index + 1
							}"
                            :disabled="step < index + 1"
                            @click="if (step > index + 1) step = index + 1"
						>
							<span 
								class="w-6 h-6 rounded-full flex items-center justify-center mr-3 text-xs"
								:class="{
									'bg-blue-600 text-white': step === index + 1,
									'bg-green-500 text-white': step > index + 1,
									'bg-gray-200 text-gray-500': step < index + 1
								}"
							>
								<template x-if="step > index + 1">
									<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
								</template>
								<template x-if="step <= index + 1">
									<span x-text="index + 1"></span>
								</template>
							</span>
							<span x-text="stepData.title"></span>
						</button>
					</template>
				</nav>
			</div>

			<!-- Main Content -->
			<div class="flex-1 flex flex-col relative">
				<div class="flex-1 p-8 md:p-10">
					
					<!-- Step 1: Welcome -->
					<div x-show="step === 1" x-transition.opacity.duration.300ms class="space-y-6">
						<div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
							<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
						</div>
						<h2 class="text-3xl font-bold text-gray-900 tracking-tight">Welcome to RECI Media Hub</h2>
						<p class="text-lg text-gray-600 leading-relaxed max-w-2xl">
							Thank you for installing the RECI Media Hub theme. This wizard will guide you through verifying your server environment, configuring core branding, and importing immersive demo content so you can launch beautifully in minutes.
						</p>
						<div class="bg-blue-50 border border-blue-100 rounded-lg p-5 mt-8 max-w-2xl">
							<h3 class="text-blue-800 font-semibold flex items-center">
								<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
								What you need to know
							</h3>
							<ul class="mt-3 text-sm text-blue-700 space-y-2 list-disc list-inside">
								<li>This wizard configures the baseline structure. You can change everything later in <strong>RECI Settings</strong>.</li>
								<li>The Demo Content importer will securely seed immersive galleries without duplicating existing posts.</li>
							</ul>
						</div>
					</div>

					<!-- Step 2: System Check -->
					<div x-show="step === 2" x-transition.opacity.duration.300ms class="space-y-6" style="display: none;">
						<h2 class="text-2xl font-bold text-gray-900">System Requirements</h2>
						<p class="text-gray-600 mb-8">We're checking if your server meets the requirements for a smooth experience.</p>
						
						<div class="space-y-4 max-w-2xl">
							<!-- PHP Version -->
							<div class="flex items-center justify-between p-4 rounded-lg border <?php echo $php_ok ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'; ?>">
								<div class="flex items-center">
									<?php if ( $php_ok ) : ?>
										<svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
									<?php else : ?>
										<svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
									<?php endif; ?>
									<div>
										<h4 class="text-sm font-semibold text-gray-900">PHP Version</h4>
										<p class="text-xs text-gray-500 mt-0.5">Required: 8.0+ | Current: <?php echo esc_html( $php_version ); ?></p>
									</div>
								</div>
								<div>
									<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $php_ok ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
										<?php echo $php_ok ? 'Passed' : 'Action Required'; ?>
									</span>
								</div>
							</div>

							<!-- WP Version -->
							<div class="flex items-center justify-between p-4 rounded-lg border <?php echo $wp_ok ? 'border-green-200 bg-green-50' : 'border-yellow-200 bg-yellow-50'; ?>">
								<div class="flex items-center">
									<?php if ( $wp_ok ) : ?>
										<svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
									<?php else : ?>
										<svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
									<?php endif; ?>
									<div>
										<h4 class="text-sm font-semibold text-gray-900">WordPress Version</h4>
										<p class="text-xs text-gray-500 mt-0.5">Recommended: 6.0+ | Current: <?php echo esc_html( $wp_version ); ?></p>
									</div>
								</div>
								<div>
									<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $wp_ok ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
										<?php echo $wp_ok ? 'Passed' : 'Warning'; ?>
									</span>
								</div>
							</div>
							<!-- Media Upload Limit -->
							<div class="flex items-center justify-between p-4 rounded-lg border <?php echo $upload_ok ? 'border-green-200 bg-green-50' : 'border-yellow-200 bg-yellow-50'; ?>">
								<div class="flex items-center">
									<?php if ( $upload_ok ) : ?>
										<svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
									<?php else : ?>
										<svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
									<?php endif; ?>
									<div>
										<h4 class="text-sm font-semibold text-gray-900">Media Upload Limit</h4>
										<p class="text-xs text-gray-500 mt-0.5">Recommended: 8 MB+ | Current: <?php echo esc_html( $upload_display ); ?></p>
									</div>
								</div>
								<div>
									<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $upload_ok ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
										<?php echo $upload_ok ? 'Passed' : 'Warning'; ?>
									</span>
								</div>
							</div>
						</div>
						
						<?php if ( ! $php_ok ) : ?>
							<div class="p-4 bg-red-50 border-l-4 border-red-400 text-red-700 text-sm max-w-2xl mt-4">
								<p><strong>Warning:</strong> Your PHP version is below the minimum requirement. Some immersive features may fail. Please ask your host to upgrade PHP to 8.0 or higher.</p>
							</div>
						<?php endif; ?>
					</div>

					<!-- Step 3: Core Configuration -->
					<div x-show="step === 3" x-transition.opacity.duration.300ms class="space-y-6" style="display: none;">
						<h2 class="text-2xl font-bold text-gray-900">Core Configuration</h2>
						<p class="text-gray-600 mb-8">Set up your baseline theme branding. These can be changed later.</p>
						
						<div class="space-y-6 max-w-2xl">
							<div>
								<label class="block text-sm font-medium text-gray-700 mb-1">Site Subtitle</label>
								<input type="text" x-model="formData.subtitle" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border" placeholder="e.g. Media Hub">
								<p class="mt-1 text-xs text-gray-500">Appears next to your logo in the main navigation.</p>
							</div>
							
							<div class="grid grid-cols-2 gap-6">
								<div>
									<label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
									<div class="flex items-center mt-1">
										<input type="color" x-model="formData.primaryColor" class="h-10 w-12 rounded border border-gray-300 p-0 cursor-pointer">
										<span class="ml-3 text-sm text-gray-500 font-mono" x-text="formData.primaryColor"></span>
									</div>
								</div>
								<div>
									<label class="block text-sm font-medium text-gray-700 mb-1">Accent Color</label>
									<div class="flex items-center mt-1">
										<input type="color" x-model="formData.accentColor" class="h-10 w-12 rounded border border-gray-300 p-0 cursor-pointer">
										<span class="ml-3 text-sm text-gray-500 font-mono" x-text="formData.accentColor"></span>
									</div>
								</div>
							</div>

							<div class="pt-4 border-t border-gray-200">
								<label class="flex items-center">
									<input type="checkbox" x-model="formData.enableRegistration" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-5 w-5">
									<span class="ml-3 text-sm text-gray-700">Enable user registration for Reflections</span>
								</label>
							</div>
						</div>
					</div>

					<!-- Step 4: Demo Content -->
					<div x-show="step === 4" x-transition.opacity.duration.300ms class="space-y-6" style="display: none;">
						<h2 class="text-2xl font-bold text-gray-900">Demo Content</h2>
						<p class="text-gray-600 mb-6">Jumpstart your site with immersive dummy reflections, articles, and events.</p>
						
                        <div x-show="demoInstalled && !showImporter" class="p-6 bg-green-50 border border-green-200 rounded-lg max-w-2xl text-center">
                            <svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="text-lg font-bold text-green-800">Demo Content Already Installed!</h3>
                            <p class="text-green-700 mt-2 text-sm">Your database is already populated with demo posts. You can skip this step, install more content, or start over by resetting.</p>
                            <div class="mt-6 flex gap-4 justify-center">
                                <button @click="showImporter = true" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    Install More Content
                                </button>
                                <button @click="resetDemo" class="inline-flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition-colors" :disabled="isImporting">
                                    <span x-text="isResetting ? 'Resetting...' : 'Reset (Delete All)'"></span>
                                </button>
                            </div>
                        </div>

                        <div x-show="!demoInstalled || showImporter" style="display: none;">
                            <div class="max-w-2xl border border-gray-200 rounded-lg overflow-hidden">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Select content to import</span>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="demoSelectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                                        <span class="ml-2 text-xs text-gray-500">Select All</span>
                                    </label>
                                </div>
                                <div class="divide-y divide-gray-100 max-h-[250px] overflow-y-auto">
                                    <?php foreach ( reci_demo_content_types() as $pt => $info ) : ?>
                                        <label class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors">
                                            <input type="checkbox" value="<?php echo esc_attr( $pt ); ?>" x-model="demoTypes" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                                            <div class="ml-3 flex-1">
                                                <span class="block text-sm font-medium text-gray-900"><?php echo esc_html( $info['label'] ); ?></span>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                <?php echo (int) $info['count']; ?> items
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Import Progress UI -->
                            <div x-show="isImporting" class="max-w-2xl mt-6 p-5 border border-blue-100 bg-blue-50 rounded-lg shadow-inner">
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-sm font-medium text-blue-800" x-text="importState.currentLabel">Importing...</span>
                                    <span class="text-xs font-semibold text-blue-600" x-text="importState.percent + '%'">0%</span>
                                </div>
                                <div class="w-full bg-blue-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" :style="`width: ${importState.percent}%`"></div>
                                </div>
                                <p class="text-xs text-blue-600 mt-2 text-right" x-text="importState.progressText"></p>
                            </div>
                        </div>
					</div>

					<!-- Step 5: Liftoff -->
					<div x-show="step === 5" x-transition.opacity.duration.300ms class="space-y-6 flex flex-col items-center justify-center text-center py-10" style="display: none;">
						<div class="relative w-24 h-24 mb-4">
							<div class="absolute inset-0 bg-green-100 rounded-full animate-ping opacity-75"></div>
							<div class="relative bg-green-500 text-white rounded-full w-24 h-24 flex items-center justify-center shadow-lg">
								<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
							</div>
						</div>
						<h2 class="text-3xl font-bold text-gray-900">You're All Set!</h2>
						<p class="text-lg text-gray-600 max-w-xl">
							RECI Media Hub has been successfully configured. Your database is primed and your branding is saved. 
						</p>
						
						<div class="flex gap-4 mt-8">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=reci-settings' ) ); ?>" class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
								Advanced Settings
							</a>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="inline-flex justify-center items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
								View Live Site
								<svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
							</a>
						</div>
					</div>

				</div>

				<!-- Footer Controls -->
				<div class="bg-gray-50 border-t border-gray-200 px-8 py-4 flex items-center justify-between">
					<button 
						x-show="step > 1 && step < 5" 
						@click="step--"
						class="text-sm font-medium text-gray-600 hover:text-gray-900 px-4 py-2"
                        :disabled="isImporting"
					>
						&larr; Back
					</button>
					<div x-show="step === 1" class="w-20"></div> <!-- spacer -->

					<button 
						x-show="step < 4" 
						@click="goToNextStep"
						class="inline-flex justify-center items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors"
					>
						Continue
					</button>
					
					<button 
						x-show="step === 4" 
						@click="handleDemoAndFinish"
						class="inline-flex justify-center items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white transition-colors"
                        :class="(demoTypes.length > 0 && (!demoInstalled || showImporter)) ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-600 hover:bg-gray-700'"
                        :disabled="isImporting || isResetting"
					>
						<span x-text="(demoTypes.length > 0 && (!demoInstalled || showImporter)) ? (isImporting ? 'Importing...' : 'Import & Finish') : 'Skip & Finish'"></span>
					</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('alpine:init', () => {
			Alpine.data('reciSetupWizard', () => ({
				step: 1,
				maxSteps: 5,
				steps: [
					{ title: 'Welcome' },
					{ title: 'System Check' },
					{ title: 'Core Config' },
					{ title: 'Demo Content' },
					{ title: 'Liftoff' }
				],
				formData: {
					subtitle: 'Media Hub',
					primaryColor: '#003594',
					accentColor: '#FFB81C',
					enableRegistration: false
				},
				demoTypes: [],
                demoInstalled: <?php echo $demo_installed ? 'true' : 'false'; ?>,
                showImporter: false,
                isImporting: false,
                isResetting: false,
                importState: {
                    percent: 0,
                    currentLabel: 'Idle',
                    progressText: ''
                },
				
				init() {
                    // Start with all demo types selected if not installed or if viewing importer
                    this.demoTypes = <?php echo wp_json_encode( array_keys( reci_demo_content_types() ) ); ?>;
					
                    this.$watch('demoSelectAll', (value) => {
						if (value) {
							this.demoTypes = <?php echo wp_json_encode( array_keys( reci_demo_content_types() ) ); ?>;
						} else {
							this.demoTypes = [];
						}
					});
				},

				get demoSelectAll() {
					return this.demoTypes.length === <?php echo count( reci_demo_content_types() ); ?>;
				},

				set demoSelectAll(value) {
                    // Handled by watch
				},

				async goToNextStep() {
					if (this.step === 3) {
						// Save core config before moving to step 4
						await this.saveConfig();
					}
					this.step++;
				},

				async saveConfig() {
                    const body = new URLSearchParams({
                        action: 'reci_wizard_save_config',
                        nonce: '<?php echo wp_create_nonce("reci_wizard_action"); ?>',
                        branding_hub_subtitle: this.formData.subtitle,
                        branding_primary_color: this.formData.primaryColor,
                        branding_accent_color: this.formData.accentColor,
                        auth_enable_registration: this.formData.enableRegistration ? '1' : '0'
                    });

                    try {
                        await fetch(ajaxurl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: body.toString()
                        });
                    } catch(e) {
                        console.error('Failed to save config', e);
                    }
				},

                async resetDemo() {
                    if (!confirm('Are you sure you want to permanently delete all demo content? This cannot be undone.')) return;
                    
                    this.isResetting = true;
                    try {
                        const body = new URLSearchParams({
                            action: 'reci_wizard_reset_demo',
                            nonce: '<?php echo wp_create_nonce("reci_wizard_action"); ?>'
                        });
                        const res = await fetch(ajaxurl, { method: 'POST', body: body });
                        const data = await res.json();
                        if (data.success) {
                            this.demoInstalled = false;
                            this.showImporter = true;
                            // Optionally select everything by default for re-import
                            this.demoTypes = <?php echo wp_json_encode( array_keys( reci_demo_content_types() ) ); ?>;
                        } else {
                            alert('Failed to reset demo content.');
                        }
                    } catch(e) {
                        alert('Error resetting demo content.');
                    }
                    this.isResetting = false;
                },

                async handleDemoAndFinish() {
                    if (this.demoInstalled && !this.showImporter) {
                        this.step = 5;
                        return;
                    }

                    if (this.demoTypes.length === 0) {
                        this.step = 5;
                        return;
                    }

                    this.isImporting = true;
                    
                    try {
                        // 1. Start Import
                        const startBody = new URLSearchParams({
                            action: 'reci_demo_start_import',
                            nonce: '<?php echo wp_create_nonce("reci_demo_action"); ?>'
                        });
                        this.demoTypes.forEach(t => startBody.append('selected[]', t));

                        const startRes = await fetch(ajaxurl, { method: 'POST', body: startBody });
                        const startData = await startRes.json();
                        
                        if (!startData.success) throw new Error('Failed to start import');
                        this.updateImportState(startData.data);

                        // 2. Loop steps
                        while (true) {
                            const stepBody = new URLSearchParams({
                                action: 'reci_demo_process_step',
                                nonce: '<?php echo wp_create_nonce("reci_demo_action"); ?>'
                            });
                            const stepRes = await fetch(ajaxurl, { method: 'POST', body: stepBody });
                            const stepData = await stepRes.json();
                            
                            this.updateImportState(stepData.data);
                            if (stepData.data.finished) break;
                        }

                        // Success! Move to step 5
                        setTimeout(() => {
                            this.isImporting = false;
                            this.step = 5;
                        }, 500);

                    } catch (e) {
                        alert('Import failed. You can retry later from RECI Settings > Demo Content.');
                        this.isImporting = false;
                        this.step = 5;
                    }
                },

                updateImportState(data) {
                    if (!data) return;
                    this.importState.percent = data.percent || 0;
                    this.importState.currentLabel = data.current_label || 'Working...';
                    this.importState.progressText = data.progress_text || '';
                }
			}));
		});
	</script>
	<?php
}

// ---------------------------------------------------------------------------
// AJAX Handlers for Wizard
// ---------------------------------------------------------------------------

add_action('wp_ajax_reci_wizard_save_config', 'reci_ajax_wizard_save_config');

function reci_ajax_wizard_save_config(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }
    check_ajax_referer( 'reci_wizard_action', 'nonce' );

    $options = get_option( 'reci_theme_settings', [] );

    if ( isset($_POST['branding_hub_subtitle']) ) $options['branding_hub_subtitle'] = sanitize_text_field($_POST['branding_hub_subtitle']);
    if ( isset($_POST['branding_primary_color']) ) $options['branding_primary_color'] = sanitize_text_field($_POST['branding_primary_color']);
    if ( isset($_POST['branding_accent_color']) ) $options['branding_accent_color'] = sanitize_text_field($_POST['branding_accent_color']);
    if ( isset($_POST['auth_enable_registration']) ) $options['auth_enable_registration'] = $_POST['auth_enable_registration'] === '1' ? '1' : '0';

    update_option( 'reci_theme_settings', $options );
    wp_send_json_success();
}

add_action('wp_ajax_reci_wizard_reset_demo', 'reci_ajax_wizard_reset_demo');

function reci_ajax_wizard_reset_demo(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }
    check_ajax_referer( 'reci_wizard_action', 'nonce' );

    if ( function_exists( 'reci_reset_demo_content' ) ) {
        reci_reset_demo_content();
        wp_send_json_success();
    } else {
        wp_send_json_error( 'Demo reset function not found' );
    }
}
