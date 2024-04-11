<p align="center"><a href="https://www.iceburg.ca" target="_blank"><img src="https://www.iceburg.ca/images/iceburg.png" width="400"></a></p>

# Iceburg CRM
### A Laravel CRM Builder
#### With optional AI Assist, AI Builder


Screenshots:
<p>
<a href="https://www.iceburg.ca/images/screenshot1.jpg" target="_blank">
<img src="https://www.iceburg.ca/images/screenshot1.jpg" width="50" />
</a>
<a href="https://www.iceburg.ca/images/screenshot2.jpg" target="_blank">
<img src="https://www.iceburg.ca/images/screenshot2.jpg" width="50" />
</a>
<a href="https://www.iceburg.ca/images/screenshot3.jpg" target="_blank">
<img src="https://www.iceburg.ca/images/screenshot3.jpg" width="50" />
</a>
<a href="https://www.iceburg.ca/images/screenshot4.jpg" target="_blank">
<img src="https://www.iceburg.ca/images/screenshot4.jpg" width="50" />
</a>
<a href="https://www.iceburg.ca/images/screenshot5.jpg" target="_blank">
<img src="https://www.iceburg.ca/images/screenshot5.jpg" width="50" />
</a>
</p>

[Project Home Page - iceburg.ca](https://www.iceburg.ca)

[Demo](https://demo.iceburg.ca)

Default usernames and passwords

- admin@iceburg.ca:admin
- user@iceburg.ca:user
- sales@iceburg.ca:sales
- accounting@iceburg.ca:accounting
- marketing@iceburg.ca:marketing

### Describe your CRM and let's AI create it.

## About Iceburg CRM

Iceburg CRM is a metadata driven CRM with AI abilities that allows you to quickly prototype any CRM.  The default CRM is based on a typical business CRM but the flexibility of dynamic modules, fields, subpanels allows prototyping of any number of different tyes of CRMs.

## Features

- [Unlimited Relationships between any number modules without common fields]
- [Metadata creations of  modules, fields, relationships, subpanels, datalets, seeding]
- [Ability to Import/Export in 6 different formats (XLSX, CSV, TSV, ODS, XLS, HTML]
- [25 different input types, <b>Laravel</b> field validation, <b>Maska</b> field masking]
- [26 themes with light and dark themes available]
- [Module based Role permissions (read, write, import, export)]
- [Calendar, Audit logs, Vue3 Charts, Convertable modules, Related Fields (related to another module)]


## Created With

Iceburg CRM is created with:
- [Vue 3](https://vuejs.org/) for the frontend
- [Laravel 10](https://laravel.com/) for the backend
- [Tailwinds](https://tailwindui.com/) with the DaisyUI plugin
- [Inertia](https://inertiajs.com/) for routing
- [heroicons](https://heroicons.com)


## Installation
### Quick Install
```php
composer create-project iceburgcrm/iceburgcrm iceburgcrm

// Default
php artisan iceburg:seed

// Convert Existing DB to CRM
php artisan iceburg:seed --type=adminpanel 

// Use AI
php artisan iceburg:seed --type=ai --prompt="Create a stamp collecting crm"
```


### Ways to Install
- <b>Default</b> - Install the default Classic IceburgCRM:  55 Modules, 282 Fields, 43 Relationships,  24 Subpanels, 5 Datalets
```php
php artisan iceburg:seed
```
- <b>AdminPanel</b> - Point to an existing Database and turn it into a CRM.  Type is requires but additional parameters are optional.  If not supplied will use existing connection details.
```php
php artisan iceburg:seed --type=adminpanel --connection_host=123.123.123.123 --connection_port=3306 --connection_database=databasename --connection_username=dbuser --connection_password=dbpassword --connection_charset=utf8mb4 --connection_collation=utf8mb4_unicode_ci
```
Example of a live wordpress database converted to a CRM.
[Wordpress CRM](https://wordpress.iceburg.ca)  
[Wordpress Website](https://wordpresssite.iceburg.ca)

- <b>Core</b> - Install only the core files.  This will create a blank CRM template.
```php
php artisan iceburg:seed --type=core
```
- <b>Custom</b> - Add your own modules, field, relationships, subpanels and generate it.
```php
php artisan iceburg:seed --type=custom
```
- <b>AI</b> - Describe the CRM you want and let AI create it.  Including the logo parameter will create an unique image for your login page.  ChatGPT 3.5 is used as the default.
  Dalle-3 is used for image generation.  Cost: 4 cents per crm with logo or a 1 penny without the logo.
```php
php artisan iceburg:seed --type=ai --prompt="Create a stamp collecting crm" logo="yes"
```
Each AI generation is different.  Based on the prompt above here are three CRM's created:
[Stamp Collectors CRM 1](https://postagestamps.iceburg.ca/)
[Stamp Collectors CRM 2](https://postagestamps2.iceburg.ca/)
[Stamp Collectors CRM 3](https://postagestamps3.iceburg.ca/)



<i>Note:  Connection parameters, can be used with different types of installation.</i>



### Full Installation


If you do not have a server available visit [digitalocean](https://www.digitalocean.com/?refcode=a52593511cc4) and get $200 dollars in free credit

If not installed, please install [composer](https://getcomposer.org/download/)

If not installed, please install [npm](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm/)

```php
composer create-project iceburgcrm/iceburgcrm iceburgcrm

or 

git clone git@github.com:iceburgcrm/iceburgcrm.git

cd iceburgcrm
```

Edit your database environment variables
```php
vim .env

DB_HOST=
DB_PORT=
DB_USERNAME=
DB_PASSWORD=
DB_DATABASE=

```

Open permissions on the storage and
```php
sudo chown -R www-data:www-data /path/to/your/project/public
sudo chown -R www-data:www-data /path/to/your/project/storage


Deploy
```php
sudo chmod 775 storage -R
```

AI Assist (Optional)
```
// Add your OPENAI KEY AND ORG ID TO your environment file to enable AI Assist
// This will enable an AI ASSIST button in add or edit modules 
// that will allow you to fill in any field with AI Assisted data


OPENAI_API_KEY=
OPENAI_ORGANIZATION=
```

## Default Iceburg CRM


### Number of Modules: 55


### Primary Modules: 14
- Accounts
- Contacts
- Contracts
- Leads
- Opportunities
- Lineitems
- Products
- Campaigns
- Cases
- Documents
- Notes
- Projects
- Groups
- Quotes

### Number of Fields: 282

### Number of Relationships: 43


### Number of Subpanels: 24


### 5 Datalets
- [pie chart] Total Sales
- [line graph] New Leads / Contacts / Accounts over 7 days
- [pie chart] New Opportunities / Contracts / Quotes
- [bar graph] Meeting (Today, 7 Days, 30 Days)
- [pie chart] Orders this month


### Admin
- Settings
- Permissions
- Modules, Fields, Subpanels, Users, Datalet editing


### Roles
- Accounting
- Admin
- HR
- Marketing
- Sales
- Support
- User

## IceburgCRM.com
### Don't want to self install?  Create CRMs Online for free
- Describe your CRM and build it with AI
- Select from our premade CRM templates
- Make any Database into a CRM

[IceburgCRM.com](https://www.iceburgcrm.com)

## Templates

### Classic CRM
![Classic CRM Icon](https://www.iceburg.ca/images/classic.jpg?rand=12456)  
**Classic CRM. Accounts, Contacts, Contracts, LineItems, etc.**  
[Preview](https://classic.iceburg.ca)

### Rare Books CRM
![Rare Books CRM Icon](https://www.iceburg.ca/images/rarebooks.jpg?rand=12456)  
**A platform for sneaker enthusiasts to catalog their collections, track market values, manage trades or sales, and connect with other collectors.**  
[Preview](https://rarebooks.iceburg.ca)

### Wine Connoisseurs CRM
![Wine CRM Icon](https://www.iceburg.ca/images/wine.jpg?rand=12456)  
**For wine enthusiasts and sellers, offering cellar management, tasting notes, vintage tracking, and a community feature for sharing recommendations and organizing tastings.**  
[Preview](https://wine.iceburg.ca)

### Fitness Studio CRM
![Fitness CRM Icon](https://www.iceburg.ca/images/fitness.jpg?rand=12356)  
**Tailored for small to medium fitness studios, featuring membership management, class scheduling, fitness progress tracking for members, and integration with wearable tech for health data.**  
[Preview](https://fitness.iceburg.ca)

### Professional Networking CRM
![Networking CRM Icon](https://www.iceburg.ca/images/networking.jpg?rand=12456)  
**A niche CRM for professional networking organizations, offering event planning, member engagement tracking, mentorship program management, and job boards.**  
[Preview](https://networking.iceburg.ca)

### Crafting Supplies CRM
![Crafting Supplies CRM Icon](https://www.iceburg.ca/images/crafting.jpg?rand=13456)  
**For retailers and enthusiasts of crafting, offering inventory management, project tracking, supplier databases, and community features for sharing project ideas and tutorials.**  
[Preview](https://crafting.iceburg.ca)

### Gourmet Coffee Enthusiasts CRM
![Gourmet Coffee CRM Icon](https://www.iceburg.ca/images/coffee.jpg)  
**A platform for coffee lovers to track their favorite beans, roasts, brewing methods, and café experiences, including a marketplace for specialty beans and equipment.**  
[Preview](https://coffee.iceburg.ca)

### BeeKeeping CRM
![BeeKeeping CRM Icon](https://www.iceburg.ca/images/beekeeping.jpg?rand=12356)  
**For beekeepers to track hive health, manage honey production records, schedule maintenance, and engage with local and online beekeeping communities.**  
[Preview](https://beekeeping.iceburg.ca)

### Wordpress CRM
![Wordpress CRM Icon](https://www.iceburg.ca/images/wordpress.jpg?rand=12346)  
**This is a premade instance of a wordpress database with iceburgcrm. Once created, download and point your wordpress files.**  Changing the data in the CRM will change the wordpress website.
[CRM Preview](https://wordpress.iceburg.ca)  
[Wordpress Website](https://wordpresssite.iceburg.ca)

## Star History

[![Star History Chart](https://api.star-history.com/svg?repos=iceburgcrm/iceburgcrm&type=Date)](https://star-history.com/#iceburgcrm/iceburgcrm&Date)


## Security Vulnerabilities

If you discover a security vulnerability within Iceburg CRM, please send an e-mail to [security@iceburg.ca](mailto:security@iceburg.ca).


## License

The Iceburg CRM is open-sourced software licensed under the [AGPL](https://www.gnu.org/licenses/agpl-3.0.en.html)

