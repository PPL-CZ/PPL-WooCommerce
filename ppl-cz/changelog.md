# 1.0.11
- úprava filtrace metod dopravy
- oprava přepočtu při výběru jiné měny
# 1.0.12
- odstranění bank account
- opravy
# 1.0.13
- oprava problému s výběrem u mapy při změně země
- oprava chování naplánovaných akcí
- drobné opravy
# 1.0.14
- možnost určení výdejního místa u produktu a kategorie
# 1.0.15
- oprava stahování souboru v případě problému stahování souboru
# 1.0.16
- oprava příplatku na dobírku v případě různých měn a DPH
# 1.0.17
- oprava hlášky v rácmi chybějících nebo špatných údajů
# 1.0.18
- upravení chování košíku a pokladny v react komponentách
# 1.0.19
- v případě, že je doprava zdarma, je vypsáno "zdarma" u ppl dopravy
- změna názvu ident. údajů pro napojení na ppl.cz api
# 1.0.20
- přepočítávání dph, pokud je $shipment->is_taxable() (issue: 2);
- odstranění warningu (isset místo @) (issue: 5, 6, 7, 8)
# 1.0.21
- možnost ceny dopravy podle váhy
# 1.0.22
- možnost povolení/zakázání mapy v rámci dopravy či globálně
- možnost odeslání info o pluginu
# 1.0.23
- oprava uložení výdejního místa v novém košíku
# 1.0.24
- oprava inicializace pluginu
# 1.0.25
- úprava logování
# 1.0.26
- chybějící kontakt ve vytištěném labelu
# 1.0.27
- zrušení svozu
# 1.0.28
- uhlazení logování
- chování smart v případě, že zona je nastavená na "všude"
# 1.0.29
- přidání informaci v ramci logu u objednávky 
- fix výpočtu ceny dopravy
# 1.0.30
- odstranění svozu v rámci gridů mimo objednávky
# 1.0.31
- přidání řazení objednávek pro tisk
# 1.0.32
- přizpůsobení pluginu stripe, změny stavu objednávek na základě změn stavu zásilky
# 1.0.33
- angličtina v rámci košíku, stavy zásilky volány pomocí do_action - více v action.md
# 1.0.34
- přidána nová dodací metoda SBOX - Dodání do parcelboxu (jen)
- pro tisk možnost vybrat stavy objednávek, které budou zahrnuty
- v tabulce je zobrazena informace o neukončných balíčcích (vytvořeny, ale neobjednány u PPL.CZ)
- přidána kontrola balíku podle velikost a počtu a možnost u produktu nastavit, že lze poslat více balíky
- kontrola velikost jména zákazníka (kontaktu)