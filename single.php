<?php
get_header();
?>
<div class="container">
<?php
if (have_posts()) {
	while (have_posts()) {
		the_post();
		//
		the_content();
		//
	} // end while
} // end if
?>
<a class="f_btn" href="/submit">我也想投稿</a>
</div>
<?php
get_footer();
