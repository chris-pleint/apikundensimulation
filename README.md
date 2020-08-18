## Example Implementation of the InterNetX PHP Domainrobot SDK

### Documentation

* https://internetx.github.io/php-domainrobot-sdk/
* https://en.help.internetx.com/display/APIXMLEN/JSON+Technical+Documentation

### Requirements to run the Scripts

* The project could also be executed on an Windows Platform, but an Linux / Unix Enviroment is preferred (e.g. Ubuntu / macOS)
* PHP Server who meets the Laravel [Requirements](https://laravel.com/docs/master/installation)
* Additionally the PHP Module **php-curl** needs to be installed
* The PHP Dependency Manager [composer](https://getcomposer.org/) installed
* GIT installed on the System
* **Optional, only necessary for UI** The Javascript Package Manager [npm](https://www.npmjs.com/) installed

### How to start the Project

**Open the Terminal to execute the following Commands in the Commandline**

Clone the Source Code from the Repository
> git clone https://github.com/chris-pleint/apikundensimulation sdk-implementation

**Change into the Directory of the cloned Source Code**
> cd sdk-implementation

**Execute the command below, the needed PHP Libraries for the Project will be installed** 
> composer install

**Afterwards it is necessary to create an .env File where the Authentication Credentials will be stored in the Root Directory of the Project. An File called .env.example is also located there which will be used as a base to create the .env File**
> cp .env.example .env

**Open the .env File with your text editor of choice e.g.**
> nano .env

**In the .env are some Placeholders that must be exchanged with the AutoDNS API Authentication Credentials**

> ###DOMAINROBOT_URL###
> 
> For the Demo System exchange the Placeholder with the following URL
>
> https://api.demo.autodns.com/v1
>
> or for the Live System exchange it with the following URL
>
> https://api.autodns.com/v1

> ###DOMAINROBOT_USER###
>
> Exchange this Placeholder with your AutoDNS API User

> ###DOMAINROBOT_PASSWORD###
>
> Exchange this Placeholder with your AutoDNS API Password

> ###DOMAINROBOT_CONTEXT###
>
> Exchange this Placeholder with the Context of your AutoDNS API User

**NOTE: To use SSL Certificate related tasks like creating SSL Contacts and SSL Certificates you need the InterNetX SSL Manager and an according API User**

> ###DOMAINROBOT_SSL_USER###
>
> Exchange this Placeholder with your SSL Manager API User

> ###DOMAINROBOT_SSL_PASSWORD###
>
> Exchange this Placeholder with your SSL Manager API Password

> ###DOMAINROBOT_SSL_CONTEXT###
>
> Exchange this Placeholder with the Context of your SSL Manager API User

**In the Root Directory of the Laravel Project execute the following Command to start Serving the Program** 

> php artisan serve --port=8181

**With an REST API Client (e.g. https://insomnia.rest/) you can now query different Tasks / Routes of the Example Implementation of the InterNetX PHP Domainrobot SDK**

> GET /api/user/{username}/{context}

**With the following Command you get an Overview of all available Routes**

> php artisan route:list