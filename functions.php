<?php
function custom_enqueue_styles() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('bootstrap-scripts', get_template_directory_uri() . '/js/bootstrap.min.js');
	wp_enqueue_script('load-image', get_template_directory_uri() . '/js/load-image.all.min.js');
	wp_enqueue_script('mobile-detect', get_template_directory_uri() . '/js/mobile-detect.min.js');
	wp_enqueue_script('waitMe_js', get_template_directory_uri() . '/js/waitMe.min.js');
	wp_enqueue_style('waitMe_css', get_template_directory_uri() . '/css/waitMe.min.css');
	wp_enqueue_script('main', get_template_directory_uri() . '/js/main.js');
	wp_localize_script('main', 'WCTPE', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('mxp-ajax-nonce'),
	));
	wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css');
	wp_enqueue_style('custom_css', get_template_directory_uri() . '/css/style.css');
}
add_action('wp_enqueue_scripts', 'custom_enqueue_styles');

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
			$src .= "&t=" . time(); // remove_query_arg('ver', $src);
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
		'title' => 'Share it! / 來投稿吧！',
		'title_tag' => 'h3',
	), $atts));
	$content = "";

	$user_name = isset($_COOKIE['user_name']) ? $_COOKIE['user_name'] : "";
	$user_website = isset($_COOKIE['user_website']) ? $_COOKIE['user_website'] : "";
	$user_posttitle = isset($_COOKIE['user_posttitle']) ? $_COOKIE['user_posttitle'] : "";
	$user_email = isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : "";

	$content .= "<p class='text-center'>WordCamp Taipei 2018 帶給你什麼感覺呢？</p><p class='text-center'>想拿隱藏版 WAPUU 貼紙或是徵才或是與我們保持聯繫嗎？</p><p class='text-center'>大方的在下方表單留下你的大會活動的回憶吧！</p>";
	$content .= '<div class="' . esc_attr($class) . '" id="chun-' . esc_attr($id) . '">';
	if ($title !== '') {
		$content .= '<' . esc_attr($title_tag) . '>' . esc_html($title) . '</' . esc_attr($title_tag) . '>';
	}
	$content .= '<form method="POST" action="" id="wctpe2018_form">';
	$content .= '<div class="qa-field"><span class="qa-desc">Name*</span><input type="text" id="qa-name" placeholder="Name / 稱呼" value="' . $user_name . '" name="mxp-name"/></div>';
	$content .= '<div class="qa-field"><span class="qa-desc">Email*</span><input type="text" id="qa-email" placeholder="Email / 信箱" value="' . $user_email . '" name="mxp-email"/></div>';
	$content .= '<div class="qa-field"><span class="qa-desc">Website</span><input type="text" id="qa-website" placeholder="Website / 網站 http(s)://..." value="' . $user_website . '" name="mxp-website"/></div>';
	$content .= '<div class="qa-field"><span class="qa-desc">Who are you? / 你是什麼人呢？*</span><input type="text" id="qa-title" placeholder="Tell us here! / 跟我們說吧！" value="' . $user_posttitle . '" name="mxp-title"/></div>';
	$content .= '<div class="qa-field"><span class="qa-desc">What are you looking for? / 想說什麼呢？*</span><textarea id="qa-content" placeholder="Message / 內文" value="" name="mxp-content"></textarea></div>';
	$content .= '<div class="qa-field"><span class="qa-desc">Image*</span><input type="file" id="qa-image" accept="image/*"/></div>';
	$content .= '<div class="qa-field"><input type="hidden" id="qa-image-proc"  value="" name="mxp-image"/></div>';
	$content .= '<div class="qa-field "><input type="hidden" value="' . wp_create_nonce('mxp-wctpe2018form-nonce') . '" name="mxp-postkey"/></div>';
	$content .= '<div id="img-preview"></div>';
	$content .= '</form>';
	$content .= '<button id="submit_btn">Submit</button>';
	$content .= '</div>';
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

	$show_content = '<div class="wctpe2018 posts row" id="post-' . esc_attr($id) . '"><div class="col-md-5 m_b_20">';
	if ($website != '') {
		if (substr($website, 0, 4) != "http") {
			$website = "http://" . $website;
		}
		$show_content .= '<div class="post-field"><span class="post-desc">Name:</span><a href="' . esc_attr($website) . '">' . esc_html($name) . '</a></div>';
	} else {
		$show_content .= '<div class="post-field"><span class="post-desc">Name:</span>' . esc_html($name) . '</div>';
	}
	if ($email != 'nobody') {
		$show_content .= '<div class="post-field"><span class="post-desc">Email:</span>' . esc_html($email) . '</div>';
	}
	$show_content .= '<div class="post-field"><span class="post-desc">Title:</span>' . esc_html($title) . '</div>';
	$show_content .= '<div class="post-field"><span class="post-desc">Message:</span>' . $content . '</div>
	<div><span id="FBShare">Share to Facebook</span><span id="TwitterShare">Share to Twitter</span></div>
	</div>';
	$show_content .= '<div class="col-md-7"><div class="post-field"><img src="' . esc_attr($image_large) . '"/></div></div>';
	// $show_content .= '';
	$show_content .= '</div>';

	return $show_content;
}

add_shortcode('wctpe2018_display', 'wctpe2018_display_shortcode');

function insert_social_tags_in_head() {
	global $post;
	if (!is_single()) {
		return;
	}
	$datetime = get_post_meta($post->ID, 'wctp2018-post-datetime', true);
	$email = get_post_meta($post->ID, 'wctp2018-author-email', true);
	$website = get_post_meta($post->ID, 'wctpe2018-author-website', true);
	$name = get_post_meta($post->ID, 'wctp2018-author-name', true);
	$title = get_post_meta($post->ID, 'wctp2018-post-title', true);
	$content = get_post_meta($post->ID, 'wctp2018-post-content', true);
	$image_full = get_post_meta($post->ID, 'wctp2018-post-image-full', true);
	$image_large = get_post_meta($post->ID, 'wctp2018-post-image-large', true);
	$thumbnail_src = get_template_directory_uri() . '/img/wapuu-267x300.png';
	if (has_post_thumbnail($post->ID)) {
		$thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
		$thumbnail_src = $thumbnail_src[0];
	}
	?>
<meta property="og:title" content="<?php echo "{$title} - {$name}"; ?>" />
<meta property="og:type" content="article" />
<meta property="og:url" content="<?php echo get_permalink($post->ID); ?>" />
<meta property="og:image" content="<?php echo esc_attr($thumbnail_src); ?>" />
<meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>" />
<meta property="og:description" content="<?php echo $content; ?>" />
<meta property="article:tag" content="WCTPE2018" />
<meta property="article:tag" content="WordCamp Taipei 2018" />
<meta property="article:publisher" content="https://www.facebook.com/WordCamp.Taipei/" />
<meta property="article:published_time" content="<?php echo date('c', $datetime); ?>" />
<meta property="article:modified_time" content="<?php echo date('c', $datetime); ?>" />
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="@WordCampTaipei" />
<meta name="twitter:domain" content="<?php echo get_bloginfo('url'); ?>" />
<meta name="twitter:title" content="<?php echo "{$title} - {$name}"; ?>" />
<meta name="twitter:description" content="<?php echo $content; ?>" />
<meta name="twitter:image" content="<?php echo esc_attr($thumbnail_src); ?>" />
<meta itemprop="image" content="<?php echo esc_attr($thumbnail_src); ?>" />
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '507436489771545',
      xfbml      : true,
      version    : 'v3.1'
    });
  };
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
	<?php

}
add_action('wp_head', 'insert_social_tags_in_head', 5);

function insert_GA_in_head() {
	if (!is_user_logged_in()):
	?>
<script>
(function(i, s, o, g, r, a, m) {
i['GoogleAnalyticsObject'] = r;i[r] = i[r] || function() {
(i[r].q = i[r].q || []).push(arguments)}, i[r].l = 1 * new Date();a = s.createElement(o), m = s.getElementsByTagName(o)[0];a.async = 1;a.src = g;m.parentNode.insertBefore(a, m)})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
ga('create', 'UA-127396041-1', 'auto');
ga('require', 'displayfeatures');
ga('send', 'pageview');
</script>
	<?php
endif;
}
add_action('wp_head', 'insert_GA_in_head', 6);

function mxp_ajax_get_next_page_data() {
	$max_num_pages = $_POST['max_num_pages'];
	$current_page = $_POST['current_page'];
	$found_posts = $_POST['found_posts'];
	$nonce = $_POST['nonce'];
	if (!wp_verify_nonce($nonce, 'mxp-ajax-nonce')) {
		wp_send_json_error(array('code' => 500, 'data' => '', 'msg' => '錯誤的請求'));
	}
	if (!isset($max_num_pages) || $max_num_pages == "" ||
		!isset($current_page) || $current_page == "" ||
		!isset($found_posts) || $found_posts == "") {
		wp_send_json_error(array('code' => 500, 'data' => '', 'msg' => '錯誤的請求'));
	}
	$ids = get_posts(array(
		'fields' => 'ids', // Only get post IDs
		'posts_per_page' => get_option('posts_per_page'),
		'post_type' => 'post',
		'paged' => intval($current_page) + 1,
	));
	$str = '';
	foreach ($ids as $key => $id) {
		$name = get_post_meta($id, 'wctp2018-author-name', true);
		$title = mb_substr(get_post_meta($id, 'wctp2018-post-title', true), 0, 20);
		$content = get_post_meta($id, 'wctp2018-post-content', true);
		$image_large = get_post_meta($id, 'wctp2018-post-image-large', true);
		$str .= '<div class="col-md-3 m_b_20 post"><div class="box"><div class=" post_img"><a href="' . get_permalink($id) . '"><img src="' . $image_large . '"/></a></div><a href="' . get_permalink($id) . '" class="name"><h2 >' . $title . ' - ' . $name . '</h2></a></div></div>';
	}
	wp_send_json_success(array('code' => 200, 'data' => $str));
}
add_action('wp_ajax_nopriv_mxp_ajax_get_next_page_data', 'mxp_ajax_get_next_page_data');

function mxp_wctpe2018_form_processing() {
	if (isset($_POST['mxp-postkey']) && wp_verify_nonce($_POST['mxp-postkey'], 'mxp-wctpe2018form-nonce')) {
		if (!function_exists('media_handle_upload')) {
			require_once ABSPATH . "wp-admin" . '/includes/image.php';
			require_once ABSPATH . "wp-admin" . '/includes/file.php';
			require_once ABSPATH . "wp-admin" . '/includes/media.php';
		}
		$url = parse_url($_SERVER['REQUEST_URI']);
		$path = isset($url['path']) ? $url['path'] : '';
		$query = isset($url['query']) ? '?' . $url['query'] : '';
		//$accept_mime_type = array('image/png', 'image/jpeg', 'image/gif');
		if (isset($_POST['mxp-image']) && $_POST['mxp-image'] != "" &&
			isset($_POST['mxp-name']) && $_POST['mxp-name'] != "" &&
			isset($_POST['mxp-email']) && $_POST['mxp-email'] != "" &&
			isset($_POST['mxp-title']) && $_POST['mxp-title'] != "" &&
			isset($_POST['mxp-content']) && $_POST['mxp-content'] != "") {
			$name = strip_tags($_POST['mxp-name']);
			$email = strip_tags($_POST['mxp-email']);
			$website = isset($_POST['mxp-website']) ? strip_tags($_POST['mxp-website']) : "";
			$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : "Anonymous";
			$posttitle = strip_tags($_POST['mxp-title']);
			$content = nl2br(strip_tags($_POST['mxp-content']));
			setcookie('user_name', $name, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
			setcookie('user_email', $email, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
			setcookie('user_website', $website, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
			setcookie('user_posttitle', $posttitle, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
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
			$base64_image_string = $_POST['mxp-image'];
			$splited = explode(',', substr($base64_image_string, 5), 2);
			$mime = $splited[0];
			$data = $splited[1];

			$mime_split_without_base64 = explode(';', $mime, 2);
			$mime_split = explode('/', $mime_split_without_base64[0], 2);
			$extension = "";
			$output_file_with_extension = "";
			if (count($mime_split) == 2) {
				$extension = $mime_split[1];
				if ($extension == 'jpeg') {
					$extension = 'jpg';
				}
				$output_file_with_extension = 'user-upload-' . time() . '.' . $extension;
			}
			if (in_array($extension, array('jpg')) && $output_file_with_extension != "") {
				$tempName = tempnam(sys_get_temp_dir(), 'mxp-tw');
				$tempName = realpath($tempName);
				file_put_contents($tempName, base64_decode($data));
				$tid = media_handle_sideload(array('name' => $output_file_with_extension, 'type' => 'image/jpeg', 'tmp_name' => $tempName, 'error' => 0, 'size' => strlen($data)), $pid, $name . " / " . $posttitle);
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
				wp_redirect(get_post_permalink($pid));
				exit;
			} else {
				if ($path != '') {
					wp_redirect($path . '?oops=1');
					exit;
				}
			}
		} else {
			if ($path != '') {
				wp_redirect($path . '?oops=2');
				exit;
			}

		}
	}
}
add_action('init', 'mxp_wctpe2018_form_processing');