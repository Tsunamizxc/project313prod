<?php
/* Template Name: Мероприятия */
get_header();
$head = p313_page_head_args(
	'page_events',
	array(
		'number' => '«09»',
		'label'  => 'мероприятия',
		'title'  => 'Афиша событий',
		'sub'    => 'Концерты, мастер-классы и дни открытых дверей Project 313.',
	)
);
?>
<main class="main"><div data-events-list><?php get_template_part( 'template-parts/page-head', null, $head ); ?><section class="section section--top-sm"><div class="container"><div class="events-list" data-events-grid></div></div></section></div><article class="article panel-hidden" data-event-article></article></main>
<?php get_footer();
