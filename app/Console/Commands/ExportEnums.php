<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ReflectionEnum;
use Illuminate\Support\Facades\File;

class ExportEnums extends Command
{
    protected $signature = 'enums:export';
    protected $description = 'Export PHP enums to JSON and Vue 3 JS format';

    public function handle()
    {
        $enumsPath = app_path('Enum');
        $output = [];

        foreach (File::allFiles($enumsPath) as $file) {
            $class = 'App\\Enum\\' . $file->getFilenameWithoutExtension();

            if (enum_exists($class)) {
                $reflection = new ReflectionEnum($class);

                $values = [];
                foreach ($class::cases() as $case) {
                    $values[$case->name] = $case->value;
                }

                $shortName = class_basename($class);
                $output[$shortName] = $values;
            }
        }

        // Write to JSON
        File::put(resource_path('js/enums.json'), json_encode($output, JSON_PRETTY_PRINT));

        // Convert to Vue3-style JS module
        $jsContent = "export const Enums = " . json_encode($output, JSON_PRETTY_PRINT) . ";\n";
        File::put(resource_path('js/enums.js'), $jsContent);

        $this->info('Enums exported to resources/js/enums.json and enums.js');
    }
}
