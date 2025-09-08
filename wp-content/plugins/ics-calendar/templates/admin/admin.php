<?php
// phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Images are part of the plugin, not Media Library.

global $R34ICS;

function r34ics_getting_started_go_pro_html() {
	ob_start();
	?>
	<div class="r34ics-pro-mo r34ics-gradient-bg postbox"><div class="inside">
		<h3><?php
		/* translators: 1: Plugin name (do not translate) */
		printf(esc_html__('Do even more with %1$s!', 'ics-calendar'), 'ICS Calendar Pro');
		?></h3>
				
		<div class="r34ics-pro-features">
			<div>
				<div style="text-align: center;"><a href="https://icscalendar.com/pro" target="_blank"><img src="<?php echo esc_url(plugins_url('assets/ics-calendar-pro-logo-2023.svg', dirname(dirname(__FILE__)))); ?>" alt="ICS Calendar Pro" width="206" height="72" /></a></div>
				<h4><?php esc_html_e('Calendar Builder', 'ics-calendar'); ?></h4>
				<p><?php esc_html_e('Configure all calendar settings with an easy visual interface... no need to manually type shortcodes.', 'ics-calendar'); ?></p>
				<h4><?php esc_html_e('Additional Views and Enhanced Features', 'ics-calendar'); ?></h4>
				<p><?php esc_html_e('New ways to display your calendar including Full, Up Next, Masonry, Month with Sidebar, Widget, and more. Additional capabilities are added to the core Month, Basic, List and Week views.', 'ics-calendar'); ?></p>
				<h4><?php esc_html_e('Customizer', 'ics-calendar'); ?></h4>
				<p><?php esc_html_e('Easily modify your calendar color palettes, fonts, and more, site-wide.', 'ics-calendar'); ?></p>
				<div style="text-align: center;"><a href="https://icscalendar.com/pro" target="_blank"><img src="<?php echo esc_url(plugins_url('assets/ics-events-logo.svg', dirname(dirname(__FILE__)))); ?>" alt="ICS Events" width="171" height="72" /></a></div>
				<h4><?php esc_html_e('ICS Events', 'ics-calendar'); ?></h4>
				<p><?php
				/* translators: 1: HTML tag 2: HTML tag 3: Plugin name (do not translate) */
				printf(esc_html__('%1$sNew!%2$s Turns %3$s into a full calendar system. Create and manage events directly in WordPress and even integrate them seamlessly with your existing feeds.', 'ics-calendar'), '<strong style="color: crimson;">', '</strong>', 'ICS Calendar Pro');
				?></p>
				<p class="large"><?php
				/* translators: 1. Link (do not translate) */
				printf(esc_html__('Visit %1$s to learn more.', 'ics-calendar'), '<strong><a href="https://icscalendar.com/pro/" target="_blank">icscalendar.com/pro</a></strong>');
				?></p>
			</div>
			<div style="position: relative;">
				<img src="<?php echo esc_url(plugins_url('assets/ics-mockups-2025.png', dirname(dirname(__FILE__)))); ?>" alt="" style="width: 100%; height: auto;" />
			</div>
		</div>

		<p class="aligncenter"><a href="https://icscalendar.com/pro/" target="_blank" class="button button-primary" style="font-size: larger;"><?php esc_html_e('Go PRO!', 'ics-calendar'); ?></a></p>
	</div></div>
	<?php
	$output = apply_filters('r34ics_getting_started_go_pro_html', ob_get_clean());
	return $output;
}

?>

<div class="wrap r34ics">

	<h2><?php echo esc_html(get_admin_page_title()); ?></h2>
	
	<div class="metabox-holder columns-2">
	
		<div class="column-1">
		
			<div class="postbox">
		
				<nav class="r34ics-menu"><ul>
					<li><a href="#getting-started"><span class="dashicons dashicons-sos"></span><?php esc_html_e('Getting Started', 'ics-calendar'); ?></a></li>
					<?php
					if (current_user_can('manage_options')) {
						?>
						<li><a href="#settings"><span class="dashicons dashicons-admin-settings"></span><?php esc_html_e('Settings', 'ics-calendar'); ?></a></li>
						<?php
					}
					?>
					<li><a href="#utilities"><span class="dashicons dashicons-admin-tools"></span><?php esc_html_e('Utilities', 'ics-calendar'); ?></a></li>
					<li><a href="#system-report"><span class="dashicons dashicons-clipboard"></span><?php esc_html_e('System Report', 'ics-calendar'); ?></a></li>
					<li><a href="https://icscalendar.com/support/" target="_blank"><span class="dashicons dashicons-email-alt"></span><?php esc_html_e('Support', 'ics-calendar'); ?></a></li>
				</ul></nav>
			
				<?php include_once(plugin_dir_path(__FILE__) . 'getting-started.php'); ?>
	
				<?php
				if (current_user_can('manage_options')) {
					?>
					<div class="inside" id="settings">
	
						<h2><?php esc_html_e('Settings', 'ics-calendar'); ?></h2>
	
						<form id="r34ics-settings" method="post" action="#settings">
							<?php
							wp_nonce_field('r34ics', 'r34ics-settings-nonce');
						
							include_once(plugin_dir_path(__FILE__) . 'settings.php');
							?>
	
							<p><input type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'ics-calendar'); ?>" /></p>
						</form>
	
					</div>
					<?php
				}
				?>
			
				<?php include_once(plugin_dir_path(__FILE__) . 'utilities.php'); ?>
	
			</div>
	
			<?php echo wp_kses_post(r34ics_getting_started_go_pro_html()); ?>

		</div>
	
		<div class="column-2">

			<?php include_once(plugin_dir_path(__FILE__) . 'sidebar.php'); ?>
	
		</div>
	
	</div>

</div>

<?php
// phpcs:enable
