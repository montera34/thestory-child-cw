<?php
get_header();
//get all the page data needed and set it to an object that can be used in other files
$pexeto_page=array();
$pexeto_page['sidebar']=pexeto_option( 'archive_sidebar' );
$pexeto_page['slider']='none';
$pexeto_page['layout']=pexeto_option( 'archive_layout' );
$pexeto_page['title'] = get_the_author();

//include the before content template
locate_template( array( 'includes/html-before-content.php' ), true, true );

$current_user = wp_get_current_user();

//print_r( $current_user );

echo $current_user->display_name;
$uid = $current_user->ID;
$metas = array(
	array( '',get_user_meta($uid,'user_image',true),'avatar' ),
	array( __('Website','cw'),$current_user->user_url,'url' ),
	array( __('Twitter','cw'),get_user_meta($uid,'user_twitter',true),'url' ),
	array( __('Facebook','cw'),get_user_meta($uid,'user_facebook',true),'url' ),
	array( __('Linkedin','cw'),get_user_meta($uid,'user_linkedin',true),'url' ),
	array( __('City','cw'),get_user_meta($uid,'user_city',true),'' ),
	array( __('Country','cw'),get_user_meta($uid,'user_country',true),'' ),
	array( __('Date of entrance in Civicwise','cw'),get_user_meta($uid,'user_entrance_date',true),'' ),
	array( __('User circle','cw'),get_user_meta($uid,'user_circle',true),'circle' ),
	array( __('Projects as coordinator','cw'),get_user_meta($uid,'user_projects_coordinator',false),'projects' ),
	array( __('Projects as core team','cw'),get_user_meta($uid,'user_projects_core_team',false),'projects' ),
	array( __('Projects as participant','cw'),get_user_meta($uid,'user_projects_participant',false),'projects' ),
	array( __('Active in these labs','cw'),get_user_meta($uid,'user_labs',false),'labs' ),
);

echo '<dl>';
foreach ( $metas as $m ) {
	if ( $m[1] != '' )  {
		$l = $m[0];
		if ( $m[2] == 'url' ) {
			$v = '<a href="'.$m[1].'">'.$m[1].'</a>';
		}
		elseif ( $m[2] == 'avatar') {
			$v = '<figure><img src="'.$m[1]['guid'].'" alt="User avatar" /></figure>';
		}
		elseif ( $m[2] == 'circle' ) {
			$id = $m[1]['ID'];
			$perma = get_permalink($id);
			$v = '<a href="'.$perma.'">'.$m[1]['post_title'].'</a>';
		}
		elseif ( $m[2] == 'projects' || $m[2] == 'labs' ) {
			//print_r($m[1]);
			$p_array = array();
			foreach ( $m[1] as $p ) {
				$id = $p['ID'];
				$perma = get_permalink($id);
				$p_array[] = '<a href="'.$perma.'">'.$p['post_title'].'</a>';
			}
			$v = '<span>'.implode('</span>, <span>',$p_array).'</span>';
		}
		else {
			$v = $m[1];
		}
		echo '<dt>'.$l.'</dt><dd>'.$v.'</dd>';
		unset($l);unset($v);
	}
}
echo '</dl>';

//include the after content template
locate_template( array( 'includes/html-after-content.php' ), true, true );

get_footer();
?>
