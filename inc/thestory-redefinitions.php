<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

////
// Functions to modify Story theme functions
// these functions have the same name that Story's function which they modify,
// but changing prefix pexeto by cw
////

function cw_get_gallery_thumbnail_html($post) {
	$output = "";
	$cos = get_the_terms($post->ID,CWNET_TX_CO);

	if ( is_single() ) {
		$cos_out = '';
		if ( !empty($cos) ) {
			$cos_list = wp_list_pluck($cos,'name');
			$cos_out = implode(', ',$cos_list);
		}
		$lcs = get_the_terms($post->ID,CWNET_TX_LC);
		$lcs_list = wp_list_pluck($lcs,'name');
		$output .= "<div class='ps-basic ps-basic-where ps-basic-inline'>".implode(", ",$lcs_list).'<br>'.$cos_out."</div>";
	}
	else {
		if ( !empty($cos) ) {
			$cos_list = wp_list_pluck($cos,'name');
			$output .= "<div class='ps-basic ps-basic-where'>".implode(', ',$cos_list)."</div>";
		}
	}
	
	$date_b = get_post_meta($post->ID,'_pj_date_begin',true);
	$date_e = get_post_meta($post->ID,'_pj_date_end',true);
	if ( !empty($date_b) ) {
		if ( empty($date_e) )
			$date_e = __('present','cw');
		if ( $date_b == $date_e )
			$output .= " <div class='ps-basic ps-basic-when ps-basic-inline'>".$date_b."</div>";
		else
			$output .= "<div class='ps-basic ps-basic-when'>".$date_b." &dash; ".$date_e."</div>";
	}

	return $output;
}

if ( !function_exists( 'pexeto_get_gallery_thumbnail_html' ) ) {

	/**
	 * Generates the HTML code for a gallery thumbnail item.
	 *
	 * @param object  $post         the post that will represent the gallery item
	 * @param int     $columns      the number of columns of items the gallery will contain
	 * @param int     $image_height the height of the thumbnail image
	 * @param string  $itemclass    the class of the wrapping div
	 * @param bool    $carousel_lb_album sets whether the item is part of a
	 * single carousel that supports lightbox album preview
	 * @return string               the generated HTML code for the item
	 */
	function pexeto_get_gallery_thumbnail_html( $post, $columns, $image_height, $itemclass='pg-item', $carousel_lb_album = false ) {

		$size_key = $itemclass == 'pc-item' ? 'carousel' : 'gallery';
		$size_options = pexeto_get_image_size_options($columns, $size_key);

		$image_width = $size_options['width'];
		$settings = pexeto_get_post_meta( $post->ID, array( 'type' ), PEXETO_PORTFOLIO_POST_TYPE );
		$exclude_info = pexeto_option( 'portfolio_exclude_info' );
		$add_class = sizeof( $exclude_info ) == 2 ? ' pg-info-dis' : '';

		$html='<div class="'.$itemclass.$add_class.'" data-defwidth="'.( $image_width+10 ).'"'.
			' data-type="'.$settings['type'].'"'.
			' data-itemid="'.$post->ID.'">';


		$preview = pexeto_get_portfolio_preview_img( $post->ID );

		$crop = $image_height ? true : false;

		//retrieve the image URL
		if ( $preview['custom'] ) {
			//use the original image set
			$img_url = $preview['img'];
		}else {
			//use a resized image
			$big_image_width = $image_width + 200;
			$big_image_height = empty($image_height)?$image_height:$image_height*($image_width+200)/$image_width;
			$img_url = pexeto_get_resized_image( $preview['img'],
				$big_image_width,
				$big_image_height );
		}

		//load the categories assigned to the item
		$terms=wp_get_post_terms( $post->ID, PEXETO_PORTFOLIO_TAXONOMY );
		$term_names=array();
		foreach ( $terms as $term ) {
			$term_names[]=$term->name;
		}

		$href='#';
		$rel='';
		$target='';

		//set the link of the item according to its type
		switch ( $settings['type'] ) {
		case 'smallslider':
		case 'fullslider':
		case 'standard':
		case 'fullvideo':
		case 'smallvideo':
			$href = get_permalink( $post->ID );
			break;
		case 'custom':
			$href = pexeto_get_single_meta( $post->ID, 'custom_link' );
			if( pexeto_get_single_meta( $post->ID, 'custom_link_open' )=='new' ){
				$target = ' target="_blank"';
			}
			break;
		case 'lightbox':
			$lightbox_preview = array();
			if($preview['custom']){
				//get the image preview, skipping the thumbnail image
				$lightbox_preview = pexeto_get_portfolio_preview_img( $post->ID, true );
			}
			$href = empty($lightbox_preview['img']) ? $preview['img'] : $lightbox_preview['img'];
			//gallery items should be in a group in the lightbox preview
			
			
			if(!$carousel_lb_album){
				$add_rel = $itemclass == 'pg-item'?'[group]':'';
				$rel = ' data-rel="pglightbox'.$add_rel.'"';
			}else{
				//carousel item with a lightbox album preview support
				$rel = ' data-rel="pclightbox"';
				$images = pexeto_get_post_gallery_images($post);
				$img_urls = array();
				$captions = array();
				foreach ( $images as $img ) {
					$img_src = wp_get_attachment_image_src($img->ID, 'full');
					$img_urls[]= $img_src[0];
					$captions[]=$img->pexeto_desc;
				}

				$rel.=' data-images="'.esc_attr(json_encode($img_urls)).'"';
				$rel.=' data-captions="'.esc_attr(json_encode($captions)).'"';
			}
			
		}

		$alt = isset($preview['alt']) ? $preview['alt'] : $post->post_title;


		$html.='<a href="'.$href.'" title="'.$post->post_title.'"'.$rel.$target.'>'.
			'<div class="pg-img-wrapper">';
			//add the item icon
			$html.='<span class="icon-circle">'.
			'<span class="pg-icon '.$settings['type'].'-icon"></span>'.
			'</span>'.
			'<img src="'.$img_url.'" alt="'.esc_attr($alt).'"/></div>';

		


			//display the item info
			$html.='<div class="pg-info">';
			$html.='<div class="pg-details'.$add_class.'">';
			if ( !in_array( 'title', $exclude_info ) ) {
				$html.='<h2>'.$post->post_title.'</h2>';
			}
			if ( !in_array( 'category', $exclude_info ) ) {
				$html.='<span class="pg-categories">'.implode( ' / ', $term_names ).'</span>';
			}
			$html .= cw_get_gallery_thumbnail_html($post);
			$html.='</div></div>';
		

		$html.='</a></div>';

		return $html;
	}
}

function cw_get_portfolio_slider_item_html($post) {
	$terms=wp_get_post_terms( $post->ID, PEXETO_PORTFOLIO_TAXONOMY );
	$term_names=array();
	foreach ( $terms as $term ) {
		$term_names[]=str_replace('@'.ICL_LANGUAGE_CODE, '', $term->name);
	}

	$ps_basics = cw_get_gallery_thumbnail_html($post);

	$ps_extra = "";
	$web = get_post_meta($post->ID,'_pj_website',true);
	$label = __('Website','cw');
	if ( !empty($web) ) {
		$ps_extra .= '<div class="ps-extra ps-extra-website"><div class="ps-extra-label">'.$label.'</div><div class="ps-extra-text"><a href="'.$web.'">'.$web.'</a></div></div>';
	}

	$output='
	<div class="ps-side">
		<div class="ps-side-up">
			<h2 class="ps-title">'.$post->post_title.'</h2>
			<span class="ps-categories">'.implode( ' / ', $term_names ).'</span>
			'.$ps_basics.'
		</div>
	';
	if ( $ps_extra != '' ) {
		$output .= '
		<aside class="ps-side-down">
			<div class="ps-side-title">'.__('More information','cw').'</div>
			'.$ps_extra.'
		</aside>
		';
	}
	$output .= '</div>';

	$content = pexeto_option( 'ps_strip_gallery' ) ?
		pexeto_remove_gallery_from_content( $post->post_content ) :
		$post->post_content;
	$output.='<div class="ps-content-text">'.do_shortcode( apply_filters( 'the_content', $content ) ).'</div>';

	return $output;
}

if ( !function_exists( 'pexeto_get_portfolio_slider_item_html' ) ) {

	/**
	 * Generates the gallery slider HTML code.
	 *
	 * @param int     $itemid the ID of the item(post) that will represent the slider
	 * @param boolean $single setting whether it is a single item page or the
	 * slider was loaded from the gallery, as part of the gallery
	 * @return string          the HTML code of the slider
	 */
	function pexeto_get_portfolio_slider_item_html( $itemid, $single=true ) {
		$html = '';
		global $post;
		if ( empty( $post ) || $post->ID !== $itemid ) {
			$post = get_post( $itemid );
		}

		$item_type = pexeto_get_single_meta( $itemid, 'type' );
		$fullwidth = $item_type=='fullslider' || $item_type=='fullvideo'?true:false;
		$video = $item_type=='fullvideo' || $item_type=='smallvideo'?true:false;
		$content_class = $video ? 'ps-video':'ps-images';

		$preview = pexeto_get_portfolio_preview_img( $post->ID );

		if ( !empty( $post ) ) {
			$add_class = $fullwidth ? ' ps-fullwidth':'';

			$html = '<div class="ps-wrapper'.$add_class.'">';

			//add the slider
			$html.='<div class="'.$content_class.'">';
			if ( $video ) {
				global $pexeto_content_sizes;
				$width = $fullwidth ?
					$pexeto_content_sizes['fullwidth'] : $pexeto_content_sizes['sliderside'];
				$video_url=pexeto_get_single_meta( $itemid, 'video' );
				$html.=pexeto_get_video_html( $video_url, $width );
			}
			$html.='</div>';

			//add the content
			$html.='<div class="ps-content">';

			//get the categories
			//load the categories assigned to the item
			//add the title and content_class
			$html.= cw_get_portfolio_slider_item_html($post);

			//add the share buttons
			$share = pexeto_get_share_btns_html( $itemid, 'slider' );
			if ( !empty( $share ) ) {
				$html.='<div class="ps-share">'.$share.'</div>';
			}
			$html.='</div>';
			$html.='<div class="clear"></div></div>';
		}

		return $html;
	}
}
?>
