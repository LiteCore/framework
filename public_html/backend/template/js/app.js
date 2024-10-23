
// Stylesheet Loader
$.loadStylesheet = function(url, options) {

	options = $.extend(options || {}, {
		rel: 'stylesheet',
		href: url,
		cache: true
	});

	$('<link>', options).appendTo('head');
}

// JavaScript Loader
$.loadScript = function(url, options) {

	options = $.extend(options || {}, {
		method: 'GET',
		dataType: 'script',
		cache: true
	});

	return jQuery.ajax(url, options);
};

// Escape HTML
function escapeHTML(string) {
	let entityMap = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;',
		'/': '&#x2F;'
	};
	return String(string).replace(/[&<>"'\/]/g, function (s) {
		return entityMap[s];
	});
};

// Money Formatting
Number.prototype.toMoney = function(use_html = false) {
	var n = this,
		c = _env.session.currency.decimals,
		d = _env.session.language.decimal_point,
		t = _env.session.language.thousands_separator,
		p = _env.session.currency.prefix,
		x = _env.session.currency.suffix,
		u = _env.session.currency.code,
		s = n < 0 ? '-' : '',
		i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + '',
		f = n - i,
		j = (j = i.length) > 3 ? j % 3 : 0;

	return s + p + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(f).toFixed(c).slice(2) : '') + x;
}

// Keep-alive
let keepAlive = setInterval(function(){
	$.get({
		url: window._env.platform.path + 'ajax/keep_alive',
		cache: false
	});
}, 60e3);

// Toggle Buttons (data-toggle="buttons")
$('body').on('click', '[data-toggle="buttons"] :checkbox', function(){
	if ($(this).is(':checked')) {
		$(this).closest('.btn').addClass('active');
	} else {
		$(this).closest('.btn').removeClass('active');
	}
});

$('body').on('click', '[data-toggle="buttons"] :radio', function(){
	$(this).closest('.btn').addClass('active').siblings().removeClass('active');
});

/*
 * jQuery Context Menu
 * by LiteCore
 */

+function($) {

	$.fn.contextMenu = function(config){
		this.each(function(){

			this.config = config;

			self = this;

			$(this).on('contextmenu').on({

			});

		});
	}

}(jQuery);


/* ========================================================================
 * Bootstrap: dropdown.js v3.4.1
 * https://getbootstrap.com/docs/3.4/javascript/#dropdowns
 * ========================================================================
 * Copyright 2011-2019 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */

+function ($) {
	'use strict';

	// DROPDOWN CLASS DEFINITION
	// =========================

	let backdrop = '.dropdown-backdrop'
	let toggle   = '[data-toggle="dropdown"]'
	let Dropdown = function (element) {
		$(element).on('click.bs.dropdown', this.toggle)
	}

	Dropdown.VERSION = '3.4.1'

	function getParent($this) {
		let selector = $this.attr('data-target')

		if (!selector) {
			selector = $this.attr('href')
			selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
		}

		let $parent = selector !== '#' ? $(document).find(selector) : null

		return $parent && $parent.length ? $parent : $this.parent()
	}

	function clearMenus(e) {
		if (e && e.which === 3) return
		$(backdrop).remove()
		$(toggle).each(function () {
			let $this         = $(this)
			let $parent       = getParent($this)
			let relatedTarget = { relatedTarget: this }

			if (!$parent.hasClass('open')) return

			if (e && e.type == 'click' && /input|textarea/i.test(e.target.tagName) && $.contains($parent[0], e.target)) return

			$parent.trigger(e = $.Event('hide.bs.dropdown', relatedTarget))

			if (e.isDefaultPrevented()) return

			$this.attr('aria-expanded', 'false')
			$parent.removeClass('open').trigger($.Event('hidden.bs.dropdown', relatedTarget))
		})
	}

	Dropdown.prototype.toggle = function (e) {
		let $this = $(this)

		if ($this.is('.disabled, :disabled')) return

		let $parent  = getParent($this)
		let isActive = $parent.hasClass('open')

		clearMenus()

		if (!isActive) {
			if ('ontouchstart' in document.documentElement && !$parent.closest('.navbar-nav').length) {
				// if mobile we use a backdrop because click events don't delegate
				$(document.createElement('div'))
					.addClass('dropdown-backdrop')
					.insertAfter($parent)
					.on('click', clearMenus)
			}

			let relatedTarget = { relatedTarget: this }
			$parent.trigger(e = $.Event('show.bs.dropdown', relatedTarget))

			if (e.isDefaultPrevented()) return

			$this
				.trigger('focus')
				.attr('aria-expanded', 'true')

			$parent
				.toggleClass('open')
				.trigger($.Event('shown.bs.dropdown', relatedTarget))
		}

		return false
	}

	Dropdown.prototype.keydown = function (e) {
		if (!/(38|40|27|32)/.test(e.which) || /input|textarea/i.test(e.target.tagName)) return

		let $this = $(this)

		e.preventDefault()
		e.stopPropagation()

		if ($this.is('.disabled, :disabled')) return

		let $parent  = getParent($this)
		let isActive = $parent.hasClass('open')

		if (!isActive && e.which != 27 || isActive && e.which == 27) {
			if (e.which == 27) $parent.find(toggle).trigger('focus')
			return $this.trigger('click')
		}

		let desc = ' li:not(.disabled):visible a'
		let $items = $parent.find('.dropdown-menu' + desc)

		if (!$items.length) return

		let index = $items.index(e.target)

		if (e.which == 38 && index > 0)                 index--         // up
		if (e.which == 40 && index < $items.length - 1) index++         // down
		if (!~index)                                    index = 0

		$items.eq(index).trigger('focus')
	}


	// DROPDOWN PLUGIN DEFINITION
	// ==========================

	function Plugin(option) {
		return this.each(function () {
			let $this = $(this)
			let data  = $this.data('bs.dropdown')

			if (!data) $this.data('bs.dropdown', (data = new Dropdown(this)))
			if (typeof option == 'string') data[option].call($this)
		})
	}

	let old = $.fn.dropdown

	$.fn.dropdown             = Plugin
	$.fn.dropdown.Constructor = Dropdown


	// DROPDOWN NO CONFLICT
	// ====================

	$.fn.dropdown.noConflict = function () {
		$.fn.dropdown = old
		return this
	}


	// APPLY TO STANDARD DROPDOWN ELEMENTS
	// ===================================

	$(document)
		.on('click.bs.dropdown.data-api', clearMenus)
		.on('click.bs.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
		.on('click.bs.dropdown.data-api', toggle, Dropdown.prototype.toggle)
		.on('keydown.bs.dropdown.data-api', toggle, Dropdown.prototype.keydown)
		.on('keydown.bs.dropdown.data-api', '.dropdown-menu', Dropdown.prototype.keydown)

}(jQuery);

/* Form Input Tags */

	$('input[data-toggle="tags"]').each(function() {

		let $originalInput = $(this);

		let $tagField = $(
			'<div class="form-input">\
				<ul class="tokens">\
					<span class="input" contenteditable></span>\
				</ul>\
			</div>'
		);

		$tagField.tags = [];

		$tagField.add = function(input){

			input = input.trim();

			if (!input) return;

			$tagField.tags.push(input);

			let $tag = $(
				'<li class="tag">\
					<span class="value"></span>\
					<span class="remove">x</span>\
				</li>');

			$('.value', $tag).text(input);
			$('.input', $tagField).before($tag);

			$tagField.trigger('change');
		};

		$tagField.remove = function(input){

			$tagField.tags = $.grep($tagField.tags, function(value) {
				return value != input;
			});

		 $('.tag .value', $tagField).each(function(){
			 if ($(this).text() == input) {
				 $(this).parent('.tag').remove();
			 }
		 })

			$tagField.trigger('change');
		};

		let tags = $.grep($originalInput.val().split(/\s*,\s*/), function(value) {
			return value;
		});

		$.each(tags, function(){
			$tagField.add(this);
		});

		$tagField.on('keypress', '.input', function(e){
			if (e.which == 44 || e.which == 13) { // Comma or enter
				e.preventDefault();
				$tagField.add($(this).text());
				$(this).text('');
			}
		});

		$tagField.on('blur', '.input', function(){
			$tagField.add($(this).text());
			$(this).text('');
		});

		$tagField.on('click', '.remove', function(e){
			$tagField.remove($(this).siblings('.value').text());
		});

		$tagField.on('change', function(){
			$originalInput.val($tagField.tags.join(','));
		});

		$(this).hide().after($tagField);
	});


// Required Input Asterix
$(':input[required]').closest('.form-group').addClass('required');

// Dropdown select
$('.dropdown .form-select + .dropdown-menu :input').on('input', function(e){
	let $dropdown = $(this).closest('.dropdown');
	let $input = $dropdown.find(':input:checked');

	if (!$dropdown.find(':input:checked').length) return;

	$dropdown.find('li.active').removeClass('active');

	if ($input.data('title')) {
		$dropdown.find('.form-select').text( $input.data('title') );
	} else if ($input.closest('.option').find('.title').length) {
		$dropdown.find('.form-select').text( $input.closest('.option').find('.title').text() );
	} else {
		$dropdown.find('.form-select').text( $input.parent().text() );
	}

	$input.closest('li').addClass('active');
	$dropdown.trigger('click.bs.dropdown');

}).trigger('input');

// Input Number Decimals
$('body').on('change', 'input[type="number"][data-decimals]', function(){
		var value = parseFloat($(this).val()),
			decimals = $(this).data('decimals');
	if (decimals != '') {
		$(this).val(value.toFixed(decimals));
	}
});

// Alerts
$('body').on('click', '.alert .close', function(e){
	e.preventDefault();
	$(this).closest('.alert').fadeOut('fast', function(){
		$(this).remove()
	});
});


// Filter
$('#sidebar input[name="filter"]').on({

	'input': function(){

		let query = $(this).val();

		if ($(this).val() == '') {
			$('#box-apps-menu .app').css('display', 'block');
			return;
		}

		$('#box-apps-menu .app').each(function(){
			var regex = new RegExp(''+ query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')  +'', 'ig');
			console.log()
			if (regex.test($(this).text())) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	}
});

// AJAX Search
let timer_ajax_search = null;
let xhr_search = null;

$('#search input[name="query"]').on({

	'focus': function(){
		$('#search.dropdown').addClass('open');
	},

	'blur': function(){
		$('#search.dropdown').removeClass('open');
	},

	'input': function(){

		if (xhr_search) {
			xhr_search.abort();
		}

		let search_field = this;

		if ($(this).val() == '') {
			$('#search .results').html('');
			return;
		}

		if (!$('#search .loader-wrapper').length) {
			$('#search .results').show().html([
				'<div class="loader-wrapper text-center">',
				'  <div class="loader" style="width: 48px; height: 48px;"></div>',
				'</div>'
			].join('\n'));
		}

		clearTimeout(timer_ajax_search);

		timer_ajax_search = setTimeout(function() {
			xhr_search = $.ajax({
				type: 'get',
				async: true,
				cache: false,
				url: window._env.backend.url + 'search_results.json?query=' + $(search_field).val(),
				dataType: 'json',

				beforeSend: function(jqXHR) {
					jqXHR.overrideMimeType('text/html;charset=' + $('html meta[charset]').attr('charset'));
				},

				error: function(jqXHR, textStatus, errorThrown) {
					$('#search .results').text(textStatus + ': ' + errorThrown);
				},

				success: function(json) {

					$('#search .results').html('');

					if (!$('#search input[name="query"]').val()) return;

					$.each(json, function(i, group){

						if (group.results.length) {

							$('#search .results').append(
								'<h4>'+ group.name +'</h4>' +
								'<ul class="flex flex-rows" data-group="'+ group.name +'"></ul>'
							);

							$.each(group.results, function(i, result){

								var $li = $([
									'<li class="result">',
									'  <a class="list-group-item" href="'+ result.link +'" style="border-inline-start: 3px solid '+ group.theme.color +';">',
									'    <small class="id float-end">#'+ result.id +'</small>',
									'    <div class="title">'+ result.title +'</div>',
									'    <div class="description"><small>'+ result.description +'</small></div>',
									'  </a>',
									'</li>'
								].join('\n'));

								$('#search .results ul[data-group="'+ group.name +'"]').append($li);
							});
						}
					});

					if ($('#search .results').html() == '') {
						$('#search .results').html('<p class="text-center no-results"><em>:(</em></p>');
					}
				},
			});
		}, 500);
	}
});

// Data-Table Toggle Checkboxes
$('body').on('click', '.data-table *[data-toggle="checkbox-toggle"], .data-table .checkbox-toggle', function() {
	$(this).closest('.data-table').find('tbody td:first-child :checkbox').each(function() {
		$(this).prop('checked', !$(this).prop('checked')).trigger('change');
	});
	return false;
});

$('body').on('click', '.data-table tbody tr', function(e) {
	if ($(e.target).is('a') || $(e.target).closest('a').length) return;
	if ($(e.target).is('.btn, :input, th, .fa-star, .fa-star-o')) return;
	$(this).find(':checkbox, :radio').first().trigger('click');
});

// Data-Table Shift Check Multiple Checkboxes
let lastTickedCheckbox = null;
$('.data-table td:first-child :checkbox').click(function(e){

	let $chkboxes = $('.data-table td:first-child :checkbox');

	if (!lastTickedCheckbox) {
		lastTickedCheckbox = this;
		return;
	}

	if (e.shiftKey) {
		let start = $chkboxes.index(this);
		let end = $chkboxes.index(lastTickedCheckbox);
		$chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastTickedCheckbox.checked);
	}

	lastTickedCheckbox = this;
});

// Data-Table Dragable
$('body').on('click', '.table-dragable tbody .grabable', function(e){
	e.preventDefault();
	return false;
});

$('body').on('mousedown', '.table-dragable tbody .grabable', function(e){
	let tr = $(e.target).closest('tr'), sy = e.pageY, drag;
	if ($(e.target).is('tr')) tr = $(e.target);
	let index = tr.index();
	$(tr).addClass('grabbed');
	$(tr).closest('tbody').css('unser-input', 'unset');
	function move(e) {
		if (!drag && Math.abs(e.pageY - sy) < 10) return;
		drag = true;
		tr.siblings().each(function() {
			let s = $(this), i = s.index(), y = s.offset().top;
			if (e.pageY >= y && e.pageY < y + s.outerHeight()) {
				if (i < tr.index()) s.insertAfter(tr);
				else s.insertBefore(tr);
				return false;
			}
		});
	}
	function up(e) {
		if (drag && index != tr.index()) {
			drag = false;
		}
		$(document).off('mousemove', move).off('mouseup', up);
		$(tr).removeClass('grabbed');
		$(tr).closest('tbody').css('unser-input', '');
	}
	$(document).mousemove(move).mouseup(up);
});

// Data-Table Sorting (Page Reload)
$('.table-sortable thead th[data-sort]').click(function(){
	let params = {};

	window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(str, key, value) {
		params[key] = value;
	});

	params.sort = $(this).data('sort');

	window.location.search = $.param(params);
});

// Tabs (data-toggle="tab")
+function($) {
	'use strict';
	$.fn.Tabs = function(){
		this.each(function(){

			let self = this;

			this.$element = $(this);

			this.$element.find('[data-toggle="tab"]').each(function(){
				let $link = $(this);

				$link.on('select', function(){
					self.$element.find('.active').removeClass('active');

					if ($link.hasClass('nav-link')) {
						$link.addClass('active');
					}

					$link.closest('.nav-item').addClass('active');

					$($link.attr('href')).show().siblings().hide();
				});

				$link.on('click', function(e) {
					e.preventDefault();
					history.replaceState(null, null, this.hash);
					$link.trigger('select');
				});
			});

			if (!this.$element.find('.active').length) {
				this.$element.find('[data-toggle="tab"]').first().select();
			} else {
				this.$element.find('[data-toggle="tab"].active').select();
			}
		});
	}

	$('.nav-tabs').Tabs();

	if (document.location.hash && document.location.hash.match(/^#tab-/)) {
		$('[data-toggle="tab"][href="' + document.location.hash +'"]').trigger('select');
	}

	$(document).on('ajaxcomplete', function(){
		$('.nav-tabs').Tabs();
	});
}(jQuery);
