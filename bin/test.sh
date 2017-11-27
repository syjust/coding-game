#/bin/bash

[ ! -z $BASH_LIB ] && source $BASH_LIB/lib/helper.inc.sh

[ -d sample ] || quit "sample dir not found"
[ -d exo ] || quit "exo dir not found"
[ -d doc ] || quit "doc dir not found"

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

php="exo/$sample.php"
sample_dir="sample/$sample"
[ -d "$sample_dir" ] || quit "'$sample_dir' : sample not found"
[ -e "$php" ] || quit "'$php' php file not found"

for input in $sample_dir/input*txt ; do
  output="${input//input/output}"
  [ -e $output ] || quit "'$input' input has no output sample !"
  info "diff '$php' $output"
  diff <(cat $input | php $php) <(echo "`cat $output`")
  if [ $? -eq 0 ] ; then
    succ "'$output' is OK"
  else
    err "'$output' is KO"
    break
  fi
done
