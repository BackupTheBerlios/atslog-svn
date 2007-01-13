#!/bin/sh
LST="modules.lst"

rm -f $LST
for FILENAME in `ls *lib`
do
    (echo $FILENAME:`grep 'MODELS: ' $FILENAME | sed 's/# MODELS: //'`:`grep 'VENDOR: ' $FILENAME | sed 's/# VENDOR: //'`)>>$LST
done;
