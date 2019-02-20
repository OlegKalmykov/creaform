<?php

use Illuminate\Database\Seeder;

class OAuthClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Schema::hasTable('oauth_clients')) {
            require_once $_SERVER['DOCUMENT_ROOT'].'/../vendor/laravel/passport/database/migrations/2016_06_01_000004_create_oauth_clients_table.php';

            $clientsTable = new \CreateOauthClientsTable();
            $clientsTable->up();
        }

        if (DB::table('oauth_clients')->count() == 0) {
            $this->createOAuthClients();
        }
    }

    private function createOAuthClients()
    {
        DB::table('oauth_clients')
            ->insert([
                [
                    'id'                     => 1,
                    'user_id'                => null,
                    'name'                   => 'Creaforn Personal Access Client',
                    'secret'                 => 'NDX8p8HUrhj4x9a8hoeFhkFTZ9QDxbzGtPBB8rAP',
                    'redirect'               => 'http://localhost',
                    'personal_access_client' => 1,
                    'password_client'        => 0,
                    'revoked'                => 0,
                    'created_at'             => date('Y-m-d H:i:s'),
                    'updated_at'             => date('Y-m-d H:i:s')
                ],
                [
                    'id'                     => 2,
                    'user_id'                => null,
                    'name'                   => 'Creaforn Password Access Client',
                    'secret'                 => 'd0NzCq7Ub73MnenleE3xulzx7xIQn3dSL68BFeWO',
                    'redirect'               => 'http://localhost',
                    'personal_access_client' => 0,
                    'password_client'        => 1,
                    'revoked'                => 0,
                    'created_at'             => date('Y-m-d H:i:s'),
                    'updated_at'             => date('Y-m-d H:i:s')
                ]
            ]);
    }
}

