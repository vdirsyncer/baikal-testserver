#!/bin/sh
set -e
BAIKAL_VERSION="0.2.7"
BAIKAL_DOWNLOAD_URL="http://baikal-server.com/get/baikal-flat-${BAIKAL_VERSION}.zip"
DIR="$( cd "$( dirname "$0" )" && pwd )"
echo "install.sh: Own directory is $DIR"

if [ "$TRAVIS" = "true" ]; then
    sudo add-apt-repository -y ppa:ondrej/php5-5.6
    # who cares if one or two repos are down. As long as i'm able to install
    # these packages...
    sudo apt-get update || true

    sudo apt-get install php5 php5-cli php5-sqlite
fi

cd "$DIR"

if [ ! -d baikal ]; then
    if [ ! -f baikal.zip ]; then
        echo "Downloading baikal version: $BAIKAL_VERSION"
        wget "$BAIKAL_DOWNLOAD_URL" -O baikal.zip
    fi
    echo "Extracting Baikal"
    unzip baikal.zip
    mv baikal-flat baikal
fi

sh $DIR/reset.sh
