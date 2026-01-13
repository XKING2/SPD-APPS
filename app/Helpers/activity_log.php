<?php

use App\Services\ActivityLogService;

if (! function_exists('activity_log')) {
    function activity_log(
        string $action,
        string $description,
        $subject = null,
        array $oldValues = null,
        array $newValues = null
    ): void {
        app(ActivityLogService::class)
            ->log($action, $description, $subject, $oldValues, $newValues);
    }
}