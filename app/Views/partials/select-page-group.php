<?php

$page_group_id = null;
if ( array_key_exists( 'np_page_group', $_REQUEST ) ) {
		if ( is_numeric( $_REQUEST['np_page_group'] ) )
				$page_group_id = $_REQUEST['np_page_group'];
}
$current_language = $this->integrations->plugins->wpml->installed ? $this->integrations->plugins->wpml->getCurrentLanguage() : '';
$all_languages = $current_language == 'all';

?>
<div class="select" id="wp-nested-pages-tools-pagegroup" style="display: none">
	<input type="hidden" name="lang" value="<?= $current_language ?>">
	<select id="wp-nested-pages-select-pagegroup" name="np_page_group">
		<option value="na">[ <?= esc_attr__('Select an item', 'wp-nested-pages') ?> ]</option>
<?php
$options = array();
foreach ( $this->all_posts as &$post ):
	if ( ! $post->post_parent )
		$options[$post->ID] = $post->post_title;
endforeach;
if ( class_exists( 'Collator' ) ) {
	$lang = $this->integrations->plugins->wpml->installed ? apply_filters('wpml_current_language', null) : get_locale();
	echo '<!-- ' . $lang . ' -->';
	$coll = new \Collator( $lang );
	$coll->asort( $options );
} else {
	echo '<!-- uasort -->';
	uasort($options, 'strnatcasecmp');
}
foreach ( $options as $post_id => $post_title ):
?>
		<option value="<?= $post_id ?>" <?php
	if ( $page_group_id !== null ):
		if ( $post_id == $page_group_id ) echo ' selected';
	endif;
?>>
<?php
echo htmlentities( $post_title );
if ( $all_languages ) {
	$language_details = apply_filters('wpml_post_language_details', null, $post_id);
	echo ' [' . $language_details['language_code'] . ']';
}
?>
</option>
<?php
endforeach;
?>
	</select>
</div>
<script>
document.addEventListener("DOMContentLoaded", function ( event ) {
	let container = document.querySelector(".np-tools-primary").querySelector(".np-tools-sort"); /* container is a <form> element */
	let el = document.getElementById("wp-nested-pages-tools-pagegroup");
	let el_before = container.lastElementChild; /* The last child is the "Apply" button containing some hidden input fields. */
	container.insertBefore(el, el_before);
	el.style.display = "block";

	document.getElementById("wp-nested-pages-select-pagegroup").addEventListener("change", function (event) {
		let inp = event.target.parentNode.querySelector("input[name='np_lang']");
		if (inp !== null) inp.value = event.target.options[event.target.selectedIndex].dataset.language;
	});
})
</script>
