import { createApp } from 'vue';
import App from './layouts/app.vue'; // Layout wrapper
import router from './router';
import vuetify from './vuetify'; // If using Vuetify
import Invoice from './Components/Invoice.vue';

createApp(App)
  .use(router)
  .use(vuetify)
  .component('Invoice',Invoice)
  .mount('#app');