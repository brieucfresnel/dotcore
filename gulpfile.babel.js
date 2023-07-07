import yargs from 'yargs';
import cleanCss from 'gulp-clean-css';
import gulpif from 'gulp-if';
import sourcemaps from 'gulp-sourcemaps';
import named from 'vinyl-named';
import del from "del";
import browserSync from 'browser-sync';

const PRODUCTION = yargs.argv.prod;
const PREFIXES = yargs.argv.prefixes;
const sass = require('gulp-dart-sass');
const cache = require('gulp-cache');

export const generalScripts = () => {
    return src(['assets/js/frontend.js', 'assets/js/admin.js'])
        .pipe(named())
        .pipe(webpack({
            module: {
                rules: [
                    {
                        test: /\.js$/,
                        use: {
                            loader: 'babel-loader',
                            options: {
                                presets: ['@babel/preset-env']
                            }
                        }
                    }
                ]
            },
            mode: PRODUCTION ? 'production' : 'development',
            devtool: !PRODUCTION ? 'inline-source-map' : false,
            optimization: {
                splitChunks: {
                    chunks: 'async',
                    minSize: 20000,
                    minRemainingSize: 0,
                    minChunks: 1,
                    maxAsyncRequests: 30,
                    maxInitialRequests: 30,
                    enforceSizeThreshold: 50000,
                    cacheGroups: {
                        defaultVendors: {
                            test: /[\\/]node_modules[\\/]/,
                            priority: -10,
                            reuseExistingChunk: true,
                        },
                        default: {
                            minChunks: 2,
                            priority: -20,
                            reuseExistingChunk: true,
                        },
                    },
                },
            },
            output: {
                filename: '[name].js'
            },

        }))
        .pipe(dest('dist/js'))
        .pipe(cache.clear());
}

export const generalStyles = () => {
    return src(['assets/scss/frontend.scss', 'assets/scss/admin.scss', 'dotstarter/**/**/*.scss'])
        .pipe(sass().on('error', sass.logError))
        .pipe(concat("frontend.css"))
        .pipe(gulpif(PRODUCTION, cleanCss({ compatibility: 'ie11' })))
        .pipe(gulpif(!PRODUCTION, sourcemaps.init()))
        .pipe(gulpif(!PRODUCTION, sourcemaps.write('.')))
        .pipe(server.stream())
        .pipe(dest("dist/css"))
}

export const adminStyles = () => {
    return src(['assets/scss/admin.scss', 'dotstarter/**/**/*.scss'])
        .pipe(gulpif(!PRODUCTION, sourcemaps.init()))
        .pipe(gulpif(!PRODUCTION, sourcemaps.write()))
        .pipe(sass().on('error', sass.logError))
        .pipe(concat("admin.css"))
        .pipe(gulpif(PRODUCTION, cleanCss({ compatibility: 'ie11' })))
        .pipe(server.stream())
        .pipe(dest("dist/css"))
}

export const watchForChanges = () => {
    watch(['assets/js/**/*.js', 'dotstarter/**/**/*.js'], generalScripts, reload);
    watch(['assets/scss/**/*.scss', 'dotstarter/**/**/*.scss', 'dotstarter/**/**/*.php', 'templates/*.php', 'templates/**/*.php'], series(generalStyles, adminStyles), reload);
}

export const clean = () => del(['dist']);

const server = browserSync.create();

export const serve = done => {
    server.init({
        proxy: "https://lemetronum.local",
    });
    done();
};

export const reload = done => {
    server.reload({ stream: true });
    done();
};

export const dev = series(clean, parallel(generalStyles, adminStyles, generalScripts), serve, watchForChanges);
export const build = series(clean, generalStyles, adminStyles, generalScripts)
export default dev;