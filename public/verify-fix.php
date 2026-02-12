<?php
/**
 * CSRF Fix Verification Script
 * 
 * Visit: http://primelandhotel.co.tz/verify-fix.php
 * DELETE THIS FILE AFTER VERIFICATION FOR SECURITY
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>CSRF Fix Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .ok { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 CSRF Fix Verification Report</h1>
        
        <?php
        $issues = [];
        $warnings = [];
        $checks = [];
        
        // Check 1: APP_URL Configuration
        $appUrl = config('app.url');
        $appUrlScheme = $appUrl ? parse_url($appUrl, PHP_URL_SCHEME) : null;
        $currentScheme = request()->isSecure() ? 'https' : 'http';
        
        $checks[] = [
            'name' => 'APP_URL Configuration',
            'value' => $appUrl ?: 'NOT SET',
            'status' => $appUrl ? 'ok' : 'error',
            'note' => $appUrl ? "Scheme: {$appUrlScheme}" : 'APP_URL must be set in .env'
        ];
        
        if (!$appUrl) {
            $issues[] = 'APP_URL is not set in .env file';
        }
        
        // Check 2: Protocol Match
        $protocolMatch = ($appUrlScheme === $currentScheme);
        $checks[] = [
            'name' => 'Protocol Match',
            'value' => $protocolMatch ? 'MATCH ✓' : 'MISMATCH ✗',
            'status' => $protocolMatch ? 'ok' : 'error',
            'note' => "APP_URL uses {$appUrlScheme}, but request is {$currentScheme}"
        ];
        
        if (!$protocolMatch) {
            $issues[] = "Protocol mismatch: APP_URL uses {$appUrlScheme} but site is accessed via {$currentScheme}";
        }
        
        // Check 3: Session Secure Cookie
        $sessionSecure = config('session.secure');
        $shouldBeSecure = ($currentScheme === 'https');
        $secureMatch = ($sessionSecure === $shouldBeSecure || ($sessionSecure === null && !$shouldBeSecure));
        
        $checks[] = [
            'name' => 'SESSION_SECURE_COOKIE',
            'value' => $sessionSecure === null ? 'null (defaults to false)' : ($sessionSecure ? 'true' : 'false'),
            'status' => $secureMatch ? 'ok' : 'warning',
            'note' => $shouldBeSecure ? 'Should be true for HTTPS' : 'Should be false for HTTP'
        ];
        
        if (!$secureMatch) {
            $warnings[] = "SESSION_SECURE_COOKIE should be " . ($shouldBeSecure ? 'true' : 'false') . " for {$currentScheme}";
        }
        
        // Check 4: Session Domain
        $sessionDomain = config('session.domain');
        $checks[] = [
            'name' => 'SESSION_DOMAIN',
            'value' => $sessionDomain ?: 'null (defaults to current domain)',
            'status' => $sessionDomain ? 'ok' : 'warning',
            'note' => $sessionDomain ? 'Set correctly' : 'Consider setting to .primelandhotel.co.tz for www/non-www support'
        ];
        
        if (!$sessionDomain) {
            $warnings[] = 'SESSION_DOMAIN not set - cookies may not work across www/non-www';
        }
        
        // Check 5: Session Driver
        $sessionDriver = config('session.driver');
        $checks[] = [
            'name' => 'Session Driver',
            'value' => $sessionDriver,
            'status' => ($sessionDriver === 'database' || $sessionDriver === 'file') ? 'ok' : 'warning',
            'note' => $sessionDriver === 'database' ? 'Using database sessions' : 'Using file sessions'
        ];
        
        // Check 6: CSRF Token Generation Test
        try {
            $csrfToken = csrf_token();
            $checks[] = [
                'name' => 'CSRF Token Generation',
                'value' => 'WORKING ✓',
                'status' => 'ok',
                'note' => 'Token: ' . substr($csrfToken, 0, 20) . '...'
            ];
        } catch (\Exception $e) {
            $checks[] = [
                'name' => 'CSRF Token Generation',
                'value' => 'FAILED ✗',
                'status' => 'error',
                'note' => $e->getMessage()
            ];
            $issues[] = 'CSRF token generation failed: ' . $e->getMessage();
        }
        
        // Check 7: Session Working
        try {
            $sessionId = session()->getId();
            $checks[] = [
                'name' => 'Session Working',
                'value' => 'WORKING ✓',
                'status' => 'ok',
                'note' => 'Session ID: ' . substr($sessionId, 0, 20) . '...'
            ];
        } catch (\Exception $e) {
            $checks[] = [
                'name' => 'Session Working',
                'value' => 'FAILED ✗',
                'status' => 'error',
                'note' => $e->getMessage()
            ];
            $issues[] = 'Session not working: ' . $e->getMessage();
        }
        
        // Summary
        $allOk = empty($issues);
        ?>
        
        <div class="status <?php echo $allOk ? 'ok' : 'error'; ?>">
            <strong>Overall Status:</strong> <?php echo $allOk ? '✓ All checks passed!' : '✗ Issues found - see details below'; ?>
        </div>
        
        <?php if (!empty($issues)): ?>
        <div class="status error">
            <strong>Critical Issues:</strong>
            <ul>
                <?php foreach ($issues as $issue): ?>
                    <li><?php echo htmlspecialchars($issue); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($warnings)): ?>
        <div class="status warning">
            <strong>Warnings:</strong>
            <ul>
                <?php foreach ($warnings as $warning): ?>
                    <li><?php echo htmlspecialchars($warning); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <h2>Detailed Checks</h2>
        <table>
            <thead>
                <tr>
                    <th>Check</th>
                    <th>Value</th>
                    <th>Status</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checks as $check): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($check['name']); ?></strong></td>
                    <td><span class="code"><?php echo htmlspecialchars($check['value']); ?></span></td>
                    <td>
                        <span class="status <?php echo $check['status']; ?>">
                            <?php 
                            echo $check['status'] === 'ok' ? '✓ OK' : 
                                ($check['status'] === 'warning' ? '⚠ Warning' : '✗ Error');
                            ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($check['note']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="status info">
            <strong>Recommendations:</strong>
            <ol>
                <li>Ensure <code class="code">APP_URL</code> in .env matches your actual domain and protocol</li>
                <li>Set <code class="code">SESSION_SECURE_COOKIE</code> to <code class="code">false</code> for HTTP sites, <code class="code">true</code> for HTTPS</li>
                <li>Set <code class="code">SESSION_DOMAIN=.primelandhotel.co.tz</code> (with leading dot)</li>
                <li>Clear all Laravel caches after making .env changes</li>
                <li>Clear browser cookies and test in incognito mode</li>
            </ol>
        </div>
        
        <div class="status warning">
            <strong>⚠ Security Warning:</strong> Delete this file (<code class="code">verify-fix.php</code>) after verification!
        </div>
    </div>
</body>
</html>


