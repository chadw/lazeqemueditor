<?php
use App\Enums\Races;
use App\Enums\Classes;
use App\Enums\Deities;
use Illuminate\Support\Facades\Config;
use App\Support\ToastManager;

if (!function_exists('toast')) {
    function toast(): ToastManager
    {
        return app(ToastManager::class);
    }
}

if (!function_exists('eq_race')) {
    function eq_race($id)
    {
        $races = config('everquest.races');
        return $races[$id] ?? 'Unknown';
    }
}

if (!function_exists('eq_class')) {
    function eq_class($id)
    {
        $classes = config('everquest.classes');
        return $classes[$id] ?? 'Unknown';
    }
}

if (!function_exists('eq_language')) {
    function eq_language($id)
    {
        $lang = config('everquest.languages');
        return $lang[$id] ?? 'Unknown';
    }
}

if (!function_exists('eq_deity')) {
    function eq_deity($id)
    {
        $deity = config('everquest.deity');
        return $deity[$id] ?? 'Unknown';
    }
}

if (!function_exists('item_bagtypes')) {
    function item_bagtypes($id) {
        $bagtypes = config('everquest.bagtypes');
        return $bagtypes[$id] ?? 'Unknown';
    }
}

if (!function_exists('eq_skills')) {
    function eq_skills() {
        return config('everquest.skills');
    }
}

if (!function_exists('eq_aa_types')) {
    function eq_aa_types() {
        return config('everquest.aa_types');
    }
}

if (!function_exists('seconds_to_human')) {
    function seconds_to_human($seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $toseconds = $seconds % 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = "{$days}d";
        }
        if ($hours > 0 || $days > 0) {
            $parts[] = "{$hours}h";
        }
        if ($minutes > 0 || $hours > 0 || $days > 0) {
            $parts[] = "{$minutes}m";
        }
        if (empty($parts) || $toseconds > 0) {
            $parts[] = "{$toseconds}s";
        }

        return implode(' ', $parts);
    }
}

if (!function_exists('sign')) {
    function sign($val) {
        return ($val > 0 ? '+' : '') . $val;
    }
}

if (!function_exists('get_class_usable_string')) {
    function get_class_usable_string($val): string {
        if ($val == 65535) {
            return 'ALL';
        }

        return collect(Classes::cases())
            ->filter(fn (Classes $class) => ($val & $class->value))
            ->map(fn (Classes $class) => $class->shortName())
            ->implode(' ');
    }
}

if (!function_exists('get_race_usable_string')) {
    function get_race_usable_string($val): string {
        if ($val == 65535) {
            return 'ALL';
        }

        return collect(Races::cases())
            ->filter(fn (Races $race) => ($val & $race->value))
            ->map(fn (Races $race) => $race->shortName())
            ->implode(' ');
    }
}

if (!function_exists('get_deity_usable_string')) {
    function get_deity_usable_string($val): string {
        if ($val == 131071) {
            return 'ALL';
        }

        return collect(Deities::cases())
            ->filter(fn (Deities $deity) => ($val & $deity->value))
            ->map(fn (Deities $deity) => $deity->label())
            ->implode(' ');
    }
}

if (!function_exists('get_slots_string')) {
    function get_slots_string($val): string {
        $slots = (array) config('everquest.slots');
        $result = [];

        foreach ($slots as $bit => $name) {
            if (($val & $bit) === $bit) {
                $result[] = $name;
            }
        }

        return implode(', ', $result);
    }
}

if (!function_exists('item_aug_data')) {
    function item_aug_data($item) {
        $augdb = (array) config('everquest.db_aug_restrict');
        $html = '';

        if (($item->itemtype ?? 0) == 54) {
            $html .= '<div class="mt-6 text-sm">';

            if (($item->augtype ?? 0) > 0) {
                $augType = $item->augtype;
                $augSlots = [];

                for ($i = 1, $bit = 1; $i <= 24; $i++, $bit *= 2) {
                    if ($bit <= $augType && ($augType & $bit)) {
                        $augSlots[] = $i;
                    }
                }

                $slotsText = implode(', ', $augSlots);
                $html .= "<p><strong>Aug Slot Type:</strong> {$slotsText}</p>";
            } else {
                $html .= "<p><strong>Aug Slot Type:</strong> All Slots</p>";
            }

            // Handle Augmentation Restriction
            $augRestrict = $item->augrestrict ?? 0;

            if ($augRestrict > 0) {
                if ($augRestrict > 12 || !isset($augdb[$augRestrict])) {
                    $html .= "<p><strong>Aug Restriction:</strong> Unknown Type</p>";
                } else {
                    $restriction = $augdb[$augRestrict];
                    $html .= "<p><strong>Aug Restriction:</strong> {$restriction}</p>";
                }
            }
            $html .= '</div>';
        }

        return $html;
    }
}

if (!function_exists('calculate_item_price')) {
    function calculate_item_price($price) {
        $platinum = intdiv($price, 1000);
        $price -= $platinum * 1000;

        $gold = intdiv($price, 100);
        $price -= $gold * 100;

        $silver = intdiv($price, 10);
        $price -= $silver * 10;

        $copper = $price;

        return [
            'platinum' => $platinum,
            'gold'     => $gold,
            'silver'   => $silver,
            'copper'   => $copper,
        ];
    }
}

if (!function_exists('get_food_drink_desc')) {
    function get_food_drink_desc(int $key, int $type) {
        if ($key <= 0) {
            return null;
        }

        if ($type == 14) {
            $str = config('everquest.food_types');
        } elseif($type == 15) {
            $str = config('everquest.drink_types');
        }

        if ($key >= 1 && $key <= 5) {
            return $str[0];
        } elseif ($key <= 20) {
            return $str[1];
        } elseif ($key <= 30) {
            return $str[2];
        } elseif ($key <= 40) {
            return $str[3];
        } elseif ($key <= 50) {
            return $str[4];
        } elseif ($key <= 60) {
            return $str[5];
        } else {
            return $str[6];
        }
    }
}

if (!function_exists('price')) {
    function price(int $price): string
    {
        if ($price <= 0) {
            return '0 cp';
        }

        $p = intdiv($price, 1000);
        $price %= 1000;

        $g = intdiv($price, 100);
        $price %= 100;

        $s = intdiv($price, 10);
        $c = $price % 10;

        $parts = [];

        if ($p > 0) {
            $parts[] = "{$p} pp";
        }
        if ($g > 0) {
            $parts[] = "{$g} gp";
        }
        if ($s > 0) {
            $parts[] = "{$s} sp";
        }
        if ($c > 0 || empty($parts)) {
            $parts[] = "{$c} cp";
        }

        return implode(' ', $parts);
    }
}

if (!function_exists('ucRomanNumeral')) {
    function ucRomanNumeral(string $string): string
    {
        return preg_replace_callback('/\b(?=[mdclxvi])([mdclxvi]+)\b/i', function ($matches) {
            $possibleRoman = strtoupper($matches[1]);

            $valid = '/^(M{0,4})(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/';

            if (preg_match($valid, $possibleRoman)) {
                return $possibleRoman;
            }

            return $matches[0];
        }, $string);
    }
}

// https://github.com/Akkadius/spire/blob/07f745962011a257227b3108590460f9d042cdb6/frontend/src/app/spells.ts#L2875
if (!function_exists('getBuffDuration')) {
    function getBuffDuration($spell)
    {
        $i = 0;
        $minLevel = getMinLevel($spell);
        $buffDuration = (int) $spell['buffduration'];
        $buffDurationFormula = (int) $spell->buffdurationformula;

        switch ($buffDurationFormula) {
            case 0:
                return 0;
            case 1:
                $i = ceil($minLevel / 2);
                return ($i < $buffDuration ? ($i < 1 ? 1 : $i) : $buffDuration);
            case 2:
                $i = ceil($buffDuration / 5 * 3);
                return ($i < $buffDuration ? ($i < 1 ? 1 : $i) : $buffDuration);
            case 3:
                $i = $minLevel * 30;
                return ($i < $buffDuration ? ($i < 1 ? 1 : $i) : $buffDuration);
            case 4:
                return $buffDuration;
            case 5:
                $i = $buffDuration;
                return ($i < 3 ? ($i < 1 ? 1 : $i) : 3);
            case 6:
                $i = ceil($minLevel / 2);
                return ($i < $buffDuration ? ($i < 1 ? 1 : $i) : $buffDuration);
            case 7:
                $i = $minLevel;
                return ($i < $buffDuration ? ($i < 1 ? 1 : $i) : $buffDuration);
            case 8:
                $i = $minLevel + 10;
                return ($i < $buffDuration ? ($i < 1 ? 1 : $i) : $buffDuration);
            case 9:
                $i = $minLevel * 2 + 10;
                return ($i < $buffDuration ? ($i < 1 ? 1 : $i) : $buffDuration);
            case 10:
                $i = $minLevel * 3 + 10;
                return ($i < $buffDuration ? ($i < 1 ? 1 : $i) : $buffDuration);
            case 11:
            case 12:
                return $buffDuration;
            case 50:
                return 72000;
            case 3600:
                return ($buffDuration ? $buffDuration : 3600);
            default:
                //return "???";
                return 0;
        }
    }
}

if (!function_exists('getMinLevel')) {
    function getMinLevel($spell)
    {
        $minLevel = 255;
        for ($i = 1; $i <= 16; $i++) {
            $classIndex = "classes" . $i;
            if (($spell[$classIndex] > 0) && ($spell[$classIndex] < 255)) {
                if ($spell[$classIndex] < $minLevel) {
                    $minLevel = $spell[$classIndex];
                }
            }
        }

        return intval($minLevel);
    }
}

if (!function_exists('sortLink')) {
    function sortLink($field, $label) {
        $currentSort = request('sort');
        $currentDirection = request('direction', 'asc');
        $isActive = $currentSort === $field;
        $nextDirection = $isActive && $currentDirection === 'asc' ? 'desc' : 'asc';
        $arrow = $isActive ? ($currentDirection === 'asc' ? '↑' : '↓') : '';

        $url = request()->fullUrlWithQuery([
            'sort' => $field,
            'direction' => $nextDirection,
            'page' => 1,
        ]);

        return '<a href="' . $url . '" class="link-accent link-hover">' . e($label) . ' ' . $arrow . '</a>';
    }
}

// guild perms
if (!function_exists('status_ok')) {
    function status_ok() {
        return '<div aria-label="status" class="status status-lg status-accent"></div>';
    }
}

if (!function_exists('status_no')) {
    function status_no() {
        return '<div aria-label="status" class="status status-neutral"></div>';
    }
}

if (!function_exists('limitedList')) {
    function limitedList(array $items, int $limit = 5) {
        $items = array_values(array_filter($items));
        $shown = array_slice($items, 0, $limit);
        $remaining = count($items) - count($shown);

        return [
            'shown' => $shown,
            'remaining' => $remaining,
            'all' => $items,
        ];
    }
}

if (!function_exists('factionValue')) {
    function factionValue(int $value): string
    {
        return match (true) {
            $value >= 1100 => '<span class="text-blue-600">Ally</span>',
            $value >= 750  => '<span class="text-emerald-600">Warmly</span>',
            $value >= 500  => '<span class="text-green-500">Kindly</span>',
            $value >= 100  => '<span class="text-lime-500">Amiable</span>',
            $value >= 0    => '<span class="text-slate-500">Indifferent</span>',
            $value >= -100 => '<span class="text-amber-400">Apprehensive</span>',
            $value >= -500 => '<span class="text-orange-500">Dubious</span>',
            $value >= -750 => '<span class="text-red-500">Threatening</span>',
            $value < -750  => '<span class="text-rose-700">Scowls</span>',
            default        => 'UNK',
        };
    }
}
