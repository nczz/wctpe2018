<?php
get_header();
if (have_posts()) {
	while (have_posts()) {
		the_post();
		//
		the_content();
		//
	} // end while
} // end if
get_footer();