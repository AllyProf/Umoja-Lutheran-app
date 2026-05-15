<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    private function getClient()
    {
        $client = new Client();
        $client->setClientId(config('filesystems.disks.google.clientId'));
        $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
        $client->setRedirectUri(route('admin.google.callback'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->addScope(\Google\Service\Drive::DRIVE_FILE);
        $client->addScope(\Google\Service\Drive::DRIVE_METADATA_READONLY);
        return $client;
    }

    public function redirectToGoogle()
    {
        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('super_admin.system-settings')->with('error', 'Google Auth Error: ' . $request->error);
        }

        if (!$request->has('code')) {
            return redirect()->route('super_admin.system-settings')->with('error', 'No auth code provided.');
        }

        try {
            $client = $this->getClient();
            $token = $client->fetchAccessTokenWithAuthCode($request->code);

            if (isset($token['error'])) {
                return redirect()->route('super_admin.system-settings')->with('error', 'Token Exchange Error: ' . $token['error_description']);
            }

            if (!isset($token['refresh_token'])) {
                return redirect()->route('super_admin.system-settings')->with('error', 'No refresh token received. Try revoking app access and re-authenticating.');
            }

            $refreshToken = $token['refresh_token'];

            // Update .env file
            $this->updateDotEnv('GOOGLE_DRIVE_REFRESH_TOKEN', $refreshToken);

            return redirect()->route('super_admin.system-settings')->with('success', 'Google Drive connected successfully! Refresh token updated.');

        } catch (\Exception $e) {
            Log::error('Google Auth Callback Error: ' . $e->getMessage());
            return redirect()->route('super_admin.system-settings')->with('error', 'An error occurred during authentication: ' . $e->getMessage());
        }
    }

    private function updateDotEnv($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $content = file_get_contents($path);
            $oldLine = preg_grep("/^$key=/", explode("\n", $content));

            if (count($oldLine) > 0) {
                $content = preg_replace("/^$key=.*$/m", "$key=$value", $content);
            } else {
                $content .= "\n$key=$value";
            }

            file_put_contents($path, $content);
        }
    }
}
