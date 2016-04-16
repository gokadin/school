#!/bin/bash

git add -A .

read -p "Message: " message
git commit -m $message

git push origin master
