<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\UserPdfExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserPdfExportController extends Controller
{
    public function download(User $user, UserPdfExportService $pdfExportService): StreamedResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        return $pdfExportService->download($user);
    }
}

