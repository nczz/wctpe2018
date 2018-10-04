
 <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
</div>

<?php
get_footer();