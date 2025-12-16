import initShare from './pages/share';
import InitBiblioteca from './pages/biblioteca';
import initFilmeDoDia from './pages/filmedodia';
import initPersonalMovie from './pages/show';
import initMovieDetails from './pages/showTmdb';

document.addEventListener('DOMContentLoaded', () => {
    initShare();
    InitBiblioteca();
    initFilmeDoDia();
    initPersonalMovie();
    initMovieDetails();
});