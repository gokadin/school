#!/bin/bash

git add -A .

read -p "Message: " message
git commit -m $message

if [ "$1" != "" ]
then branch="$1"
else branch="master"
fi

echo $branch

#git push origin $branch
