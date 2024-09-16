const path = require('path');
const { VueLoaderPlugin } = require('vue-loader');

module.exports = {
    entry: './resources/js/app.js',
    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'public/js'),
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader']
            },
            {
                test: /\.(png|jpe?g|gif)$/i,
                use: ['file-loader']
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: { loader: 'babel-loader' }
            }
        ]
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js'
        },
        extensions: ['*', '.js', '.vue', '.json']
    },
    plugins: [
        new VueLoaderPlugin()
    ]
};
