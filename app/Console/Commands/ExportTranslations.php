<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportTranslations extends Command
{
    protected $signature = 'translations:export';
    protected $description = 'Export all translations into a JSON file for Vue or other frontend usage.';

    public function handle()
    {
        $langPath = resource_path('lang');
        $directories = array_filter(scandir($langPath), function ($item) use ($langPath) {
            return is_dir($langPath . DIRECTORY_SEPARATOR . $item) && !in_array($item, ['.', '..']);
        });

        if (empty($directories)) {
            $this->error("No language directories found in: {$langPath}");
            return;
        }

        foreach ($directories as $locale) {
            $this->info("Processing language: {$locale}");

            $localePath = "{$langPath}/{$locale}";
            $outputPath = "{$localePath}.json";
            $translations = [];

            foreach (File::allFiles($localePath) as $file) {
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $fileTranslations = include $file->getPathname();

                if (is_array($fileTranslations)) {
                    foreach ($fileTranslations as $key => $value) {
                        $translations["{$fileName}.{$key}"] = $value;
                    }
                }
            }

            File::put($outputPath, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("Exported: {$outputPath}");
        }

        $this->info("All translations exported successfully.");
    }

}
