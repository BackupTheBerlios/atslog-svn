#!/bin/sh

PATH=${PATH}:/usr/local/gnu-autotools/bin/

rm -rf tmp
. version.inc
echo "Creating atslog-${ver} tarball"
mkdir -p tmp/atslog-${ver}
echo "Export directories"
svn export www/atslog tmp/atslog-${ver}/www >/dev/null
svn export include tmp/atslog-${ver}/include >/dev/null
svn export libexec tmp/atslog-${ver}/libexec >/dev/null
svn export man tmp/atslog-${ver}/man >/dev/null
svn export lang tmp/atslog-${ver}/include/lang >/dev/null
mkdir -p tmp/atslog-${ver}/data >/dev/null
svn export src/atslogd tmp/atslog-${ver}/atslogd >/dev/null
svn export sql tmp/atslog-${ver}/data/sql >/dev/null
svn export textlogs tmp/atslog-${ver}/data/textlogs >/dev/null
svn export INSTALL tmp/atslog-${ver}/INSTALL
svn export DEINSTALL tmp/atslog-${ver}/DEINSTALL
svn export USAGE tmp/atslog-${ver}/USAGE
svn export UPDATING tmp/atslog-${ver}/UPDATING
svn export TODO tmp/atslog-${ver}/TODO
svn export CHANGES tmp/atslog-${ver}/CHANGES
svn export Makefile.in tmp/atslog-${ver}/Makefile.in
svn export configure.in tmp/atslog-${ver}/configure.in
svn export aclocal.m4 tmp/atslog-${ver}/aclocal.m4
cd tmp/atslog-${ver};autoconf
cd ../../
echo "replacing @version@ and buildnumber"
perl scripts/subdir_subst.pl -x -d tmp/ '\@version\@' ${ver}  >/dev/null
perl scripts/subdir_subst.pl -x -d tmp/ '\@buildnumber\@' ${build} >/dev/null
find ./tmp -name \*sds_sav -type f |xargs rm

echo "Creating ChangeLog"
svn2cl ../ -o tmp/atslog-${ver}/ChangeLog
echo "Creating atslog-${ver}.tar.gz"
cd tmp
tar -cvzf atslog-${ver}.tar.gz  atslog-${ver}
echo "Done"
