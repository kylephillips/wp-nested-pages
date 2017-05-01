<div class="wrap">
	<h1><?php _e('Nested Pages Settings', 'wp-nested-pages'); ?></h1>

	<h2 class="nav-tab-wrapper">
		<a class="nav-tab <?php if ( $tab == 'general' ) echo 'nav-tab-active'; ?>" href="options-general.php?page=nested-pages-settings">
			<?php _e('General', 'wp-nested-pages'); ?>
		</a>
		<?php if ( count($this->getPostTypes()) > 0 ) : ?>
		<a class="nav-tab <?php if ( $tab == 'posttypes' ) echo 'nav-tab-active'; ?>" href="options-general.php?page=nested-pages-settings&tab=posttypes">
			<?php _e('Post Types', 'wp-nested-pages'); ?>
		</a>
		<?php endif; ?>
	</h2>

	<form method="post" enctype="multipart/form-data" action="options.php">
		<?php include(NestedPages\Helpers::view('settings/settings-' . $tab)); ?>
		<?php submit_button(); ?>
	</form>
</div><!-- .wrap -->