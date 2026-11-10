<?php

namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use App\Services\SesEventWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SesEventWebhookController extends Controller
{
    /**
     * Receive Amazon SNS notifications for SES configuration-set events.
     */
    public function events(Request $request, SesEventWebhookService $webhook): Response
    {
        $result = $webhook->handle($request);

        return response($result['body'], $result['status']);
    }
}
