import initShare from './pages/share';
import InitBiblioteca from './pages/biblioteca';
import initFilmeDoDia from './pages/filmedodia';
import initPersonalMovie from './pages/show';
import initMovieDetails from './pages/showTmdb';
import initToast from './components/toastComp/init_Toast';
import initDashboard from './pages/dashboard';
import './components/loading/auto-loading';
import { initMobileNavigation } from './ui/navigation';


document.addEventListener('DOMContentLoaded', () => {
    initToast();

    initMobileNavigation();
    initShare();
    InitBiblioteca();
    initFilmeDoDia();
    initPersonalMovie();
    initMovieDetails();
    initDashboard();
});
