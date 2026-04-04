<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\DiscordAlerts\Facades\DiscordAlert;
use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class BaseModel extends EloquentModel
{
    use LogsActivity;

    protected bool $isAlerted = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($this->isAlerted) {
            return;
        }

        $properties = $activity->properties->toArray();
        $attributes = $properties['attributes'] ?? [];
        $oldData = $properties['old'] ?? [];
        $fields = [];

        // for deletes, we use oldData, for others we use attributes
        $data = ($eventName === 'deleted') ? $oldData : $attributes;
        $idString = $activity->subject_id ?? $this->getKey();

        $fields[] = "**id:** " . ($idString ?? 'Unknown');

        foreach ($data as $key => $value) {
            if ($key === 'id' || $key === 'updated_at') continue;

            $displayValue = match(true) {
                default => $value ?? 'NULL'
            };

            if ($eventName === 'updated' && array_key_exists($key, $oldData)) {
                $oldValue = $oldData[$key];

                $oldDisplay = match(true) {
                    default => $oldValue ?? 'NULL'
                };

                if ($oldDisplay != $displayValue) {
                    $displayValue .= " *(**was**: {$oldDisplay})*";
                }
            }

            $fields[] = "**{$key}:** {$displayValue}";
        }

        $fieldString = implode(', ', $fields);
        $user = auth()->user()?->name ?? 'System';

        $modelName = isset($activity->subject_type) && $activity->subject_type
            ? class_basename($activity->subject_type)
            : class_basename($this);
        $action = strtoupper($eventName);

        $message = "[{$action}] [{$modelName}] - **User**: {$user}, {$fieldString}";

        try {
            DiscordAlert::message($message);
            $this->isAlerted = true;
        } catch (\Throwable $e) {
        }
    }

    public function triggerEvent(string $event)
    {
        $this->fireModelEvent($event, false);
    }

    public function silentSave()
    {
        $this->isAlerted = true;
        return $this->save();
    }
}
