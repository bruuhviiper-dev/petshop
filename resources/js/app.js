import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Dark mode global function used in layouts
window.darkModeApp = () => ({
    dark: localStorage.getItem('darkMode') === 'true',
    toggleDark() {
        this.dark = !this.dark;
        localStorage.setItem('darkMode', this.dark);
    },
});

Alpine.start();
