var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('assets/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
     .addEntry('js/tree', './assets/jsx/NestedTree.jsx')
     .addStyleEntry('css/app', './assets/scss/app.scss')

    .createSharedEntry('vendor', [
        'jquery',
        'bootstrap',
        'bootstrap-sass/assets/stylesheets/_bootstrap.scss'
    ])
     .enableSassLoader()
     .enableReactPreset()
     .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
