#!/bin/sh
browser-sync start --proxy "stellite.live.local" --files "site/web/css/*.css, \
  site/web/js/*.js, \
  site/controllers/*.php, \
  site/views/layouts/*.php, \
  site/views/site/*.php"
