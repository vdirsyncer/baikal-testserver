#!/bin/sh
set -e
BAIKAL_VERSION="0.4.6"
BAIKAL_DOWNLOAD_URL="https://github.com/fruux/Baikal/releases/download/${BAIKAL_VERSION}/baikal-${BAIKAL_VERSION}.zip"
DIR="$( cd "$( dirname "$0" )" && pwd )"
echo "install.sh: Own directory is $DIR"

if [ "$CI" = "true" ]; then
    sudo add-apt-repository -y ppa:ondrej/php
    # who cares if one or two repos are down. As long as i'm able to install
    # these packages...
    sudo apt-get update || true

    sudo apt-get install \
        php php-cgi php-cli \
        php-gd php-json php-sqlite3 php-curl \
        php-intl php-mcrypt php-imagick \
        php-xml php-zip php-mbstring
fi

cd "$DIR"

if [ ! -d baikal ]; then
    if [ ! -f baikal.zip ]; then
        echo "Downloading baikal version: $BAIKAL_VERSION"
        wget "$BAIKAL_DOWNLOAD_URL" -O baikal.zip
    fi
    echo "Extracting Baikal"
    unzip baikal.zip
fi

pip install pytest-xprocess

sh $DIR/reset.sh
