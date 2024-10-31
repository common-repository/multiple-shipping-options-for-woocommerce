<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Composer\\InstalledVersions' => $vendorDir . '/composer/InstalledVersions.php',
    'MsoConnection\\MsoConnection' => $baseDir . '/admin/tab/server/connection.php',
    'MsoCsv\\MsoCsv' => $baseDir . '/admin/csv/csv.php',
    'MsoDhlShipment\\MsoDhlShipment' => $baseDir . '/admin/order/carriers/spq/dhl.php',
    'MsoDhl\\MsoDhl' => $baseDir . '/admin/tab/settings/carriers/spq/dhl/dhl.php',
    'MsoFedexFreight\\MsoFedexFreight' => $baseDir . '/admin/tab/settings/carriers/lfq/fedex/fedex.php',
    'MsoFedexLfqShipment\\MsoFedexLfqShipment' => $baseDir . '/admin/order/carriers/lfq/fedex.php',
    'MsoFedexShipment\\MsoFedexShipment' => $baseDir . '/admin/order/carriers/spq/fedex.php',
    'MsoFedex\\MsoFedex' => $baseDir . '/admin/tab/settings/carriers/spq/fedex/fedex.php',
    'MsoLfq\\MsoLfq' => $baseDir . '/admin/tab/settings/carriers/lfq/lfq.php',
    'MsoLogs\\MsoLogs' => $baseDir . '/admin/tab/settings/logs/logs.php',
    'MsoOrderShippingOptions\\MsoOrderShippingOptions' => $baseDir . '/admin/order/shipping-options.php',
    'MsoOrder\\MsoOrder' => $baseDir . '/admin/order/order.php',
    'MsoPackage\\MsoPackage' => $baseDir . '/shipping/package/package.php',
    'MsoPackagingAjax\\MsoPackagingAjax' => $baseDir . '/admin/tab/settings/packaging/packaging-ajax.php',
    'MsoPackaging\\MsoPackaging' => $baseDir . '/admin/tab/settings/packaging/packaging.php',
    'MsoPrerequisites\\MsoPrerequisites' => $baseDir . '/prerequisites/prerequisites.php',
    'MsoProductAjax\\MsoProductAjax' => $baseDir . '/admin/product/product-ajax.php',
    'MsoProductDetail\\MsoProductDetail' => $baseDir . '/admin/product/product-detail.php',
    'MsoSettingsAjax\\MsoSettingsAjax' => $baseDir . '/admin/tab/settings/settings-ajax.php',
    'MsoSettings\\MsoSettings' => $baseDir . '/admin/tab/settings/settings.php',
    'MsoShipping' => $baseDir . '/shipping/shipping.php',
    'MsoShippingInit' => $baseDir . '/shipping/shipping.php',
    'MsoSpq\\MsoSpq' => $baseDir . '/admin/tab/settings/carriers/spq/spq.php',
    'MsoTab' => $baseDir . '/admin/tab/tab.php',
    'MsoUpsFreight\\MsoUpsFreight' => $baseDir . '/admin/tab/settings/carriers/lfq/ups/ups.php',
    'MsoUpsLfqShipment\\MsoUpsLfqShipment' => $baseDir . '/admin/order/carriers/lfq/ups.php',
    'MsoUpsShipment\\MsoUpsShipment' => $baseDir . '/admin/order/carriers/spq/ups.php',
    'MsoUps\\MsoUps' => $baseDir . '/admin/tab/settings/carriers/spq/ups/ups.php',
    'MsoUspsShipment\\MsoUspsShipment' => $baseDir . '/admin/order/carriers/spq/usps.php',
    'MsoUsps\\MsoUsps' => $baseDir . '/admin/tab/settings/carriers/spq/usps/usps.php',
    'ShippingSettings\\ShippingSettings' => $baseDir . '/shipping/package/shipping-settings.php',
    'WasaioCurl\\WasaioCurl' => $baseDir . '/http/curl.php',
    'WasaioReceiverAddress\\WasaioReceiverAddress' => $baseDir . '/shipping/package/receiver-address.php',
);
