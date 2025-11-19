Intricare Practical by Siddharth

To run the application please run:
php artisan migrate
php artisan db:seed
after creating empty database in localhost and connecting it into .env.


Project Structure

app/
  Http/Controllers/ContactController.php
  Models/Contact.php
  Models/CustomField.php
  Models/ContactMergeLog.php

resources/views/contacts/index.blade.php
database/migrations/
database/seeders/
public/storage/ (linked)


Start the Development Server
php artisan serve
this is redirect to http://127.0.0.1:8000/contacts