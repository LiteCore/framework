/*
 * Bootstrap: carousel.js v3.4.1
 * https://getbootstrap.com/docs/3.4/javascript/#carousel
 *
 * Copyright 2011-2019 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */

waitFor('jQuery', ($) => {
	'use strict'

	class Carousel {
		constructor(element, options) {
			this.$element = $(element);
			this.$indicators = this.$element.find('.carousel-indicators');
			this.options = options;
			this.paused = null;
			this.sliding = null;
			this.interval = null;
			this.$active = null;
			this.$items = null;

			if (this.options.keyboard) {
				this.$element.on('keydown.carousel', (e) => this.keydown(e));
			}

			if (this.options.pause === 'hover' && !('ontouchstart' in document.documentElement)) {
				this.$element
					.on('mouseenter.carousel', () => this.pause())
					.on('mouseleave.carousel', () => this.cycle());
			}
		}

		static get DEFAULTS() {
			return {
				interval: 5000,
				pause: 'hover',
				wrap: true,
				keyboard: true
			};
		}

		keydown(e) {
			if (/input|textarea/i.test(e.target.tagName)) return;
			switch (e.which) {
				case 37: this.prev(); break;
				case 39: this.next(); break;
				default: return;
			}
			e.preventDefault();
		}

		cycle(e) {
			if (!e) this.paused = false;

			if (this.interval) clearInterval(this.interval);

			if (this.options.interval && !this.paused) {
				this.interval = setInterval(() => this.next(), this.options.interval);
			}

			return this;
		}

		getItemIndex(item) {
			this.$items = item.parent().children('.item');
			return this.$items.index(item || this.$active);
		}

		getItemForDirection(direction, active) {
			const activeIndex = this.getItemIndex(active);
			const willWrap = (direction === 'prev' && activeIndex === 0) ||
							 (direction === 'next' && activeIndex === (this.$items.length - 1));
			if (willWrap && !this.options.wrap) return active;

			const delta = direction === 'prev' ? -1 : 1;
			const itemIndex = (activeIndex + delta) % this.$items.length;
			return this.$items.eq(itemIndex);
		}

		to(pos) {
			const activeIndex = this.getItemIndex(this.$active = this.$element.find('.item.active'));

			if (pos > (this.$items.length - 1) || pos < 0) return;

			if (this.sliding) {
				return this.$element.one('slid.carousel', () => this.to(pos));
			}
			if (activeIndex === pos) return this.pause().cycle();

			return this.slide(pos > activeIndex ? 'next' : 'prev', this.$items.eq(pos));
		}

		pause(e) {
			if (!e) this.paused = true;

			if (this.$element.find('.next, .prev').length && $.support.transition) {
				this.$element.trigger($.support.transition.end);
				this.cycle(true);
			}

			clearInterval(this.interval);
			this.interval = null;

			return this;
		}

		next() {
			if (this.sliding) return;
			return this.slide('next');
		}

		prev() {
			if (this.sliding) return;
			return this.slide('prev');
		}

		slide(type, next) {
			const $active = this.$element.find('.item.active');
			const $next = next || this.getItemForDirection(type, $active);
			const isCycling = this.interval;
			const direction = type === 'next' ? 'left' : 'right';

			if ($next.hasClass('active')) return (this.sliding = false);

			const relatedTarget = $next[0];
			const slideEvent = $.Event('slide.carousel', {
				relatedTarget,
				direction
			});
			this.$element.trigger(slideEvent);
			if (slideEvent.isDefaultPrevented()) return;

			this.sliding = true;

			if (isCycling) this.pause();

			if (this.$indicators.length) {
				this.$indicators.find('.active').removeClass('active');
				const $nextIndicator = $(this.$indicators.children()[this.getItemIndex($next)]);
				if ($nextIndicator) $nextIndicator.addClass('active');
			}

			const slidEvent = $.Event('slid.carousel', { relatedTarget, direction });
			if ($.support.transition && this.$element.hasClass('slide')) {
				$next.addClass(type);
				if (typeof $next === 'object' && $next.length) {
					$next[0].offsetWidth; // force reflow
				}
				$active.addClass(direction);
				$next.addClass(direction);
				$active
					.one('bsTransitionEnd', () => {
						$next.removeClass(`${type} ${direction}`).addClass('active');
						$active.removeClass(`active ${direction}`);
						this.sliding = false;
						setTimeout(() => this.$element.trigger(slidEvent), 0);
					})
					.emulateTransitionEnd(600);
			} else {
				$active.removeClass('active');
				$next.addClass('active');
				this.sliding = false;
				this.$element.trigger(slidEvent);
			}

			if (isCycling) this.cycle();

			return this;
		}
	}

	// CAROUSEL PLUGIN DEFINITION

	function Plugin(option) {
		return this.each(function () {
			const $this = $(this);
			let data = $this.data('carousel');
			const options = { ...Carousel.DEFAULTS, ...$this.data(), ...(typeof option === 'object' && option) };
			const action = typeof option === 'string' ? option : options.slide;

			if (!data) $this.data('carousel', (data = new Carousel(this, options)));
			if (typeof option === 'number') data.to(option);
			else if (action) data[action]();
			else if (options.interval) data.pause().cycle();
		});
	}

	const old = $.fn.carousel;

	$.fn.carousel = Plugin;
	$.fn.carousel.Constructor = Carousel;

	// CAROUSEL NO CONFLICT

	$.fn.carousel.noConflict = function () {
		$.fn.carousel = old;
		return this;
	};

	const clickHandler = (e) => {
		const $this = $(e.currentTarget);
		const $target = $($this.attr('data-target') || $this.closest('.carousel'));
		if (!$target.hasClass('carousel')) return;

		const options = { ...$target.data(), ...$this.data() };
		const slideIndex = $this.attr('data-slide-to');

		if (slideIndex) options.interval = false;

		Plugin.call($target, options);

		if (slideIndex) {
			$target.data('carousel').to(slideIndex);
		}

		e.preventDefault();
	};

	$(document).on('click.carousel.data-api', '[data-slide], [data-slide-to]', clickHandler);

	$(window).on('load', () => {
		$('[data-ride="carousel"]').each(function () {
			const $carousel = $(this);
			Plugin.call($carousel, $carousel.data());
		});
	});
});