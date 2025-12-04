<?php
/**
 * Batch Update Script - Replace Headers and Footers
 * Mengupdate semua file untuk menggunakan header dan footer yang konsisten
 * 
 * CARA PAKAI:
 * 1. Buka terminal/command prompt
 * 2. Jalankan: php batch-update-headers.php
 * 3. Atau buka di browser: http://localhost/ManchesterSide/batch-update-headers.php
 */

// Jika diakses via browser, tampilkan HTML
$is_browser = php_sapi_name() !== 'cli';

if ($is_browser) {
    echo "<!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Batch Update Headers & Footers</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 1200px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
            .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
            .success { color: #28a745; }
            .error { color: #dc3545; }
            .warning { color: #ffc107; }
            .info { color: #17a2b8; }
            pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background: #f8f9fa; font-weight: bold; }
            .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
            .btn:hover { background: #0056b3; }
        </style>
    </head>
    <body>";
    
    echo "<div class='card'>";
    echo "<h1>🔄 Batch Update Headers & Footers</h1>";
    echo "<p>Script ini akan mengupdate semua file PHP untuk menggunakan header dan footer yang konsisten.</p>";
    echo "</div>";
}

function output($message, $type = 'info') {
    global $is_browser;
    
    $icons = [
        'success' => '✅',
        'error' => '❌',
        'warning' => '⚠️',
        'info' => 'ℹ️'
    ];
    
    $icon = $icons[$type] ?? 'ℹ️';
    
    if ($is_browser) {
        echo "<p class='$type'>$icon $message</p>";
    } else {
        echo "$icon $message\n";
    }
}

if ($is_browser) echo "<div class='card'>";

output("===========================================", 'info');
output("Manchester Side - Batch Header & Footer Update", 'info');
output("===========================================", 'info');
output("", 'info');

// Scan semua file PHP di root directory
$all_php_files = glob('*.php');

// Exclude files yang tidak perlu diupdate
$exclude_files = [
    'batch-update-headers.php',
    'test-connection.php',
    'check-mysql.php',
    'update-headers-footers.php',
    'fix_database.php',
];

// Filter file yang akan diupdate
$files_to_update = array_diff($all_php_files, $exclude_files);

output("File yang akan diupdate:", 'info');
foreach ($files_to_update as $file) {
    output("  - $file", 'info');
}
output("", 'info');

$success_count = 0;
$skip_count = 0;
$error_count = 0;
$results = [];

foreach ($files_to_update as $file) {
    if (!file_exists($file)) {
        output("File tidak ditemukan: $file", 'error');
        $error_count++;
        $results[$file] = ['status' => 'error', 'message' => 'File tidak ditemukan'];
        continue;
    }

    $content = file_get_contents($file);
    $original_content = $content;
    $changed = false;
    $changes = [];

    // Check if already using includes
    $already_has_header = strpos($content, "include 'includes/header.php'") !== false || 
                          strpos($content, 'include "includes/header.php"') !== false;
    $already_has_footer = strpos($content, "include 'includes/footer.php'") !== false || 
                          strpos($content, 'include "includes/footer.php"') !== false;

    // Pattern untuk navbar - lebih fleksibel dan comprehensive
    if (!$already_has_header) {
        $nav_patterns = [
            // Pattern 1: Full nav dengan Navigation comment
            '/<\!-- Navigation -->\s*<nav\s+class="[^"]*"[^>]*>.*?<\/nav>/s',
            // Pattern 2: Nav tanpa comment tapi dengan class bg-white shadow
            '/<nav\s+class="bg-white\s+shadow[^"]*"[^>]*>.*?<\/nav>/s',
            // Pattern 3: Any nav tag
            '/<nav[^>]*class="[^"]*"[^>]*>.*?<\/nav>/s',
        ];

        foreach ($nav_patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "<?php include 'includes/header.php'; ?>", $content, 1);
                $changed = true;
                $changes[] = 'Header replaced';
                break;
            }
        }
    }

    // Pattern untuk footer - lebih fleksibel dan comprehensive
    if (!$already_has_footer) {
        $footer_patterns = [
            // Pattern 1: Full footer dengan Footer comment
            '/<\!-- Footer -->\s*<footer\s+class="[^"]*"[^>]*>.*?<\/footer>/s',
            // Pattern 2: Footer tanpa comment tapi dengan class bg-gray-900
            '/<footer\s+class="bg-gray-900[^"]*"[^>]*>.*?<\/footer>/s',
            // Pattern 3: Any footer tag
            '/<footer[^>]*class="[^"]*"[^>]*>.*?<\/footer>/s',
        ];

        foreach ($footer_patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "<?php include 'includes/footer.php'; ?>", $content, 1);
                $changed = true;
                $changes[] = 'Footer replaced';
                break;
            }
        }
    }

    // Check if content changed
    if ($changed && $content !== $original_content) {
        // Backup original file
        $backup_file = $file . '.backup';
        file_put_contents($backup_file, $original_content);

        // Write updated content
        file_put_contents($file, $content);
        
        output("Berhasil: $file (" . implode(', ', $changes) . ") - backup: $backup_file", 'success');
        $success_count++;
        $results[$file] = ['status' => 'success', 'message' => implode(', ', $changes), 'backup' => $backup_file];
    } else {
        if ($already_has_header && $already_has_footer) {
            output("Dilewati: $file (sudah menggunakan include)", 'info');
            $results[$file] = ['status' => 'skip', 'message' => 'Sudah menggunakan include'];
        } else {
            output("Dilewati: $file (tidak ada perubahan atau tidak ditemukan navbar/footer)", 'warning');
            $results[$file] = ['status' => 'skip', 'message' => 'Tidak ada navbar/footer yang ditemukan'];
        }
        $skip_count++;
    }
}

if ($is_browser) echo "</div>";

// Summary
if ($is_browser) echo "<div class='card'>";

output("", 'info');
output("===========================================", 'info');
output("Update selesai!", 'success');
output("✅ Berhasil diupdate: $success_count file", 'success');
output("⏭️  Dilewati: $skip_count file", 'info');
output("❌ Error: $error_count file", 'error');
output("===========================================", 'info');

if ($is_browser) echo "</div>";

// Detailed Status
if ($is_browser) {
    echo "<div class='card'>";
    echo "<h2>📋 Status Detail</h2>";
    echo "<table>";
    echo "<tr><th>File</th><th>Status</th><th>Keterangan</th></tr>";
    
    foreach ($results as $file => $result) {
        $status_class = $result['status'];
        $status_icon = [
            'success' => '✅',
            'skip' => '⏭️',
            'error' => '❌'
        ][$result['status']];
        
        echo "<tr>";
        echo "<td><strong>$file</strong></td>";
        echo "<td class='$status_class'>$status_icon " . ucfirst($result['status']) . "</td>";
        echo "<td>" . $result['message'];
        if (isset($result['backup'])) {
            echo " <br><small style='color: #666;'>Backup: {$result['backup']}</small>";
        }
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "</div>";
}

// Final verification
if ($is_browser) echo "<div class='card'>";

output("", 'info');
output("📋 Verifikasi Akhir:", 'info');

foreach ($files_to_update as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $has_header = strpos($content, "include 'includes/header.php'") !== false || 
                      strpos($content, 'include "includes/header.php"') !== false;
        $has_footer = strpos($content, "include 'includes/footer.php'") !== false || 
                      strpos($content, 'include "includes/footer.php"') !== false;
        
        if ($has_header && $has_footer) {
            output("  $file: ✅ Header & Footer OK", 'success');
        } elseif ($has_header) {
            output("  $file: ⚠️  Header OK, Footer belum", 'warning');
        } elseif ($has_footer) {
            output("  $file: ⚠️  Footer OK, Header belum", 'warning');
        } else {
            output("  $file: ❌ Belum menggunakan include", 'error');
        }
    }
}

if ($is_browser) echo "</div>";

// Notes
if ($is_browser) echo "<div class='card'>";

output("", 'info');
output("💡 Catatan:", 'info');
output("- File backup disimpan dengan ekstensi .backup", 'info');
output("- Silakan test semua halaman di browser", 'info');
output("- Hapus file .backup setelah yakin update berhasil", 'info');
output("- Jika ada masalah, restore dari file backup", 'info');

if ($is_browser) {
    echo "</div>";
    
    echo "<div class='card' style='text-align: center;'>";
    echo "<h3>🎉 Selesai!</h3>";
    echo "<p>Sekarang test website Anda:</p>";
    echo "<a href='index.php' class='btn'>🏠 Buka Website</a>";
    echo "<a href='javascript:location.reload()' class='btn'>🔄 Refresh</a>";
    echo "</div>";
    
    echo "</body></html>";
}
?>
