import { createApp } from 'vue';
import App from './layouts/app.vue'; // Layout wrapper
import router from './router';
import vuetify from './vuetify'; // If using Vuetify

createApp(App)
  .use(router)
  .use(vuetify)
  .mount('#app');