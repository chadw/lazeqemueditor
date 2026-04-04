<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TradeskillContainerTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // If the tables don't exist or already have data, skip to avoid duplicate inserts.
        if (! Schema::hasTable('tradeskill_container_templates') || ! Schema::hasTable('tradeskill_container_template_items')) {
            $this->command->info('Tradeskill container templates tables do not exist, skipping seeder.');
            return;
        }

        $existing = DB::table('tradeskill_container_templates')->count();
        if ($existing > 0) {
            $this->command->info('Tradeskill container templates already seeded, skipping.');
            return;
        }

        DB::table('tradeskill_container_templates')->insert([
            ['id' => 1, 'name' => 'Alchemy Default', 'skill' => '59', 'created_at' => '2026-03-02 17:37:29', 'updated_at' => '2026-03-02 17:37:29'],
            ['id' => 2, 'name' => 'Baking Default', 'skill' => '60', 'created_at' => '2026-03-02 18:22:13', 'updated_at' => '2026-03-02 18:22:13'],
            ['id' => 3, 'name' => 'Tailoring Default', 'skill' => '61', 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 4, 'name' => 'Tinkering Default', 'skill' => '57', 'created_at' => '2026-03-03 06:46:28', 'updated_at' => '2026-03-03 06:46:28'],
            ['id' => 5, 'name' => 'Poison Default', 'skill' => '56', 'created_at' => '2026-03-03 06:53:13', 'updated_at' => '2026-03-03 06:53:13'],
            ['id' => 6, 'name' => 'Jewelry Default', 'skill' => '68', 'created_at' => '2026-03-03 06:56:02', 'updated_at' => '2026-03-03 06:56:02'],
            ['id' => 7, 'name' => 'Fletching Default', 'skill' => '64', 'created_at' => '2026-03-03 06:58:26', 'updated_at' => '2026-03-03 06:58:26'],
            ['id' => 8, 'name' => 'Fishing Default', 'skill' => '55', 'created_at' => '2026-03-03 06:59:23', 'updated_at' => '2026-03-03 06:59:23'],
            ['id' => 9, 'name' => 'Brewing Default', 'skill' => '65', 'created_at' => '2026-03-03 07:04:01', 'updated_at' => '2026-03-03 07:04:01'],
            ['id' => 10, 'name' => 'Baking - Spit', 'skill' => '60', 'created_at' => '2026-03-03 07:39:22', 'updated_at' => '2026-03-03 07:39:22'],
            ['id' => 11, 'name' => 'Research - Spell Research', 'skill' => '58', 'created_at' => '2026-03-03 07:45:26', 'updated_at' => '2026-03-03 07:45:26'],
        ]);

        DB::table('tradeskill_container_template_items')->insert([
            ['id' => 23, 'tradeskill_container_template_id' => 2, 'item_id' => 17906, 'created_at' => '2026-03-03 06:35:59', 'updated_at' => '2026-03-03 06:35:59'],
            ['id' => 24, 'tradeskill_container_template_id' => 2, 'item_id' => 17162, 'created_at' => '2026-03-03 06:35:59', 'updated_at' => '2026-03-03 06:35:59'],
            ['id' => 25, 'tradeskill_container_template_id' => 2, 'item_id' => 15, 'created_at' => '2026-03-03 06:35:59', 'updated_at' => '2026-03-03 06:35:59'],
            ['id' => 26, 'tradeskill_container_template_id' => 3, 'item_id' => 16, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 27, 'tradeskill_container_template_id' => 3, 'item_id' => 17880, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 28, 'tradeskill_container_template_id' => 3, 'item_id' => 17966, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 29, 'tradeskill_container_template_id' => 3, 'item_id' => 17768, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 30, 'tradeskill_container_template_id' => 3, 'item_id' => 17165, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 31, 'tradeskill_container_template_id' => 3, 'item_id' => 17185, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 32, 'tradeskill_container_template_id' => 3, 'item_id' => 17806, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 33, 'tradeskill_container_template_id' => 3, 'item_id' => 17812, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 34, 'tradeskill_container_template_id' => 3, 'item_id' => 17813, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 35, 'tradeskill_container_template_id' => 3, 'item_id' => 17863, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 36, 'tradeskill_container_template_id' => 3, 'item_id' => 92868, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 37, 'tradeskill_container_template_id' => 3, 'item_id' => 92869, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 38, 'tradeskill_container_template_id' => 3, 'item_id' => 92870, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 39, 'tradeskill_container_template_id' => 3, 'item_id' => 92871, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 40, 'tradeskill_container_template_id' => 3, 'item_id' => 92872, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 41, 'tradeskill_container_template_id' => 3, 'item_id' => 92873, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 42, 'tradeskill_container_template_id' => 3, 'item_id' => 92874, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 43, 'tradeskill_container_template_id' => 3, 'item_id' => 92875, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 44, 'tradeskill_container_template_id' => 3, 'item_id' => 92876, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 45, 'tradeskill_container_template_id' => 3, 'item_id' => 92877, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 46, 'tradeskill_container_template_id' => 3, 'item_id' => 92878, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 47, 'tradeskill_container_template_id' => 3, 'item_id' => 92879, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 48, 'tradeskill_container_template_id' => 3, 'item_id' => 92880, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 49, 'tradeskill_container_template_id' => 3, 'item_id' => 92881, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 50, 'tradeskill_container_template_id' => 3, 'item_id' => 92882, 'created_at' => '2026-03-03 06:44:17', 'updated_at' => '2026-03-03 06:44:17'],
            ['id' => 51, 'tradeskill_container_template_id' => 4, 'item_id' => 17902, 'created_at' => '2026-03-03 06:46:29', 'updated_at' => '2026-03-03 06:46:29'],
            ['id' => 52, 'tradeskill_container_template_id' => 4, 'item_id' => 17877, 'created_at' => '2026-03-03 06:46:29', 'updated_at' => '2026-03-03 06:46:29'],
            ['id' => 53, 'tradeskill_container_template_id' => 4, 'item_id' => 17276, 'created_at' => '2026-03-03 06:46:29', 'updated_at' => '2026-03-03 06:46:29'],
            ['id' => 54, 'tradeskill_container_template_id' => 5, 'item_id' => 17904, 'created_at' => '2026-03-03 06:53:14', 'updated_at' => '2026-03-03 06:53:14'],
            ['id' => 55, 'tradeskill_container_template_id' => 5, 'item_id' => 17190, 'created_at' => '2026-03-03 06:53:14', 'updated_at' => '2026-03-03 06:53:14'],
            ['id' => 56, 'tradeskill_container_template_id' => 5, 'item_id' => 17139, 'created_at' => '2026-03-03 06:53:14', 'updated_at' => '2026-03-03 06:53:14'],
            ['id' => 57, 'tradeskill_container_template_id' => 6, 'item_id' => 17912, 'created_at' => '2026-03-03 06:56:02', 'updated_at' => '2026-03-03 06:56:02'],
            ['id' => 58, 'tradeskill_container_template_id' => 6, 'item_id' => 17764, 'created_at' => '2026-03-03 06:56:02', 'updated_at' => '2026-03-03 06:56:02'],
            ['id' => 59, 'tradeskill_container_template_id' => 6, 'item_id' => 62480, 'created_at' => '2026-03-03 06:56:02', 'updated_at' => '2026-03-03 06:56:02'],
            ['id' => 60, 'tradeskill_container_template_id' => 6, 'item_id' => 17187, 'created_at' => '2026-03-03 06:56:02', 'updated_at' => '2026-03-03 06:56:02'],
            ['id' => 61, 'tradeskill_container_template_id' => 7, 'item_id' => 17910, 'created_at' => '2026-03-03 06:58:26', 'updated_at' => '2026-03-03 06:58:26'],
            ['id' => 62, 'tradeskill_container_template_id' => 7, 'item_id' => 17762, 'created_at' => '2026-03-03 06:58:26', 'updated_at' => '2026-03-03 06:58:26'],
            ['id' => 63, 'tradeskill_container_template_id' => 7, 'item_id' => 17189, 'created_at' => '2026-03-03 06:58:26', 'updated_at' => '2026-03-03 06:58:26'],
            ['id' => 64, 'tradeskill_container_template_id' => 7, 'item_id' => 17128, 'created_at' => '2026-03-03 06:58:26', 'updated_at' => '2026-03-03 06:58:26'],
            ['id' => 65, 'tradeskill_container_template_id' => 7, 'item_id' => 66163, 'created_at' => '2026-03-03 06:58:26', 'updated_at' => '2026-03-03 06:58:26'],
            ['id' => 68, 'tradeskill_container_template_id' => 9, 'item_id' => 19, 'created_at' => '2026-03-03 07:04:01', 'updated_at' => '2026-03-03 07:04:01'],
            ['id' => 69, 'tradeskill_container_template_id' => 9, 'item_id' => 17179, 'created_at' => '2026-03-03 07:04:01', 'updated_at' => '2026-03-03 07:04:01'],
            ['id' => 70, 'tradeskill_container_template_id' => 9, 'item_id' => 17521, 'created_at' => '2026-03-03 07:04:01', 'updated_at' => '2026-03-03 07:04:01'],
            ['id' => 71, 'tradeskill_container_template_id' => 8, 'item_id' => 17048, 'created_at' => '2026-03-03 07:27:54', 'updated_at' => '2026-03-03 07:27:54'],
            ['id' => 72, 'tradeskill_container_template_id' => 8, 'item_id' => 17163, 'created_at' => '2026-03-03 07:27:54', 'updated_at' => '2026-03-03 07:27:54'],
            ['id' => 73, 'tradeskill_container_template_id' => 8, 'item_id' => 46, 'created_at' => '2026-03-03 07:27:54', 'updated_at' => '2026-03-03 07:27:54'],
            ['id' => 74, 'tradeskill_container_template_id' => 10, 'item_id' => 15, 'created_at' => '2026-03-03 07:39:23', 'updated_at' => '2026-03-03 07:39:23'],
            ['id' => 75, 'tradeskill_container_template_id' => 10, 'item_id' => 17947, 'created_at' => '2026-03-03 07:39:23', 'updated_at' => '2026-03-03 07:39:23'],
            ['id' => 76, 'tradeskill_container_template_id' => 10, 'item_id' => 17164, 'created_at' => '2026-03-03 07:39:23', 'updated_at' => '2026-03-03 07:39:23'],
            ['id' => 77, 'tradeskill_container_template_id' => 1, 'item_id' => 9, 'created_at' => '2026-03-03 07:41:48', 'updated_at' => '2026-03-03 07:41:48'],
            ['id' => 78, 'tradeskill_container_template_id' => 1, 'item_id' => 17901, 'created_at' => '2026-03-03 07:41:48', 'updated_at' => '2026-03-03 07:41:48'],
            ['id' => 79, 'tradeskill_container_template_id' => 1, 'item_id' => 17811, 'created_at' => '2026-03-03 07:41:48', 'updated_at' => '2026-03-03 07:41:48'],
            ['id' => 80, 'tradeskill_container_template_id' => 1, 'item_id' => 17770, 'created_at' => '2026-03-03 07:41:48', 'updated_at' => '2026-03-03 07:41:48'],
            ['id' => 81, 'tradeskill_container_template_id' => 1, 'item_id' => 17771, 'created_at' => '2026-03-03 07:41:48', 'updated_at' => '2026-03-03 07:41:48'],
            ['id' => 82, 'tradeskill_container_template_id' => 11, 'item_id' => 93492, 'created_at' => '2026-03-03 07:45:26', 'updated_at' => '2026-03-03 07:45:26'],
        ]);
    }
}
