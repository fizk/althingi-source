# Loggjafarthing


## Configuration:
The API is configures with environment variables

| name                | default                          | Options                               | description                   |
|---------------------|----------------------------------|---------------------------------------|-------------------------------|
| DB_HOST             | localhost                        | <string>                              |                               |
| DB_PORT             | 3306                             | <number>                              |                               |
| DB_NAME             | althingi                         | <string>                              |                               |
| DB_USER             | root                             | <string>                              |                               |
| DB_PASSWORD         |                                  | <string>                              |                               |
| LOG_PATH            |                                  | <string>                              | Save logs to disk or php://stdout
| BROKER              | none                             | amqp / none                           |                               |
| BROKER_HOST         | localhost                        | <string>                              |                               |
| BROKER_PORT         | 5672                             | <string>                              |                               |
| BROKER_USER         | guest                            | <string>                              |                               |
| BROKER_PASSWORD     | guest                            | <string>                              |                               |
| BROKER_VHOST        | /                                | <string>                              |                               |
| QUEUE_FORCED        | false                            | true / false                          | if event should be sent to queue even though no update
| DOCUMENT_SERVER     |                                  | <string>                              |                               |
| DOCUMENT_DEFINITION |                                  | <string>                              |                               |
| INDEXER_STALL_TIME  | 150000                           | <int>                                 | How long the indexer sleeps between actions |

## Development
This repo comes with a **Dockerfile**. It is based off of a Apache/PHP image and will install all needed PHP extensions and
as well as all dependencies via [composer](https://getcomposer.org/). It can be configured at build-time for either
**production** or **development** via [--build-arg](https://docs.docker.com/engine/reference/commandline/build/#set-build-time-variables---build-arg).

| arg         | values       | description |
| ----------- | ------------ | ----------- |
| ENV         | production / development |  |


This repo also comes with **docker-compose** file that has some pre-configured environments.

host.docker.internal

#### test
The Docker Compose file has a `$ docker-compose run test` service. When run, it will spin up a MySQL instance.

## Debugging.
As stated previously, the `$ docker-compose up run` will install Xdebug and configure the _remote host_ to be the
host system. The remote port will be `9003`.


## Non Docker development
The system can be run without Docker, but that requires a lot of manual configuration. Have a look at the Dockerfile for complete
list of PHP extensions required.

Then run `$ composer i` to install dependencies.


## CommandLine


## Other useful commands
All GET requests are cached, if you need to flush the cache, it can be done via
```bash
$ docker exec -it cache-api redis-cli FLUSHALL
```

To make a dump of the database, run
```bash
docker exec CONTAINER /usr/bin/mysqldump -u root --password=example althingi > backup.sql
```
## About developing with PHPStorm.
PHPStorm is a good IDE than can run PHP inside a Docker Container and connect to its Xdebug server. A few steps need to be
completed for this to work.

Interpreter:
A Docker image needs to be available that gets spun up each time the IDE want to run some PHP code. Create this container by calling
```bash
$ docker build -t althingi-source-runtime --build-arg ENV=development .
```

Dependencies:
You also need running containers for MySQL and MongoDB
```bash
$ docker run --name dev_api_database -e MYSQL_ROOT_PASSWORD=example -d einarvalur/althingi-source-db
$ docker run --name dev_api_store -d mongo:4.2.0-bionic
```
just make sure you run the containers from the project directory, so they end up in the same network.

Now it's time to link this all together

Go into Preferences > Languages & Frameworks > PHP
under CLI Interpreter, click the ... where you are taken to a prompt to create a new Interpreter. Click the + sign on the left
hand site and select From Docker, Vagrant Remote... Select Docker from the radio buttons and find the **dev_api:latest** docker image
from the dropdown where it says Image name.

Now you should have an PHP interpreter.

From the original Preferences > Languages & Frameworks > PHP dialog, click the folder icon next to _Docker container_, here you can
configure the docker container.

The volume binding should be
* /var/www/config	| <your host path to>/config
* /var/www/module	| <your host path to>/module
* /var/www/public	| <your host path to>/public
* /var/www/phpunit.xml	| <your host path to>/phpunit.xml.dist

Links should be
* dev_api_store	    | store
* dev_api_database	| database

The env variables should be
* WITH_XDEBUG             | true
* APPLICATION_ENVIRONMENT | development
* DB_HOST                 | dev_api_database
* DB_PORT                 | 3306
* DB_NAME                 | althingi
* DB_USER                 | root
* DB_PASSWORD             | example
* CACHE_TYPE              | none
* SEARCH                  | none
* LOG_PATH                | none
* QUEUE                   | none
* QUEUE_FORCED            | false
* STORAGE_HOST            | dev_api_store
* STORAGE_DB              | althingi

Lastly under Preferences > Languages & Frameworks > PHP > Test frameworks, make sure that the **althingi-source-runtime:latest** images
has been selected
and that _Use Composer autoloader_ is also selected, the path should be `/opt/project/vendor/autoload.php` and the default
configuration file is `/opt/project/phpunit.xml.dist`



```

root
├── Assembly
│   ├── Plenary
│   │   ├── PlenaryAgenda
│   │   └──
│   └── Issue
│       ├── Speech
│       ├── Vote
│       │   └── VoteItem
│       ├── Document
│       └── CongressmanDocument
├── Cabinet
├── Category
├── Super
├── Committee
│   ├── CommitteeMeeting
│   ├── CommitteeMeetingAgenda
│   ├── CommitteeSitting
│   └── CommitteeDocument
├── Congressman
│   ├── MinisterSitting
│   └── Session
├── Constituency
├── Election
├── Inflation
├── IssueCategory
├── Ministry
├── Party
├── President
└── (IssueLink)


```
