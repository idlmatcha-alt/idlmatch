<?php
declare(strict_types=1);
require __DIR__ . '/config/config.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error'=>'POST required']); exit; }
$input = json_decode(file_get_contents('php://input'), true);
$message = trim((string)($input['message'] ?? ''));
if ($message === '') { http_response_code(400); echo json_encode(['error'=>'Message required']); exit; }
if (mb_strlen($message) > 2000) { http_response_code(413); echo json_encode(['error'=>'Message too long']); exit; }

$system = <<<'PROMPT'
You are IDMatch AI Customer Support Agent, the official virtual customer-support assistant for IDMatch, an online matchmaking platform.

Your goal is to provide helpful, accurate, friendly, and safe support 24/7.

Help users with account creation, login, profile creation/editing, matchmaking, search/discovery, photos/profile information, verification and safety, membership/subscriptions/renewals/cancellations, payment and billing guidance, privacy/block/report, troubleshooting, general IDMatch features/policies, and escalation to human support.

Rules:
- Friendly, respectful, professional, concise, patient, non-judgmental.
- Never invent IDMatch policies, prices, features, refund rules, or technical capabilities.
- If you lack information, say so and advise the user to contact official IDMatch human support.
- Never request or expose passwords, OTP/verification codes, payment-card numbers, or other sensitive credentials.
- Never reveal another user's private information.
- Do not guarantee a partner or claim a match is definitively compatible.
- For scams, harassment, impersonation, suspicious accounts, or abuse, recommend reporting and escalation to human support.
- Do not claim an issue has been fixed unless it truly has.
- For troubleshooting, identify the issue, give simple steps, then escalate if needed.
- Encourage complete and accurate profile information and realistic preferences to improve recommendations.
- When a matter requires account access, billing investigation, deletion/recovery, fraud handling, or the user asks for a human, escalate.

You represent IDMatch and should maintain a trustworthy, welcoming, privacy-conscious support experience.
PROMPT;

if (OPENAI_API_KEY === 'REPLACE_WITH_SERVER_SIDE_OPENAI_API_KEY') {
    echo json_encode(['reply'=>'I’m ready to help with IDMatch. The AI connection is not configured yet. Please contact IDMatch Support for immediate assistance, or ask me about profiles, matches, verification, membership, or account help.']);
    exit;
}

$payload = [
    'model' => OPENAI_MODEL,
    'input' => [
        ['role'=>'system','content'=>$system],
        ['role'=>'user','content'=>$message],
    ],
    'max_output_tokens' => 600,
];

$ch = curl_init('https://api.openai.com/v1/responses');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json','Authorization: Bearer '.OPENAI_API_KEY],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 30,
]);
$response = curl_exec($ch);
$http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false || $curlError) { http_response_code(502); echo json_encode(['error'=>'AI service connection failed']); exit; }
$data = json_decode($response, true);
if ($http < 200 || $http >= 300) { http_response_code(502); echo json_encode(['error'=>'AI service returned an error']); exit; }

$text = trim((string)($data['output_text'] ?? ''));
if ($text === '' && isset($data['output']) && is_array($data['output'])) {
    $parts = [];
    foreach ($data['output'] as $item) {
        if (($item['type'] ?? '') !== 'message') continue;
        foreach (($item['content'] ?? []) as $content) {
            if (($content['type'] ?? '') === 'output_text') $parts[] = (string)($content['text'] ?? '');
        }
    }
    $text = trim(implode("\n", $parts));
}
if ($text === '') { $text = 'I’m sorry, I could not generate a response right now. Please contact IDMatch Support.'; }

echo json_encode(['reply'=>$text], JSON_UNESCAPED_UNICODE);
