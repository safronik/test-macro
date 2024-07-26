### Hello!

Please, run the folliwing in the /var/www/html folder insede the container:

``docker update``

``php index.php Migrations initDB`` - creates tables

``php index.php Migrations addTestData`` - isntall example data

- website should be available on http://macro.loc:8080/
- api route is /api
- apikey is 12345 ``http://macro.loc:8080/?api_key=12345``
- openAPI (Swagger) description is here https://github.com/safronik/test-macro/blob/master/code/openAPI.yml

I will be happy if you send me a feedback!
