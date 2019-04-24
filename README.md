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
| QUEUE_FORCED        | false                            | true / false                          | if event should be sent to queue even though no update occurred
| STORAGE_HOST        | localhost                        | <string>                              | MongoDB host
| STORAGE_PORT        | 27017                            | <number>                              | MongoDB port


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

| command-name     | args | description                                                                                             |
|------------------|------------------------------------------------|---------------------------------------------------------------|
| index:assembly   | --assembly= -a                                 | Calls events for adding/creating assembly (and sub items)     |
| index:issue      | --assembly= -a, --issue= -i --category= -c     | Calls events for adding/creating issues (and sub items)       |
| document:api     |                                                | Prints out all available API endpoints                        |


### index:assembly | index:issue
More for development. What this command does is to fetch each requested assembly/issue and then for each of the sub-items like:
documents, speeches, proponents etc.. calls the event that is usually called when that items is created by normal API POST/PUSH/PATCH
calls. That is, an `*.add` event is created in the queue (RabbitMQ).

This allows for, in development, to clear everything from the Store (MongoDB) and have everything re-indexed/accumulated from
scratch.  

## The bigger picture
The service is a part of a bigger system that includes:

* [AlthingiQL](https://github.com/fizk/AlthingiQL)
* [AlthingiAggregator](https://github.com/fizk/AlthingiAggregator)

This system is the blue box in this diagram: 

![althingi-schema 2 0](https://user-images.githubusercontent.com/386336/54566370-c53e8e00-4a24-11e9-99e7-8cedad9113b2.png)

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

GET althingi_model_issue/_mapping

GET althingi_model_issue/_search
{
  "query": {
    "bool": {
      "must": [
        {
          "bool": {
            "must": [
              {
                "term": {
                  "type": {
                    "value": "l"
                  }
                }
              }, {
                "term": {
                  "assembly_id": {
                    "value": 146
                  }
                }
              }, {
                "term": {
                  "category": {
                    "value": "A"
                  }
                }
              }
            ]
          }
        },
        {
          "query_string": {
            "default_operator": "OR",
            "fields": [
              "name",
              "sub_name",
              "goal"
            ],
            "query": "kjararáð"
          }
        }
      ]
    }
  }
}


DELETE _template/template_althingi_model_issue
PUT _template/template_althingi_model_issue?include_type_name=true
{
    "settings": {
    "analysis": {
      "analyzer": {
        "text_searcher": {
          "type": "custom",
          "tokenizer": "max_size_tokenizer",
          "filter": ["lowercase", "icelandic_stop"],
          "char_filter": ["icelandic", "html_strip"]
        }
      },
      "tokenizer": {
        "max_size_tokenizer": {
          "type": "standard",
          "max_token_length": 5
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
          "analyzer": "text_searcher", 
          "type": "text"
        },
        "sub_name": {
          "analyzer": "text_searcher", 
          "type": "text"
        },
        "question": {
          "analyzer": "text_searcher", 
          "type": "text"
        },
        "goal": {
          "analyzer": "text_searcher", 
          "type": "text"
        },
        "changes_in_law": {
          "analyzer": "text_searcher", 
          "type": "text"
        },
        "costs_and_revenues": {
          "analyzer": "text_searcher", 
          "type": "text"
        },
        "deliveries": {
          "analyzer": "text_searcher", 
          "type": "text"
        },
        "additional_information": {
          "analyzer": "text_searcher", 
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
  },
  "index_patterns": "*_model_issue"
}

DELETE _template/template_althingi_model_speech
PUT _template/template_althingi_model_speech?include_type_name=true
{
  "settings": {
    "analysis": {
      "analyzer": {
        "text_searcher": {
          "type": "custom",
          "tokenizer": "max_size_tokenizer",
          "filter": [
            "lowercase",
            "icelandic_stop"
          ],
          "char_filter": [
            "icelandic",
            "html_strip"
          ]
        }
      },
      "tokenizer": {
        "max_size_tokenizer": {
          "type": "standard",
          "max_token_length": 5
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
          "type": "stop",
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
        "congressman_id": {
          "type": "long"
        },
        "congressman_type": {
          "analyzer": "text_searcher",
          "type": "text"
        },
        "iteration": {
          "type": "keyword"
        },
        "from": {
          "format": "yyyy-MM-dd HH:mm:ss",
          "type": "date"
        },
        "to": {
          "format": "yyyy-MM-dd HH:mm:ss",
          "type": "date"
        },
        "text": {
          "analyzer": "text_searcher",
          "type": "text"
        },
        "speech_id": {
          "type": "keyword"
        },
        "type": {
          "type": "keyword"
        },
        "category": {
          "type": "keyword"
        },
        "plenary_id": {
          "type": "long"
        },
        "word_count": {
          "type": "long"
        }
      }
    }
  },
  "index_patterns": "*_model_speech"
}



```



```sh
$ docker run -e MAX_WIDTH=11402470 -e MAX_HEIGHT=11402470 -e UPLOAD_MAX_SIZE=11982500 -e UPLOAD_ENABLED=True -e UPLOAD_PUT_ALLOWED=True -e DETECTORS="['thumbor.detectors.face_detector','thumbor.detectors.feature_detector']" -p 8000:8000 apsl/thumbor
```
