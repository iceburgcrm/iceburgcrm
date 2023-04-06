<p align="center"><a href="https://www.iceburg.ca" target="_blank"><img src="https://www.iceburg.ca/images/iceburg.png" width="400"></a></p>

# Iceburg CRM
### Laravel CRM

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

[Demo](http://demo.iceburg.ca)


Default usernames and passwords

- admin@iceburg.ca:admin
- user@iceburg.ca:user
- sales@iceburg.ca:sales
- accounting@iceburg.ca:accounting
- marketing@iceburg.ca:marketing


<br />

### Playground
- Connect any MySQL database and create a CRM from it.  
- Upload your MySQL dump file and create a CRM.  
- Create your own IceburgCRM without hosting
- Host your CRM

[IceburgCRM.com](https://www.iceburgcrm.com)



## About Iceburg CRM

Iceburg CRM is a metadata driven CRM that allows you to quickly prototype any CRM.  The default CRM is based on a typical business CRM but the flexibility of dynamic modules, fields, subpanels allows prototyping of any number of different tyes of CRMs.   



## Features

- [Unlimited Relationships between any number modules without common fields]
- [Metadata creations of  modules, fields, relationships, subpanels, datalets, seeding]
- [Ability to Import/Export in 6 different formats (XLSX, CSV, TSV, ODS, XLS, HTML] 
- [25 different input types, <b>Laravel</b> field validation, <b>Maska</b> field masking]
- [26 themes with light and dark themes available]
- [Module based Role permissions (read, write, import, export)]
- [Audit logs, Vue3 Charts, Convertable modules, Related Fields (related to another module)]


## Created With

Iceburg CRM is created with:
- [Vue 3](https://vuejs.org/) for the frontend
- [Laravel 10](https://laravel.com/) for the backend
- [Tailwinds](https://tailwindui.com/) with the DaisyUI plugin
- [Inertia](https://inertiajs.com/) for routing
- [heroicons](https://heroicons.com)
 

## Installation

If you do not have a server available visit [digitalocean](https://m.do.co/c/a52593511cc4) and get $200 dollars in free credit

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
```

Migrate
```php
php artisan migrate
php artisan db:seed
```

Deploy
```php
sudo chmod 775 storage -R
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

### Number of Relationships: 282

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



## Future Development

Roadmaps may include:

- [additional field types such as: image, video, files]
- [admin tools for creating, editing and generating new module, field, subpanel, etc types]
- [automation]
- [calendar tooling]
- [timeline, tying in different moduels through stages]
- [Automatic crm generation of any mysql variant database based on the schema]  


## Sponsorship / Support

If you are interested in becoming a sponsor and getting direct email support. please visit the Iceburg [Patreon page](https://patreon.com/iceburgcrm)



## Security Vulnerabilities

If you discover a security vulnerability within Iceburg CRM, please send an e-mail to [security@iceburg.ca](mailto:security@iceburg.ca). 


## License

The Iceburg CRM is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
