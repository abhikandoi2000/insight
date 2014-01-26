# Insight

Analytics for Git Repositories and Redmine

## Development

* Clone the repository
* Install dependencies (`php composer.phar install`)
* Use the database schema(`schema/insight.sql`) to create your database
* Copy over `config/sample.config.ini` to `config.ini` and update the config file.
* Copy over `config/sample.config.cfg` to `config.cfg` and update the config file.
* Create a new folder `cache` in the root directory of *insight* and change its group to `www-data` (`sudo chown <your_username>:www-data cache`)
* Create a vhost entry by using the file `insight.localhost` as template.

## Data Crunching

* Change directory to *scripts* (`cd scripts`)
* Make the script executable (`chmod +x commits.sh`)
* Run the script on your repositories (`./commits.sh /path/to/repo`)

## Author

Abhishek Kandoi <abhikandoi2000@gmail.com>
