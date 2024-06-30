"use strict";

import gulp from "gulp";
const { src, dest, watch, series, parallel } = gulp;
import concat from "gulp-concat";
import sourcemaps from "gulp-sourcemaps";
import * as dartSASS from "sass";
import gulpSASS from "gulp-sass";
const sass = gulpSASS(dartSASS);
import minifyCSS from "gulp-clean-css";
import minifyJS from "gulp-uglify";

const paths = {
  scripts: {
    src: [
      "./assets/scripts/bookmark-me-button.js",
      "./assets/scripts/bookmarked-posts-list.js",
    ],
    dest: "./assets/_dist_",
  },
};

function doScript() {
  return src(paths.scripts.src)
    .pipe(sourcemaps.init())
    .pipe(minifyJS())
    .pipe(concat("generated-scripts.js"))
    .pipe(sourcemaps.write("./"))
    .pipe(dest(paths.scripts.dest));
}

const doAll = doScript;

function watcher() {
  watch("./assets/scripts/**/*.js", doScript);
}

export {
  doScript,
  doAll,
  watcher
}
