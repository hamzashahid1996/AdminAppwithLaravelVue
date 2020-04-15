# AdminAppwithLaravelVue
Laravel 6.0 with Vue.js and Vuetify - buliding and administarion app

run this command

npm install or npm i

database file( laravue_db ) is added to project

                     or

you can migrate tables to new database . for that you have to congfigure the .env file and change set the new database name.
and then follow this command

php artisan migrate

after migratation we have to make an admin role  and a user
so we can add directly add a new role to the role table or we can use tinker to add a new role

php artisan tinker

Psy Shell v0.10.2 (PHP 7.2.19 — cli) by Justin Hileman
>>> $role = new App\Role
=> App\Role {#3069}
>>> $role->name = 'administrator'
=> "administrator"
>>> $role->id = 2
=> 2
>>> $role->save()
=> true
>>>


now to run the project type this command

npm run watch


