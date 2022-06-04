#!/usr/bin/env bash

# sudo sh -c "echo 'precedence ::ffff:0:0/96 100' >> /etc/gai.conf"
# In case of
# The "https://getcomposer.org/version" file could not be downloaded: failed to open stream: Operation timed out
sh -c "echo 'precedence ::ffff:0:0/96 100' >> /etc/gai.conf"
