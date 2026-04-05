<?php
require 'classes/Database.php';
$db = Database::getInstance();

$out = "=== ALL PLANS (ordered by max_houses ASC) ===\n";
$plans = $db->fetchAll("SELECT * FROM subscription_plans ORDER BY max_houses ASC");
foreach ($plans as $p) {
    $out .= "ID:{$p['id']} | name: {$p['name']} | price: {$p['price']} | max_houses: {$p['max_houses']}\n";
}

$out .= "\n=== TENANTS ===\n";
$tenants = $db->fetchAll("SELECT t.id, t.name, t.plan_id, t.subscription_status, p.name as pname, p.price as pprice, p.max_houses as pmax, (SELECT COUNT(*) FROM houses WHERE tenant_id=t.id) as hcount FROM tenants t LEFT JOIN subscription_plans p ON t.plan_id=p.id");
foreach ($tenants as $t) {
    $out .= "ID:{$t['id']} | {$t['name']} | plan_id:{$t['plan_id']} | plan:{$t['pname']} | price:{$t['pprice']} | max:{$t['pmax']} | status:{$t['subscription_status']} | houses:{$t['hcount']}\n";
}

file_put_contents(__DIR__ . '/debug_plans.log', $out);
echo "Done";
