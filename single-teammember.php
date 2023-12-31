<?php
get_header();
?>

<style type="text/css">
h1{
	font-size:30px;
}
h3{
	font-size:20px;
}
.portfolio > img{
	width:200px;
}
</style>

<?php while ( have_posts() ) : the_post(); ?>
<div class="container text-center">
<div class="portfolio"><img src="<?=get_the_post_thumbnail_url();?>" alt="" /></div>
<div class="single-page-post-heading"><h1>Name : <?=the_title(); ?></h1></div>
<div class="position"><h3>Position: <?=get_the_excerpt();?></h3></div>
<div class="content-here">Bio : <?=get_the_content();?></div>
<div class="col-sm-4 offset-sm-4"><a href="<?=site_url();?>?post_type=teammembers" class="btn btn-secondary" role="button">See All</a></div>
<a href="javascript:history.back()">Go Back</a>
</div>

<?php endwhile; ?>

<?php
get_footer();
?>