# AdminAppwithLaravelVue
Laravel 6.0 with Vue.js and Vuetify - buliding and administarion app

run this command

    npm install or npm i

database file( laravue_db ) is added to project you can login with the given user:
        
        Username: admin@admin.com
        Password: Admin@123

or

you can migrate tables to new database . for that you have to congfigure the .env file and set the new database name.
and then follow this command to add tables to new database

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
    
--You can change port in the file name webpack.mix.js
       
    mix.browserSync({proxy: "laravue.test", port: yourport});

now to run the project type this command

    npm run watch


