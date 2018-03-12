#/bin/bash

[ ! -z $BASH_LIB ] && source $BASH_LIB/lib/helper.inc.sh

[ -d sample ] || quit "sample dir not found"
[ -d exo ] || quit "exo dir not found"
[ -d doc ] || quit "doc dir not found"

# get arguments
PHP_ARGS=""
while [ "x${1:0:1}" == "x-" ] ; do
  case $1 in
    -d|--debug) PHP_ARGS="-d xdebug.profiler_enable=On" ; shift ;;
    *) quit "'$1' bad arg" ;;
  esac
done

if [ -z $1 ] ; then
  info "please choose a sample :"
  select sample in `ls -1 sample/` ; do
    if [ -z $sample ] ; then
      warn "please choose a valid sample !"
    else
      info "'$sample' choosen."
      break
    fi
  done
else
  sample="$1"
fi

PHP_BIN="/usr/local/bin/php7"
[ -x $PHP_BIN ] || quit "php binary ($PHP_BIN) not found"
php="exo/$sample.php"
sample_dir="sample/$sample"
[ -d "$sample_dir" ] || quit "'$sample_dir' : sample not found"
[ -e "$php" ] || quit "'$php' php file not found"

for input in `find $sample_dir -name "input*.txt" | grep -v debug` ; do
  output="${input//input/output}"
  [ -e $output ] || quit "'$input' input has no output sample !"
  info "diff '$php' $output"
  prg_out="`cat $input | $PHP_BIN $PHP_ARGS $php`"
  expected_out="$(echo "`cat $output`")"
  diff <(echo "$prg_out") <(echo "$expected_out")
  out=$?
  if [ $out -ne 0 -a $sample == "04-adn" ] ; then
    diff <(echo "$prg_out") <(echo "$expected_out" | sed 's/^\([^#]*\)#\([^#]*\)$/\2#\1/')
    out=$?
  fi
  if [ $out -eq 0 ] ; then
    succ "'$output' is OK"
  else
    err "'$output' is KO"
    break
  fi
done
