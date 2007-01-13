#!/bin/sh

for FILENAME in `ls *lib`
do
    echo $FILENAME:`grep 'MODELS: ' $FILENAME | sed 's/# MODELS: //'`:`grep 'VENDOR: ' $FILENAME | sed 's/# VENDOR: //'`
done;
