<?php
/**
 * Cross-Platform Email Sync System Test
 * 
 * This script tests the email sync system configuration
 * and validates that all components are working correctly.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        api: __DIR__.'/routes/api.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Cross-Platform Email Sync System Test ===\n\n";

// Test 1: Check operating system detection
echo "1. Operating System Detection:\n";
echo "   OS: " . PHP_OS . "\n";
echo "   Is Windows: " . (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'Yes' : 'No') . "\n\n";

// Test 2: Check configuration loading
echo "2. Configuration Loading:\n";
try {
    $config = config('mail_sync');
    echo "   ✅ Mail sync config loaded successfully\n";
    echo "   Sync timeout: " . ($config['sync_timeout'] ?? 'Not set') . "\n";
    echo "   Max sync limit: " . ($config['max_sync_limit'] ?? 'Not set') . "\n";
} catch (Exception $e) {
    echo "   ❌ Failed to load mail sync config: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Check Python script directory detection
echo "3. Python Script Directory Detection:\n";
try {
    $scriptDir = config('mail_sync.python_script_dir') ?? env('MAIL_PYTHON_SCRIPT_DIR', base_path('python_outlook_web'));
    echo "   Script directory: $scriptDir\n";
    
    if (is_dir($scriptDir)) {
        echo "   ✅ Script directory exists\n";
    } else {
        echo "   ❌ Script directory not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error detecting script directory: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Check Python executable detection
echo "4. Python Executable Detection:\n";
try {
    // Simulate the controller's Python path detection
    $customPath = config('mail_sync.python_executable') ?? env('MAIL_PYTHON_EXECUTABLE');
    if ($customPath && file_exists($customPath)) {
        $pythonPath = $customPath;
        echo "   ✅ Custom Python path found: $pythonPath\n";
    } else {
        $scriptDir = config('mail_sync.python_script_dir') ?? env('MAIL_PYTHON_SCRIPT_DIR', base_path('python_outlook_web'));
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $pythonPath = $scriptDir . '/' . config('mail_sync.windows.python_executable', 'venv/Scripts/python.exe');
        } else {
            $pythonPath = $scriptDir . '/' . config('mail_sync.linux.python_executable', 'venv/bin/python');
        }
        
        echo "   Auto-detected Python path: $pythonPath\n";
        
        if (file_exists($pythonPath)) {
            echo "   ✅ Python executable exists\n";
        } else {
            echo "   ❌ Python executable not found\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Error detecting Python path: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Check sync script detection
echo "5. Sync Script Detection:\n";
try {
    $scriptDir = config('mail_sync.python_script_dir') ?? env('MAIL_PYTHON_SCRIPT_DIR', base_path('python_outlook_web'));
    
    $scripts = [
        'sync_emails_optimized.py',
        'sync_emails_simple.py',
        'sync_emails.py'
    ];
    
    $foundScript = null;
    foreach ($scripts as $script) {
        $scriptPath = $scriptDir . '/' . $script;
        if (file_exists($scriptPath)) {
            $foundScript = $scriptPath;
            echo "   ✅ Found sync script: $script\n";
            break;
        }
    }
    
    if (!$foundScript) {
        echo "   ❌ No sync scripts found in: $scriptDir\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error detecting sync script: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 6: Check send mail script detection
echo "6. Send Mail Script Detection:\n";
try {
    $scriptDir = config('mail_sync.python_script_dir') ?? env('MAIL_PYTHON_SCRIPT_DIR', base_path('python_outlook_web'));
    $sendMailScript = $scriptDir . '/send_mail.py';
    
    if (file_exists($sendMailScript)) {
        echo "   ✅ Send mail script found: $sendMailScript\n";
    } else {
        echo "   ❌ Send mail script not found: $sendMailScript\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error detecting send mail script: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 7: Check runner script detection
echo "7. Runner Script Detection:\n";
try {
    $customRunner = config('mail_sync.python_runner_script') ?? env('MAIL_PYTHON_RUNNER_SCRIPT');
    if ($customRunner && file_exists($customRunner)) {
        echo "   ✅ Custom runner script found: $customRunner\n";
    } else {
        $scriptDir = config('mail_sync.python_script_dir') ?? env('MAIL_PYTHON_SCRIPT_DIR', base_path('python_outlook_web'));
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $runnerPath = $scriptDir . '/' . config('mail_sync.windows.python_runner_script', 'run_python.bat');
        } else {
            $runnerPath = $scriptDir . '/' . config('mail_sync.linux.python_runner_script', 'run_python.sh');
        }
        
        echo "   Auto-detected runner script: $runnerPath\n";
        
        if (file_exists($runnerPath)) {
            echo "   ✅ Runner script exists\n";
        } else {
            echo "   ❌ Runner script not found\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Error detecting runner script: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 8: Check working directory
echo "8. Working Directory Detection:\n";
try {
    $customDir = config('mail_sync.python_script_dir') ?? env('MAIL_PYTHON_SCRIPT_DIR');
    if ($customDir && is_dir($customDir)) {
        $workingDir = $customDir;
        echo "   ✅ Custom working directory: $workingDir\n";
    } else {
        $workingDir = base_path('python_outlook_web');
        echo "   Default working directory: $workingDir\n";
    }
    
    if (is_dir($workingDir)) {
        echo "   ✅ Working directory exists\n";
    } else {
        echo "   ❌ Working directory not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error detecting working directory: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 9: Check environment variables
echo "9. Environment Variables:\n";
$envVars = config('mail_sync.python_env_vars', []);
echo "   PYTHONIOENCODING: " . ($envVars['PYTHONIOENCODING'] ?? 'Not set') . "\n";
echo "   LANG: " . ($envVars['LANG'] ?? 'Not set') . "\n";
echo "   LC_ALL: " . ($envVars['LC_ALL'] ?? 'Not set') . "\n";
echo "\n";

// Test 10: Check storage directories
echo "10. Storage Directories:\n";
$storageDirs = config('mail_sync.storage_directories', []);
foreach ($storageDirs as $name => $path) {
    if (is_dir($path)) {
        echo "   ✅ $name: $path\n";
    } else {
        echo "   ❌ $name: $path (not found)\n";
    }
}
echo "\n";

echo "=== Test Complete ===\n";
echo "If all tests show ✅, your email sync system is properly configured.\n";
echo "If any tests show ❌, please check the configuration and file paths.\n";
