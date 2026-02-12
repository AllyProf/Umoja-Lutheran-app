<?php
/**
 * Login Issue Diagnostic Script
 * 
 * Visit: http://primelandhotel.co.tz/diagnose-login.php
 * This will show exactly what's wrong with your login configuration
 * 
 * DELETE THIS FILE AFTER FIXING THE ISSUE FOR SECURITY
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Start session to test
$request = Illuminate\Http\Request::capture();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Issue Diagnostic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; margin-bottom: 10px; }
        .content { padding: 30px; }
        .section { margin-bottom: 30px; padding: 20px; background: #f9f9f9; border-radius: 6px; border-left: 4px solid #667eea; }
        .section h2 { color: #333; margin-bottom: 15px; font-size: 20px; }
        .check { display: flex; align-items: center; padding: 10px; margin: 8px 0; background: white; border-radius: 4px; }
        .check-icon { font-size: 24px; margin-right: 15px; width: 30px; text-align: center; }
        .ok { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .check-details { flex: 1; }
        .check-label { font-weight: 600; color: #333; margin-bottom: 5px; }
        .check-value { color: #666; font-family: 'Courier New', monospace; font-size: 14px; }
        .check-note { color: #888; font-size: 12px; margin-top: 5px; font-style: italic; }
        .fix-box { background: #fff3cd; border: 2px solid #ffc107; border-radius: 6px; padding: 20px; margin-top: 20px; }
        .fix-box h3 { color: #856404; margin-bottom: 15px; }
        .fix-box code { background: #fff; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
        .fix-box pre { background: #fff; padding: 15px; border-radius: 4px; overflow-x: auto; margin-top: 10px; }
        .critical { background: #f8d7da; border-left-color: #dc3545; }
        .success { background: #d4edda; border-left-color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Login Issue Diagnostic Report</h1>
            <p>Analyzing your login configuration...</p>
        </div>
        
        <div class="content">
            <?php
            $issues = [];
            $warnings = [];
            $checks = [];
            
            // Get current request
            $currentRequest = request();
            $currentScheme = $currentRequest->isSecure() ? 'https' : 'http';
            $currentHost = $currentRequest->getHost();
            $currentUrl = $currentRequest->fullUrl();
            
            // Check 1: APP_URL Configuration
            $appUrl = config('app.url');
            $appUrlScheme = $appUrl ? parse_url($appUrl, PHP_URL_SCHEME) : null;
            $appUrlHost = $appUrl ? parse_url($appUrl, PHP_URL_HOST) : null;
            
            $checks[] = [
                'label' => 'APP_URL Configuration',
                'value' => $appUrl ?: '❌ NOT SET',
                'status' => $appUrl ? 'ok' : 'error',
                'note' => $appUrl ? "Configured: {$appUrl}" : 'APP_URL must be set in .env file',
                'critical' => !$appUrl
            ];
            
            if (!$appUrl) {
                $issues[] = 'APP_URL is not set in .env file';
            }
            
            // Check 2: Protocol Match
            $protocolMatch = ($appUrlScheme === $currentScheme);
            $checks[] = [
                'label' => 'Protocol Match (HTTP/HTTPS)',
                'value' => $protocolMatch ? '✅ MATCH' : '❌ MISMATCH',
                'status' => $protocolMatch ? 'ok' : 'error',
                'note' => $protocolMatch 
                    ? "Both use {$currentScheme}" 
                    : "APP_URL uses {$appUrlScheme} but site is accessed via {$currentScheme}",
                'critical' => !$protocolMatch
            ];
            
            if (!$protocolMatch) {
                $issues[] = "Protocol mismatch: APP_URL uses {$appUrlScheme} but site is accessed via {$currentScheme}";
            }
            
            // Check 3: Domain Match
            $domainMatch = (!$appUrlHost || $appUrlHost === $currentHost || str_ends_with($currentHost, '.' . $appUrlHost));
            $checks[] = [
                'label' => 'Domain Match',
                'value' => $domainMatch ? '✅ MATCH' : '⚠️ CHECK',
                'status' => $domainMatch ? 'ok' : 'warning',
                'note' => $domainMatch 
                    ? "Domains match" 
                    : "APP_URL host: {$appUrlHost}, Current host: {$currentHost}",
                'critical' => false
            ];
            
            if (!$domainMatch) {
                $warnings[] = "Domain mismatch: APP_URL host ({$appUrlHost}) doesn't match current host ({$currentHost})";
            }
            
            // Check 4: Session Secure Cookie
            $sessionSecure = config('session.secure');
            $shouldBeSecure = ($currentScheme === 'https');
            $secureMatch = ($sessionSecure === $shouldBeSecure || ($sessionSecure === null && !$shouldBeSecure));
            
            $checks[] = [
                'label' => 'SESSION_SECURE_COOKIE',
                'value' => $sessionSecure === null ? 'null (defaults to false)' : ($sessionSecure ? 'true' : 'false'),
                'status' => $secureMatch ? 'ok' : ($sessionSecure === null ? 'warning' : 'error'),
                'note' => $shouldBeSecure 
                    ? ($sessionSecure ? '✅ Correct for HTTPS' : '❌ Should be true for HTTPS')
                    : ($sessionSecure === false || $sessionSecure === null ? '✅ Correct for HTTP' : '❌ Should be false for HTTP'),
                'critical' => !$secureMatch && $sessionSecure !== null
            ];
            
            if (!$secureMatch && $sessionSecure !== null) {
                $issues[] = "SESSION_SECURE_COOKIE is " . ($sessionSecure ? 'true' : 'false') . " but should be " . ($shouldBeSecure ? 'true' : 'false') . " for {$currentScheme}";
            } elseif ($sessionSecure === null) {
                $warnings[] = "SESSION_SECURE_COOKIE not explicitly set - consider setting it to " . ($shouldBeSecure ? 'true' : 'false');
            }
            
            // Check 5: Session Domain
            $sessionDomain = config('session.domain');
            $checks[] = [
                'label' => 'SESSION_DOMAIN',
                'value' => $sessionDomain ?: 'null (defaults to current domain)',
                'status' => $sessionDomain ? 'ok' : 'warning',
                'note' => $sessionDomain 
                    ? "Set to: {$sessionDomain}" 
                    : 'Not set - cookies may not work across www/non-www subdomains',
                'critical' => false
            ];
            
            if (!$sessionDomain) {
                $warnings[] = 'SESSION_DOMAIN not set - consider setting to .primelandhotel.co.tz (with leading dot)';
            }
            
            // Check 6: Session Driver
            $sessionDriver = config('session.driver');
            $checks[] = [
                'label' => 'Session Driver',
                'value' => $sessionDriver,
                'status' => ($sessionDriver === 'database' || $sessionDriver === 'file') ? 'ok' : 'warning',
                'note' => $sessionDriver === 'database' ? '✅ Using database sessions' : ($sessionDriver === 'file' ? '✅ Using file sessions' : '⚠️ Unusual driver'),
                'critical' => false
            ];
            
            // Check 7: CSRF Token Generation
            try {
                $csrfToken = csrf_token();
                $checks[] = [
                    'label' => 'CSRF Token Generation',
                    'value' => '✅ WORKING',
                    'status' => 'ok',
                    'note' => 'Token generated successfully: ' . substr($csrfToken, 0, 20) . '...',
                    'critical' => false
                ];
            } catch (\Exception $e) {
                $checks[] = [
                    'label' => 'CSRF Token Generation',
                    'value' => '❌ FAILED',
                    'status' => 'error',
                    'note' => 'Error: ' . $e->getMessage(),
                    'critical' => true
                ];
                $issues[] = 'CSRF token generation failed: ' . $e->getMessage();
            }
            
            // Check 8: Session Working
            try {
                $session = session();
                $sessionId = $session->getId();
                $sessionToken = $session->token();
                $checks[] = [
                    'label' => 'Session Status',
                    'value' => '✅ WORKING',
                    'status' => 'ok',
                    'note' => 'Session ID: ' . substr($sessionId, 0, 20) . '... | Token: ' . substr($sessionToken, 0, 20) . '...',
                    'critical' => false
                ];
            } catch (\Exception $e) {
                $checks[] = [
                    'label' => 'Session Status',
                    'value' => '❌ FAILED',
                    'status' => 'error',
                    'note' => 'Error: ' . $e->getMessage(),
                    'critical' => true
                ];
                $issues[] = 'Session not working: ' . $e->getMessage();
            }
            
            // Check 9: Session Cookie Present
            $sessionCookieName = config('session.cookie');
            $hasSessionCookie = $currentRequest->hasCookie($sessionCookieName);
            $checks[] = [
                'label' => 'Session Cookie Present',
                'value' => $hasSessionCookie ? '✅ YES' : '⚠️ NO',
                'status' => $hasSessionCookie ? 'ok' : 'warning',
                'note' => $hasSessionCookie 
                    ? "Cookie '{$sessionCookieName}' is present" 
                    : "Cookie '{$sessionCookieName}' not found - this is normal on first visit",
                'critical' => false
            ];
            
            // Summary
            $allOk = empty($issues);
            ?>
            
            <!-- Summary Section -->
            <div class="section <?php echo $allOk ? 'success' : 'critical'; ?>">
                <h2>📊 Summary</h2>
                <div style="font-size: 18px; margin: 15px 0;">
                    <?php if ($allOk): ?>
                        <strong style="color: #28a745;">✅ Configuration looks good!</strong>
                        <p style="margin-top: 10px; color: #666;">All critical checks passed. If login still fails, check browser console and network tab.</p>
                    <?php else: ?>
                        <strong style="color: #dc3545;">❌ Issues Found</strong>
                        <p style="margin-top: 10px; color: #666;"><?php echo count($issues); ?> critical issue(s) need to be fixed.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Critical Issues -->
            <?php if (!empty($issues)): ?>
            <div class="section critical">
                <h2>🚨 Critical Issues</h2>
                <ul style="list-style: none; padding-left: 0;">
                    <?php foreach ($issues as $issue): ?>
                        <li style="padding: 10px; margin: 5px 0; background: white; border-radius: 4px; border-left: 3px solid #dc3545;">
                            ❌ <?php echo htmlspecialchars($issue); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Warnings -->
            <?php if (!empty($warnings)): ?>
            <div class="section">
                <h2>⚠️ Warnings</h2>
                <ul style="list-style: none; padding-left: 0;">
                    <?php foreach ($warnings as $warning): ?>
                        <li style="padding: 10px; margin: 5px 0; background: white; border-radius: 4px; border-left: 3px solid #ffc107;">
                            ⚠️ <?php echo htmlspecialchars($warning); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Detailed Checks -->
            <div class="section">
                <h2>🔍 Detailed Checks</h2>
                <?php foreach ($checks as $check): ?>
                    <div class="check">
                        <div class="check-icon">
                            <?php 
                            if ($check['status'] === 'ok') echo '✅';
                            elseif ($check['status'] === 'error') echo '❌';
                            else echo '⚠️';
                            ?>
                        </div>
                        <div class="check-details">
                            <div class="check-label"><?php echo htmlspecialchars($check['label']); ?></div>
                            <div class="check-value"><?php echo htmlspecialchars($check['value']); ?></div>
                            <div class="check-note"><?php echo htmlspecialchars($check['note']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Fix Instructions -->
            <?php if (!empty($issues) || !empty($warnings)): ?>
            <div class="fix-box">
                <h3>🔧 How to Fix</h3>
                <p>Update your <code>.env</code> file with these settings:</p>
                <pre><?php
echo "# Current Configuration\n";
echo "APP_URL=" . ($appUrl ?: 'http://primelandhotel.co.tz') . "\n";
echo "APP_ENV=production\n";
echo "\n";
echo "# Session Configuration\n";
echo "SESSION_DRIVER=database\n";
echo "SESSION_LIFETIME=120\n";
echo "SESSION_DOMAIN=.primelandhotel.co.tz\n";
echo "SESSION_SECURE_COOKIE=" . ($shouldBeSecure ? 'true' : 'false') . "\n";
echo "SESSION_SAME_SITE=lax\n";
echo "\n";
echo "# After updating .env, run:\n";
echo "# php artisan config:clear\n";
echo "# php artisan cache:clear\n";
                ?></pre>
                
                <p style="margin-top: 15px;"><strong>Important:</strong></p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>Set <code>APP_URL</code> to match your actual domain and protocol (<?php echo $currentScheme; ?>://<?php echo $currentHost; ?>)</li>
                    <li>Set <code>SESSION_SECURE_COOKIE</code> to <?php echo $shouldBeSecure ? 'true' : 'false'; ?> for <?php echo strtoupper($currentScheme); ?> sites</li>
                    <li>Set <code>SESSION_DOMAIN</code> to <code>.primelandhotel.co.tz</code> (with leading dot) for www/non-www support</li>
                    <li>After updating, clear caches: <code>php artisan config:clear && php artisan cache:clear</code></li>
                    <li>Clear browser cookies and try again</li>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Current Configuration -->
            <div class="section">
                <h2>📋 Current Configuration</h2>
                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px; font-weight: 600;">Setting</td>
                        <td style="padding: 10px; font-weight: 600;">Value</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">Current URL</td>
                        <td style="padding: 10px; font-family: monospace;"><?php echo htmlspecialchars($currentUrl); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">Current Scheme</td>
                        <td style="padding: 10px; font-family: monospace;"><?php echo strtoupper($currentScheme); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">Current Host</td>
                        <td style="padding: 10px; font-family: monospace;"><?php echo htmlspecialchars($currentHost); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">APP_URL</td>
                        <td style="padding: 10px; font-family: monospace;"><?php echo htmlspecialchars($appUrl ?: 'NOT SET'); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">SESSION_DOMAIN</td>
                        <td style="padding: 10px; font-family: monospace;"><?php echo htmlspecialchars($sessionDomain ?: 'null (not set)'); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">SESSION_SECURE_COOKIE</td>
                        <td style="padding: 10px; font-family: monospace;"><?php echo $sessionSecure === null ? 'null (defaults to false)' : ($sessionSecure ? 'true' : 'false'); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">SESSION_DRIVER</td>
                        <td style="padding: 10px; font-family: monospace;"><?php echo htmlspecialchars($sessionDriver); ?></td>
                    </tr>
                </table>
            </div>
            
            <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 6px; padding: 15px; margin-top: 30px; text-align: center;">
                <strong>⚠️ Security Warning:</strong> Delete this file (<code>diagnose-login.php</code>) after fixing the issue!
            </div>
        </div>
    </div>
</body>
</html>


