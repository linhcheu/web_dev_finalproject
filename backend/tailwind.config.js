// Builds public/css/admin.css from the Tailwind classes used in the Blade views.
// Only needed after changing classes in a view:  npm install && npm run build:css
module.exports = {
    content: ['./resources/views/**/*.blade.php'],
    theme: { extend: {} },
    plugins: [],
};
