<?php
/**
* The post thumbnail
* Configurable under post type settings
*/
$thumbnail_source = $this->post_type_repo->thumbnails($this->post_type->name, 'source');
$thumbnail_size = $this->post_type_repo->thumbnails($this->post_type->name, 'display_size');
if ( !$thumbnail_source ) return;
$out = '<div class="np-thumbnail ' . $thumbnail_size . '">';
if ( has_post_thumbnail($this->post->id) ) :
	$image = get_the_post_thumbnail($this->post->id, $thumbnail_source);
	$out .= apply_filters('nestedpages_thumbnail', $image, $this->post);
else :
	$image_fallback = apply_filters('nestedpages_thumbnail_fallback', false, $this->post);
	if ( $image_fallback ) :
		$out .= apply_filters('nestedpages_thumbnail_fallback', $image_fallback, $this->post);
	endif;
endif;
$out .= '</div>';
echo $out;