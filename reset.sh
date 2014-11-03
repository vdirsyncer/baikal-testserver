#!/bin/sh
set -e
DIR="$( cd "$( dirname "$0" )" && pwd )"

cd "$DIR"
cp config/db.sqlite baikal/Specific/db/db.sqlite
