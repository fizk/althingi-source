# Althingi

This is an API application that serves althingi (icelandic parliament) data through a HTTP API calls.

Built with [ZF3](https://github.com/zendframework/zf3-web).

## Pre-requirements

* PHP 7.2 ([pdo_mysql](http://php.net/manual/en/ref.pdo-mysql.php), [redis](https://pecl.php.net/package/redis))
* MySQL 5.6
* [Composer](https://getcomposer.org/)
* (ElasticSearch)
* (Redis)

## Install
Clone this repository. Run `$ composer install` and you are pretty much done.

## Configure
This application is configured via environment variables:

| name                | default                          | Options                               | description                   |
|---------------------|----------------------------------|---------------------------------------|-------------------------------|
| DB_HOST             | localhost                        | <string>                              |
| DB_PORT             | 3306                             | <number>                              |
| DB_NAME             | althingi                         | <string>                              |
| DB_USER             | root                             | <string>                              |
| DB_PASSWORD         |                                  | <string>                              |
| SEARCH              | elasticsearch                    | elasticsearch / none                  |
| LOGGER_SAVE         | true                             | true / false                          | Save logs to disk
| LOGGER_STREAM       | true                             | true / false                          | Print logs to stdout
| LOGGER_PATH_LOGS    | ./data/log/althingi.log          | <string>                              | Where to save logs
| LOGGER_PATH_ERROR   | ./data/log/althingi.error.json   | <string>                              | Where to save error logs
| LOGGER_FORMAT       | none                             | logstash / json / line / color / none |
| CACHE_TYPE          | none                             | file / memory / none                  |
| CACHE_HOST          |                                  | <string>                              |
| CACHE_PORT          |                                  | <number>                              |
| ES_HOST             | localhost                        | <string>                              |
| ES_PROTO            | http                             | <string>                              |
| ES_PORT             | 9200                             | <number>                              |
| ES_USER             | elastic                          | <string>                              |
| ES_PASSWORD         | changeme                         | <string>                              |


## Database
Under `./auto/db/schema.sql` is a file containing the database schema. Import it anyway you want.
One way is to first create the database (often called `althingi`) via command-line and then pipe it in.

```bash
$ mysql -u root -p althingi < ./auto/db/schema
```








This is the REST API data server for the **Loggjafarthing** system.

This is a ZF2 module and as such you have to put it into the `module` directory of an
installed ZF2 application and register it (more in the install section).

## Install
First of all, this code requires PHP 7.1. You also need MySQL (>=5.7.15) and additionally 
you might require ElasticSearch and Memcached. You need x-debug to run tests and Composer
to do the install.

### Pre-install.
This system used ElasticSearch for searching. You do need to set that up. If you are lazy
you can run it as a Docker service. Just make sure you run it in the project's root directory
and not the module's as it created files you don't want to get into the GIT repo
```sh
$ docker run -p 9200:9200 -p 9300:9300 -v "$PWD/data":/usr/share/elasticsearch/data -e "discovery.type=single-node" docker.elastic.co/elasticsearch/elasticsearch:5.6.3
```

Elastic search requires some indexes. They are at the bottom of this document.

Before you start the PHP bits, make sure you have [Composer](https://getcomposer.org/) globally installed
on your system.

Install a [ZF2 skeleton app](https://github.com/zendframework/ZendSkeletonApplication)
```sh
composer create-project --stability="dev" zendframework/skeleton-application Althingi
```

The system will listen for some Env variables. For development, the defaults should work but
if you need to change anything, here is a complete list:

| name            | default   | description                                             |
|-----------------|-----------|---------------------------------------------------------|
| DB_HOST         | localhost | MySQL                                                   |
| DB_PORT         | 3306      |                                                         |
| DB_NAME         | althingi  |                                                         |
| DB_USER         | root      |                                                         |
| DB_DB_PASSWORD  |           |                                                         |
| ES_HOST         | localhost | ElasticSearch                                           |
| ES_PROTO        | http      |                                                         |
| ES_PORT         | 9200      |                                                         |
| ES_USER         | elastic   |                                                         |
| ES_PASSWORD     | changeme  |                                                         |
| CACHE           |           | What to use for response cache: FILE, MEMCACHED or NONE |
| SEARCH          |           | What to use for searching: ELASTICSEARCH or NONE        |


### Install.
Next you need to clone this repo and hook it into the **ZF2 skeleton app**.

First `cd` into the project's root directory and clone the repo into the `module` directory
```sh
cd ./Althingi
git clone https://github.com/fizk/Loggjafarthing.git ./module/Althingi
```
Next you need to get all the 3rd party libraries and dependencies. I like to install them into
the module and NOT into the root project directory, it makes testing easier. 
```sh
composer install -d ./module/Althingi 
```
I like to remove the default module so it is not in my way as I don't need it.
```sh
rm -rf ./module/Application
```

Now you need to tell ZF2 about your new module, so register it
```php
//  ./config/application.config.php

'modules' => array(
    'Althingi',
),
```

Because we are not using the normal path to the `vendor` directory, you need to change its include
path
```php
//  ./init_autoload.php

...

if (file_exists('module/Althingi/vendor/autoload.php')) {
    $loader = include 'module/Althingi/vendor/autoload.php';
}

...

```

### Post-install.
Now it's time to set up the database.

Start off by creating a `althingi` database. You can call it what you want but I do think
`althingi` is the best option.

There is a snapshot of the schema located in the `assets/database` directory. You can just
run it into the newly created database.
```sh
$ mysql -u root althingi < ./module/Althingi/assets/database/dump.sql
```
if you are having problems with the GROUP BY clauses, add this to /etc/mysql/my.cnf
```
[mysqld]
sql_mode = STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION
```

Now everything should be working and you could start the system by running it through the built-in
server. In your terminal go to the project's `public` directory (not the module's) and run

```sh
$ php -S 0.0.0.0:8080

```

## Cache
The system will cache the response of any successful GET request. During development when you
constantly changing data in the database, you might want the turned of in that case, don't set
a value for the `CACHE` env variable.

When you are developing the front-end and want a snappy response but don't care about the data,
use the value `FILE` for the `CACHE` env variable. It will use a caching-system that writes
file onto the disk.

When you are in production use the value `MEMCACHED` for the `CACHE` env variable. It will cache
the data in a MemCached cluster. You then have to give the right `MEMCACHED_*` values for the
username/pass etc...

If you want yet another caching system you will need to go into `` and return another adapter.
ZF2 already comes with a few: Apc, Dba, Filesystem , Memcached, Redis, Memory, WinCache, XCache
and a ZendServerDisk Adapter. If you need yet another you will have to implement the 
[Zend\Cache\Storage\StorageInterface](https://framework.zend.com/apidoc/2.1/classes/Zend.Cache.Storage.StorageInterface.html)
interface.

## Search
This system uses ElasticSearch for full-text-search. If you are just doing development and don't care
id the search produces any results, DON'T set any value for the `SEARCH` env variable. Else if you
are interested in searching and have ElasticSearch set up, use `ELASTICSEARCH` as a value for
the `SEARCH` env variable.

When ever a **POST**, **PUSH** or **PATCH** request is made to a _Party_, _Congressman_, _Issue_ or
_Speech_, the ElasticSearch index will be updated with the new data.

If you consumed some data while the ElasticSearch adapter was disabled, you can re-index with data
from your database by running the command-line `index:*` commands. See the **CommandLine** section
for more information.


## Unit-Tests
You can run the whole unit-test suite by going to the the test directory and running
```sh
$ cd ./module/Althingi/tests
$ ../vendor/bin/phpunit
```
Just be aware of the following:
 
The tests will also run tests on the database. Before it starts it will dump a dataless schema from
you development MySQL database and pipe it into a new database called `althingi_test` and then
use that database to create and delete records.

For this to work, you need to have `mysql` and `mysqldump` available in you `$PATH`. I just could not
get it to work on my machine, and therefore needed to have the full path to these binaries.

Head over to `./module/Althingi/tests/phpunit.xml` and change the values of `MYSQL.BIN` and
`MYSQLDUMP.BIN` if needed.
```xml
    <php>
        <var name="APPLICATION_ENVIRONMENT" value="development" />
        <var name="MYSQL.BIN" value="/usr/local/mysql/bin/mysql" />
        <var name="MYSQLDUMP.BIN" value="/usr/local/mysql/bin/mysqldump" />
    </php>
```
You also need `x-debug` if you want code coverage.

## CommandLine
 
This systems comes with a few command-line commands that you can run from the project's `public`
directory like so:

```sh
$ php index.php [command-name]
```

| command-name  | args | description                                                   |
|---------------|------|---------------------------------------------------------------|
| index:speech  |      | Creates a record of each Speech in the ElasticSearch cluster  |
| index:issue   |      | Creates a record of each Issue in the ElasticSearch cluster   |
| document:api  |      | Prints out all available API endpoints                        |


## The bigger picture
The service is a part of a bigger system that includes:

* [AlthingiQL](https://github.com/fizk/AlthingiQL)
* [AlthingiAggregator](https://github.com/fizk/AlthingiAggregator)

This system is the blue box in this diagram: 

![loggjafarthing-schema](https://user-images.githubusercontent.com/386336/33863222-d396775c-df3a-11e7-8ed1-34a67bebecdc.png)

http://www.althingi.is/xml/145
    fundir
    dagskra
    lidaskjol
    raedur
    raedur_bradabirgda
    utbyting
    fjarvist
    inn
    thingmalaskra
    lestrarsalur


## ElasticSearch
```
PUT althingi_model_speech
{
  "settings": {
    "analysis": {
      "analyzer": {
        "std_lower_case": {
          "type": "custom",
          "tokenizer": "standard",
          "filter": ["lowercase"],
          "char_filter":  [ "html_strip" ]
        }
      }
    }
  }, 
  "mappings": {
    "althingi_model_speech": {
      "properties": {
        "assembly_id": {
          "type": "long"
        },
        "issue_id": {
          "type": "long"
        },
        "plenary_id": {
          "type": "long"
        },
        "congressman_id": {
          "type": "long"
        },
        "congressman_type": {
          "type": "keyword"
        },
        "to": {
          "format": "yyyy-MM-dd HH:mm:ss",
          "type": "date"
        },
        "from": {
          "format": "yyyy-MM-dd HH:mm:ss",
          "type": "date"
        },
        "iteration": {
          "type": "keyword"
        },
        "speech_id": {
          "type": "keyword"
        },
        "text": {
          "fields": {
            "raw": {
              "analyzer": "std_lower_case", 
              "type": "text"
            }
          },
          "type": "text"
        },

        "type": {
          "type": "keyword"
        }
      }
    }
  }
}
```


```
PUT althingi_model_issue
{
  "settings": {
    "analysis": {
      "analyzer": {
        "std_lower_case": {
          "type": "custom",
          "tokenizer": "standard",
          "filter": ["lowercase"],
          "char_filter":  [ "html_strip" ]
        }
      }
    }
  }, 
  "mappings": {
    "althingi_model_issue": {
      "properties": {
        "assembly_id": {
          "type": "long"
        },
        "issue_id": {
          "type": "long"
        },
        "congressman_id": {
          "type": "long"
        },
        "category": {
          "type": "keyword"
        },
        "name": {
          "fields": {
            "raw": {
              "analyzer": "std_lower_case", 
              "type": "text"
            }
          },
          "type": "text"
        },
        "sub_name": {
          "fields": {
            "raw": {
              "analyzer": "std_lower_case", 
              "type": "text"
            }
          },
          "type": "text"
        },
        "question": {
          "fields": {
            "raw": {
              "analyzer": "std_lower_case", 
              "type": "text"
            }
          },
          "type": "text"
        },
        "goal": {
          "fields": {
            "raw": {
              "analyzer": "std_lower_case", 
              "type": "text"
            }
          },
          "type": "text"
        },
        "changes_in_law": {
          "fields": {
            "raw": {
              "analyzer": "std_lower_case", 
              "type": "text"
            }
          },
          "type": "text"
        },
        "costs_and_revenues": {
          "fields": {
            "raw": {
              "analyzer": "std_lower_case", 
              "type": "text"
            }
          },
          "type": "text"
        },
        "deliveries": {
          "fields": {
            "raw": {
              "analyzer": "std_lower_case", 
              "type": "text"
            }
          },
          "type": "text"
        },
        "additional_information": {
          "fields": {
            "raw": {
              "analyzer": "std_lower_case", 
              "type": "text"
            }
          },
          "type": "text"
        },
        "type": {
          "type": "keyword"
        },
        "type_name": {
          "type": "keyword"
        },
        "type_subname": {
          "type": "keyword"
        },
        "status": {
          "type": "keyword"
        }
      }
    }
  }
}
```



```sh
$ docker run -e MAX_WIDTH=11402470 -e MAX_HEIGHT=11402470 -e UPLOAD_MAX_SIZE=11982500 -e UPLOAD_ENABLED=True -e UPLOAD_PUT_ALLOWED=True -e DETECTORS="['thumbor.detectors.face_detector','thumbor.detectors.feature_detector']" -p 8000:8000 apsl/thumbor
```
