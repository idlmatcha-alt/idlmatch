<?php

namespace App\Services\Admin;

use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserPdfExportService
{
    public function download(User $user): StreamedResponse
    {
        $user->loadMissing(['profile.preferences', 'profile.photos']);

        $profile = $user->profile;
        $preferences = $profile?->preferences;

        $userFields = $this->collectModelFields($user, $this->userFields($user));
        $profileFields = $profile ? $this->collectModelFields($profile, $this->profileFields($profile)) : [];
        $preferenceFields = $preferences ? $this->collectModelFields($preferences, $this->preferenceFields($preferences)) : [];
        $photoPaths = $profile ? $profile->photos->pluck('path')->filter()->values()->all() : [];

        $html = view('admin.users.profile-pdf', [
            'user' => $user,
            'profile' => $profile,
            'preferences' => $preferences,
            'userFields' => $userFields,
            'profileFields' => $profileFields,
            'preferenceFields' => $preferenceFields,
            'photoPaths' => $photoPaths,
            'generatedAt' => now(),
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdf = $dompdf->output();
        $filename = sprintf('user-%d-profile-%s.pdf', $user->id, now()->format('Ymd-His'));

        return response()->streamDownload(
            static function () use ($pdf): void {
                echo $pdf;
            },
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * @param  array<int, string>  $fields
     * @return array<int, array{label: string, value: string}>
     */
    private function collectModelFields(Model $model, array $fields): array
    {
        $rows = [];

        foreach ($fields as $field) {
            $rows[] = [
                'label' => $this->humanize($field),
                'value' => $this->formatValue(data_get($model, $field)),
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, string>
     */
    private function userFields(User $user): array
    {
        return array_values(array_diff(array_unique(array_merge(
            ['id'],
            $user->getFillable(),
            ['email_verified_at', 'last_login_at', 'created_at', 'updated_at']
        )), ['password', 'remember_token']));
    }

    /**
     * @return array<int, string>
     */
    private function profileFields(Model $profile): array
    {
        return array_values(array_unique(array_merge(
            ['id'],
            $profile->getFillable(),
            ['created_at', 'updated_at', 'deleted_at']
        )));
    }

    /**
     * @return array<int, string>
     */
    private function preferenceFields(Model $preferences): array
    {
        return array_values(array_unique(array_merge(
            ['id'],
            $preferences->getFillable(),
            ['created_at', 'updated_at']
        )));
    }

    private function humanize(string $field): string
    {
        return ucwords(str_replace('_', ' ', $field));
    }

    private function formatValue($value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            $flat = collect($value)->flatten()->map(function ($item) {
                if ($item instanceof \DateTimeInterface) {
                    return $item->format('Y-m-d H:i:s');
                }

                if (is_bool($item)) {
                    return $item ? 'Yes' : 'No';
                }

                if (is_scalar($item) || $item === null) {
                    return (string) $item;
                }

                return json_encode($item);
            })->filter(fn ($item) => $item !== null && $item !== '')->values();

            return $flat->isEmpty() ? '-' : $flat->implode(', ');
        }

        if ($value === null) {
            return '-';
        }

        $string = trim((string) $value);

        return $string === '' ? '-' : $string;
    }
}

