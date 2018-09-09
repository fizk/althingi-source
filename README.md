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
| DB_NAME_TEST        | althingi_test                    | <string>                              |
| DB_SETUP            | false                            | true / false                          |
| SEARCH              | elasticsearch                    | elasticsearch / none                  |
| LOG_PATH            |                                  | <string>                              | Save logs to disk
| LOG_FORMAT          | none                             | logstash / json / line / color / none |
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
