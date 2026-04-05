<?php
// Generate password hashes and update seed.sql
$hash_password = password_hash('password', PASSWORD_BCRYPT);
$hash_warga = password_hash('warga123', PASSWORD_BCRYPT);

echo "password hash: $hash_password\n";
echo "warga123 hash: $hash_warga\n";

// Read seed.sql and replace placeholder hashes
$seed = file_get_contents(__DIR__ . '/sql/seed.sql');

// Replace the admin hash (first user)
$seed = preg_replace(
    "/\('Admin Satu'.*?'\\\$2y\\\$10\\\$[^']+'/",
    "('Admin Satu', 'admin@wargaku.id', '081234567890', '$hash_password'",
    $seed,
    1
);

// Replace all other user hashes
$seed = preg_replace(
    "/'\\\$2y\\\$10\\\$LKaJMz7JG5ZKtVL6bSODdu3O5GPjEH1SvFQ0NP\.PXjUBDaHVaO6Gy'/",
    "'$hash_warga'",
    $seed
);

file_put_contents(__DIR__ . '/sql/seed.sql', $seed);
echo "seed.sql updated!\n";
