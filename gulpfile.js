"use strict";

const { src, dest, watch, series, parallel } = require("gulp");
const concat = require("gulp-concat");
const sourcemaps = require("gulp-sourcemaps");
const sass = require("gulp-sass")(require("sass"));
const minifyCSS = require("gulp-clean-css");
const minifyJS = require("gulp-uglify");

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

function watcher() {
  watch("./assets/scripts/**/*.js", doScript);
}

exports.doScript = doScript;
exports.watcher = watcher;
