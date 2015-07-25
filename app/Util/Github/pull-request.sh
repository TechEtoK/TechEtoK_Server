#!/bin/sh
cd $1
hub pull-request -m "$2"
