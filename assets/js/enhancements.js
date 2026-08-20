(function () {
	'use strict';

	var data = window.P313 || {};

	function esc(value) {
		return String(value || '')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	function serviceById(id) {
		var list = Array.isArray(data.SERVICES) ? data.SERVICES : [];
		for (var i = 0; i < list.length; i++) {
			if (String(list[i].id) === String(id)) {
				return list[i];
			}
		}
		return null;
	}

	function servicePhotos(item) {
		if (!item) {
			return [];
		}
		var photos = Array.isArray(item.photos) ? item.photos.filter(Boolean) : [];
		if (!photos.length && item.photoUrl) {
			photos = [item.photoUrl];
		}
		return photos;
	}

	function sliderHtml(photos, alt, imgClass) {
		if (!photos.length) {
			return '';
		}
		if (photos.length === 1) {
			return '<img class="' + esc(imgClass) + '" src="' + esc(photos[0]) + '" alt="' + esc(alt) + '" loading="lazy">';
		}
		return (
			'<div class="card-slider" data-card-slider>' +
				'<div class="card-slider__viewport">' +
					photos
						.map(function (src, index) {
							return (
								'<img class="card-slider__img ' +
								esc(imgClass) +
								(index === 0 ? ' is-active' : '') +
								'" src="' +
								esc(src) +
								'" alt="' +
								esc(alt) +
								'" loading="' +
								(index === 0 ? 'eager' : 'lazy') +
								'">'
							);
						})
						.join('') +
				'</div>' +
				'<button class="card-slider__nav card-slider__nav--prev" type="button" aria-label="Предыдущее фото" data-slider-prev></button>' +
				'<button class="card-slider__nav card-slider__nav--next" type="button" aria-label="Следующее фото" data-slider-next></button>' +
				'<div class="card-slider__dots" data-slider-dots>' +
					photos
						.map(function (_src, index) {
							return (
								'<button class="card-slider__dot' +
								(index === 0 ? ' is-active' : '') +
								'" type="button" aria-label="Фото ' +
								(index + 1) +
								'" data-slider-to="' +
								index +
								'"></button>'
							);
						})
						.join('') +
				'</div>' +
			'</div>'
		);
	}

	function bindSlider(root) {
		if (!root || root.getAttribute('data-slider-ready')) {
			return;
		}
		var slides = root.querySelectorAll('.card-slider__img');
		var dots = root.querySelectorAll('[data-slider-to]');
		if (slides.length < 2) {
			return;
		}
		root.setAttribute('data-slider-ready', '1');
		var index = 0;

		function show(next) {
			index = (next + slides.length) % slides.length;
			slides.forEach(function (slide, i) {
				slide.classList.toggle('is-active', i === index);
			});
			dots.forEach(function (dot, i) {
				dot.classList.toggle('is-active', i === index);
			});
		}

		root.querySelector('[data-slider-prev]')?.addEventListener('click', function (event) {
			event.preventDefault();
			event.stopPropagation();
			show(index - 1);
		});
		root.querySelector('[data-slider-next]')?.addEventListener('click', function (event) {
			event.preventDefault();
			event.stopPropagation();
			show(index + 1);
		});
		dots.forEach(function (dot) {
			dot.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();
				show(parseInt(dot.getAttribute('data-slider-to'), 10) || 0);
			});
		});

		var startX = 0;
		root.addEventListener(
			'touchstart',
			function (event) {
				startX = event.changedTouches[0].clientX;
			},
			{ passive: true }
		);
		root.addEventListener(
			'touchend',
			function (event) {
				var dx = event.changedTouches[0].clientX - startX;
				if (Math.abs(dx) < 40) {
					return;
				}
				event.stopPropagation();
				show(dx < 0 ? index + 1 : index - 1);
			},
			{ passive: true }
		);
	}

	function enhanceServiceCard(card) {
		if (!card || card.getAttribute('data-slider-bound')) {
			return;
		}
		var item = serviceById(card.getAttribute('data-service-id'));
		var photos = servicePhotos(item);
		if (photos.length < 2) {
			return;
		}
		var media = card.querySelector('.service-card__media');
		if (!media) {
			return;
		}
		card.setAttribute('data-slider-bound', '1');
		var price = media.querySelector('.service-card__price');
		var priceHtml = price ? price.outerHTML : '';
		media.innerHTML = sliderHtml(photos, item.title, 'service-card__img') + priceHtml;
		bindSlider(media.querySelector('[data-card-slider]'));
	}

	function enhanceServiceGrid(root) {
		if (!root) {
			return;
		}
		root.querySelectorAll('[data-service-id]').forEach(enhanceServiceCard);
	}

	function enhanceServiceModal(content) {
		if (!content) {
			return;
		}
		var signup = content.querySelector('[data-service-signup]');
		if (signup) {
			signup.textContent = (data.strings && data.strings.formSubmit) || 'Записаться на просмотр';
		}
		var img = content.querySelector('.modal__media');
		if (!img || img.closest('[data-card-slider]')) {
			return;
		}
		var dialog = content.closest('[data-service-content]') || content;
		var titleNode = dialog.querySelector('h3');
		var title = titleNode ? titleNode.textContent.trim() : '';
		var item = null;
		var list = Array.isArray(data.SERVICES) ? data.SERVICES : [];
		for (var i = 0; i < list.length; i++) {
			if (list[i].title === title) {
				item = list[i];
				break;
			}
		}
		var photos = servicePhotos(item);
		if (photos.length < 2) {
			return;
		}
		var wrap = document.createElement('div');
		wrap.className = 'modal__media-slider';
		wrap.innerHTML = sliderHtml(photos, title, 'modal__media');
		img.replaceWith(wrap);
		bindSlider(wrap.querySelector('[data-card-slider]'));
	}

	function renderKids() {
		var wrap = document.querySelector('[data-kids-grid]');
		if (!wrap) {
			return;
		}
		var groups = Array.isArray(data.KIDS_GROUPS) ? data.KIDS_GROUPS : [];
		if (!groups.length) {
			return;
		}
		wrap.innerHTML = groups
			.map(function (group) {
				var href = group.url || '#';
				var photo = group.photoUrl
					? '<img class="teacher-card__img" src="' +
						esc(group.photoUrl) +
						'" alt="' +
						esc(group.name) +
						'" loading="lazy">'
					: '<div class="teacher-card__img teacher-card__img--empty"></div>';
				var members = Array.isArray(group.members) ? group.members : [];
				var membersHtml = members.length
					? '<div class="teachers-grid kids-members-grid">' +
						members
							.map(function (member) {
								var mphoto = member.photoUrl
									? '<img class="teacher-card__img" src="' +
										esc(member.photoUrl) +
										'" alt="' +
										esc(member.name) +
										'" loading="lazy">'
									: '<div class="teacher-card__img teacher-card__img--empty"></div>';
								return (
									'<article class="card teacher-card">' +
									'<div style="overflow: hidden;">' +
									mphoto +
									'</div>' +
									'<div class="teacher-card__body"><h3 class="teacher-card__name">' +
									esc(member.name) +
									'</h3></div></article>'
								);
							})
							.join('') +
						'</div>'
					: '';
				return (
					'<section class="kids-group-block">' +
					'<a class="card card--hover teacher-card kids-card--link reveal" href="' +
					esc(href) +
					'">' +
					'<div style="overflow: hidden;">' +
					photo +
					'</div>' +
					'<div class="teacher-card__body">' +
					(group.age ? '<p class="teacher-card__exp">' + esc(group.age) + '</p>' : '') +
					'<h3 class="teacher-card__name">' +
					esc(group.name) +
					'</h3>' +
					'<p class="teacher-card__role">' +
					esc(group.note || '') +
					'</p>' +
					'</div></a>' +
					membersHtml +
					'</section>'
				);
			})
			.join('');
	}

	function mediaUrl(value, w, h) {
		if (!value) {
			return '';
		}
		var src = String(value);
		if (/^https?:\/\//i.test(src) || src.indexOf('/') === 0 || src.indexOf('data:') === 0) {
			return src;
		}
		return (
			'https://images.unsplash.com/photo-' +
			src +
			'?w=' +
			(w || 500) +
			'&h=' +
			(h || 620) +
			'&fit=crop&auto=format&q=80'
		);
	}

	function teacherById(id) {
		var list = Array.isArray(data.TEACHERS) ? data.TEACHERS : [];
		for (var i = 0; i < list.length; i++) {
			if (String(list[i].id) === String(id) || list[i].name === id) {
				return list[i];
			}
		}
		return null;
	}

	function teacherPhoto(teacher, w, h) {
		if (!teacher) {
			return '';
		}
		return mediaUrl(teacher.photoUrl || teacher.photo, w, h);
	}

	function metaBlock(label, value, extraClass) {
		if (!value) {
			return '';
		}
		return (
			'<div class="teacher-card__meta' +
			(extraClass ? ' ' + extraClass : '') +
			'"><span class="teacher-card__meta-label">' +
			esc(label) +
			'</span><p class="teacher-card__meta-text">' +
			esc(value).replace(/\n/g, '<br>') +
			'</p></div>'
		);
	}

	function teacherActions(teacher, extraClass) {
		if (!teacher || !teacher.isLeader) {
			return '';
		}
		var more = teacher.moreUrl || (data.FOUNDER && data.FOUNDER.moreUrl) || 'https://project313.ru/founder/';
		return (
			'<div class="teacher-card__actions' +
			(extraClass ? ' ' + extraClass : '') +
			'" data-teacher-actions>' +
			'<button class="btn btn--primary" type="button" data-open-form data-teacher-signup>Записаться</button>' +
			'<a class="btn btn--secondary" href="' +
			esc(more) +
			'">Подробнее</a>' +
			'</div>'
		);
	}

	function teacherBodyHtml(teacher) {
		if (!teacher) {
			return '';
		}
		return (
			'<h3 class="teacher-card__name">' +
			esc(teacher.name) +
			'</h3>' +
			(teacher.role ? '<p class="teacher-card__role">' + esc(teacher.role) + '</p>' : '') +
			metaBlock('Опыт', teacher.exp) +
			metaBlock('Образование', teacher.education) +
			metaBlock('О преподавателе', teacher.bio, 'teacher-card__about') +
			teacherActions(teacher)
		);
	}

	function enhanceTeacherCards() {
		var grid = document.querySelector('[data-teachers-grid]');
		if (!grid) {
			return;
		}
		grid.querySelectorAll('[data-teacher-id]').forEach(function (card) {
			var teacher = teacherById(card.getAttribute('data-teacher-id'));
			var body = card.querySelector('.teacher-card__body');
			if (!teacher || !body || body.getAttribute('data-enhanced')) {
				return;
			}
			card.classList.add('teacher-card--full');
			body.innerHTML = teacherBodyHtml(teacher);
			body.setAttribute('data-enhanced', '1');
			var actions = body.querySelector('[data-teacher-actions]');
			if (actions) {
				actions.addEventListener('click', function (event) {
					event.stopPropagation();
				});
			}
		});
	}

	function enhanceTeacherModal(root) {
		if (!root || root.getAttribute('data-teacher-enhanced')) {
			return;
		}
		var titleNode = root.querySelector('h3');
		var title = titleNode ? titleNode.textContent.trim() : '';
		var teacher = lastTeacherId ? teacherById(lastTeacherId) : null;
		if (!teacher && title) {
			teacher = teacherById(title);
		}
		if (!teacher) {
			return;
		}
		root.setAttribute('data-teacher-enhanced', '1');
		var closeIcon = root.querySelector('[data-teacher-close]');
		var closeHtml = closeIcon ? closeIcon.outerHTML : '';
		var photo = teacherPhoto(teacher, 600, 760);
		root.innerHTML =
			(photo ? '<img class="modal__media" src="' + esc(photo) + '" alt="' + esc(teacher.name) + '">' : '') +
			'<div class="modal__body">' +
			closeHtml +
			'<h3 class="teacher-card__name" style="font-size: 1.875rem;">' +
			esc(teacher.name) +
			'</h3>' +
			(teacher.role ? '<p class="teacher-card__role">' + esc(teacher.role) + '</p>' : '') +
			metaBlock('Опыт', teacher.exp) +
			metaBlock('Образование', teacher.education) +
			metaBlock('О преподавателе', teacher.bio, 'teacher-card__about teacher-card__about--modal') +
			teacherActions(teacher, 'teacher-card__actions--modal') +
			'</div>';
		root.querySelectorAll('[data-teacher-close]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var overlay = document.querySelector('[data-teacher-modal] .modal__overlay');
				if (overlay) {
					overlay.click();
				}
			});
		});
	}

	var lastTeacherId = '';
	document.addEventListener('click', function (event) {
		var card = event.target.closest('[data-teacher-id]');
		if (card) {
			lastTeacherId = card.getAttribute('data-teacher-id') || '';
		}
	});

	function yearList() {
		var years = Array.isArray(data.YEAR_RANGE) && data.YEAR_RANGE.length ? data.YEAR_RANGE.slice() : [];
		if (!years.length) {
			var current = new Date().getFullYear();
			for (var y = current; y >= 2016; y--) {
				years.push(String(y));
			}
		}
		return years.map(String);
	}

	function renderExpandableYears(container, selected, onSelect) {
		if (!container) {
			return;
		}
		var visibleFrom = parseInt(data.YEAR_VISIBLE, 10) || 2020;
		var years = yearList();
		var primary = ['Все'];
		var extra = [];
		years.forEach(function (year) {
			if (parseInt(year, 10) >= visibleFrom) {
				primary.push(year);
			} else {
				extra.push(year);
			}
		});
		var expanded = container.getAttribute('data-years-open') === '1';

		function tag(year) {
			return (
				'<button class="tag' +
				(String(year) === String(selected) ? ' tag--active' : '') +
				'" type="button" data-year="' +
				esc(year) +
				'">' +
				esc(year) +
				'</button>'
			);
		}

		container.classList.add('year-filters');
		container.innerHTML =
			primary.map(tag).join('') +
			(extra.length
				? '<button class="tag year-filters__toggle' +
					(expanded ? ' tag--active' : '') +
					'" type="button" data-years-toggle>' +
					(expanded ? 'Свернуть' : 'Ещё') +
					'</button><div class="year-filters__more"' +
					(expanded ? '' : ' hidden') +
					'>' +
					extra.map(tag).join('') +
					'</div>'
				: '');

		container.querySelectorAll('[data-year]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				onSelect(btn.getAttribute('data-year') || 'Все');
			});
		});
		var toggle = container.querySelector('[data-years-toggle]');
		if (toggle) {
			toggle.addEventListener('click', function () {
				container.setAttribute('data-years-open', expanded ? '0' : '1');
				renderExpandableYears(container, selected, onSelect);
			});
		}
	}

	function enhanceAwards() {
		var filters = document.querySelector('[data-awards-filters]');
		var list = document.querySelector('[data-awards-list]');
		if (!filters || !list) {
			return;
		}
		var awards = Array.isArray(data.AWARDS) ? data.AWARDS : [];
		var selected = 'Все';

		function paintList() {
			var rows = awards.filter(function (item) {
				return selected === 'Все' || String(item.year) === String(selected);
			});
			if (!rows.length) {
				list.innerHTML = '<p class="group-page__empty">Наград за этот год пока нет.</p>';
				return;
			}
			list.innerHTML = rows
				.map(function (item) {
					return (
						'<div class="awards-list__row reveal">' +
						'<span class="awards-list__year">' +
						esc(item.year) +
						'</span><div><p class="awards-list__title">' +
						esc(item.title) +
						'</p><p class="awards-list__place">' +
						esc(item.place) +
						'</p></div><span class="awards-list__level">' +
						esc(item.level) +
						'</span></div>'
					);
				})
				.join('');
		}

		function paint() {
			renderExpandableYears(filters, selected, function (year) {
				selected = year;
				paint();
			});
			paintList();
		}

		paint();
	}

	function enhanceGalleryYears() {
		var container = document.querySelector('[data-gallery-years]');
		if (!container || container.getAttribute('data-years-ready')) {
			return;
		}
		var visibleFrom = parseInt(data.YEAR_VISIBLE, 10) || 2020;
		var extra = Array.prototype.filter.call(container.querySelectorAll('[data-year]'), function (btn) {
			var year = parseInt(btn.getAttribute('data-year'), 10);
			return year && year < visibleFrom;
		});
		if (extra.length) {
			var wrap = document.createElement('div');
			wrap.className = 'year-filters__more';
			wrap.hidden = true;
			extra.forEach(function (btn) {
				wrap.appendChild(btn);
			});
			var toggle = document.createElement('button');
			toggle.className = 'tag year-filters__toggle';
			toggle.type = 'button';
			toggle.textContent = 'Ещё';
			toggle.addEventListener('click', function () {
				wrap.hidden = !wrap.hidden;
				toggle.classList.toggle('tag--active', !wrap.hidden);
				toggle.textContent = wrap.hidden ? 'Ещё' : 'Свернуть';
			});
			container.classList.add('year-filters');
			container.appendChild(toggle);
			container.appendChild(wrap);
		}
		container.setAttribute('data-years-ready', '1');
	}

	function observe(target, callback) {
		if (!target || typeof MutationObserver === 'undefined') {
			return;
		}
		var timer = null;
		var observer = new MutationObserver(function () {
			if (timer) {
				clearTimeout(timer);
			}
			timer = setTimeout(callback, 30);
		});
		observer.observe(target, { childList: true, subtree: true });
		callback();
	}

	function bindGroupBranchSwitch() {
		var root = document.querySelector('[data-group-branch-switch]');
		var grid = document.querySelector('[data-group-members]');
		if (!root || !grid) {
			return;
		}
		var empty = document.querySelector('[data-group-empty]');
		var cards = grid.querySelectorAll('[data-member-branch]');

		function apply(branch) {
			var visible = 0;
			cards.forEach(function (card) {
				var value = card.getAttribute('data-member-branch') || '';
				var show = !value || value === branch;
				card.classList.toggle('panel-hidden', !show);
				if (show) {
					visible += 1;
				}
			});
			if (empty) {
				empty.classList.toggle('panel-hidden', visible > 0);
			}
			grid.classList.toggle('panel-hidden', visible === 0);
		}

		root.querySelectorAll('[data-group-branch]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				root.querySelectorAll('[data-group-branch]').forEach(function (item) {
					item.classList.remove('tag--active');
				});
				btn.classList.add('tag--active');
				apply(btn.getAttribute('data-group-branch') || 'krasny');
			});
		});

		apply('krasny');
	}

	var revealObserver = null;

	function bindReveals(root) {
		var nodes = (root || document).querySelectorAll('.reveal:not(.is-visible)');
		if (!nodes.length) {
			return;
		}
		if (typeof IntersectionObserver === 'undefined') {
			nodes.forEach(function (el) {
				el.classList.add('is-visible');
			});
			return;
		}
		if (!revealObserver) {
			revealObserver = new IntersectionObserver(
				function (entries) {
					entries.forEach(function (entry) {
						if (!entry.isIntersecting) {
							return;
						}
						entry.target.classList.add('is-visible');
						revealObserver.unobserve(entry.target);
					});
				},
				{ threshold: 0.01, rootMargin: '0px 0px 0px 0px' }
			);
		}
		nodes.forEach(function (el) {
			revealObserver.observe(el);
		});
	}

	function watchReveals() {
		bindReveals(document);
		if (typeof MutationObserver === 'undefined' || !document.body) {
			return;
		}
		var timer = null;
		var observer = new MutationObserver(function () {
			if (timer) {
				clearTimeout(timer);
			}
			timer = setTimeout(function () {
				bindReveals(document);
			}, 30);
		});
		observer.observe(document.body, { childList: true, subtree: true });
	}

	function init() {
		renderKids();
		bindGroupBranchSwitch();
		enhanceTeacherCards();
		enhanceAwards();
		enhanceGalleryYears();
		enhanceServiceGrid(document.querySelector('[data-services-home]'));
		enhanceServiceGrid(document.querySelector('[data-services-page]'));
		watchReveals();
		observe(document.querySelector('[data-services-home]'), function () {
			enhanceServiceGrid(document.querySelector('[data-services-home]'));
		});
		observe(document.querySelector('[data-services-page]'), function () {
			enhanceServiceGrid(document.querySelector('[data-services-page]'));
		});
		observe(document.querySelector('[data-service-content]'), function () {
			enhanceServiceModal(document.querySelector('[data-service-content]'));
		});
		observe(document.querySelector('[data-teacher-content]'), function () {
			enhanceTeacherModal(document.querySelector('[data-teacher-content]'));
		});
		observe(document.querySelector('[data-teachers-grid]'), function () {
			enhanceTeacherCards();
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
