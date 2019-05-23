#!/usr/bin/env python3

from urllib.request import urlopen
from urllib.parse import quote
from os.path import normpath
from argparse import ArgumentParser
from os import environ


class PHPDB:
    def __init__(self, url, database_name, access_key=None):
        self._url = url
        self._database = database_name
        self._access_key = access_key

    def _fetch(self, url_path, params=None):
        url = normpath(self._url + '/' + url_path).replace(':/', '://', 1)

        params = params if params is not None else {}
        if self._access_key is not None:
            params['key'] = self._access_key

        if len(params):
            params_str = ''
            for k, v in params.items():
                params_str += '&' + quote(str(k)) + '=' + quote(str(v))
            url += '?' + params_str[1:]

        return urlopen(url).read().decode('utf8')

    def __getitem__(self, key):
        return self._fetch('/{0}/{1}'.format(self._database, key))

    def __setitem__(self, key, value):
        if value is None:
            value = ''
        return self._fetch('/{0}/{1}'.format(self._database, key), {'val': value})

    def __delitem__(self, key):
        return self.__setitem__(key, None)


if __name__ == '__main__':
    args_parser = ArgumentParser()
    args_parser.add_argument('server_url', help='url to the PHP database script')
    args_parser.add_argument('database', help='name of the database to user')
    args_parser.add_argument('item', help='item to get/set value of')
    args_parser.add_argument('--key', '-k', help='specifies the access key to the database')
    args_parser.add_argument('--value', '-s', help='sets the new value of the item')
    args_parser.add_argument('--env', '-e', help='takes key from PDB_KEY environmental variable', action='store_true')
    args = args_parser.parse_args()

    database_access_key = args.key
    if args.env:
        database_access_key = environ['PDB_KEY']

    db = PHPDB(args.server_url, args.database, database_access_key)
    if args.value is not None:
        db[args.item] = args.value
    else:
        print(db[args.item])
