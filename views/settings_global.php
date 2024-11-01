<?php

// check user capabilities
if (!current_user_can('manage_options')) {
	return;
}

// show error/update messages
settings_errors();
?>
<div class="wrap">
	<h1><?php echo  esc_html(get_admin_page_title()); ?></h1>
	<form action="options.php" method="post">
		<?php
		// output security fields for the registered setting "wc_rlc"
		settings_fields('wc_rlc');
		settings_fields('wc_rlc_settings');
		// output setting sections and their fields
		// (sections are registered for "wc_rlc", each field is registered to a specific section)
		do_settings_sections('wc_rlc_settings');
		// output save settings button
		submit_button('Save Settings');
		?>
	</form>
</div>