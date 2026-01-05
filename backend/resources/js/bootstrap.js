import axios from 'axios';

window.axios = axios;
window.axios.defaults.baseURL = 'http://192.168.1.135:8080'; // API origin
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
