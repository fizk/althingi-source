#!/usr/bin/env bash

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#
#   Run Docker for server
#
#   Runs the docker-compose script for server, exposes ports and
#   removes after exiting.
#
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

docker-compose run --service-ports --rm server
