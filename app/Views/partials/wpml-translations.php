<?php $all_languages = $this->integrations->plugins->wpml->getLanguages(); ?>
<div class="quick-edit np-wpml-translations-modal np-inline-modal loading" data-np-wpml-translations-modal style="display:none;">
	<div class="form-interior">
	<h3><?php _e('Translations for ', 'wp-nested-pages'); ?><strong data-wmpl-translation-title></strong></h3>
	<div class="loading-indicator">
		<img src="<?php echo NestedPages\Helpers::plugin_url(); ?>/assets/images/spinner-2x.gif" alt="<?php _e('Loading', 'wp-nested-pages'); ?>" />
	</div>
	<div class="np-quickedit-error" style="clear:both;display:none;"></div>
		<table class="np-translations-table" data-np-wpml-translations-modal-table></table>
	</div><!-- .form-interior -->
	<div class="buttons">
		<a accesskey="c" href="#inline-edit" class="button-secondary alignleft np-cancel-quickedit">
			<?php _e( 'Close' ); ?>
		</a>
	</div>
</div><!-- .np-wpml-translations-modal -->