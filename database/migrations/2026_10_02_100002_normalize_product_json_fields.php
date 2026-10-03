<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The gallery field used to be a repeater storing [{url: ...}];
        // the multi-upload field needs flat [path-or-url, ...].
        DB::table('products')->orderBy('id')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                $patch = [];
                foreach (['gallery', 'platforms_a', 'platforms_b', 'usage_steps'] as $col) {
                    $raw = $row->{$col};
                    if (! filled($raw)) {
                        continue;
                    }
                    $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
                    if (! is_array($decoded)) {
                        continue;
                    }
                    $flat = array_values(array_filter(array_map(
                        fn ($i) => is_array($i) ? ($i['url'] ?? $i['step'] ?? null) : $i,
                        $decoded
                    )));
                    $patch[$col] = json_encode($flat);
                }
                if ($patch !== []) {
                    DB::table('products')->where('id', $row->id)->update($patch);
                }
            }
        });
    }

    public function down(): void
    {
        // Data normalization is one-way; nothing to revert.
    }
};
