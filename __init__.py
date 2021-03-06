# -*- coding: utf-8 -*-

import os
import subprocess
import time

import pytest

import requests

owncloud_repo = os.path.dirname(__file__)
php_sh = os.path.abspath(os.path.join(owncloud_repo, 'php.sh'))


def wait():
    for i in range(5):
        try:
            requests.get('http://127.0.0.1:8080/')
        except Exception as e:
            # Don't know exact exception class, don't care.
            # Also, https://github.com/kennethreitz/requests/issues/2192
            if 'connection refused' not in str(e).lower():
                raise
            time.sleep(2 ** i)
        else:
            return True
    return False


class ServerMixin(object):
    @pytest.fixture(autouse=True)
    def setup(self, xprocess):
        def preparefunc(cwd):
            return wait, ['sh', php_sh]

        xprocess.ensure('owncloud_server', preparefunc)
        subprocess.check_call([os.path.join(owncloud_repo, 'reset.sh')])

    @pytest.fixture
    def get_storage_args(self):
        def inner(collection='test'):
            url = 'http://127.0.0.1:8080'
            if self.storage_class.fileext == '.vcf':
                url += '/card.php/addressbooks/asdf/'
            elif self.storage_class.fileext == '.ics':
                url += '/cal.php/calendars/asdf/'
            else:
                raise RuntimeError(self.storage_class.fileext)

            rv = {'url': url, 'username': 'asdf', 'password': 'asdf'}
            if collection is not None:
                return self.storage_class.create_collection(
                    collection=collection, **rv)
            else:
                return rv
        return inner
