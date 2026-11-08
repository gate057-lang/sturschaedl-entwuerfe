// Sturschädl – masterPage.js (läuft auf allen Seiten): Footer-Baustein #html5 verbinden.
import { verbindeBausteine } from 'public/bausteine.js';

$w.onReady(function () {
    verbindeBausteine($w, ['#html5']);
});
