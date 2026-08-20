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
		wrap.innerHTML =
			'<div class="kids-grid">' +
			groups
				.map(function (group, index) {
					var href = group.url || '#';
					var num = String(index + 1).padStart(2, '0');
					var photo = group.photoUrl
						? '<div class="kids-card__photo-wrap"><img class="kids-card__photo" src="' +
							esc(group.photoUrl) +
							'" alt="' +
							esc(group.name) +
							'" loading="lazy"></div>'
						: '';
					return (
						'<a class="card kids-card kids-card--link reveal" href="' +
						esc(href) +
						'">' +
						photo +
						'<span class="kids-card__num">«' +
						num +
						'»</span>' +
						'<h3 class="kids-card__name">' +
						esc(group.name) +
						'</h3>' +
						(group.age ? '<p class="kids-card__age">' + esc(group.age) + '</p>' : '') +
						'<span class="link-arrow kids-card__more">Открыть группу</span>' +
						'</a>'
					);
				})
				.join('') +
			'</div>';
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
					var contest = item.contest || item.title || '';
					var result = item.result || item.place || '';
					var meta = [item.age, item.date, item.qty ? 'кол-во: ' + item.qty : '', item.level]
						.filter(Boolean)
						.join(' · ');
					return (
						'<div class="awards-list__row reveal">' +
						'<span class="awards-list__year">' +
						esc(item.year) +
						'</span><div><p class="awards-list__title">' +
						esc(contest) +
						'</p><p class="awards-list__place">' +
						esc(result) +
						(meta ? '</p><p class="awards-list__meta">' + esc(meta) + '</p>' : '</p>') +
						'</div><span class="awards-list__level">' +
						esc(item.qty || item.level || '') +
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

	function galleryAlbums() {
		function isAdminPhoto(src) {
			if (!src) {
				return false;
			}
			var value = String(src);
			if (value.indexOf('unsplash.com') !== -1) {
				return false;
			}
			return /^https?:\/\//i.test(value) || value.indexOf('/') === 0;
		}

		if (Array.isArray(data.GALLERY_ALBUMS) && data.GALLERY_ALBUMS.length) {
			return data.GALLERY_ALBUMS
				.map(function (album) {
					var photos = (album.photos || []).filter(isAdminPhoto);
					return {
						id: album.id,
						title: album.title,
						year: album.year,
						cat: album.cat,
						cover: isAdminPhoto(album.cover) ? album.cover : photos[0] || '',
						photos: photos,
						count: photos.length,
					};
				})
				.filter(function (album) {
					return album.photos.length;
				});
		}
		var grouped = {};
		(Array.isArray(data.GALLERY) ? data.GALLERY : []).forEach(function (item) {
			var src = item.photoUrl || item.photo;
			if (!isAdminPhoto(src)) {
				return;
			}
			var key = String(item.album || '') + '|' + String(item.year || '') + '|' + String(item.cat || '');
			if (!grouped[key]) {
				grouped[key] = {
					id: key,
					title: item.album || '',
					year: item.year,
					cat: item.cat,
					cover: src,
					photos: [],
				};
			}
			grouped[key].photos.push(src);
		});
		return Object.keys(grouped).map(function (key) {
			grouped[key].count = grouped[key].photos.length;
			return grouped[key];
		});
	}

	function enhanceGallery() {
		var grid = document.querySelector('[data-gallery-grid]');
		var cats = document.querySelector('[data-gallery-cats]');
		var years = document.querySelector('[data-gallery-years]');
		if (!grid) {
			return;
		}
		if (grid.getAttribute('data-albums-ready')) {
			return;
		}
		grid.setAttribute('data-albums-ready', '1');

		var albums = galleryAlbums();
		var openId = '';

		function activeValue(root, attr) {
			if (!root) {
				return 'Все';
			}
			var btn = root.querySelector('.tag--active[' + attr + ']');
			return btn ? btn.getAttribute(attr) || 'Все' : 'Все';
		}

		function filteredAlbums() {
			var cat = activeValue(cats, 'data-cat');
			var year = activeValue(years, 'data-year');
			return albums.filter(function (album) {
				var catOk = cat === 'Все' || album.cat === cat;
				var yearOk = year === 'Все' || String(album.year) === String(year);
				return catOk && yearOk;
			});
		}

		function openLightbox(src, alt) {
			var box = document.querySelector('[data-lightbox]');
			var img = document.querySelector('[data-lightbox-img]');
			if (!box || !img || !src) {
				return;
			}
			if (box._closeTimer) {
				clearTimeout(box._closeTimer);
				box._closeTimer = null;
			}
			box.classList.remove('lightbox--closing');
			img.src = src;
			img.alt = alt || '';
			box.classList.add('lightbox--open');
			box.setAttribute('aria-hidden', 'false');
		}

		function bindPhotos() {
			grid.querySelectorAll('[data-album-photo]').forEach(function (btn) {
				btn.addEventListener('click', function () {
					openLightbox(btn.getAttribute('data-album-photo') || '', btn.getAttribute('data-alt') || '');
				});
			});
		}

		function paintPhotos(album) {
			var meta = [album.cat, album.year].filter(Boolean).join(' · ');
			grid.innerHTML =
				'<div class="gallery-album-view" data-gallery-view>' +
					'<button class="link-arrow gallery-album__back" type="button" data-gallery-back>' +
						'К событиям' +
					'</button>' +
					'<h2 class="gallery-album__heading">' +
					esc(album.title) +
					'</h2>' +
					(meta ? '<p class="gallery-album__sub">' + esc(meta) + '</p>' : '') +
					'<div class="gallery-masonry">' +
					(album.photos || [])
						.map(function (src) {
							return (
								'<button class="gallery-masonry__item reveal" type="button" data-album-photo="' +
								esc(src) +
								'" data-alt="' +
								esc(album.title) +
								'">' +
								'<img class="gallery-masonry__img" src="' +
								esc(src) +
								'" alt="' +
								esc(album.title) +
								'" loading="lazy">' +
								'</button>'
							);
						})
						.join('') +
					'</div>' +
				'</div>';
			var back = grid.querySelector('[data-gallery-back]');
			if (back) {
				back.addEventListener('click', function () {
					openId = '';
					paintAlbums();
				});
			}
			bindPhotos();
		}

		function paintAlbums() {
			var rows = filteredAlbums();
			if (openId) {
				var current = null;
				rows.forEach(function (album) {
					if (String(album.id) === String(openId)) {
						current = album;
					}
				});
				if (current) {
					paintPhotos(current);
					return;
				}
				openId = '';
			}
			if (!rows.length) {
				grid.innerHTML =
					'<div class="empty-state">' +
						'<h3 class="empty-state__title">Здесь пока пусто</h3>' +
						'<p class="empty-state__text">Для выбранных фильтров нет событий. Попробуйте другой год или категорию.</p>' +
					'</div>';
				return;
			}
			grid.innerHTML =
				'<div class="gallery-albums" data-gallery-view>' +
				rows
					.map(function (album) {
						var meta = [album.cat, album.year, album.count ? album.count + ' фото' : '']
							.filter(Boolean)
							.join(' · ');
						return (
							'<button class="card gallery-album reveal" type="button" data-gallery-album="' +
							esc(album.id) +
							'">' +
							(album.cover
								? '<img class="gallery-album__img" src="' +
									esc(album.cover) +
									'" alt="' +
									esc(album.title) +
									'" loading="lazy">'
								: '') +
							'<div class="gallery-album__body">' +
							(meta ? '<p class="gallery-album__meta">' + esc(meta) + '</p>' : '') +
							'<h3 class="gallery-album__title">' +
							esc(album.title) +
							'</h3>' +
							'<span class="link-arrow gallery-album__more">Открыть альбом</span>' +
							'</div></button>'
						);
					})
					.join('') +
				'</div>';
			grid.querySelectorAll('[data-gallery-album]').forEach(function (btn) {
				btn.addEventListener('click', function () {
					openId = btn.getAttribute('data-gallery-album') || '';
					paintAlbums();
				});
			});
		}

		observe(grid, function () {
			if (grid.querySelector('[data-gallery-view]')) {
				return;
			}
			paintAlbums();
		});

		if (cats) {
			cats.addEventListener('click', function (event) {
				if (!event.target.closest('[data-cat]')) {
					return;
				}
				openId = '';
				setTimeout(paintAlbums, 0);
			});
		}
		if (years) {
			years.addEventListener('click', function (event) {
				if (!event.target.closest('[data-year]')) {
					return;
				}
				openId = '';
				setTimeout(paintAlbums, 0);
			});
		}
		paintAlbums();
	}

	function vkVideoHtml(item) {
		if (item && item.videoHtml) {
			return item.videoHtml;
		}
		var raw = item && item.video ? String(item.video) : '';
		if (!raw) {
			return '';
		}
		var oid = '';
		var id = '';
		var ext = raw.match(/video_ext\.php\?([^#\s"']+)/i);
		var pair = raw.match(/(?:vk\.com|vkvideo\.ru)\/video(-?\d+)_(\d+)/i);
		if (ext && ext[1]) {
			ext[1].split('&').forEach(function (part) {
				var bits = part.split('=');
				if (bits[0] === 'oid') {
					oid = decodeURIComponent(bits[1] || '');
				}
				if (bits[0] === 'id') {
					id = decodeURIComponent(bits[1] || '');
				}
			});
		} else if (pair) {
			oid = pair[1];
			id = pair[2];
		}
		if (!oid || !id) {
			return '';
		}
		return (
			'<div class="review-card__video"><iframe src="https://vk.com/video_ext.php?oid=' +
			esc(oid) +
			'&id=' +
			esc(id) +
			'&hd=2" title="Видео VK" allow="autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;" allowfullscreen loading="lazy"></iframe></div>'
		);
	}

	function reviewCardHtml(item) {
		var stars = '★'.repeat(item.rating || 0);
		var video = vkVideoHtml(item);
		return (
			'<article class="card review-card reveal' +
			(video ? ' review-card--video' : '') +
			'">' +
			(video ? video : '') +
			'<div class="review-card__stars" aria-label="' +
			esc(item.rating) +
			' из 5">' +
			stars +
			'</div>' +
			(item.text ? '<p class="review-card__text">«' + esc(item.text) + '»</p>' : '') +
			'<div class="hairline" style="width: 40%; margin-top: 1.5rem;"></div>' +
			'<p class="review-card__name">' +
			esc(item.name) +
			'</p>' +
			'<p class="review-card__role">' +
			esc(item.role) +
			'</p></article>'
		);
	}

	function enhanceReviews() {
		var reviews = Array.isArray(data.REVIEWS) ? data.REVIEWS : [];
		var home = document.querySelector('[data-reviews-home]');
		var page = document.querySelector('[data-reviews-page]');
		if (home) {
			home.innerHTML = reviews.map(reviewCardHtml).join('');
		}
		if (page) {
			page.innerHTML = reviews.map(reviewCardHtml).join('');
		}
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

	function bindMenuBackdrop() {
		var header = document.querySelector('[data-header]');
		var menu = document.querySelector('[data-menu]');
		var burger = document.querySelector('[data-burger]');
		var backdrop = document.querySelector('[data-menu-backdrop]');
		if (!menu || !backdrop || !header) {
			return;
		}

		function placeBackdrop() {
			var rect = header.getBoundingClientRect();
			backdrop.style.top = Math.max(0, Math.ceil(rect.bottom)) + 'px';
		}

		function sync() {
			var open = menu.classList.contains('header__drawer--open');
			header.classList.toggle('header--menu-open', open);
			backdrop.classList.toggle('is-open', open);
			backdrop.hidden = !open;
			backdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
			if (open) {
				placeBackdrop();
			} else {
				backdrop.style.top = '';
			}
		}

		backdrop.addEventListener('click', function () {
			if (!menu.classList.contains('header__drawer--open')) {
				return;
			}
			if (burger) {
				burger.click();
				return;
			}
			menu.classList.remove('header__drawer--open');
			sync();
		});

		window.addEventListener('resize', function () {
			if (menu.classList.contains('header__drawer--open')) {
				placeBackdrop();
			}
		});

		if (typeof MutationObserver !== 'undefined') {
			new MutationObserver(function () {
				sync();
				if (menu.classList.contains('header__drawer--open')) {
					requestAnimationFrame(placeBackdrop);
				}
			}).observe(menu, { attributes: true, attributeFilter: ['class'] });
		}
		sync();
	}

	function init() {
		renderKids();
		bindGroupBranchSwitch();
		bindMenuBackdrop();
		enhanceTeacherCards();
		enhanceAwards();
		enhanceReviews();
		enhanceGalleryYears();
		enhanceGallery();
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
