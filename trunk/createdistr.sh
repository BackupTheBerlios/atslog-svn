#!/bin/sh

rm -rf tmp
. version.inc
echo "Creating atslog-${ver} tarball"
mkdir -p tmp/atslog-${ver}
echo "Export directories"
svn export www/atslog tmp/atslog-${ver}/www >/dev/null
svn export include tmp/atslog-${ver}/include >/dev/null
svn export libexec tmp/atslog-${ver}/libexec >/dev/null
svn export man tmp/atslog-${ver}/man >/dev/null
mkdir -p tmp/atslog-${ver}/data >/dev/null
svn export src/atslogd tmp/atslog-${ver}/atslogd >/dev/null
svn export sql tmp/atslog-${ver}/data/sql >/dev/null
svn export textlogs tmp/atslog-${ver}/data/textlogs >/dev/null
cd tmp
tar -cvzf atslog-${ver}.tar.gz  atslog-${ver} 