<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Filesystem\Filesystem;

class MakeValidationRules extends Command
{
    protected $signature = 'make:validation-rules
        {table : DB table name}
        {--to=request : write a FormRequest file}
        {--name= : FormRequest class name (if --to=request) }
        {--force : overwrite existing file}';

    protected $description = 'Generate Laravel validation rules from a DB table schema';

    public function handle()
    {
        $table = $this->argument('table');
        $columns = DB::connection('eqemu')->select("SHOW COLUMNS FROM `{$table}`");

        if (empty($columns)) {
            $this->error("Table {$table} not found or has no columns.");
            return 1;
        }

        $rules = [];
        foreach ($columns as $col) {
            $field = $col->Field;
            if (in_array($field, ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            $rules[$field] = $this->mapColumnToRule($col);
        }

        $this->info("Rules for table `{$table}`:");
        $this->line(var_export($rules, true));

        if ($this->option('to')) {
            $className = $this->option('name') ?: Str::studly($table) . 'Request';
            $this->writeFormRequest($className, $rules, $columns);
        }

        return 0;
    }

    protected function mapColumnToRule($col)
    {
        $type = $col->Type;
        $nullable = (strtoupper($col->Null ?? '') === 'YES');
        $autoInc = stripos($col->Extra ?? '', 'auto_increment') !== false;
        $ruleParts = [];

        // skip auto increment PK
        if ($autoInc) {
            return 'nullable';
        }

        $intType = null;
        if (stripos($type, 'tinyint(1)') !== false) {
            $ruleParts[] = 'boolean';
        } elseif (preg_match('/\btinyint\b/i', $type)) {
            $ruleParts[] = 'integer';
            $intType = 'tinyint';
        } elseif (preg_match('/\bsmallint\b/i', $type)) {
            $ruleParts[] = 'integer';
            $intType = 'smallint';
        } elseif (preg_match('/\bmediumint\b/i', $type)) {
            $ruleParts[] = 'integer';
            $intType = 'mediumint';
        } elseif (preg_match('/\bbigint\b/i', $type)) {
            $ruleParts[] = 'integer';
            $intType = 'bigint';
        } elseif (stripos($type, 'int') !== false) {
            $ruleParts[] = 'integer';
            $intType = 'int';
        } elseif (stripos($type, 'decimal') !== false || stripos($type, 'float') !== false || stripos($type, 'double') !== false) {
            $ruleParts[] = 'numeric';
        } elseif (stripos($type, 'datetime') !== false || stripos($type, 'timestamp') !== false || stripos($type, 'date') !== false) {
            $ruleParts[] = 'date';
        } elseif (preg_match('/varchar\((\d+)\)/i', $type, $m)) {
            $ruleParts[] = 'string';
            $ruleParts[] = 'max:' . $m[1];
        } elseif (stripos($type, 'text') !== false) {
            $ruleParts[] = 'string';
        } elseif (preg_match("/enum\((.*)\)/i", $type, $m)) {
            $vals = array_map(function ($v) {
                return trim($v, " '\"");
            }, explode(',', $m[1]));
            $ruleParts[] = 'in:' . implode(',', array_map(function ($v) {
                return str_replace(',', '\,', $v);
            }, $vals));
        } else {
            $ruleParts[] = 'nullable';
        }

        if ($intType) {
            $unsigned = stripos($type, 'unsigned') !== false;
            switch ($intType) {
                case 'tinyint':
                    $signedMin = -128; $signedMax = 127; $unsignedMax = 255; break;
                case 'smallint':
                    $signedMin = -32768; $signedMax = 32767; $unsignedMax = 65535; break;
                case 'mediumint':
                    $signedMin = -8388608; $signedMax = 8388607; $unsignedMax = 16777215; break;
                case 'int':
                    $signedMin = -2147483648; $signedMax = 2147483647; $unsignedMax = 4294967295; break;
                case 'bigint':
                    $signedMin = '-9223372036854775808'; $signedMax = '9223372036854775807'; $unsignedMax = '18446744073709551615'; break;
                default:
                    $signedMin = null; $signedMax = null; $unsignedMax = null; break;
            }

            if ($signedMin !== null) {
                if ($unsigned) {
                    $ruleParts[] = 'min:0';
                    $ruleParts[] = 'max:' . $unsignedMax;
                } else {
                    $ruleParts[] = 'min:' . $signedMin;
                    $ruleParts[] = 'max:' . $signedMax;
                }
            }
        }

        if (!$nullable && !in_array('nullable', $ruleParts)) {
            if ($col->Default === null) {
                array_unshift($ruleParts, 'required');
            } else {
                $ruleParts[] = 'nullable';
            }
        } else {
            $ruleParts[] = 'nullable';
        }

        $ruleParts = array_unique($ruleParts);
        if (in_array('required', $ruleParts) && ($k = array_search('nullable', $ruleParts)) !== false) {
            unset($ruleParts[$k]);
        }

        return implode('|', array_values($ruleParts));
    }

    protected function writeFormRequest(string $className, array $rules, array $columns)
    {
        $fs = new Filesystem;
        $dir = app_path('Http/Requests');
        if (! $fs->isDirectory($dir)) {
            $fs->makeDirectory($dir, 0755, true);
        }

        $file = "{$dir}/{$className}.php";
        if ($fs->exists($file) && ! $this->option('force')) {
            $this->error("File {$file} exists. Use --force to overwrite.");
            return;
        }

        $rulesExport = var_export($rules, true);
        $prepareMethod = $this->generatePrepareForValidation($columns);

        $stub = <<<PHP
<?php

namespace App\Http\Requests;

class {$className} extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return {$rulesExport};
    }

{$prepareMethod}
}
PHP;
        $fs->put($file, $stub);
        $this->info("Wrote {$className} to {$file}");
    }

    protected function generatePrepareForValidation(array $columns): string
    {
        $lines = [];
        foreach ($columns as $col) {
            $field = $col->Field;

            if (in_array($field, ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            if (stripos($col->Extra ?? '', 'auto_increment') !== false) {
                continue;
            }

            $type = strtolower($col->Type ?? '');
            $default = $col->Default;

            $useInt = false;
            if (stripos($type, 'int') !== false || stripos($type, 'tinyint') !== false || stripos($type, 'decimal') !== false || stripos($type, 'float') !== false || stripos($type, 'double') !== false) {
                $useInt = true;
            }

            if ($useInt) {
                $val = ($default !== null) ? $default : 0;
                if ($val === '') {
                    $val = 0;
                }
                $lines[] = "            '{$field}' => \$this->defaultInt('{$field}', {$val}),";
            } else {
                $val = ($default !== null) ? $default : '';

                $valEsc = str_replace("'", "\\'", $val);
                $lines[] = "            '{$field}' => \$this->defaultString('{$field}', '{$valEsc}'),";
            }
        }

        if (empty($lines)) {
            return "    // No prepareForValidation overrides generated.";
        }

        $body = implode("\n", $lines);

        return "    protected function prepareForValidation(): void\n    {\n        \$this->merge([\n{$body}\n        ]);\n    }";
    }
}
