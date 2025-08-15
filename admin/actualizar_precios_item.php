<?php
require_once '../_init.php';

// Verifica que el usuario tenga permisos
if (!is_loggedin()) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
    exit;
}

if (user_group_id() != 1 && !has_permission('access', 'update_item_cost')) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Permiso denegado']);
    exit;
}

$store_id = store_id();

try {
    $statement = $db->prepare("
        UPDATE selling_item si
        JOIN purchase_item pi 
        ON si.purchase_invoice_id = pi.invoice_id
        AND si.item_id = pi.item_id
        AND si.store_id = pi.store_id
        SET si.item_purchase_price = pi.item_purchase_price
        WHERE si.store_id = ?
    ");
    $statement->execute([$store_id]);

    echo json_encode(['status' => 'success', 'message' => 'Precios de compra actualizados correctamente.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar: ' . $e->getMessage()]);
}
