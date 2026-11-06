/* Sturschädl – Handy-Menü, Video erst nach Klick, einfache Bildvergrößerung */
(function () {
	var nav = document.querySelector('.nav'), burger = document.querySelector('.burger');
	if (nav && burger) {
		burger.addEventListener('click', function () {
			var auf = nav.classList.toggle('open');
			burger.setAttribute('aria-expanded', auf ? 'true' : 'false');
			burger.setAttribute('aria-label', auf ? 'Menü schließen' : 'Menü öffnen');
		});
	}
	document.querySelectorAll('.video[data-embed]').forEach(function (v) {
		v.addEventListener('click', function () {
			if (v.querySelector('iframe')) return;
			var f = document.createElement('iframe');
			f.src = v.dataset.embed;
			f.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
			f.allowFullscreen = true;
			f.title = 'Video';
			v.appendChild(f);
		});
	});
	document.addEventListener('click', function (e) {
		var a = e.target.closest('a[data-lightbox]');
		if (!a) return;
		e.preventDefault();
		var box = document.createElement('div');
		box.className = 'lightbox';
		box.innerHTML = '<img alt="">';
		box.firstChild.src = a.href;
		box.addEventListener('click', function () { box.remove(); });
		document.addEventListener('keydown', function esc(k) { if (k.key === 'Escape') { box.remove(); document.removeEventListener('keydown', esc); } });
		document.body.appendChild(box);
	});
})();
