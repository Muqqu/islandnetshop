const mix = require('laravel-mix');
const CompressionPlugin = require("compression-webpack-plugin");
const path = require('path');
const CopyPlugin = require("copy-webpack-plugin");

let css_plugin = 'public/frontend/css/';
let js_plugin = 'public/frontend/js/';
mix.setResourceRoot(process.env.MIX_ASSET_URL);

if (process.env.MIX_ENV_MODE == 'production')
{
    mix.js('resources/js/app.js', 'public/frontend/js/app.js')
        .vue().combine([
        css_plugin + 'bootstrap.min.css',
        css_plugin + 'animate.min.css',
        css_plugin + 'structure.css',
        css_plugin + 'main.css',
        css_plugin + 'development.css',
        css_plugin + 'responsive.css',
        css_plugin + 'vue-plyr.css',
    ], 'public/frontend/css/app.css').combine([
        js_plugin + 'html5shiv.min.js',
        js_plugin + 'respond.min.js',
    ], 'public/frontend/js/plugin.js').webpackConfig({
        output: {
            // filename: '[name].js',
            chunkFilename: 'public/frontend/js/chunks-140/[name][hash].js',
            publicPath: 'auto',
            path: path.resolve(__dirname, 'public'),
        },
        plugins: [
            // new BundleAnalyzerPlugin(),      // load this package to see which plugins with its size detail
            new CompressionPlugin({             // very import to compress the assets
                filename: "[path][base].gz",
                algorithm: "gzip",
                test: /\.js$|\.css$|\.html$|\.svg$/,
                threshold: 10240,
                minRatio: 0.8
            })
        ]
    });
}
else{
    mix.js('resources/js/app.js', 'public/frontend/js/app.js')
        .vue().combine([
        css_plugin + 'bootstrap.min.css',
        css_plugin + 'animate.min.css',
        css_plugin + 'structure.css',
        css_plugin + 'main.css',
        css_plugin + 'development.css',
        css_plugin + 'responsive.css',
        css_plugin + 'vue-plyr.css',
    ], 'public/frontend/css/app.css').combine([
        js_plugin + 'html5shiv.min.js',
        js_plugin + 'respond.min.js',
    ], 'public/frontend/js/plugin.js').webpackConfig({
        output: {
            // filename: '[name].js',
            chunkFilename: 'public/frontend/js/frontendChunks/[name].js',
            publicPath: 'auto',
            path: path.resolve(__dirname, 'public'),
        },
        plugins: [
            // new BundleAnalyzerPlugin(),      // load this package to see which plugins with its size detail
            new CompressionPlugin({             // very import to compress the assets
                filename: "[path][base].gz",
                algorithm: "gzip",
                test: /\.js$|\.css$|\.html$|\.svg$/,
                threshold: 10240,
                minRatio: 0.8
            }),
            new CopyPlugin({
                patterns: [
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_errors_not_found_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_master_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_about_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_after-track-order_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_all_campaign_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_all-seller_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_all-seller_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_blog_details_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_blogs_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_brands_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_campaign_details_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_cart_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_category_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_checkout_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_compare-list_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_contact_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_daily-deals_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_filter_sidebar_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_flash-sale_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_forgot-password_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_home_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_login_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_order-confirmation_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_payment_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_product-details_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_register_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_seller_register_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_shop_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_track-order_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_addresses_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_change_password_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_dashboard_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_digital-product-orders_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_edit-profile_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_followed-shop_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_get-invoice_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_gift-voucher_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_migrate-to-seller_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_notification_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_order-history_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_rewards_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_wallet_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_user_wishlist_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_video_shop_vue.js", to: "frontend/js/frontendChunks" },
                    { from: "public/public/frontend/js/frontendChunks/resources_js_components_frontend_pages_video_shop_details_vue.js", to: "frontend/js/frontendChunks" },
                ],
            })
        ]
    });
}
mix.js('resources/js/admin.js', 'public/admin/js/app.js')


mix.version();

