## Example Implementation of the InterNetX PHP Domainrobot SDK

### Requirements to run the Scripts

* PHP Server who meets the Laravel [Requirements](https://laravel.com/docs/master/installation)
* Additionally the PHP Module **php-curl** needs to be installed
* The PHP Dependency Manager [composer](https://getcomposer.org/) installed
* The Javascript Package Manager [npm](https://www.npmjs.com/) installed

### How to start the Project

* git clone https://github.com/chris-pleint/apikundensimulation sdk-implementation
* cd sdk-implementation
* composer install
* npm install --no-audit
* cp .env.example .env
* nano .env
* Edit in the file the constants nested with three hashes e.g. ###DOMAINROBOT_USER### accordingly to your AutoDNS API account. **Note** To create SSL Contacts or SSL Certificates you need the Product SSL Manager.
* php artisan serve --port=8181