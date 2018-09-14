<?php
//優化主題樣式相關
function optimize_theme_setup() {
	//整理head資訊
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_shortlink_wp_head');
	add_filter('the_generator', '__return_false');
	add_filter('show_admin_bar', '__return_false');
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('wp_head', 'feed_links_extra', 3);
	//移除css, js資源載入時的版本資訊
	function remove_version_query($src) {
		if (strpos($src, 'ver=')) {
			$src = remove_query_arg('ver', $src);
		}
		return $src;
	}
	add_filter('style_loader_src', 'remove_version_query', 999);
	add_filter('script_loader_src', 'remove_version_query', 999);
	add_filter('widget_text', 'do_shortcode');
	//加上縮圖
	add_theme_support('post-thumbnails');
	add_theme_support('title-tag');
	add_theme_support('custom-logo');
	$defaults = array(
		'default-image' => '',
		'random-default' => false,
		'width' => 0,
		'height' => 0,
		'flex-height' => false,
		'flex-width' => false,
		'default-text-color' => '',
		'header-text' => true,
		'uploads' => true,
		'wp-head-callback' => '',
		'admin-head-callback' => '',
		'admin-preview-callback' => '',
		'video' => false,
		'video-active-callback' => 'is_front_page',
	);
	add_theme_support('custom-header', $defaults);
	$defaults = array(
		'default-image' => '',
		'default-preset' => 'default',
		'default-position-x' => 'left',
		'default-position-y' => 'top',
		'default-size' => 'auto',
		'default-repeat' => 'repeat',
		'default-attachment' => 'scroll',
		'default-color' => '',
		'wp-head-callback' => '_custom_background_cb',
		'admin-head-callback' => '',
		'admin-preview-callback' => '',
	);
	add_theme_support('custom-background', $defaults);
	register_nav_menus(array(
		'primary' => '主選單',
		'secondary' => '次選單',
	));
}
add_action('after_setup_theme', 'optimize_theme_setup');

function wctpe2018_form_shortcode($atts) {
	extract(shortcode_atts(array(
		'id' => 'wctpe2018_form',
		'class' => 'wctpe2018_form',
		'title' => '與我們分享你的感覺吧！',
		'title_tag' => 'h3',
	), $atts));
	$content = "";
	if (isset($_POST['mxp-postkey']) && $_POST['mxp-postkey'] == $id) {
		if (!function_exists('media_handle_upload')) {
			require_once ABSPATH . "wp-admin" . '/includes/image.php';
			require_once ABSPATH . "wp-admin" . '/includes/file.php';
			require_once ABSPATH . "wp-admin" . '/includes/media.php';
		}

		$accept_mime_type = array('image/png', 'image/jpeg', 'image/gif');
		if (isset($_FILES['mxp-image']) && in_array($_FILES['mxp-image']['type'], $accept_mime_type) &&
			isset($_POST['mxp-name']) && $_POST['mxp-name'] != "" &&
			isset($_POST['mxp-email']) && $_POST['mxp-email'] != "" &&
			isset($_POST['mxp-title']) && $_POST['mxp-title'] != "" &&
			isset($_POST['mxp-content']) && $_POST['mxp-content'] != "") {
			$name = strip_tags($_POST['mxp-name']);
			$email = strip_tags($_POST['mxp-email']);
			$website = isset($_POST['mxp-website']) ? strip_tags($_POST['mxp-website']) : "";
			$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : "nobody";
			$posttitle = strip_tags($_POST['mxp-title']);
			$content = nl2br(strip_tags($_POST['mxp-content']));
			//建立一篇文章
			$now = time();
			$pid = wp_insert_post(array(
				'post_title' => $name . " : " . $posttitle,
				'post_content' => '',
				'post_name' => 'post-' . $now,
				'post_excerpt' => $content,
				'post_status' => "publish",
				'post_author' => 1,
				'post_category' => 0,
				'tags_input' => array('WCTPE'),
				'comment_status' => "open",
				'ping_status' => "open",
				'post_type' => "post",
			));
			add_post_meta($pid, 'wctp2018-post-datetime', $now);
			add_post_meta($pid, 'wctp2018-author-email', $email);
			add_post_meta($pid, 'wctpe2018-author-website', $website);
			add_post_meta($pid, 'wctp2018-author-name', $name);
			add_post_meta($pid, 'wctp2018-post-title', $posttitle);
			add_post_meta($pid, 'wctp2018-post-content', $content);
			$tid = media_handle_sideload($_FILES['mxp-image'], $pid, "test");
			if (!is_wp_error($tid)) {
				$src = wp_get_attachment_url($tid);
				$large_thum = image_downsize($tid, 'large');
				$large_thum_path = $large_thum[0];
				add_post_meta($pid, 'wctp2018-post-image-full', $src);
				add_post_meta($pid, 'wctp2018-post-image-large', $large_thum_path);
				set_post_thumbnail($pid, $tid);
			}
			$update_post = array(
				'ID' => $pid,
				'post_content' => '[wctpe2018_display id="' . $pid . '"]',
			);
			wp_update_post($update_post);
			return '<script>location.href="' . get_post_permalink($pid) . '"</script>';
		} else {
			$content .= "<script>alert('發生錯誤，請確認資料是否正確！');</script>";
		}
	}

	$content .= '<div class="' . esc_attr($class) . '" id="chun-' . esc_attr($id) . '">';
	if ($title !== '') {
		$content .= '<' . esc_attr($title_tag) . '>' . esc_html($title) . '</' . esc_attr($title_tag) . '>';
	}
	$content .= '<form method="POST" action="" enctype="multipart/form-data">';
	$content .= '<div class="qa-field"><span class="qa-desc">Name:</span><input type="text" id="qa-name" placeholder="Name / 稱呼" value="" name="mxp-name"/></div>';
	$content .= '<div class="qa-field"><span class="qa-desc">Email:</span><input type="text" id="qa-email" placeholder="Email / 信箱" value="" name="mxp-email"/></div>';
	$content .= '<div class="qa-field"><span class="qa-desc">Website:</span><input type="text" id="qa-website" placeholder="Website / 網站" value="" name="mxp-website"/></div>';
	$content .= '<div class="qa-field"><span class="qa-desc">Title:</span><input type="text" id="qa-title" placeholder="Title / 標題" value="" name="mxp-title"/></div>';
	$content .= '<div class="qa-field"><span class="qa-desc">Content:</span><textarea id="qa-content" placeholder="Content / 內文" value="" name="mxp-content"></textarea></div>';
	$content .= '<div class="qa-field"><span class="qa-desc">Image:</span><input type="file" id="qa-image"  value="" name="mxp-image" accept="image/*"/></div>';
	$content .= '<div class="qa-field"><input type="hidden" value="' . esc_attr($id) . '" name="mxp-postkey"/></div>';
	$content .= '<div id="img-preview"></div>';
	$content .= '<button id="submit_btn">Submit</button>';
	$content .= '</form>';
	$content .= '</div>';
	$content .= '<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>';
	$content .= '
	<script>
	(function($){
		function draw(id,imgsrc) {
			$("#"+id).html(\'<img src=\'+imgsrc+\' id="img-preview" height="200" alt="Image preview...">\');
		}
		$(document).ready(function(){
			$("#qa-image").change(function(){
				var FR= new FileReader();
			    FR.addEventListener("load", function () {
			    	draw("img-preview",FR.result);
			  	}, false);
			  FR.readAsDataURL( this.files[0] );
			})
		});
	}(jQuery))
	</script>
	';
	return $content;
}
add_shortcode('wctpe2018_form', 'wctpe2018_form_shortcode');

function wctpe2018_display_shortcode($atts) {
	extract(shortcode_atts(array(
		'id' => '',
	), $atts));
	if ($id == "") {
		return '';
	}
	$datetime = get_post_meta($id, 'wctp2018-post-datetime', true);
	$email = get_post_meta($id, 'wctp2018-author-email', true);
	$website = get_post_meta($id, 'wctpe2018-author-website', true);
	$name = get_post_meta($id, 'wctp2018-author-name', true);
	$title = get_post_meta($id, 'wctp2018-post-title', true);
	$content = get_post_meta($id, 'wctp2018-post-content', true);
	$image_full = get_post_meta($id, 'wctp2018-post-image-full', true);
	$image_large = get_post_meta($id, 'wctp2018-post-image-large', true);

	$show_content = '';
	$show_content .= '<div class="wctpe2018 posts" id="post-' . esc_attr($id) . '">';
	if ($website != '') {
		$show_content .= '<div class="post-field"><span class="post-desc">Name:</span><a href="' . esc_attr($website) . '"' . esc_html($name) . '</a></div>';
	} else {
		$show_content .= '<div class="post-field"><span class="post-desc">Name:</span>' . esc_html($name) . '</div>';
	}
	if ($email != 'nobody') {
		$show_content .= '<div class="post-field"><span class="post-desc">Email:</span>' . esc_html($email) . '</div>';
	}
	$show_content .= '<div class="post-field"><span class="post-desc">Title:</span>' . esc_html($title) . '</div>';
	$show_content .= '<div class="post-field"><span class="post-desc">Content:</span>' . $content . '</div>';
	$show_content .= '<div class="post-field"><span class="post-desc">Image:</span><img src="' . esc_attr($image_large) . '"/></div>';
	$show_content .= '</div>';
	return $show_content;
}

add_shortcode('wctpe2018_display', 'wctpe2018_display_shortcode');
