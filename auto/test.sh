#!/usr/bin/env bash

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#
#   Run Docker for tests
#
#   Runs the docker-compose script for tests, exposes ports and
#   removes after exiting.
#
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

docker-compose run --service-ports --rm tests
