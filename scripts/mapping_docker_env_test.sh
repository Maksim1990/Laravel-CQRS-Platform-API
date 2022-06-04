#!/usr/bin/env bash

### !!!!!!!!! ##########
#Prepare variables that has slash(/) as value
#VAR_DEV_GOOGLE_DRIVE_REFRESH_TOKEN=${DEV_GOOGLE_DRIVE_REFRESH_TOKEN//\//\\/};
#VAR_DEV_MONGO_PROTOCOL=${DEV_MONGO_PROTOCOL//\//\\/};
### !!!!!!!!! ##########

sed -e "s/\${ALGOLIA_APP_ID_TEST}/${ALGOLIA_APP_ID_TEST}/g;
        s/\${ALGOLIA_SECRET_TEST}/${ALGOLIA_SECRET_TEST}/g;"  ./deploy/testing/.env.testing > ./.env.dist

sed -i 's#\\/#\/#g;' ./.env.dist
