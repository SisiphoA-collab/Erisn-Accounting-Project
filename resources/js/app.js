import { createApp } from 'vue';
import App from './layouts/app.vue';
import router from './router';
import vuetify from './vuetify';
import { createHead } from '@vueuse/head';
import Invoice from './Components/Invoice.vue';
const head = createHead();
createApp(App)
  .use(head)
  .use(router)
  .use(vuetify)
  .component('Invoice',Invoice)
  .mount('#app');
