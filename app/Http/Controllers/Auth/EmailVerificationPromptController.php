<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Generate and send OTP if not already sent recently
        $user = $request->user();
        $cacheKey = 'otp_' . $user->id;
        
        if (!Cache::has($cacheKey)) {
            $this->sendOtp($user);
        }

        return view('auth.verify-email');
    }

    /**
     * Send OTP to user's email.
     */
    protected function sendOtp($user)
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = 'otp_' . $user->id;
        
        // Store OTP in cache for 10 minutes
        Cache::put($cacheKey, $otp, now()->addMinutes(10));
        
        // Send OTP email
        // Send OTP email
        try {
            Mail::send([], [], function ($message) use ($user, $otp) {
                $message->to($user->email)
                    ->subject('Your IDLMatch Verification Code')
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 30px; background: #f8f9fa; border-radius: 10px;'>
                            <div style='text-align: center; margin-bottom: 30px;'>
                                <h1 style='color: #E91E63; margin: 0;'>IDLMatch</h1>
                            </div>
                            <div style='background: white; padding: 30px; border-radius: 10px; text-align: center;'>
                                <h2 style='color: #333; margin-bottom: 10px;'>Email Verification</h2>
                                <p style='color: #666; margin-bottom: 25px;'>Use the following code to verify your email address:</p>
                                <div style='background: #f0f0f0; padding: 20px; border-radius: 8px; display: inline-block;'>
                                    <span style='font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #E91E63;'>{$otp}</span>
                                </div>
                                <p style='color: #999; margin-top: 25px; font-size: 14px;'>This code will expire in 10 minutes.</p>
                            </div>
                            <p style='color: #999; font-size: 12px; text-align: center; margin-top: 20px;'>
                                If you didn't request this code, please ignore this email.
                            </p>
                        </div>
                    ");
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send OTP email: ' . $e->getMessage());
            // Continue without crashing
        }
    }
}
