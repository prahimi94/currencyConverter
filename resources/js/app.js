import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import axios from 'axios';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.baseURL = import.meta.env.APP_URL;

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

const app = createApp(App)
app.use(Toast, {
  position: 'top-right',
  timeout: 3000,
});
app.mount('#app')