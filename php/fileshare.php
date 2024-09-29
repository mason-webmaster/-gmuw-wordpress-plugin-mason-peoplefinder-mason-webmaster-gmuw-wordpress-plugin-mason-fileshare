<?php

/**
 * Summary: php file which implements customizations related to the file sharing
 */


//display most recent uploaded files
function gmuw_fs_custom_dashboard_meta_box_files($post,$args) {

	//get mode from arguments
	if (
		isset($args) &&
		isset($args['args']) &&
		isset($args['args'][0])
	) {
		$mode=$args['args'][0];
	} else {
		$mode=null;
	}

	//set up query arguments
	$get_posts_args = array(
		'post_type'      => 'attachment',
		'posts_per_page' => 10,
		'post_status'    => 'any',
		'post_parent'    => null,
		'orderby'		 => 'modified',
	);

	//restrict query based on file mime type, if specified
	switch ($mode) {
		case 'documents':
			$get_posts_args['post_mime_type']='application/*';
			break;
		case 'images':
			$get_posts_args['post_mime_type']='image/*';
			break;
		case 'pdfs':
			$get_posts_args['post_mime_type']='application/pdf';
			break;
		case 'yours':
			$get_posts_args['author']=get_current_user_id();
			break;
	}

	//get attachments
	$attachments = get_posts($get_posts_args);

	//put into table
	echo gmuw_fs_dashboard_widget_file_table($attachments);

}

//function to display dashboard meta box datatables file table
function gmuw_fs_dashboard_widget_file_table($posts){

	//initialize return variable
	$return_value='';

	if ($posts) {
		$return_value.='<table class="data_table dashboardwidget">';
		$return_value.='<thead>';
		$return_value.='<tr>';
		$return_value.='<td>File</td>';
		$return_value.='<td>Modified</td>';
		$return_value.='</tr>';
		$return_value.='</thead>';
		$return_value.='<tbody>';
		foreach ($posts as $post) {
			$return_value.='<tr>';
			$return_value.='<td><a href="'.wp_get_attachment_url($post->ID).'" target="_blank">'.get_the_title($post).'</a></td>';
			$return_value.='<td>'.get_the_modified_date('Y-m-d', $post).'</td>';
			$return_value.='</tr>';
		}
		$return_value.='</tbody>';
		$return_value.='</table>';
	}

	//return value
	return $return_value;

}

//function to display admin file index page datatables file table
function gmuw_fs_index_file_table($posts){

	//initialize return variable
	$return_value='';

	//process any actions
	if (isset($_GET['action'])) {

		if (
			$_GET['action']=='edit' ||
			$_GET['action']=='delete'
		) {

			//get post id
			$mypostid = isset($_GET['mypostid']) ? (int)$_GET['mypostid'] : 0;

			//do we not have a good value?
			if ($mypostid==0) {
				$return_value='<div class="error notice is-dismissable "><p>Bad post ID. Unable to '.$_GET['action'].'.</p></div>';
			} else {

				if ($_GET['action']=='edit') {
					$return_value='<div class="updated notice is-dismissable "><p>'.$_GET['action'].' post '.$mypostid.'</p></div>';
				}

				if ($_GET['action']=='delete') {
					$return_value='<div class="updated notice is-dismissable "><p>'.$_GET['action'].' post '.$mypostid.'</p></div>';
				}

			}

		}

	}

	if ($posts) {
		$return_value.='<table class="data_table">';
		$return_value.='<thead>';
		$return_value.='<tr>';
		$return_value.='<td>File</td>';
		$return_value.='<td>Post ID</td>';
		$return_value.='<td>Mime type</td>';
		$return_value.='<td>User</td>';
		$return_value.='<td>Modified</td>';
		$return_value.='<td>Actions</td>';
		$return_value.='</tr>';
		$return_value.='</thead>';
		$return_value.='<tbody>';
		foreach ($posts as $post) {
			$return_value.='<tr>';
			$return_value.='<td><a href="'.wp_get_attachment_url($post->ID).'" target="_blank">'.get_the_title($post).'</a></td>';
			$return_value.='<td>'. $post->ID.'</td>';
			$return_value.='<td>'. $post->post_mime_type.'</td>';
			$return_value.='<td>'. get_user_by('id', $post->post_author)->user_login .'</td>';
			$return_value.='<td>'.get_the_modified_date('Y-m-d', $post).'</td>';
			$return_value.='<td>';
			$return_value.='<a class="button button-primary" href="admin.php?page=gmuw_fs_file_index&action=edit&mypostid='.$post->ID.'">edit</a> ';
			$return_value.='<a class="button" href="admin.php?page=gmuw_fs_file_index&action=delete&mypostid='.$post->ID.'" onclick="return confirm(\'Are you sure you want to delete this file?\')">delete</a> ';
			$return_value.='</td>';
			$return_value.='</tr>';
		}
		$return_value.='</tbody>';
		$return_value.='</table>';
	}

	//return value
	return $return_value;

}
