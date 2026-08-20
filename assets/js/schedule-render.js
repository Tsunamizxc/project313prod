(function () {
	'use strict';

	var data = window.P313 && Array.isArray(window.P313.SCHEDULE) ? window.P313.SCHEDULE : [];

	function esc(value) {
		return String(value || '')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	function openSignupForm(direction) {
		var modal = document.querySelector('[data-form-modal]');
		var select = modal ? modal.querySelector('[data-field-direction]') : null;
		if (select && direction) {
			select.value = direction;
		}
		var opener = document.querySelector('.header__cta[data-open-form]');
		if (opener) {
			opener.click();
		}
	}

	document.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-schedule-table] [data-open-form], [data-schedule-teaser] [data-open-form]');
		if (!btn) {
			return;
		}
		event.preventDefault();
		event.stopPropagation();
		openSignupForm(btn.getAttribute('data-direction') || '');
	});

	function tableRow(item) {
		return (
			'<div class="schedule-table__row">' +
				'<div>' +
					'<span class="schedule-table__time">' + esc(item.time) + '</span>' +
					'<span class="schedule-table__day-m">' + esc(item.day) + '</span>' +
				'</div>' +
				'<p class="schedule-table__dir">' + esc(item.dir) + '</p>' +
				'<span class="schedule-table__group">' + esc(item.group || '—') + '</span>' +
				'<span class="schedule-table__teacher">' + esc(item.teacher || '—') + '</span>' +
				'<span class="schedule-table__branch">' + esc(item.branch || '—') + '</span>' +
				'<div class="schedule-table__action">' +
					'<button class="schedule-table__btn" type="button" data-open-form data-direction="' + esc(item.dir) + '">Записаться</button>' +
				'</div>' +
			'</div>'
		);
	}

	function teaserRow(item) {
		var meta = [item.day, item.group, item.branch].filter(Boolean).join(' · ');
		return (
			'<div class="schedule-teaser__row">' +
				'<div class="schedule-teaser__info">' +
					'<span class="schedule-teaser__time">' + esc(item.time) + '</span>' +
					'<div>' +
						'<p class="schedule-teaser__dir">' + esc(item.dir) + '</p>' +
						'<p class="schedule-teaser__meta">' + esc(meta) + '</p>' +
					'</div>' +
				'</div>' +
				'<button class="schedule-teaser__book" type="button" data-open-form data-direction="' + esc(item.dir) + '">Запись</button>' +
			'</div>'
		);
	}

	function renderTable() {
		var table = document.querySelector('[data-schedule-table]');
		if (!table || !data.length) {
			return;
		}
		table.querySelectorAll('.schedule-table__row').forEach(function (row) {
			row.remove();
		});
		table.insertAdjacentHTML('beforeend', data.map(tableRow).join(''));
	}

	function renderTeaser() {
		var teaser = document.querySelector('[data-schedule-teaser]');
		if (!teaser || !data.length) {
			return;
		}
		teaser.innerHTML = data.slice(0, 4).map(teaserRow).join('');
	}

	function init() {
		renderTable();
		renderTeaser();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
