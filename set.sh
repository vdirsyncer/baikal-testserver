#!/bin/sh
set -e
DIR="$( cd "$( dirname "$0" )" && pwd )"

cd "$DIR"
cp baikal/Specific/db/db.sqlite config/
cp baikal/Specific/config.php config/
cp baikal/Specific/config.system.php config/
