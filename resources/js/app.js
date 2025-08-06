import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import { registerSW } from 'virtual:pwa-register';

registerSW({
  onNeedRefresh() {
    console.log('New content available, please refresh.');
  },
  onOfflineReady() {
    console.log('App ready to work offline');
  },
});

