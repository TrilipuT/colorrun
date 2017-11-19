const gulp = require('gulp');
const ftp = require('vinyl-ftp');

module.exports = function () {
    var remotePath = process.env.FTP_PATH;
    var conn = ftp.create({
        host: process.env.FTP_HOST,
        user: process.env.FTP_USER,
        password: process.env.FTP_PASS,
    });
    //gulp.src(['*.{php,css}', './**/*.{css,js,php,png,jpg,gif,svg}', '!node_modules/**/*','!assets/src/**/*','!gulp/**/*'])
    return gulp.src(['*.{php,css}', 'assets/built/**'], {base: '.'})
        .pipe(conn.newer(remotePath))
        .pipe(conn.dest(remotePath));
};