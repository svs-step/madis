var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/dashboard', './assets/js/dashboard.js')
    .addEntry('js/dashboard_admin', './assets/js/dashboard_admin.js')
    .addEntry('js/collectionManager', './assets/js/collectionManager.js')
    .addEntry('js/evaluation', './assets/js/evaluation.js')
    .addStyleEntry('css/app', './assets/css/app.scss')

    .copyFiles([
        {from: './node_modules/ckeditor4/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
        {from: './node_modules/ckeditor4/adapters', to: 'ckeditor/adapters/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/lang', to: 'ckeditor/lang/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/skins', to: 'ckeditor/skins/[path][name].[ext]'}
    ])

    .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
    .enableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();
