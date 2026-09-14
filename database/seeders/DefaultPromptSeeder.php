<?php

namespace Database\Seeders;

use App\Models\PromptTemplate;
use App\Models\PromptVersion;
use Illuminate\Database\Seeder;

class DefaultPromptSeeder extends Seeder
{
    public function run(): void
    {
        $prompts = [
            ['PA', 'Source Culture Preservation', 'SOURCE', 'Translate the full Malay source text into Arabic while preserving Malay cultural identity, historical references, and culturally specific meaning. Avoid replacing source-culture references with non-equivalent Arab references.'],
            ['PB', 'Domestication', 'TARGET', 'Translate the full Malay source text into natural Arabic for an Arab reader. Where adaptation is necessary, retain enough information for culturally significant source references to remain traceable.'],
            ['PC', 'Macro Translation', 'TARGET', 'Translate the full Malay source text into coherent, natural Arabic at whole-text level. Preserve meaning and context while prioritising strategic textual coherence.'],
        ];

        foreach ($prompts as [$code, $name, $orientation, $body]) {
            $template = PromptTemplate::query()->updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'orientation' => $orientation, 'is_system_default' => true, 'active' => true],
            );

            PromptVersion::query()->firstOrCreate(
                ['prompt_template_id' => $template->getKey(), 'version_number' => 1],
                ['prompt_body' => $body, 'content_hash' => hash('sha256', $body)],
            );
        }
    }
}
