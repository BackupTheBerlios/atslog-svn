#!/bin/sh

find ../libexec -name \*lib|xargs -n 1 ./libtest.pl >libtest.log
