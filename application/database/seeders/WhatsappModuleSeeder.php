<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * @fileoverview Seeds default WhatsApp module data
 * @description Inserts default ticket types and a couple of tags for quick testing.
 */
class WhatsappModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Seed ticket types (idempotent by name+tenant)
        $types = [
            ['tenant_id' => 1, 'name' => 'Bug', 'color' => '#e03131', 'description' => 'Bug report'],
            ['tenant_id' => 1, 'name' => 'Inquiry', 'color' => '#1971c2', 'description' => 'General question'],
            ['tenant_id' => 1, 'name' => 'Feature', 'color' => '#2f9e44', 'description' => 'Feature request'],
        ];
        foreach ($types as $t) {
            try {
                $exists = DB::table('whatsapp_ticket_types')
                    ->where('tenant_id', $t['tenant_id'])
                    ->where('name', $t['name'])
                    ->exists();
                if (!$exists) {
                    DB::table('whatsapp_ticket_types')->insert(array_merge($t, [
                        'is_active' => 1,
                        'sort_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            } catch (\Throwable $e) {
                // Table may not exist yet; let caller know via console output
                echo "Seeder warning: ".$e->getMessage()."\n";
            }
        }

        // Optional: seed sample tags table if present
        if (Schema()->hasTable('whatsapp_tags')) {
            $tags = [
                ['tenant_id' => 1, 'name' => 'VIP'],
                ['tenant_id' => 1, 'name' => 'Priority'],
            ];
            foreach ($tags as $tag) {
                $exists = DB::table('whatsapp_tags')
                    ->where('tenant_id', $tag['tenant_id'])
                    ->where('name', $tag['name'])
                    ->exists();
                if (!$exists) {
                    DB::table('whatsapp_tags')->insert(array_merge($tag, [
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            }
        }
    }
}


