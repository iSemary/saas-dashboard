- Please note that i have in the migrations folder a technique for migrate, if there's a folder called landlord inside migrations then the tables will be migrated to landlord database, if tenant then in tenant database, if shared then will be migrated for both, don't put the migration files inside the migration folder directly

- In the views you created, don't use your own way to write views, follow my old controllers and views, for example  category

- Make sure each new controller follows the same structure, Controller -> Service -> Interface -> Repository

