<?php
/**
* Display the row for the WP_Posts_List_Table
* @see NestedPages\Entities\PostType\PostTypeColumns
*/
if ( !$this->post_list_table ) return;
?>
<table class="np-post-columns-wp fixed">
	<tbody>
		<?php echo $this->post_list_table->get_single_row($this->post, $this->showSortHandle()); ?>
	</tbody>
</table>