<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 
 
use Faker\Factory as Faker; 
use Carbon\Carbon; 
use App\Posts;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Posts::truncate();

        $faker = Faker::create('ja_JP');
        $data = [];
        ## 100件のダミーデータを作成
        for ($i = 0; $i < 100; $i++) {
        	$date = $faker->dateTimeThisDecade($max = 'now');
            $data[] = [
                'title' => $faker->realText(30,5),
                'content' => $faker->realText(400,2),
                'author' => $faker->numberBetween(1,15),
                'created_at' => $date,
                'updated_at' => $date
            ];
        }

        Posts::insert($data);
    }
}
