import './bootstrap';
import {registrarServiceWorker} from "./sw-register.js";
import {initGeolocation} from "./init-geolocation.js";

registrarServiceWorker();
initGeolocation();
