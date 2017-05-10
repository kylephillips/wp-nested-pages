<?php $all_languages = $this->integrations->plugins->wpml->getLanguages(); ?>
<div class="quick-edit np-wpml-translations-modal np-inline-modal loading" data-wpml-translations-modal style="display:none;">
	<div class="form-interior">
	<h3><?php _e('Translations for ', 'wp-nested-pages'); ?><strong data-wmpl-translation-title></strong></h3>
	<div class="loading-indicator">
		<img src="<?php echo NestedPages\Helpers::plugin_url(); ?>/assets/images/spinner-2x.gif" alt="<?php _e('Loading', 'wp-nested-pages'); ?>" />
	</div>
	<table class="np-translations-table">
		<tbody>
			<?php foreach( $all_languages as $code => $lang ) : ?>
			<tr>
				<td>
					<img src="<?php echo esc_html($lang['country_flag_url']); ?>" alt="<?php echo esc_attr($lang['translated_name']) . ' ' . __('Flag', 'wp-nested-pages'); ?>" /> <?php echo esc_attr($lang['translated_name']); ?>
				</td>
				<td>
					<a href="#">Add Translation</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	</div><!-- .form-interior -->
	<div class="buttons">
		<a accesskey="c" href="#inline-edit" class="button-secondary alignleft np-cancel-quickedit">
			<?php _e( 'Close' ); ?>
		</a>
	</div>
</div><!-- .np-wpml-translations-modal -->