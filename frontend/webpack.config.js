
const Encore = require("@symfony/webpack-encore");

Encore
    .setOutputPath("../public/build")
    .setPublicPath("public")
    .addEntry("index", "./src/index.js")
    .enableSourceMaps(!Encore.isProduction())
    .configureBabel(function(babelConfig) {
        babelConfig.presets.push('@babel/preset-react');
        babelConfig.plugins.push("@babel/plugin-proposal-class-properties");
    })
    .cleanupOutputBeforeBuild();

module.exports = Encore.getWebpackConfig();
