<?php
/**
* Display the row for the WP_Posts_List_Table
* @see NestedPages\Entities\PostType\PostTypeColumns
* Level set in Listing::listPostLevel()
*/
if ( !$this->post_list_table ) return;
$current_level = ( $level <= 2 ) ? 0 : $level - 2;
?>
<table class="np-post-columns-wp fixed <?php if ( $this->post_type->hierarchical && $this->showSortHandle() ) echo 'hierarchical'; ?>">
	<tbody>
		<?php echo $this->post_list_table->get_single_row($this->post, $this->showSortHandle(), $current_level); ?>
	</tbody>
</table>