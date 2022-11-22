import webpack from 'webpack-stream';
import {src, dest, watch, series, parallel} from 'gulp';
import yargs from 'yargs';
import cleanCss from 'gulp-clean-css';
import gulpif from 'gulp-if';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import autoprefixer from 'autoprefixer';
import named from 'vinyl-named';
import del from "del";
import browserSync from 'browser-sync';

const PRODUCTION = yargs.argv.prod;
const sass = require('gulp-sass')(require('sass'));

export const scripts = () => {
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
            output: {
                filename: '[name].js'
            },
            externals: {
                jquery: 'jQuery'
            }
        }))
        .pipe(dest('dist/js'));
}

export const styles = () => {
    return src(['assets/scss/frontend.scss', 'assets/scss/admin.scss'])
        .pipe(gulpif(!PRODUCTION, sourcemaps.init()))
        .pipe(sass().on('error', sass.logError))
        .pipe(gulpif(PRODUCTION, postcss([autoprefixer])))
        .pipe(gulpif(PRODUCTION, cleanCss({compatibility: '*'})))
        .pipe(gulpif(!PRODUCTION, sourcemaps.write()))
        .pipe(dest('dist/css'))
}

export const watchForChanges = () => {
    watch('assets/scss/**/*.scss', series(styles));
    watch('assets/js/**/*.js', series(scripts));
}

export const clean = () => del(['dist']);

export const build = series(clean, styles, scripts)