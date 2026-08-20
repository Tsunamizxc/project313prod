<?php
/* Template Name: Награды */
get_header();
$head = p313_page_head_args(
	'page_awards',
	array(
		'label' => 'награды',
		'title' => '58 поводов гордиться',
		'sub'   => 'Гран-при, лауреатства и звание образцового коллектива — путь длиной в 11 лет.',
	)
);
$head['images'] = array_values(
	array_unique(
		array_merge(
			$head['images'] ?? array(),
			p313_gallery_urls( p313_field( 'page_awards_photos', array() ), 'p313-card' )
		)
	)
);
?>
<main class="main"><?php get_template_part( 'template-parts/page-head', null, $head ); ?><section class="section section--top-sm"><div class="container"><div class="filters year-filters" data-awards-filters></div><div class="awards-list" data-awards-list></div></div></section></main>
<?php get_footer();
