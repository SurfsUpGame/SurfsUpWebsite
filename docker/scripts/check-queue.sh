#!/bin/bash

# Check if queue worker is running
if supervisorctl status laravel-worker | grep -q "RUNNING"; then
    echo "Queue worker is running"
    exit 0
else
    echo "Queue worker is NOT running"
    supervisorctl status laravel-worker
    exit 1
fi