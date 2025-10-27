Important Instructions before work:

-   Please note that i have in the migrations folder a technique for migrate, if there's a folder called landlord inside migrations then the tables will be migrated to landlord database, if tenant then in tenant database, if shared then will be migrated for both, don't put the migration files inside the migration folder directly

-   Please note that i have in the seeders folder a technique for seed, if there's a folder called landlord inside seeders then the tables will be seeded to tenant database, if tenant then in tenant database, if shared then will be seeded for both, don't put the seeders files inside the migration folder directly

-   In the views you created, don't use your own way to write views, follow my old controllers and views, for example category

-   Make sure each new controller follows the same structure, Controller -> Service -> Interface -> Repository

-   Please use the same way i write translations in the html elements or the error messages / success messages, like @translate in blade or translate() or t() or @t() same also in js

-   First of all read the project well, then read my requirements, beside my instructions, then write your rich todo-list finally go ahead and work on each todo in a proper way considering the whole thing


- If you're adding new .env keys in the code, make sure you add them to the .env.example

- Use mysql user: "user" and password: "password" if you want to access the database

- I prefer the Allman brace style for methods, functions, etc...

- When you're going to use alerts in the front end use the sweetalert alerts please not js default alert method

- Make sure in working in the views to make sure that you're using the same app structure: app.blade.php, header.blade.php, footer.blade.php, aside.blade.php (optional), etc...

- In the CRUD operations, please take a look on how my structure works, the views like index.blade.php and editor.blade.php INSTEAD of create.blade.php, edit.blade.php, index.blade.php ALSO editor.js and index.js, check the category for your reference, and do it like this