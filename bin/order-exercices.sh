#!/bin/bash

DEBUG=0
while [ ! -z $1 ]  ; do
    case $1 in
        -d|--debug) DEBUG=1 ; shift ;;
        *) echo "'$1' bad arg" ; exit 1 ;;
    esac
done

for exo in `cat lst/*.txt` ; do
    suf="${exo:0:2}"
    name="${exo:3}"
    echo "exo:$exo"
    for dir in `find ./sample -maxdepth 1 -name "${name}" -type d` ; do
        echo "  dir:$dir"
        rootdir="`dirname $dir`"
        basedir="`basename $dir`"
        if [ $DEBUG -eq 1 ] ; then
            echo "    mv -v $dir $rootdir/${suf}-$basedir | sed 's/^/    /'"
        else
            mv -v $dir $rootdir/${suf}-$basedir | sed 's/^/    /'
        fi
    done
    for file in `find ./doc ./exo -name "${name}*" -type f` ; do
        echo "  file:$file"
        dir="`dirname $file`"
        base="`basename $file`"
        if [ $DEBUG -eq 1 ] ; then
            echo "    mv -v $file $dir/${suf}-$base | sed 's/^/    /'"
        else
            mv -v $file $dir/${suf}-$base | sed 's/^/    /'
        fi
    done
    echo
done
