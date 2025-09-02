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


### Default usernames and passwords

Username | Password
--- | ---
admin@iceburg.ca | admin
user@iceburg.ca | user
sales@iceburg.ca | sales
accounting@iceburg.ca | accounting
marketing@iceburg.ca | marketing

### Describe your CRM and let's AI create it.

## About Iceburg CRM

Iceburg CRM is a metadata driven CRM with AI abilities that allows you to quickly prototype any CRM.  The default CRM is based on a typical business CRM but the flexibility of dynamic modules, fields, subpanels allows prototyping of any number of different tyes of CRMs.

## Features

- [Metadata creations of  modules, fields, relationships, subpanels, datalets, seeding]
- [Ability to Import/Export in 6 different formats (XLSX, CSV, TSV, ODS, XLS, HTML]
- [25 different input types, <b>Laravel</b> field validation, <b>Maska</b> field masking]
- [26 themes with light and dark themes available]
- [Module based Role permissions (read, write, import, export)]
- [Calendar, Audit logs, Vue3 Charts, Convertable modules, Related Fields (related to another module)]
- [Field Level Relationships, Module Level Relationships 2 way, 3 way, 4 way, ...]
- [Build-in API, Workflow]
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
php artisan iceburg:create
```
- <b>AdminPanel</b> - Point to an existing Database and turn it into a CRM.  Type is requires but additional parameters are optional.  If not supplied will use existing connection details.
```php
php artisan iceburg:create --type=adminpanel --connection_host=123.123.123.123 --connection_port=3306 --connection_database=databasename --connection_username=dbuser --connection_password=dbpassword --connection_charset=utf8mb4 --connection_collation=utf8mb4_unicode_ci
```
Example of a live wordpress database converted to a CRM.
[Wordpress CRM](https://wordpress.iceburg.ca)  
[Wordpress Website](https://wordpresssite.iceburg.ca)

- <b>Core</b> - Install only the core files.  This will create a blank CRM template.
```php
php artisan iceburg:create --type=core
```
- <b>Custom</b> - Add your own modules, field, relationships, subpanels and generate it.
```php
php artisan iceburg:create --type=custom
```
- <b>AI</b> - Describe the CRM you want and let AI create it.  Including the logo parameter will create an unique image for your login page.  ChatGPT 3.5 is used as the default.
  Dalle-3 is used for image generation.  Cost: 4 cents per crm with logo or a 1 penny without the logo.
```php
php artisan iceburg:create --type=ai --prompt="Create a stamp collecting crm" logo="yes"
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

## AI Assist (Optional)
AI Assist will try to determine values for your current module by using the name, description of the module and the field.   It suggests for blank fields and provides a confirmation preview before you commit to saving.
```
// Add your OPENAI KEY AND ORG ID TO your environment file to enable AI Assist
// This will enable an AI ASSIST button in add or edit modules 
// that will allow you to fill in any field with AI Assisted data


OPENAI_API_KEY=
OPENAI_ORGANIZATION=
```

## API
```
Get Token
curl -X POST localhost:8000/api/login -H "Content-Type: application/json" -d '{"email": "admin@iceburg.ca", "password": "admin"}

Sample Return:
{"token":"2|16ajbNyxwDhBUvupqLCSQSJyFV5d0IQao7Bwm2ch3b6e331b"}


# 1. Get All CRM Modules
curl -X GET http://localhost:8000/api/crm -H "Authorization: Bearer YOUR_TOKEN_HERE"

Sample Return:
[{"id":1,"name":"ice_users","label":"Users","description":"Users","status":1,"faker_seed":0,"create_table":0,"view_order":0,"admin":0,"parent_id":0,"primary":1,"primary_field":"id","icon":"UserPlusIcon","module_group_id":6,"created_at":null,"updated_at":null},{"id":2,"name":"ice_roles","label":"Roles","description":"Roles","status":1,"faker_seed":0,"create_table":0,"view_order":1,"admin":0,"parent_id":0,"primary":1,"primary_field":"id","icon":"CircleStackIcon","module_group_id":6,"created_at":null,"updated_at":null},


# 2. Search CRM Data
curl -X GET http://localhost:8000/api/crm/search \
-H "Authorization: Bearer YOUR_TOKEN_HERE" \
-H "Content-Type: application/json" \
-d '{"search_key": "value"}'

Use the format of the search which can be seen on the network tab after you've completed a search in the page called search_data.

This will take the users module for a name 'admin' and save the data to a text file.
curl -X GET http://localhost:8000/api/crm/search -H "Authorization: Bearer 4|DPGVyKHEXoZiWT4kBQYdzC0uzw9EpcR0JeDhBUx6d2744c5c" -H "Content-Type: application/json" -d '{
    "1__name": "admin",
    "1__email": "undefined",
    "1__role_id": "undefined",
    "page": 1,
    "per_page": 10,
    "search_order": "asc",
    "order_by": "",
    "search_type": "module",
    "module_id": 1,
    "text_search_type": "fuzzy"
}' > data.txt

Sample output
{"current_page":1,"data":[{"ice_users__name":"Admin","ice_users__profile_pic":"data:image\/jpg;base64,\/9j...

# 3. Get a Specific CRM Module
curl -X GET http://localhost:8000/api/crm/1 \
-H "Authorization: Bearer YOUR_TOKEN_HERE"

Sample output
{"id":1,"name":"ice_users","label":"Users","description":"Users","status":1,"faker_seed":0,"create_table":0,"view_order":0,"admin":0,"parent_id":0,"primary":1,"primary_field":"id","icon":"UserPlusIcon","module_group_id":6,"created_at":null,"updated_at":null}

# 4. Update or Add a CRM Record
curl -X PUT http://localhost:8000/api/crm/1 \
-H "Authorization: Bearer YOUR_TOKEN_HERE" \
-H "Content-Type: application/json" \
-d '{"field1": "new_value1", "field2": "new_value2"}'


To update include the record id
curl -X PUT http://localhost:8000/api/crm/9 \
-H "Authorization: Bearer 2|qXONV6OYboLruwcdBP3mL55XsEftujd5vogQ5EI9ebb51884" \
-H "Content-Type: application/json" \
-d '{"record_id": 1, "9__name": "Jacobs Ltd 22test"}'

Output
ID record saved

To Add a new record do not include a record id
curl -X PUT http://localhost:8000/api/crm/9 -H "Authorization: Bearer 2|qXONV6OYboLruwcdBP3mL55XsEftujd5vogQ5EI9ebb51884" -H "Content-Type: application/json" -d '{"9__name": "Jacobs Ltd test2"}'

Output
ID of new record

# 5. Delete a Record in a CRM Module
curl -X DELETE http://localhost:8000/api/crm/1/["module" or "relationship"] \
-H "Authorization: Bearer YOUR_TOKEN_HERE" \
-H "Content-Type: application/json" \
-d '{"record_ids": [1, 2, 3]}'

curl -X DELETE http://localhost:8000/api/crm/2/module -H "Authorization: Bearer 2|16ajbNyxwDhBUvupqLCSQSJyFV5d0IQao7Bwm2ch3b6e331b" -H "Content-Type: application/json" -d '{"record_ids": [1, 2, 3]}' > a.txt



```


## Relationships

### Field Level Relationships

IceburgCRM supports field level relationships.   A field level relationship could be a state field in an accounts module.  When added or searching you would select the value from the dropdown.

In the database, the ID of the related module and field is stored.  When you export module or relationship data IceburgCRM substitutes the ids for the value in the related module.  When you import the reverse process happens.
For example:   If you had a related State field and had the Alabama saved.  In the database you would see 1 in the accounts tables record.  In the ice_fields you would see that this field is a related type pointing to module with the id 17 (States module id is 17) and field id 2 (2nd field is the value field). 
When you export by default you will be Alabama and when you import Alabama is replaced with 1 again.


### Module Level Relationships

IceburgCRM supports Unlimited relationships between modules.

A typical CRM has a two way relationship between two modules.  For example Accounts have many contacts and contacts can have many accounts.

IceburgCRM allows you to create relationships between 2, 3, 4 or more modules.

Why would you need a relationship between more than 2 modules?
Let's say you wanted to have a subpanel that stored when a contract was signed, who signed, what location.  A typical CRM will duplicate data or create new fields (that sit empty for all of the other records) or find some awkward method to force the data into a two way relationship.

IceburgCRM allows you to relate a Contract module, Account module, Contact module, City module, State module, Country module without having to add a related field or worse a free text field.

You can have multiple relationships and use them to create multiple subpanels for modules.  For example you may want an Account & Contact subpanel but you may also want an Account Contact Opportunity subpanel.

You would use a relationship for a subpanel and then select the fields you want from any module.


A contract module could have a relationship to an account, a contact, a location


### Themes

Out of the box these themes are available.  For more information or to try different themes visit (https://daisyui.com/docs/themes/)

```
themes: [
"light",
"dark",
"cupcake",
"bumblebee",
"emerald",
"corporate",
"synthwave",
"retro",
"cyberpunk",
"valentine",
"halloween",
"garden",
"forest",
"aqua",
"lofi",
"pastel",
"fantasy",
"wireframe",
"black",
"luxury",
"dracula",
"cmyk",
"autumn",
"business",
"acid",
"lemonade",
"night",
"coffee",
"winter",
"dim",
"nord",
"sunset"],
```

## Field Types

You can customize and add your own field types with their own special properties.  Out of the box IceburgCRM has these:
```
tel
currency
checkbox
password
image
video
audio
file
number
email
url
zip
date
related
address
textarea
color
radio
text
```
_Try the Color field type.  It will present a color picker._

## Calender

Fully customizable including colors and event hooks.  You can select, day, week or month for the calendar view.  The meetings module holds the data that powers the calendar.  Allows multiple appointments at the same time.  Click on an event to generate a popup.

## Datalets

Datalets are frontpage widgets.  They can take the form of a graph, table, or video or anything.  In the default IceburgCRM we provide a number of different graphs.  In the AI generated CRMs we provide information base datalets.

Adding your own is easy.  Add the datalet to the datalets table, add your new data function in the backend and create your vue template. 

## Workflow - Confortable Modules
You can setup a workflow between modules.  A stage box will appear in the module detail screen showing you at what stage this record is in the workflow.  It also allows you to select other records in the workflow to examine what happened previously or later in the chain. 

There a 5 module workflow setup in the default IceburgCRM.  You can modify the workflow in the seeding files or in the workflow table.

Change these records in the ModuleSeeker file:
```
        ModuleConvertable::insert([
        'primary_module_id' => Module::where('name', 'leads')->first()->id,
        'module_id' => Module::where('name', 'contacts')->first()->id,
        'level' => 1,
        ]);

        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'contacts')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
            'level' => 2,
        ]);

        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'accounts')->first()->id,
            'module_id' => Module::where('name', 'quotes')->first()->id,
            'level' => 3,
        ]);

        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'quotes')->first()->id,
            'module_id' => Module::where('name', 'opportunities')->first()->id,
            'level' => 4,
        ]);

        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'opportunities')->first()->id,
            'module_id' => Module::where('name', 'contracts')->first()->id,
            'level' => 5,
        ]);

        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'contracts')->first()->id,
            'module_id' => 0,
            'level' => 6,
        ]);
```

## Roles and Permission

IceburgCRM provides permissions by role and by module.  And allows you to set read, write, export import permissions.


## Import / Export


You have the ability to Import/Export in 6 different formats (XLSX, CSV, TSV, ODS, XLS, HTML].  Word is also available to be used but requires a system specific driver so it has been left out of the default options.



## Custom Seeding

### Files explained

These files will be run in this sequence

####  DatabaseSeeder
A database seeder file calls the remaining seeder files in sequence.  These must be run in sequence and data generated in the previous step may be required in the next step.

#### ModuleSeeder

This file creates the module records, module groups records and the module_convertable records (workflow)

#### FieldSeeder

This file creates all of the fields and relates them to modules

#### RelationshipSeeder

This file seeds the relationships between modules

#### GenerateSeeder

This file generates the default data for the modules.  It also adds the datalets, roles and permissions and any sample media.

#### ModuleSubpanelSeeder

This file generates the subpanel data.  It needs to be run last.



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

## hosted.iceburg.ca
### Don't want to self install?  Create CRMs Online for free
- Describe your CRM and build it with AI
- Select from our premade CRM templates
- Make any Database into a CRM

[hosted.iceburgcrm.ca](https://hosted.iceburg.ca)

## Templates

### Classic CRM
<img src="https://demo.iceburg.ca/images/classic.jpg?rand=12346" alt="Classic CRM Icon" width="100" height="100">
**Classic CRM. Accounts, Contacts, Contracts, LineItems, etc.**  
[Preview](https://classic.iceburg.ca)

### Rare Books CRM
<img src="https://demo.iceburg.ca/images/rarebooks.jpg?rand=12346" alt="Rare Books CRM Icon" width="100" height="100">
**A platform for sneaker enthusiasts to catalog their collections, track market values, manage trades or sales, and connect with other collectors.**  
[Preview](https://rarebooks.iceburg.ca)

### Wine Connoisseurs CRM
<img src="https://demo.iceburg.ca/images/wine.jpg?rand=12346" alt="Wine CRM Icon" width="100" height="100"> 
**For wine enthusiasts and sellers, offering cellar management, tasting notes, vintage tracking, and a community feature for sharing recommendations and organizing tastings.**  
[Preview](https://wine.iceburg.ca)

### Fitness Studio CRM
<img src="https://demo.iceburg.ca/images/fitness.jpg?rand=12346" alt="Fitness CRM Icon" width="100" height="100"> 
**Tailored for small to medium fitness studios, featuring membership management, class scheduling, fitness progress tracking for members, and integration with wearable tech for health data.**  
[Preview](https://fitness.iceburg.ca)

### Professional Networking CRM
<img src="https://demo.iceburg.ca/images/networking.jpg?rand=12346" alt="Networking CRM Icon" width="100" height="100">  
**A niche CRM for professional networking organizations, offering event planning, member engagement tracking, mentorship program management, and job boards.**  
[Preview](https://networking.iceburg.ca)

### Crafting Supplies CRM
<img src="https://demo.iceburg.ca/images/crafting.jpg?rand=12346" alt="Crafting Supplies CRM Icon" width="100" height="100"> 
**For retailers and enthusiasts of crafting, offering inventory management, project tracking, supplier databases, and community features for sharing project ideas and tutorials.**  
[Preview](https://crafting.iceburg.ca)

### Gourmet Coffee Enthusiasts CRM
<img src="https://demo.iceburg.ca/images/coffee.jpg?rand=12346" alt="Gourmet Coffee CRM Icon" width="100" height="100">  
**A platform for coffee lovers to track their favorite beans, roasts, brewing methods, and café experiences, including a marketplace for specialty beans and equipment.**  
[Preview](https://coffee.iceburg.ca)

### BeeKeeping CRM
<img src="https://demo.iceburg.ca/images/beekeeping.jpg?rand=12346" alt="BeeKeeping CRM Icon" width="100" height="100"> 
**For beekeepers to track hive health, manage honey production records, schedule maintenance, and engage with local and online beekeeping communities.**  
[Preview](https://beekeeping.iceburg.ca)

### Wordpress CRM  
<img src="https://demo.iceburg.ca/images/wordpress.jpg?rand=12346" alt="Wordpress CRM Icon" width="100" height="100">
**This is a premade instance of a wordpress database with iceburgcrm. Once created, download and point your wordpress files.**  Changing the data in the CRM will change the wordpress website.
[CRM Preview](https://wordpress.iceburg.ca)  
[Wordpress Website](https://wordpresssite.iceburg.ca)

## Star History

[![Star History Chart](https://api.star-history.com/svg?repos=iceburgcrm/iceburgcrm&type=Date)](https://star-history.com/#iceburgcrm/iceburgcrm&Date)


## Security Vulnerabilities

If you discover a security vulnerability within Iceburg CRM, please send an e-mail to [security@iceburg.ca](mailto:security@iceburg.ca).


## License

The Iceburg CRM is open-sourced software licensed under the [AGPL](https://www.gnu.org/licenses/agpl-3.0.en.html)

## Other Frameworks
A [Python Django Version](https://github.com/iceburgcrm/iceburgcrmpython) is available

## Sponsorship Opportunity
[<img src="https://api.gitsponsors.com/api/badge/img?id=569728313" height="20">](https://api.gitsponsors.com/api/badge/link?p=65KmCE3UtHXYCihBQwYrgahBsZrHiWOx3S4aLghrVXfYkaPDzuikx/QaiYMR76aK1IffSTO2pP1HGA4Zthk6d+EOC5kcJUP9HSFK+14jnfKbEXF/sXGOD6QxNlYWJtgXFh7StIL6O3F6PGUxOlm1Wg==)
