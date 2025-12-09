# Získání reference na zásilku
```
<?php
$numberOfShipments = 1; 
$packageReferences = apply_filters("pplcz_package_reference", [], $woocommerce_order_id, $numberOfShipments); // pole řetězců
?>
```
$packageReferences obsahuje seznam referencí. Může být voláno několikrát (měl by vracet stejné výsledky pro stejný vstup)

# Získání variabilního symbolu v případě dobírky
```
<?php
$variableNumber = apply_filters("pplcz_variable_number", "", $woocommerce_order_id); // řetězec
?>
```
$packageReferences obsahuje seznam referencí. Může být voláno několikrát (měl by vracet stejné výsledky pro stejný vstup)

# Fáze zásilky

* \`Order` => Objednávka
* \`InTransport` => V přepravě
* \`Delivering` => Na cestě
* \`PickupPoint` => Na výdejním místě
* \`Delivered` => Doručeno
* \`Returning` => Na cestě zpět odesílateli
* \`BackToSender` => Vráceno odesílateli
* \`Deleted` => "Zrušená zásilka"

Když se jen informuje o aktuálním stavu (nedošlo ke změně), ale byl zjišťován
```
<?php
do_action("pplcz_package_phase", [
           "orderId" => 123,
           "packageNumber" => "123",
           "phase" => "Order",
           "phase_label" => "Objednávka",
]);
?>
```

Při načtení stavu zásilky se změněnou fází (např Objednáno -> Doručeno)
```
<?php
do_action("pplcz_package_change_phase", [
           "orderId" => 123,
           "packageNumber" => "123",
           "phase" => "Order",
           "phase_label" => "Objednávka",
]);
?>
```

Při vytvoření zásilky
```
<?php
do_action("pplcz_package", [
           "orderId" => 123,
           "packageNumber" => "123",
]);
?>
```
