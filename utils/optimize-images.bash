#!/bin/bash
#
# Need optipng and jpegoptim installed

find news_img -type f -name "*.jpg" -exec jpegoptim --strip-all {} \;
find news_img -type f -name "*.jpeg" -exec jpegoptim --strip-all {} \;
find news_img -type f -name "*.png" -exec optipng -fix -o2 {} \;