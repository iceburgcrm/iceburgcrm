<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateJsonLang extends Command
{
    protected $signature = 'app:generate-json-lang {locale=en}';
    protected $description = 'Generate JSON language file from all PHP language files';

    public function handle()
    {
        $locale = $this->argument('locale');
        $phpLangDir = resource_path("lang/{$locale}");
        $jsonLangPath = resource_path("lang/{$locale}.json");

        if (!File::isDirectory($phpLangDir)) {
            $this->error("Language directory not found: {$phpLangDir}");
            return 1;
        }

        $messages = [];

        // Loop through all PHP files in the lang directory
        foreach (File::files($phpLangDir) as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $array = include $file;

            // Flatten nested arrays and prefix with filename if desired
            $flattened = $this->flattenArray($array, $filename);
            $messages = array_merge($messages, $flattened);
        }

        File::put($jsonLangPath, json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->info("Generated JSON language file: {$jsonLangPath}");

        return 0;
    }

    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
