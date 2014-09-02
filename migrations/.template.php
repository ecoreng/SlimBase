<?php

$content = <<<PHP
<?php

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class $className extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        /**
        * check: http://laravel.com/docs/schema
        * for more information
        */
   
        /*
            Capsule::schema()->create('users', function(\$table) {
                \$table->increments('id');
                \$table->string('email')->unique();
                \$table->timestamps();
            });

            Capsule::table('users', function(\$table)
            {
               \$table->renameColumn('email', 'email-original');
            });
        */
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        /*
            Capsule::schema()->drop('users');
        */
    }
}

PHP;
echo $content;
