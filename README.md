# Loggjafarthing
This is the single-source-of-truth system for `althingi.is` data. It is a PHP 8 API with a MySQL database.

## Environment variables:

| name                | default                          | Options                               | description                   |
|---------------------|----------------------------------|---------------------------------------|-------------------------------|
| DB_HOST             | localhost                        | <string>                              |                               |
| DB_PORT             | 3306                             | <number>                              |                               |
| DB_NAME             | althingi                         | <string>                              |                               |
| DB_USER             | root                             | <string>                              |                               |
| DB_PASSWORD         |                                  | <string>                              |                               |
| LOG_PATH            |                                  | <string>                              | Save logs to disk or php://stdout
| BROKER              | none                             | kafka / none                          |                               |
| BROKER_HOST         | localhost                        | <string>                              |                               |
| QUEUE_FORCED        | false                            | true / false                          | if event should be sent to queue even though no update
| DOCUMENT_SERVER     |                                  | <string>                              |                               |
| DOCUMENT_DEFINITION |                                  | <string>                              |                               |
| INDEXER_STALL_TIME  | 150000                           | <int>                                 | How long the indexer sleeps between actions |

## Development
This repo comes with a **Dockerfile**. It is based off of a Apache/PHP image and will install all needed PHP extensions and
as well as all dependencies via [composer](https://getcomposer.org/). It can be configured at build-time for either
**production** or **development** via [--build-arg](https://docs.docker.com/engine/reference/commandline/build/#set-build-time-variables---build-arg).

| name     | default     |  options                 | description                            |
| -------- | ----------- | ------------------------ | -------------------------------------- |
| ENV      | development | production / development | xDebug is not included in _production_ |


This repo contains a `docker-compose.yml` file that defines services for development as well as dependencies.

Use `run` for devlopment, it will mound the `src` directory (among others). It will install and attach a MySQL database as well.

```sh
docker compose up run
```

Use `test` to run all PHPUnit tests and linting. It will install and attach a MySQL database. This service is used in the CI/CD pipeline.

```sh
docker compose run test
```


## Debugging.
As stated previously, the `$ docker-compose up run` will install Xdebug and configure the _remote host_ to be the
host system. The remote port will be `9003`.

## Commandline
This system can be forced to brodcast event the same way as it broadcasts events when it creates or updates entries.

To get a list of all available commands, run:
```sh
docker compose run --rm run index console
```

To index all assemblies, for example, run
```sh
docker compose run --rm run index console:assembly
```


## Other useful commands

To make a dump of the database, run
```sh
docker exec CONTAINER /usr/bin/mysqldump -u root --password=example althingi > path/to/backup.sql
```

To import dump into the database
```sh
docker exec -i CONTAINER mysql -u root --password=example althingi < path/to/backup.sql
```


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


```sh
docker build -t x-althingi-source --build-arg ENV=development .
docker build -t x-althingi-aggregator --build-arg ENV=development .


docker run --name x-althingi-database --network althingi-network -p 4406:3306 -e MYSQL_ROOT_PASSWORD=example -e MYSQL_DATABASE=althingi einarvalur/althingi-source-db:97bec3d2041832e170f4185167d515f0f0bcff4c
docker run --name x-althingi-source -e DB_HOST=x-althingi-database -e DB_NAME=althingi -e DB_USER=root -e DB_PASSWORD=example -e BROKER=none --network althingi-network x-althingi-source
docker run --name x-althingi-aggregator --network althingi-network --rm -e CONSUMER_CACHE_TYPE=none -e PROVIDER_CACHE_TYPE=none -e AGGREGATOR_CONSUMER_HOST=x-althingi-source  -e AGGREGATOR_CONSUMER_PORT=80 x-althingi-aggregator assembly 154
```
