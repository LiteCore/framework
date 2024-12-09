/*!
 * LiteCore v1.0.0 - Lightweight website core framework built with PHP, jQuery and HTML.
 * @link https://www.litecore.dev/
 * @license CC-BY-ND-4.0
 * @author T. Almroth
 */

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

// Alerts
$('body').on('click', '.alert .close', function(e){
	e.preventDefault();
	$(this).closest('.alert').fadeOut('fast', function(){$(this).remove()});
});

// Form required asterix
$(':input[required]').closest('.form-group').addClass('required');

// Detect scroll direction
let lastScrollTop = 0;
$(document).on('scroll', function(){
	var scrollTop = $(this).scrollTop();
	if (scrollTop > lastScrollTop) {
		$('body').addClass('scrolling-down');
	} else {
		$('body').removeClass('scrolling-down');
	}
	lastScrollTop = (scrollTop < 0) ? 0 : scrollTop;
});

// Scroll Up
$(window).scroll(function(){
	if ($(this).scrollTop() > 100) {
		$('#scroll-up').fadeIn();
	} else {
		$('#scroll-up').fadeOut();
	}
});

$('#scroll-up').click(function(){
	$('html, body').animate({scrollTop: 0}, 1000, 'swing');
	return false;
});


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
* Bootstrap: carousel.js v3.4.1
* https://getbootstrap.com/docs/3.4/javascript/#carousel
 *
* Copyright 2011-2019 Twitter, Inc.
* Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */

+function ($) {
	'use strict'

		// CAROUSEL CLASS DEFINITION

	let Carousel = function (element, options) {
		this.$element    = $(element)
		this.$indicators = this.$element.find('.carousel-indicators')
		this.options     = options
		this.paused      = null
		this.sliding     = null
		this.interval    = null
		this.$active     = null
		this.$items      = null

		this.options.keyboard && this.$element.on('keydown.bs.carousel', $.proxy(this.keydown, this))

		this.options.pause == 'hover' && !('ontouchstart' in document.documentElement) && this.$element
			.on('mouseenter.bs.carousel', $.proxy(this.pause, this))
			.on('mouseleave.bs.carousel', $.proxy(this.cycle, this))
	}

	Carousel.VERSION  = '3.4.1'

	Carousel.TRANSITION_DURATION = 600

	Carousel.DEFAULTS = {
		interval: 5000,
		pause: 'hover',
		wrap: true,
		keyboard: true
	}

	Carousel.prototype.keydown = function (e) {
		if (/input|textarea/i.test(e.target.tagName)) return
		switch (e.which) {
			case 37: this.prev(); break
			case 39: this.next(); break
			default: return
		}

		e.preventDefault()
	}

	Carousel.prototype.cycle = function (e) {
		e || (this.paused = false)

		this.interval && clearInterval(this.interval)

		this.options.interval
			&& !this.paused
			&& (this.interval = setInterval($.proxy(this.next, this), this.options.interval))

		return this
	}

	Carousel.prototype.getItemIndex = function (item) {
		this.$items = item.parent().children('.item')
		return this.$items.index(item || this.$active)
	}

	Carousel.prototype.getItemForDirection = function (direction, active) {
		let activeIndex = this.getItemIndex(active)
		let willWrap = (direction == 'prev' && activeIndex === 0)
								|| (direction == 'next' && activeIndex == (this.$items.length - 1))
		if (willWrap && !this.options.wrap) return active
		let delta = direction == 'prev' ? -1 : 1
		let itemIndex = (activeIndex + delta) % this.$items.length
		return this.$items.eq(itemIndex)
	}

	Carousel.prototype.to = function (pos) {
		let that        = this
		let activeIndex = this.getItemIndex(this.$active = this.$element.find('.item.active'))

		if (pos > (this.$items.length - 1) || pos < 0) return

		if (this.sliding)       return this.$element.one('slid.bs.carousel', function () { that.to(pos) }) // yes, "slid"
		if (activeIndex == pos) return this.pause().cycle()

		return this.slide(pos > activeIndex ? 'next' : 'prev', this.$items.eq(pos))
	}

	Carousel.prototype.pause = function (e) {
		e || (this.paused = true)

		if (this.$element.find('.next, .prev').length && $.support.transition) {
			this.$element.trigger($.support.transition.end)
			this.cycle(true)
		}

		this.interval = clearInterval(this.interval)

		return this
	}

	Carousel.prototype.next = function () {
		if (this.sliding) return
		return this.slide('next')
	}

	Carousel.prototype.prev = function () {
		if (this.sliding) return
		return this.slide('prev')
	}

	Carousel.prototype.slide = function (type, next) {
		let $active   = this.$element.find('.item.active')
		let $next     = next || this.getItemForDirection(type, $active)
		let isCycling = this.interval
		let direction = type == 'next' ? 'left' : 'right'
		let that      = this

		if ($next.hasClass('active')) return (this.sliding = false)

		let relatedTarget = $next[0]
		let slideEvent = $.Event('slide.bs.carousel', {
			relatedTarget: relatedTarget,
			direction: direction
		})
		this.$element.trigger(slideEvent)
		if (slideEvent.isDefaultPrevented()) return

		this.sliding = true

		isCycling && this.pause()

		if (this.$indicators.length) {
			this.$indicators.find('.active').removeClass('active')
			let $nextIndicator = $(this.$indicators.children()[this.getItemIndex($next)])
			$nextIndicator && $nextIndicator.addClass('active')
		}

		let slidEvent = $.Event('slid.bs.carousel', { relatedTarget: relatedTarget, direction: direction }) // yes, "slid"
		if ($.support.transition && this.$element.hasClass('slide')) {
			$next.addClass(type)
			if (typeof $next === 'object' && $next.length) {
				$next[0].offsetWidth // force reflow
			}
			$active.addClass(direction)
			$next.addClass(direction)
			$active
				.one('bsTransitionEnd', function () {
					$next.removeClass([type, direction].join(' ')).addClass('active')
					$active.removeClass(['active', direction].join(' '))
					that.sliding = false
					setTimeout(function () {
						that.$element.trigger(slidEvent)
					}, 0)
				})
				.emulateTransitionEnd(Carousel.TRANSITION_DURATION)
		} else {
			$active.removeClass('active')
			$next.addClass('active')
			this.sliding = false
			this.$element.trigger(slidEvent)
		}

		isCycling && this.cycle()

		return this
	}

		// CAROUSEL PLUGIN DEFINITION

	function Plugin(option) {
		return this.each(function () {
			let $this   = $(this)
			let data    = $this.data('bs.carousel')
			let options = $.extend({}, Carousel.DEFAULTS, $this.data(), typeof option == 'object' && option)
			let action  = typeof option == 'string' ? option : options.slide

			if (!data) $this.data('bs.carousel', (data = new Carousel(this, options)))
			if (typeof option == 'number') data.to(option)
			else if (action) data[action]()
			else if (options.interval) data.pause().cycle()
		})
	}

	let old = $.fn.carousel

	$.fn.carousel             = Plugin
	$.fn.carousel.Constructor = Carousel

		// CAROUSEL NO CONFLICT

	$.fn.carousel.noConflict = function () {
		$.fn.carousel = old
		return this
	}

		// CAROUSEL DATA-API

	let clickHandler = function (e) {
		let href
		let $this   = $(this)
		let $target = $($this.attr('data-target') || $this.closest('.carousel'))
		if (!$target.hasClass('carousel')) return

		let options = $.extend({}, $target.data(), $this.data())
		let slideIndex = $this.attr('data-slide-to')

		if (slideIndex) options.interval = false

		Plugin.call($target, options)

		if (slideIndex) {
			$target.data('bs.carousel').to(slideIndex)
		}

		e.preventDefault()
	}

	$(document)
		.on('click.bs.carousel.data-api', '[data-slide]', clickHandler)
		.on('click.bs.carousel.data-api', '[data-slide-to]', clickHandler)

	$(window).on('load', function () {
		$('[data-ride="carousel"]').each(function () {
			let $carousel = $(this)
			Plugin.call($carousel, $carousel.data())
		})
	})

}(jQuery)


/* ========================================================================
* Bootstrap: collapse.js v3.4.1
* https://getbootstrap.com/docs/3.4/javascript/#collapse
* ========================================================================
* Copyright 2011-2019 Twitter, Inc.
* Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
* ======================================================================== */

+function ($) {
	'use strict';

		// COLLAPSE PUBLIC CLASS DEFINITION
		// ================================

	var Collapse = function (element, options) {
		this.$element      = $(element)
		this.options       = $.extend({}, Collapse.DEFAULTS, options)
		this.$trigger      = $('[data-toggle="collapse"][href="#' + element.id + '"],' +
														'[data-toggle="collapse"][data-target="#' + element.id + '"]')
		this.transitioning = null

		if (this.options.parent) {
			this.$parent = this.getParent()
		} else {
			this.addAriaAndCollapsedClass(this.$element, this.$trigger)
		}

		if (this.options.toggle) this.toggle()
	}

	Collapse.VERSION  = '3.4.1'

	Collapse.TRANSITION_DURATION = 350

	Collapse.DEFAULTS = {
		toggle: true
	}

	Collapse.prototype.dimension = function () {
		var hasWidth = this.$element.hasClass('width')
		return hasWidth ? 'width' : 'height'
	}

	Collapse.prototype.show = function () {
		if (this.transitioning || this.$element.hasClass('in')) return

		var activesData
		var actives = this.$parent && this.$parent.children('.panel').children('.in, .collapsing')

		if (actives && actives.length) {
			activesData = actives.data('bs.collapse')
			if (activesData && activesData.transitioning) return
		}

		var startEvent = $.Event('show.bs.collapse')
		this.$element.trigger(startEvent)
		if (startEvent.isDefaultPrevented()) return

		if (actives && actives.length) {
			Plugin.call(actives, 'hide')
			activesData || actives.data('bs.collapse', null)
		}

		var dimension = this.dimension()

		this.$element
			.removeClass('collapse')
			.addClass('collapsing')[dimension](0)
			.attr('aria-expanded', true)

		this.$trigger
			.removeClass('collapsed')
			.attr('aria-expanded', true)

		this.transitioning = 1

		var complete = function () {
			this.$element
				.removeClass('collapsing')
				.addClass('collapse in')[dimension]('')
			this.transitioning = 0
			this.$element
				.trigger('shown.bs.collapse')
		}

		if (!$.support.transition) return complete.call(this)

		var scrollSize = $.camelCase(['scroll', dimension].join('-'))

		this.$element
			.one('bsTransitionEnd', $.proxy(complete, this))
			.emulateTransitionEnd(Collapse.TRANSITION_DURATION)[dimension](this.$element[0][scrollSize])
	}

	Collapse.prototype.hide = function () {
		if (this.transitioning || !this.$element.hasClass('in')) return

		var startEvent = $.Event('hide.bs.collapse')
		this.$element.trigger(startEvent)
		if (startEvent.isDefaultPrevented()) return

		var dimension = this.dimension()

		this.$element[dimension](this.$element[dimension]())[0].offsetHeight

		this.$element
			.addClass('collapsing')
			.removeClass('collapse in')
			.attr('aria-expanded', false)

		this.$trigger
			.addClass('collapsed')
			.attr('aria-expanded', false)

		this.transitioning = 1

		var complete = function () {
			this.transitioning = 0
			this.$element
				.removeClass('collapsing')
				.addClass('collapse')
				.trigger('hidden.bs.collapse')
		}

		if (!$.support.transition) return complete.call(this)

		this.$element
			[dimension](0)
			.one('bsTransitionEnd', $.proxy(complete, this))
			.emulateTransitionEnd(Collapse.TRANSITION_DURATION)
	}

	Collapse.prototype.toggle = function () {
		this[this.$element.hasClass('in') ? 'hide' : 'show']()
	}

	Collapse.prototype.getParent = function () {
		return $(document).find(this.options.parent)
			.find('[data-toggle="collapse"][data-parent="' + this.options.parent + '"]')
			.each($.proxy(function (i, element) {
				var $element = $(element)
				this.addAriaAndCollapsedClass(getTargetFromTrigger($element), $element)
			}, this))
			.end()
	}

	Collapse.prototype.addAriaAndCollapsedClass = function ($element, $trigger) {
		var isOpen = $element.hasClass('in')

		$element.attr('aria-expanded', isOpen)
		$trigger
			.toggleClass('collapsed', !isOpen)
			.attr('aria-expanded', isOpen)
	}

	function getTargetFromTrigger($trigger) {
		var href
		var target = $trigger.attr('data-target')
			|| (href = $trigger.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') // strip for ie7

		return $(document).find(target)
	}


		// COLLAPSE PLUGIN DEFINITION
		// ==========================

	function Plugin(option) {
		return this.each(function () {
			var $this   = $(this)
			var data    = $this.data('bs.collapse')
			var options = $.extend({}, Collapse.DEFAULTS, $this.data(), typeof option == 'object' && option)

			if (!data && options.toggle && /show|hide/.test(option)) options.toggle = false
			if (!data) $this.data('bs.collapse', (data = new Collapse(this, options)))
			if (typeof option == 'string') data[option]()
		})
	}

	var old = $.fn.collapse

	$.fn.collapse             = Plugin
	$.fn.collapse.Constructor = Collapse


		// COLLAPSE NO CONFLICT
		// ====================

	$.fn.collapse.noConflict = function () {
		$.fn.collapse = old
		return this
	}


		// COLLAPSE DATA-API
		// =================

	$(document).on('click.bs.collapse.data-api', '[data-toggle="collapse"]', function (e) {
		var $this   = $(this)

		if (!$this.attr('data-target')) e.preventDefault()

		var $target = getTargetFromTrigger($this)
		var data    = $target.data('bs.collapse')
		var option  = data ? 'toggle' : $this.data()

		Plugin.call($target, option)
	})

}(jQuery);


// Dropdown

$('.dropdown [data-toggle="dropdown"]').on('click', function(e) {
	$(this).closest('.dropdown').toggleClass('open')
		})

$('.dropdown').on('focusout', function(e) {
	$(this).closest('.dropdown').removeClass('open')
		})

$('.dropdown').on('click', 'a', function(e) {
	$(this).closest('.dropdown').removeClass('open')
})


// Form required asterix
$(':input[required]').closest('.form-group').addClass('required');

// Password Strength
$('form').on('input', 'input[type="password"][data-toggle="password-strength"]', function(){

	$(this).siblings('meter').remove();

	if ($(this).val() == '') return;

	var numbers = ($(this).val().match(/[0-9]/g) || []).length,
		lowercases = ($(this).val().match(/[a-z]/g) || []).length,
		uppercases = ($(this).val().match(/[A-Z]/g) || []).length,
		symbols =   ($(this).val().match(/[^\w]/g) || []).length,

		score = (numbers * 9) + (lowercases * 11.25) + (uppercases * 11.25) + (symbols * 15)
					+ (numbers ? 10 : 0) + (lowercases ? 10 : 0) + (uppercases ? 10 : 0) + (symbols ? 10 : 0);

	var meter = $('<meter min="0" low="80" high="120" optimum="150" max="150" value="'+ score +'"></meter>').css({
		position: 'absolute',
		bottom: '-1em',
		width: '100%',
		height: '1em'
	});

	$(this).after(meter);
});

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


// Alerts
$('body').on('click', '.alert .close', function(e){
		e.preventDefault()
		$(this).closest('.alert').fadeOut('fast', function(){
			$(this).remove()
		})
	})



// Offcanvas
$('[data-toggle="offcanvas"]').click(function(e){
	e.preventDefault();
	var target = $(this).data('target');
	if ($(target).hasClass('show')) {
		$(target).removeClass('show');
		$(this).removeClass('toggled');
		$('body').removeClass('has-offcanvas');
	} else {
		$(target).addClass('show');
		$(this).addClass('toggled');
		$('body').addClass('has-offcanvas');
	}
});

$('.offcanvas [data-toggle="dismiss"]').click(function(e){
	$('.offcanvas').removeClass('show');
	$('[data-toggle="offcanvas"]').removeClass('toggled');
	$('body').removeClass('has-offcanvas');
});


// Data-Table Toggle Checkboxes
$('body').on('click', '.data-table *[data-toggle="checkbox-toggle"], .data-table .checkbox-toggle', function() {
	$(this).closest('.data-table').find('tbody td:first-child :checkbox').each(function() {
		$(this).prop('checked', !$(this).prop('checked')).trigger('change')
	})
	return false
})

$('body').on('click', '.data-table tbody tr', function(e) {
	if ($(e.target).is('a') || $(e.target).closest('a').length) return
	if ($(e.target).is('.btn, :input, th')) return
	$(this).find(':checkbox, :radio').first().trigger('click')
})



// Tabs (data-toggle="tab")
$('.nav-tabs').each(function(){
	if (!$(this).find('.active').length) {
		$(this).find('[data-toggle="tab"]:first').addClass('active');
	}

	$(this).on('select', '[data-toggle="tab"]', function() {
		$(this).siblings().removeClass('active');
		$(this).addClass('active');
		$($(this).attr('href')).show().siblings().hide();
	});

	$(this).on('click', '[data-toggle="tab"]', function(e) {
		e.preventDefault();
		$(this).trigger('select');
		history.replaceState({}, '', location.toString().replace(/#.*$/, '') + $(this).attr('href'));
	});

	$(this).find('.active').trigger('select');
});

if (document.location.hash != '') {
	$('a[data-toggle="tab"][href="' + document.location.hash + '"]').click();
}
