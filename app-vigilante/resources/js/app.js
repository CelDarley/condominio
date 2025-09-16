import './bootstrap';
import {registrarServiceWorker} from "./sw-register.js";
import {initGeolocation} from "./init-geolocation.js";

registrarServiceWorker();
navigator.serviceWorker.ready.then(() => { initGeolocation() });
