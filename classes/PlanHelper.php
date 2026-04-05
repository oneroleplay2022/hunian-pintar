<?php
/**
 * Auto-assign subscription plan based on house count.
 * 
 * Logic: Plans are ordered by max_houses ASC.
 * The tenant gets the smallest plan whose max_houses >= their house count.
 * If they exceed ALL plans, they get the highest plan available.
 * 
 * Call this after inserting or deleting a house.
 * 
 * @param int $tenant_id
 * @return array ['changed' => bool, 'old_plan' => string, 'new_plan' => string, 'house_count' => int]
 */
function autoAssignPlan(int $tenant_id): array {
    $db = Database::getInstance();
    
    // Count current houses for this tenant
    $houseCount = (int) $db->fetchColumn("SELECT COUNT(*) FROM houses WHERE tenant_id = ?", [$tenant_id]);
    
    // Get current tenant plan
    $tenant = $db->fetch("SELECT t.plan_id, p.name as current_plan_name FROM tenants t LEFT JOIN subscription_plans p ON t.plan_id = p.id WHERE t.id = ?", [$tenant_id]);
    
    if (!$tenant) {
        return ['changed' => false, 'old_plan' => '-', 'new_plan' => '-', 'house_count' => 0];
    }
    
    $oldPlanId = $tenant['plan_id'];
    $oldPlanName = $tenant['current_plan_name'] ?? 'Tidak Ada';
    
    // Find the appropriate plan: smallest plan where max_houses >= houseCount
    // Example: Lite(50), Pro(100), Business(200)
    //   - 1-50 houses  → Lite
    //   - 51-100 houses → Pro
    //   - 101-200 houses → Business
    $matchedPlan = $db->fetch(
        "SELECT id, name, max_houses FROM subscription_plans WHERE max_houses >= ? ORDER BY max_houses ASC LIMIT 1",
        [max(1, $houseCount)] // min 1 so that 0 houses still gets the smallest plan
    );
    
    // If no plan covers this many houses, assign the highest plan available
    if (!$matchedPlan) {
        $matchedPlan = $db->fetch(
            "SELECT id, name, max_houses FROM subscription_plans ORDER BY max_houses DESC LIMIT 1"
        );
    }
    
    // If still no plan (no plans exist at all), skip
    if (!$matchedPlan) {
        return ['changed' => false, 'old_plan' => $oldPlanName, 'new_plan' => $oldPlanName, 'house_count' => $houseCount];
    }
    
    $newPlanId = $matchedPlan['id'];
    $newPlanName = $matchedPlan['name'];
    
    // Only update if plan actually changed
    if ((int)$oldPlanId !== (int)$newPlanId) {
        $db->update('tenants', ['plan_id' => $newPlanId], 'id = ?', [$tenant_id]);
        
        return [
            'changed' => true,
            'old_plan' => $oldPlanName,
            'new_plan' => $newPlanName,
            'house_count' => $houseCount
        ];
    }
    
    return [
        'changed' => false,
        'old_plan' => $oldPlanName,
        'new_plan' => $newPlanName,
        'house_count' => $houseCount
    ];
}

/**
 * Get the monthly subscription price for a tenant based on their active plan.
 * 
 * Looks up tenants.plan_id → subscription_plans.price.
 * Falls back to the cheapest plan's price, or 0 if no plans exist.
 * 
 * @param int $tenant_id
 * @return float The monthly price in IDR
 */
function getTenantPlanPrice(int $tenant_id): float {
    $db = Database::getInstance();
    
    // Try to get price from tenant's assigned plan
    $price = $db->fetchColumn(
        "SELECT p.price FROM tenants t JOIN subscription_plans p ON t.plan_id = p.id WHERE t.id = ?",
        [$tenant_id]
    );
    
    if ($price !== false && $price !== null) {
        return (float) $price;
    }
    
    // Fallback: get the cheapest plan price (for tenants without a plan yet)
    $fallback = $db->fetchColumn("SELECT price FROM subscription_plans ORDER BY price ASC LIMIT 1");
    
    return $fallback ? (float) $fallback : 0;
}

/**
 * Re-evaluate ALL tenants' plans.
 * 
 * Call this after adding, editing, or deleting a plan on saas_pricing.php.
 * Each tenant's plan will be recalculated based on their current house count
 * and the latest subscription_plans data.
 * 
 * @return array ['total' => int, 'changed' => int, 'details' => array]
 */
function reassignAllTenantPlans(): array {
    $db = Database::getInstance();
    
    $tenants = $db->fetchAll("SELECT id FROM tenants");
    $totalChanged = 0;
    $details = [];
    
    foreach ($tenants as $t) {
        $result = autoAssignPlan($t['id']);
        if ($result['changed']) {
            $totalChanged++;
            $details[] = $result;
        }
    }
    
    return [
        'total' => count($tenants),
        'changed' => $totalChanged,
        'details' => $details
    ];
}

