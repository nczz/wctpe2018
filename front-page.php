<?php get_header();?>
<?php if (have_posts()): ?>

<!-- Add the pagination functions here. -->

<!-- Start of the main loop. -->
<?php while (have_posts()): the_post();
	// $datetime = get_post_meta(get_the_ID(), 'wctp2018-post-datetime', true);
	// $email = get_post_meta(get_the_ID(), 'wctp2018-author-email', true);
	// $website = get_post_meta(get_the_ID(), 'wctpe2018-author-website', true);
	$name = get_post_meta(get_the_ID(), 'wctp2018-author-name', true);
	$title = get_post_meta(get_the_ID(), 'wctp2018-post-title', true);
	// $content = get_post_meta(get_the_ID(), 'wctp2018-post-content', true);
	// $image_full = get_post_meta(get_the_ID(), 'wctp2018-post-image-full', true);
	$image_large = get_post_meta(get_the_ID(), 'wctp2018-post-image-large', true);

	echo $name . ":" . $title . '<img src="' . $image_large . '"/>'; //小豬～ 樣式組裝的架構在這邊！
endwhile
?>
<!-- End of the main loop -->

<!-- Add the pagination functions here. -->

<div class="nav-previous alignleft"><?php previous_posts_link('Older posts / 後一頁');?></div>
<div class="nav-next alignright"><?php next_posts_link('Newer posts / 前一頁');?></div>

<?php else: ?>
<p><?php echo 'Sorry, no posts here.'; ?></p>
<?php endif;?>
<?php get_footer();?>
