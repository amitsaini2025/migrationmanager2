
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app'
});

/**
 * Email Handling Modules
 * Import and initialize email handling functionality
 */
// import './modules/emailList';  // Commented out temporarily to fix 404 error
import './modules/upload';
// import './modules/search';  // Commented out temporarily to fix 404 error

// Initialize email handling when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Email handling modules loaded');
    
    // Check if we're on a page with email handling
    const emailInterface = document.querySelector('.email-interface-container');
    if (emailInterface) {
        console.log('Email interface detected, initializing modules');
        
        // Initialize modules if they haven't been initialized yet
        if (typeof window.initializeUpload === 'function') {
            window.initializeUpload();
        }
        
        if (typeof window.initializeSearch === 'function') {
            window.initializeSearch();
        }
        
        // Load initial emails
        if (typeof window.loadEmails === 'function') {
            window.loadEmails();
        }
    }
});
