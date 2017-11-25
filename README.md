#Althingi

You will need PHP7 with x-debug.
You will need MySQL (>=5.7.15)
...and you will need composer. You can install composer how ever you want. I have installed it as a global bin, so every time you see `composer` in teh description below you will have to call it `php /path/to/composer.phar` if you do not have it in you $PATH


then get ZF2 skeleton app
composer create-project --stability="dev" zendframework/skeleton-application Althingi

now `cd` into the project's root directory
cd ./Althingi

then get the module
git clone https://github.com/fizk/Loggjafarthing.git ./module/Althingi


then get the depenencies
composer install -d ./module/Althingi 


remove unwanted modules
rm -rf ./module/Application

now, register the new module

./config/application.config.php

'modules' => array(
        'Althingi',
    ),

now point to the dependencies
./init_autoload.php

```php
if (file_exists('module/Althingi/vendor/autoload.php')) {
    $loader = include 'module/Althingi/vendor/autoload.php';
}

...

```

Now to configure the Database... go to ./config/autoload/global.php and set it like this:
```php
return [
    'db' => [
        'dns' => 'mysql:host=127.0.0.1;dbname=althingi',
        'user' => 'root',
        'password' => '',
    ]
];
```

...or what ever the name of your host and database it...

then shell into it and create the database
```sql
create database althingi;
```
you can get the latest snapshot of the db here http://107.170.87.126/dump.sql
and then you just have to pipe it in
```sh
$ mysql -u root althingi < ~/Desktop/dump.sql
```

if you are having problems with the GROUP BY clauses, add this to /etc/mysql/my.cnf
```
[mysqld]
sql_mode = STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION
```
and restart

now you can run the server to make sure everythinbg is working
php -S 0.0.0.0:3030 -t public

if it says something like this:

```
Parse error: parse error in ~/workspace/Althingi/module/Althingi/view/althingi/index/index.phtml on line 1
```

you have to go into that file and remove the XML decleration

```
<?xml version="1.0" encoding="UTF-8"?>
```


ZF2 application. Place in your module directory under then name **Althingi**. Remember to register in `config/application.config.php`

I usually override `init_autoloader.php` so I can use the `vendor` directory inside the module and not the one in the skeleton application.
Replace content in  `init_autoloader.php` with

```php

// Composer autoloading
if (file_exists('module/Althingi/vendor/autoload.php')) {
    $loader = include 'module/Althingi/vendor/autoload.php';
}

if (class_exists('Zend\Loader\AutoloaderFactory')) {
    return;
}

$zf2Path = false;

if (is_dir('vendor/ZF2/library')) {
    $zf2Path = 'vendor/ZF2/library';
} elseif (getenv('ZF2_PATH')) {      // Support for ZF2_PATH environment variable or git submodule
    $zf2Path = getenv('ZF2_PATH');
} elseif (get_cfg_var('zf2_path')) { // Support for zf2_path directive value
    $zf2Path = get_cfg_var('zf2_path');
}

if ($zf2Path) {
    if (isset($loader)) {
        $loader->add('Zend', $zf2Path);
        $loader->add('ZendXml', $zf2Path);
    } else {
        include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        Zend\Loader\AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true
            )
        ));
    }
}

if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
}

```

This makes PHPUnits tests easier to manage.





docker run -p 9200:9200 -p 9300:9300 -v "$PWD/data":/usr/share/elasticsearch/data -e "discovery.type=single-node" docker.elastic.co/elasticsearch/elasticsearch:5.6.3


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


```
select D.`assembly_id`, 
		D.`issue_id`, 
        null as `committee_id`, 
        null as `speech_id`, 
        D.`document_id`,
        null as `committee_name`,
        D.`date`, 
        D.`type` as `title`,
        'document' as `type`,
        true as `completed`
	from `Document` D 
    where D.`assembly_id` = 146 and D.`issue_id` = 258
	union
select B.`assembly_id`, 
		B.`issue_id`, 
        null as `committee_id`, 
        B.`speech_id`, 
        null as `document_id`, 
        null as `committee_name`,
        B.`date`, 
        B.`type` as `title`, 
        'speech' as `type`,
        true as `completed`
        from (
			select S.`assembly_id`, 
					S.`issue_id`, 
                    null as `committee_id`, 
                    S.speech_id, null, 
                    S.`from` as `date`, 
                    CONCAT(S.`iteration`, '. umræða') as `type`, 
                    S.`iteration` 
			from `Speech` S 
			where assembly_id = 146 and issue_id = 258
			order by S.`from`
    ) as B
    group by B.`iteration`
    union
select CMA.assembly_id, 
		CMA.issue_id, 
        C.committee_id, 
        null as `speech_id`, 
        null as `document_id`, 
        C.name as `committee_name`,
        CM.`from`, 
        'í nefnd' as `title`, 
        'committee' as `type`,
        true as `completed`
    from `CommitteeMeetingAgenda` CMA
	join `CommitteeMeeting` CM on (CM.committee_meeting_id = CMA.committee_meeting_id and CM.`from` is not null)
	join `Committee` C on (CM.committee_id = C.committee_id)
	where CMA.assembly_id = 146 and CMA.issue_id = 258
order by `date`;
```
