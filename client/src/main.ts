import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './routes' // Pastikan ini mengarah ke folder routes

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')