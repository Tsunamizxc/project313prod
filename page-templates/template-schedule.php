<?php
/* Template Name: Расписание */
get_header();
$head = p313_page_head_args(
	'page_schedule',
	array(
		'number' => '«04»',
		'label'  => 'расписание',
		'title'  => 'Когда мы танцуем',
		'sub'    => 'Два зала, шесть направлений, каждый день недели. Запишитесь прямо из строки.',
	)
);
?>
<main class="main"><?php get_template_part( 'template-parts/page-head', null, $head ); ?><section class="section section--top-sm"><div class="container"><div class="schedule-table schedule-table--extended" data-schedule-table><div class="schedule-table__head"><span>Время</span><span>Направление</span><span>Группа</span><span>Педагог</span><span>Филиал</span><span></span></div></div></div></section></main>
<?php get_footer();
