#!/bin/bash

## Updates `commits` table
##
## Fetches data using git log fu 
## and posts data using the `/commits/new` endpoint

# load config variables
. ../config/config.cfg

echo $URL_ROOT

# TODO: do this for all repos
# cd /home/abhi/projects/insight

git log --format=tformat:"%H%n" | egrep  --color=never -o  "[a-f0-9]{40}" | while read hash; do
  echo "$hash"
  message=`git log --format=%B -n 1 $hash`
  echo "$message"
  additions=`git log --shortstat --format=format:"" -n 1 $hash | egrep --color=never -o "changed, [0-9][0-9]* insertions"`
  deletions=`git log --shortstat --format=format:"" -n 1 $hash | egrep --color=never -o "\), [0-9][0-9]* deletions"`
  files_affected=`git log --shortstat --format=format:"" -n 1 $hash | egrep --color=never -o "[0-9][0-9]* file"`
  author=`git log --format=tformat:"%ae" -n 1 $hash`
  timestamp=`git log --format=format:"%ct" -n 1 $hash`
  files_affected=${files_affected::-5}
  identifier=${PWD##*/}
  if [[ -z "$additions" ]]; then
    additions="0"
  else
    additions=${additions:9}
    additions=${additions::-11}
  fi
  if [[ -z "$deletions" ]]; then
    deletions="0"
  else
    deletions=${deletions:3}
    deletions=${deletions::-10}
  fi
  echo $additions
  echo $deletions
  echo $files_affected
  echo $author
  echo $identifier
  echo $timestamp
  curl -X POST -d "hash=$hash" -d "message=$message" -d "timestamp=$timestamp&author=$author&additions=$additions&deletions=$deletions&files_affected=$files_affected&identifier=$identifier" $URL_ROOT
done
