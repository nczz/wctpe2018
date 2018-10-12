<?php get_header();?>

<div class="container home_content">

<?php if (have_posts()): ?>

<!-- Add the pagination functions here. -->
<div class="row">
<!-- Start of the main loop. -->
<?php while (have_posts()): the_post();
	// $datetime = get_post_meta(get_the_ID(), 'wctp2018-post-datetime', true);
	// $email = get_post_meta(get_the_ID(), 'wctp2018-author-email', true);
	// $website = get_post_meta(get_the_ID(), 'wctpe2018-author-website', true);
	$name = get_post_meta(get_the_ID(), 'wctp2018-author-name', true);
	$title = get_post_meta(get_the_ID(), 'wctp2018-post-title', true);
	$content = get_post_meta(get_the_ID(), 'wctp2018-post-content', true);
	// $image_full = get_post_meta(get_the_ID(), 'wctp2018-post-image-full', true);
	$image_large = get_post_meta(get_the_ID(), 'wctp2018-post-image-large', true);

	echo '<div class="col-md-3 m_b_20 post"><div class="box"><div class=" post_img"><a href="' . get_permalink($post->ID) . '"><img src="' . $image_large . '"/></a></div><a href="' . get_permalink($post->ID) . '" class="name"><h2 >' . $title . ' By ' . $name . '</h2></a></div></div>';
endwhile
?>
<!-- End of the main loop -->

<!-- Add the pagination functions here. -->

<div class="nav-previous alignleft"><?php previous_posts_link('Older posts / 後一頁');?></div>
<div class="nav-next alignright"><?php next_posts_link('Newer posts / 前一頁');?></div>
</div>
<?php else: ?>
<p><?php echo 'Sorry, no posts here.'; ?></p>
<?php endif;?>
<a class="f_btn" href="/submit">這是什麼？ / What's this?</a>
</div>
</div>
<?php get_footer();?>
