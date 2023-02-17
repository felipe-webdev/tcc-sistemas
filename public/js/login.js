import { api } from "./api.js";

window.api = api;

window.app = {};

const { loadModule } = window['vue2-sfc-loader'];

const options = {
  moduleCache: {
    vue: Vue,
  },
  getFile(url) {
    return fetch(url).then(response => response.ok ? response.text() : Promise.reject(response));
  },
  addStyle(styleStr) {
    const style = document.createElement('style');
    style.textContent = styleStr;
    const ref = document.head.getElementsByTagName('style')[0] || null;
    document.head.insertBefore(style, ref);
  },
  log(type, ...args) {
    console.log(type, ...args);
  }
}

// Vue.component('loading', () => loadModule('/rhxpert/public/components/loading.vue', options));
// Vue.component('login',   () => loadModule('/rhxpert/public/components/login.vue',   options));
// Vue.component('alert',   () => loadModule('/rhxpert/public/components/alert.vue',   options));

const RHXPERT = new Vue({
  el: '#rhxpert',

  template: `
    <div>
      <loading 
        ref="loading"
        :isLoading="isLoading"
      />
      <alert 
        ref="alert"
      />
      <login 
        ref="login"
        @showAlert="showAlert"
        @isLoading="setIsLoading"
      />
    </div>
  `,

  components: {
    'loading': () => loadModule('/rhxpert/public/components/loading.vue', options),
    'alert':   () => loadModule('/rhxpert/public/components/alert.vue',   options),
    'login':   () => loadModule('/rhxpert/public/components/login.vue',   options),
  },

  directives: {},

  props: [],

  data: {
    isLoading: false,
  },

  computed: {},

  watch: {},

  created(){
    window.app = this;
  },

  mounted(){},

  updated(){},
  
  methods: {
    showAlert(type, msg){
      this.$refs.alert.showAlert(type, msg);
    },

    setIsLoading(value){
      this.isLoading = value;
    },
  },
});