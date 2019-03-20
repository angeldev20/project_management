var webpack = require('webpack');
var path = require('path');
var commonsPlugin = new webpack.optimize.CommonsChunkPlugin({
    name: 'components'
});

module.exports = {
    watch: true,
    entry: {
        'bundle': [
            './react_components/Initialize.js',
            './react_components/Money.js',
            './react_components/Work.js',
            './react_components/Project.js',
            './react_components/People.js'
        ]
    },
    output: {
        path: path.resolve('react_bundle'),
        filename: '[name].js' // Template based on keys in entry above
    },
    plugins: [commonsPlugin],
    module: {
        loaders: [
            {test: /\.coffee$/, loader: 'coffee-loader'},
            {
                test: /\.js$/,
                loader: 'babel-loader',
                query: {
                    presets: ['es2015', 'react']
                }
            }
        ]
    }
};