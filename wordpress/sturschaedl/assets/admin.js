/* Sturschädl – Backend: Mediathek-Auswahl, Galerie sortieren, Listenzeilen hinzufügen/entfernen */
jQuery(function ($) {
	function setGalerie($box) {
		$box.find('input[type=hidden]').first().val($box.find('.st-galerie li').map(function () { return $(this).data('id'); }).get().join(','));
	}
	$(document).on('click', '.st-waehlen', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.st-bild'), multi = $box.data('multi') == 1;
		var frame = wp.media({ title: multi ? 'Bilder auswählen' : 'Bild auswählen', button: { text: 'Übernehmen' }, multiple: multi ? 'add' : false, library: { type: 'image' } });
		frame.on('select', function () {
			var sel = frame.state().get('selection').toJSON();
			if (multi) {
				sel.forEach(function (a) {
					var u = (a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url);
					$box.find('.st-galerie').append('<li data-id="' + a.id + '"><img src="' + u + '" alt=""><button type="button" class="st-x" aria-label="Bild entfernen">×</button></li>');
				});
				setGalerie($box);
			} else {
				var a = sel[0], u = (a.sizes && a.sizes.medium ? a.sizes.medium.url : a.url);
				$box.find('input[type=hidden]').val(a.id);
				$box.find('.st-vorschau').html('<img src="' + u + '" alt="">');
				$box.find('.st-weg').prop('hidden', false);
			}
		});
		frame.open();
	});
	$(document).on('click', '.st-weg', function () {
		var $box = $(this).closest('.st-bild');
		$box.find('input[type=hidden]').val('');
		$box.find('.st-vorschau').empty();
		$(this).prop('hidden', true);
	});
	$(document).on('click', '.st-x', function () {
		var $box = $(this).closest('.st-bild');
		$(this).closest('li').remove();
		setGalerie($box);
	});
	$('.st-galerie').sortable({ update: function () { setGalerie($(this).closest('.st-bild')); } });

	function nummern($liste) {
		var key = $liste.data('key');
		$liste.find('.st-zeilen > .st-zeile').each(function (i) {
			$(this).find('[name]').each(function () {
				this.name = this.name.replace(new RegExp('^sturschaedl\\[' + key + '\\]\\[[^\\]]+\\]'), 'sturschaedl[' + key + '][' + i + ']');
			});
		});
	}
	$('.st-zeilen').sortable({ handle: '.st-griff', update: function () { nummern($(this).closest('.st-liste')); } });
	$(document).on('click', '.st-zeile-neu', function () {
		var $l = $(this).closest('.st-liste'), n = $l.find('.st-zeilen > .st-zeile').length;
		$l.find('.st-zeilen').append($l.find('.st-vorlage').html().replace(/__i__/g, n));
	});
	$(document).on('click', '.st-zeile-weg', function () {
		var $l = $(this).closest('.st-liste');
		$(this).closest('.st-zeile').remove();
		nummern($l);
	});
});
