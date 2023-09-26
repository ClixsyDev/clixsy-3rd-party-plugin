const ifSelectorExist = (selector) => {
	if (selector !== null && selector !== undefined) {
		return true;
	} else {
		return false;
	}
};

const getElement = (el, wrap = document) => {
	if (el == null) return undefined;

	if (typeof el === 'string') {
		try {
			return wrap.querySelector(el) || undefined;
		} catch ({ message }) {
			console.error(message);
			return undefined;
		}
	}
};

const getElements = (el, wrap = document) => {
	if (el == null) return undefined;

	if (typeof el === 'string') {
		try {
			return wrap.querySelectorAll(el) || undefined;
		} catch ({ message }) {
			console.error(message);
			return undefined;
		}
	}
};

const style = (el, property) => window.getComputedStyle(el).getPropertyValue(property);

const css = (el, css) => {
	Object.keys(css).forEach((prop) => {
		el.style[prop] = css[prop];
	});
};

const ready = (callback) => {
	if (document.readyState !== 'loading') callback();
	else document.addEventListener('DOMContentLoaded', callback);
};

const stopAndPrevent = (e) => {
	e.cancelable !== false && e.preventDefault();
	e.stopPropagation();
};

/* SLIDE UP */
const slideUp = (target, duration = 500) => {
	target.style.transitionProperty = 'height, margin, padding';
	target.style.transitionDuration = duration + 'ms';
	target.style.boxSizing = 'border-box';
	target.style.height = target.offsetHeight + 'px';
	target.offsetHeight;
	target.style.overflow = 'hidden';
	target.style.height = 0;
	target.style.paddingTop = 0;
	target.style.paddingBottom = 0;
	target.style.marginTop = 0;
	target.style.marginBottom = 0;
	window.setTimeout(() => {
		target.style.display = 'none';
		target.style.removeProperty('height');
		target.style.removeProperty('padding-top');
		target.style.removeProperty('padding-bottom');
		target.style.removeProperty('margin-top');
		target.style.removeProperty('margin-bottom');
		target.style.removeProperty('overflow');
		target.style.removeProperty('transition-duration');
		target.style.removeProperty('transition-property');
		//alert("!");
	}, duration);
};

/* SLIDE DOWN */
const slideDown = (target, duration = 500, displayStyle = 'block') => {
	target.style.removeProperty('display');
	let display = window.getComputedStyle(target).display;
	if (display === 'none') display = displayStyle;
	target.style.display = display;
	let height = target.offsetHeight;
	target.style.overflow = 'hidden';
	target.style.height = 0;
	target.style.paddingTop = 0;
	target.style.paddingBottom = 0;
	target.style.marginTop = 0;
	target.style.marginBottom = 0;
	target.offsetHeight;
	target.style.boxSizing = 'border-box';
	target.style.transitionProperty = 'height, margin, padding';
	target.style.transitionDuration = duration + 'ms';
	target.style.height = height + 'px';
	target.style.removeProperty('padding-top');
	target.style.removeProperty('padding-bottom');
	target.style.removeProperty('margin-top');
	target.style.removeProperty('margin-bottom');
	window.setTimeout(() => {
		target.style.removeProperty('height');
		target.style.removeProperty('overflow');
		target.style.removeProperty('transition-duration');
		target.style.removeProperty('transition-property');
	}, duration);
};

/* TOOGLE */
const slideToggle = (target, duration = 500, displayStyle = 'block') => {
	if (window.getComputedStyle(target).display === 'none') {
		return slideDown(target, duration, displayStyle);
	} else {
		return slideUp(target, duration);
	}
};

/*!
 * Remove an event listener
 * (c) 2017 Chris Ferdinandi, MIT License, https://gomakethings.com
 * @param  {String}   event    The event type
 * @param  {Node}     elem     The element to remove the event to (optional, defaults to window)
 * @param  {Function} callback The callback that ran on the event
 * @param  {Boolean}  capture  If true, forces bubbling on non-bubbling events
 */
const off = function (event, elem, callback, capture) {
	if (typeof elem === 'function') {
		capture = callback;
		callback = elem;
		elem = window;
	}
	capture = capture ? true : false;
	elem = typeof elem === 'string' ? document.querySelector(elem) : elem;
	if (!elem) return;
	elem.removeEventListener(event, callback, capture);
};

// Don't see any places where we can use it
const humanDate = (date) => {
	let dateObj = void 0;
	if (typeof date === 'string') dateObj = new Date(date);
	else dateObj = date;

	let options = { month: 'long', day: 'numeric' };

	let dateYear = dateObj.toLocaleString('latn', { year: 'numeric' });
	let dateMonth = dateObj.toLocaleString('latn', { month: 'numeric' });
	let dateDay = dateObj.toLocaleString('latn', { day: 'numeric' });
	let dateHour = dateObj.getHours();
	let dateMinute = dateObj.getMinutes();

	let now = new Date();
	let nowYear = now.toLocaleString('latn', { year: 'numeric' });
	let nowMonth = now.toLocaleString('latn', { month: 'numeric' });
	let nowDay = now.toLocaleString('latn', { day: 'numeric' });
	let nowHour = now.getHours();
	let nowMinute = now.getMinutes();

	// set year only if not the same year as now
	if (dateYear !== nowYear) options.year = 'numeric';

	// if today, display relative time
	if (dateYear === nowYear && dateMonth === nowMonth && dateDay === nowDay) {
		let diffHour = nowHour - dateHour;
		let diffMinute = Math.abs(nowMinute - dateMinute);

		if (diffHour === 0 && diffMinute > 30) return '1 h';
		else if (diffHour === 0) return diffMinute + ' min';
		else if (diffMinute >= 30) return diffHour + 1 + ' h';
		return diffHour + ' h';
	}

	return dateObj.toLocaleString('latn', options);
};

// Work with cookies

const setCookie = (name, value, days) => {
	let expires = '';
	if (days) {
		let date = new Date();
		date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
		expires = '; expires=' + date.toUTCString();
	}
	document.cookie = name + '=' + (value || '') + expires + '; path=/';
};

const getCookie = (name) => {
	let nameEQ = name + '=';
	let ca = document.cookie.split(';');
	for (let i = 0; i < ca.length; i++) {
		let c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
};

// Haven't seen that we use this one
const eraseCookie = (name) => {
	document.cookie = name + '=; Max-Age=-99999999;';
};

// Windows width
const windowsWidth = window.innerWidth;

// Scroll to function
const scrollTo = (element, to, duration) => {
	if (duration <= 0) return;
	var difference = to - element.scrollTop;
	var perTick = (difference / duration) * 10;

	setTimeout(function () {
		element.scrollTop = element.scrollTop + perTick;
		if (element.scrollTop === to) return;
		scrollTo(element, to, duration - 10);
	}, 10);
};

// https://dev.to/bmsvieira/vanilla-js-fadein-out-2a6o

const fadeOut = (el) => {
	el.style.opacity = 1;
	(function fade() {
		if ((el.style.opacity -= 0.1) < 0) {
			el.style.display = 'none';
		} else {
			requestAnimationFrame(fade);
		}
	})();
};

const fadeIn = (el, display) => {
	el.style.opacity = 0;
	el.style.display = display || 'block';
	(function fade() {
		var val = parseFloat(el.style.opacity);
		if (!((val += 0.1) >= 1))
			if (!((val += 0.1) > 1)) {
				el.style.opacity = val;
				requestAnimationFrame(fade);
			}
	})();
};

const listAllEventListeners = () => {
	const allElements = Array.prototype.slice.call(document.querySelectorAll('*'));
	allElements.push(document);
	allElements.push(window);

	const types = [];

	for (let ev in window) {
		if (/^on/.test(ev)) types[types.length] = ev;
	}

	let elements = [];
	for (let i = 0; i < allElements.length; i++) {
		const currentElement = allElements[i];
		for (let j = 0; j < types.length; j++) {
			if (typeof currentElement[types[j]] === 'function') {
				elements.push({
					node: currentElement,
					type: types[j],
					func: currentElement[types[j]].toString(),
				});
			}
		}
	}

	return elements.sort(function (a, b) {
		return a.type.localeCompare(b.type);
	});
};

ready(() => {
	let containerPostFields = getElements('input[name="post_id"]');

	if (ifSelectorExist(containerPostFields)) {
		Array.from(containerPostFields).forEach(input => {
			input.value = clixsy_js_data.page_id;
			console.log('page ID is == ', input.value)
		})
	}
	

	

});


