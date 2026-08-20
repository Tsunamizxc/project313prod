<?php
/* Template Name: Блог */
get_header();
$head = p313_page_head_args(
	'page_blog',
	array(
		'number' => '«08»',
		'label'  => 'блог',
		'title'  => 'Дневник студии',
		'sub'    => 'Заметки педагогов, советы родителям и истории из-за кулис.',
	)
);
?>
<main class="main"><div data-blog-list><?php get_template_part( 'template-parts/page-head', null, $head ); ?><section class="section section--top-sm"><div class="container"><div class="blog-grid" data-blog-grid></div></div></section></div><article class="article panel-hidden" data-blog-article></article></main>
<?php get_footer();
