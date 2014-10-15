<div class="netsedpages-meta">
	<p class="full">
		<label for="np_nav_status">
			<input type="checkbox" name="np_nav_status" id="np_nav_status" <?php if ( $np_nav_status == 'hide' ) echo 'checked'; ?>>
			<?php _e('Hide in Nav Menu', 'nestedpages'); ?>
		</label>
	</p>
	<p>
		<label for="np_status">
			<input type="checkbox" name="nested_pages_status" id="np_status" <?php if ( $nested_pages_status == 'hide' ) echo 'checked'; ?>>
			<?php _e('Hide in Nested Pages Tree', 'nestedpages'); ?>
		</label>
	</p>
	<p>
		<label for="np_nav_title"><?php _e('Nav Menu Title', 'nestedpages'); ?></label>
		<input type="text" name="np_nav_title" id="np_nav_title" value="<?php echo $np_nav_title; ?>" />
	</p>
</div><!-- .nestedpages-meta -->