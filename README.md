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
| LOG_PATH            |                                  | <string>                              | Save logs to disk or php://stdout
| LOG_FORMAT          | none                             | logstash / json / line / color / none |
| CACHE_TYPE          | none                             | file / memory / none                  |
| CACHE_HOST          |                                  | <string>                              |
| CACHE_PORT          |                                  | <number>                              |
| ES_HOST             | localhost                        | <string>                              |
| ES_PROTO            | http                             | <string>                              |
| ES_PORT             | 9200                             | <number>                              |
| ES_USER             | elastic                          | <string>                              |
| ES_PASSWORD         | changeme                         | <string>                              |
| QUEUE               | none                             | rabbitmq / none                       |
| QUEUE_HOST          | localhost                        | <string>                              |
| QUEUE_PORT          | 5672                             | <string>                              |
| QUEUE_USER          | guest                            | <string>                              |
| QUEUE_PASSWORD      | guest                            | <string>                              |
| QUEUE_VHOST         | /                                | <string>                              |


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

![althingi-schema 2 0](https://user-images.githubusercontent.com/386336/45269836-f8352180-b4d7-11e8-9085-b18a33f6efec.png)
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
        "text_searcher": {
          "type": "custom",
          "tokenizer": "standard",
          "filter": ["lowercase", "asciifolding", "icelandic_stop"],
          "char_filter": ["icelandic", "html_strip"]
        }
      },
      "normalizer": {
        "lowercase_normalizer": {
          "type": "custom",
          "char_filter": ["icelandic"],
          "filter": ["lowercase", "asciifolding"]
        }
      },
      "char_filter": {
        "icelandic": {
          "type": "mapping",
          "mappings": [
            "Á => A",
            "Ð => D",
            "É => E",
            "Í => I",
            "Ó => O",
            "Ú => O",
            "Ý => Y",
            "Þ => TH",
            "Æ => AE",
            "Ö => O",
            "á => a",
            "ð => d",
            "é => e",
            "í => i",
            "ó => o",
            "ú => u",
            "ý => y",
            "þ => th",
            "æ => ae",
            "ö => o"
          ]
        }
      },
      "filter": {
        "icelandic_stop": {
          "type":       "stop",
          "stopwords": [
            "a",
            "ad",
            "adra",
            "adrar",
            "adrir",
            "adur en",
            "af",
            "af thvi",
            "af thvi ad",
            "alla",
            "allan",
            "allar",
            "allir",
            "allra",
            "allrar",
            "allri",
            "alls",
            "allt",
            "allur",
            "an",
            "annad",
            "annan",
            "annar",
            "annarra",
            "annarrar",
            "annarri",
            "annars",
            "auk",
            "bada",
            "badar",
            "badir",
            "badum",
            "baedi",
            "beggja",
            "e",
            "ed",
            "eda",
            "ef",
            "eftir",
            "eftir ad",
            "eg",
            "einhver",
            "einhverja",
            "einhverjar",
            "einhverjir",
            "einhverju",
            "einhverjum",
            "einhvern",
            "einhverra",
            "einhverrar",
            "einhverri",
            "einhvers",
            "einn",
            "eins og",
            "einskis",
            "eitt",
            "eitthvad",
            "eitthvert",
            "ek",
            "ekkert",
            "ekki",
            "ellegar",
            "en",
            "enda",
            "enga",
            "engan",
            "engar",
            "engi",
            "engin",
            "enginn",
            "engir",
            "engra",
            "engrar",
            "engri",
            "engu",
            "engum",
            "er",
            "faeinir",
            "fra",
            "fyrir",
            "hana",
            "hann",
            "hans",
            "hanum",
            "heldur",
            "heldur en",
            "hennar",
            "henni",
            "herna",
            "hinn",
            "hja",
            "hon",
            "honum",
            "hun",
            "hvad",
            "hvada",
            "hver",
            "hvergi",
            "hverja",
            "hverjar",
            "hverjir",
            "hverju",
            "hverjum",
            "hvern",
            "hverra",
            "hverrar",
            "hverri",
            "hvers",
            "hvert",
            "hvilíkur",
            "hvor",
            "hvora",
            "hvorar",
            "hvorir",
            "hvorn",
            "hvorra",
            "hvorrar",
            "hvorri",
            "hvors",
            "hvort",
            "hvoru",
            "hvorugur",
            "hvorum",
            "i",
            "id",
            "innan",
            "m",
            "med",
            "medan",
            "medfram",
            "mer",
            "mig",
            "milli",
            "min",
            "mina",
            "minar",
            "minir",
            "minn",
            "minna",
            "minnar",
            "minni",
            "mins",
            "minu",
            "minum",
            "mitt",
            "neinn",
            "nema",
            "nokkrir",
            "nokkur",
            "odru",
            "odrum",
            "og",
            "okkar",
            "okkur",
            "oll",
            "ollu",
            "ollum",
            "onnur",
            "oss",
            "sa",
            "sem",
            "ser",
            "serhver",
            "sig",
            "sin",
            "sina",
            "sinar",
            "sinir",
            "sinn",
            "sinna",
            "sinnar",
            "sinni",
            "sins",
            "sinu",
            "sinum",
            "sitt",
            "sitthvad",
            "sjalfur",
            "sko",
            "su",
            "sumur",
            "tha",
            "thad",
            "thaer",
            "thann",
            "thar sem",
            "that",
            "thau",
            "thegar",
            "theim",
            "their",
            "theirra",
            "theirrar",
            "theirri",
            "thennan",
            "ther",
            "thess",
            "thessa",
            "thessar",
            "thessara",
            "thessarar",
            "thessari",
            "thessi",
            "thessir",
            "thessu",
            "thessum",
            "thetta",
            "thid",
            "thig",
            "thin",
            "thina",
            "thinar",
            "thinir",
            "thinn",
            "thinna",
            "thinnar",
            "thinni",
            "thins",
            "thinu",
            "thinum",
            "thit",
            "thitt",
            "tho ad",
            "thott",
            "thu",
            "thvi",
            "til",
            "til thess ad",
            "um",
            "und",
            "undir",
            "ur",
            "vegna",
            "ver",
            "vid",
            "vor",
            "ydar",
            "ydur",
            "yfir",
            "ykkar",
            "ykkur",
            "ymis"
          ]
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
        "type": {
          "type": "keyword",
          "normalizer": "lowercase_normalizer"
        },
        "text": {
          "type": "text",
          "analyzer": "text_searcher"
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
