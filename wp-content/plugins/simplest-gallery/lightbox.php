<?php
/*
   Lightbox Style Module for the Simplest Gallery plugin
   It might be used as a base for implementing a new, custom gallery style plugin
   
   History
   + 1.1 2016-06-29 	Removed override CSS
   + 1.0 2016-04-17	First working version
*/

global $sgli_version;
$sgli_version='1.1';	// Version

add_action('init', 'sgli_init');

// Init tasks: adds a new gallery format to the Simplest Gallery plugin via an API call
function sgli_init() {
	$urlpath = WP_PLUGIN_URL . '/' . basename(dirname(__FILE__));

	// If Simplest Gallery Plugin is not installed & activated display a reminder line
	if (!function_exists('sga_register_gallery_type') && !($_REQUEST['plugin']=='simplest-gallery/simplest-gallery.php' && $_REQUEST['action']=='activate')) {
		echo "Please install and activate Simplest Gallery plugin!";
		return;
	} else {
		if (is_callable('sga_register_gallery_type')) {
   
		// Adds new gallery type to the Simplest Gallery Plugin
		$result = sga_register_gallery_type(
							'lightbox', 		/* this is the gallery type's unique ID */
							'FancyBox without labels', /* this is the gallery type name (what the user will see in the settings page) */
							'sgli_render',		/* Function to be called for the gallery rendering */
							'sgli_header',		/* Function to be called on header() */
							array(			/* Array of scripts to be included, possibly empty */
								'jquery'=>array($urlpath . '/lib/jquery-1.10.2.min.js', false, '1.10.2'),
								'jquery-migrate'=>array($urlpath . '/lib/jquery-migrate-1.2.1.min.js', array('jquery'), '1.2.1'),
								'jquery.mousewheel'=>array($urlpath . '/lib/jquery.mousewheel-3.0.6.pack.js', array('jquery'), '3.0.6'),
								'fancybox'=>array($urlpath . '/fancybox/jquery.fancybox-1.3.4.js', array('jquery'), '1.3.4'),
							      ),
							      			/* Array of CSS to be included, possibly empty */
							array('fancybox'=>$urlpath . '/fancybox/jquery.fancybox-1.3.4.css')			
						);
						
		/* The following is identical to the previous one, except from gallery ID and rendering function name (1st two fields) */
		
		$result = sga_register_gallery_type(
							'lightbox_labeled', 		/* this is the gallery type's unique ID */
							'FancyBox WITH labels', /* this is the gallery type name (what the user will see in the settings page) */
							'sgli_render_labeled',		/* Function to be called for the gallery rendering */
							'sgli_header',		/* Function to be called on header() */
							array(			/* Array of scripts to be included, possibly empty */
								'jquery'=>array($urlpath . '/lib/jquery-1.10.2.min.js', false, '1.10.2'),
								'jquery-migrate'=>array($urlpath . '/lib/jquery-migrate-1.2.1.min.js', array('jquery'), '1.2.1'),
								'jquery.mousewheel'=>array($urlpath . '/lib/jquery.mousewheel-3.0.6.pack.js', array('jquery'), '3.0.6'),
								'fancybox'=>array($urlpath . '/fancybox/jquery.fancybox-1.3.4.js', array('jquery'), '1.3.4'),
							      ),
							      			/* Array of CSS to be included, possibly empty */
							array('fancybox'=>$urlpath . '/fancybox/jquery.fancybox-1.3.4.css')			
						);
		}
	}

}

// Sample header custom function. If we need to make special things in the header of pages for our gallery format, we will do so here
function sgli_header() {
	global $sgli_version;
	return "<!-- Lighbox module for Simplest Gallery, v. $sgli_version -->\n";
}


function sgli_render_labeled($images,$thumbs,$post_id=NULL,$gall_id=NULL,$attrs) {
	return sgli_render($images,$thumbs,$post_id,$gall_id,$attrs,'lightbox_labeled');
}

// This is a the gallery-rendering function. We don't need to care about gathering images because the Simplest Gallery plugin does this for us.
// First parameter is an array of images data (images of the gallery to be rendered), second parameter is an array of thumbs data (unused here)
// data here means that each image/thumb is represented by an array. Each position holds a specific thing:
// 0=URL,1=width,2=height,3=unused,4=ID,5=Label
function sgli_render($images,$thumbs,$post_id=NULL,$gall_id=NULL,$attrs,$gallery_type='lightbox') {

	// Set default values
	$columns=3;
	$border=0;
	$width='';
	$height='';

	if (is_array($attrs)) { // Are parameters specified in the shortcode?

		if (intval($attrs['columns'])) { // Modify column value
			$columns = intval($attrs['columns']);
		}

		if (isset($attrs['border'])) { 
			$border = $attrs['border'];
		}

		if (isset($attrs['width'])) {
			$width = $attrs['width'];
		}

		if (isset($attrs['height'])) {
			$height = $attrs['height'];
		}
	}
	
	$html_id='gallery-'.$gall_id;

	$gall = '';


	$gall .= '
	<style type="text/css">
					#gallery-'.$gall_id.' {
						margin: auto;
					}
					#gallery-'.$gall_id.' .gallery-item {
						float: left;
						margin-top: 10px;
						text-align: center;
						width: '.intval(98/$columns).'%;
					}
					#gallery-'.$gall_id.' img {
						border: 2px solid #cfcfcf;
					}
					#gallery-'.$gall_id.' .gallery-caption {
						margin-left: 0;
					}
	</style>';


					$gall .= '
<script type="text/javascript">
$(document).ready(function() {
	/*
	 *  Simple image gallery. Uses default settings
	 */

	$(".fancybox").fancybox({
		"transitionIn"		: "none",
		"transitionOut"		: "none",
		"titlePosition" 	: "over"';

					if ($gallery_type == 'lightbox_labeled') // Add labels
		$gall .= '
		,
		"titleFormat"		: function(title, currentArray, currentIndex, currentOpts) {
			return "<span id=\'fancybox-title-over\'><span id=\'fancybox-title-pretext\'>'.__('Image','simplest-gallery').' </span>"+(currentIndex+1)+" / " + currentArray.length + (title.length ? " &nbsp; " + title : "") + "</span>";
		}';
		$gall .= '

	});
});
</script>
';

	$gall .= '
	<div id="gallery-'.$gall_id.'" class="gallery galleryid-'.$gall_id.' gallery-size-thumbnail" style="'.($width?"width:$width;":'').($height?"height:$height;":'').($border?"border:$border;":'').'">';

	for ($i=0;$i<count($thumbs);$i++) {
		$thumb = $thumbs[$i];
		$image = $images[$i];
		$gall .= '<dl class="gallery-item"><dt class="gallery-icon">
		<a class="fancybox" href="'.$image[0].'"'.(($gallery_type == 'lightbox_labeled')?' title="'.htmlspecialchars($thumb[5]).'"':'').' rel="gallery-'.$gall_id.'"><img width="'.$thumb[1].'" height="'.$thumb[2].'" class="attachment-thumbnail" alt="'.htmlspecialchars($thumb[5]).'" src="'.$thumb[0].'" /></a></dt>';
		if ($gallery_type == 'lightbox_labeled') {	// Add labels
			$gall .= '<dd class="wp-caption-text gallery-caption">'.$thumb[5].'</dd>';
		}
		$gall .= '</dl>'."\n\n"; // title="'.print_r($thumb,true).'"
	}

	$gall .= '</div><br clear="all" />';


	return $gall;


}


?>