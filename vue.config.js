module.exports = {
    devServer: {
        proxy: {
            '/api' : {
                target: 'http://172.17.100.242',
                changeOrigin: true,
            }
        }
    }
}