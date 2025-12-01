<?php
namespace PPLCZ\Utils;
class BoxPacker {

    private $boxes = [];
    private $items = [];
    private $maxPackages = null;
    private $stackingMode = 'all';

    public function setMaxPackages($max) {
        $this->maxPackages = $max;
        return $this;
    }

    public function setStackingMode($mode) {
        $this->stackingMode = in_array($mode, ['all', 'same', 'none']) ? $mode : 'all';
        return $this;
    }

    public function addBox($name, $dim1, $dim2, $dim3, $padding = 0) {
        $outer = [$dim1, $dim2, $dim3];
        rsort($outer);

        $inner = [
            max(0, $outer[0] - (2 * $padding)),
            max(0, $outer[1] - (2 * $padding)),
            max(0, $outer[2] - (2 * $padding)),
        ];

        if ($inner[0] <= 0 || $inner[1] <= 0 || $inner[2] <= 0) {
            return $this;
        }

        $this->boxes[] = [
            'name' => $name,
            'outer' => $outer,
            'inner' => $inner,
            'padding' => $padding,
            'inner_volume' => $inner[0] * $inner[1] * $inner[2],
        ];

        return $this;
    }

    public function addItem($name, $dim1, $dim2, $dim3, $qty = 1) {
        $dims = [$dim1, $dim2, $dim3];
        rsort($dims);

        $this->items[] = [
            'name' => $name,
            'dims' => $dims,
            'qty' => $qty,
            'volume' => $dims[0] * $dims[1] * $dims[2],
        ];

        return $this;
    }

    public function clearItems() {
        $this->items = [];
        return $this;
    }

    public function pack() {
        if (empty($this->boxes)) {
            return $this->errorResult('Nejsou definovány žádné krabice');
        }

        if (empty($this->items)) {
            return $this->errorResult('Nejsou žádné položky k zabalení');
        }

        $boxes = $this->boxes;
        usort($boxes, function($a, $b) {
            return $a['inner_volume'] <=> $b['inner_volume'];
        });

        $allItems = $this->expandItems();

        $largestBox = end($boxes);
        foreach ($allItems as $item) {
            if (!$this->itemFitsInBox($item['dims'], $largestBox['inner'])) {
                return $this->errorResult(
                    sprintf('Položka "%s" (%s) se nevejde do žádné krabice',
                        $item['name'],
                        $this->formatDimensions($item['dims'])
                    ),
                    [$item]
                );
            }
        }

        $bestResult = null;
        $bestScore = -PHP_INT_MAX;

        $strategies = $this->generateStrategies($allItems);

        foreach ($strategies as $strategy) {
            $result = $this->tryPackWithStrategy($boxes, $strategy['items'], $strategy['name']);

            if ($result['success']) {
                $score = $this->calculateOverallScore($result);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestResult = $result;
                }
            } elseif ($bestResult === null || count($result['boxes']) > count($bestResult['boxes'])) {
                $bestResult = $result;
            }
        }

        if ($bestResult === null) {
            return $this->errorResult('Nepodařilo se zabalit položky', $allItems);
        }

        $bestResult['summary'] = $this->calculateSummary($bestResult['boxes']);

        return $bestResult;
    }

    /**
     * Generuje různé strategie řazení položek
     */
    private function generateStrategies($items) {
        $strategies = [];

        // Od největšího objemu
        $byVolume = $items;
        usort($byVolume, function($a, $b) {
            return $b['volume'] <=> $a['volume'];
        });
        $strategies[] = ['name' => 'by_volume_desc', 'items' => $byVolume];

        // Od nejmenšího objemu
        $byVolumeAsc = $items;
        usort($byVolumeAsc, function($a, $b) {
            return $a['volume'] <=> $b['volume'];
        });
        $strategies[] = ['name' => 'by_volume_asc', 'items' => $byVolumeAsc];

        // Od největší plochy podstavy
        $byFootprint = $items;
        usort($byFootprint, function($a, $b) {
            $aFoot = $a['dims'][0] * $a['dims'][1];
            $bFoot = $b['dims'][0] * $b['dims'][1];
            return $bFoot <=> $aFoot;
        });
        $strategies[] = ['name' => 'by_footprint', 'items' => $byFootprint];

        // Od největšího rozměru
        $byMaxDim = $items;
        usort($byMaxDim, function($a, $b) {
            return $b['dims'][0] <=> $a['dims'][0];
        });
        $strategies[] = ['name' => 'by_max_dim', 'items' => $byMaxDim];

        // Od nejmenší výšky
        $byHeight = $items;
        usort($byHeight, function($a, $b) {
            return $a['dims'][2] <=> $b['dims'][2];
        });
        $strategies[] = ['name' => 'by_height_asc', 'items' => $byHeight];

        // Seskupit stejné položky
        $byName = $items;
        usort($byName, function($a, $b) {
            $cmp = strcmp($a['name'], $b['name']);
            return $cmp !== 0 ? $cmp : $b['volume'] <=> $a['volume'];
        });
        $strategies[] = ['name' => 'by_name', 'items' => $byName];

        // Náhodné strategie
        for ($i = 0; $i < 5; $i++) {
            $shuffled = $items;
            shuffle($shuffled);
            $strategies[] = ['name' => 'random_' . $i, 'items' => $shuffled];
        }

        return $strategies;
    }

    /**
     * Zkusí zabalit s danou strategií
     */
    private function tryPackWithStrategy($boxes, $items, $strategyName) {
        $packedBoxes = [];
        $remaining = $items;

        while (!empty($remaining)) {
            if ($this->maxPackages !== null && count($packedBoxes) >= $this->maxPackages) {
                return [
                    'success' => false,
                    'error' => sprintf('Překročen limit %d balíků', $this->maxPackages),
                    'boxes' => $packedBoxes,
                    'total_boxes' => count($packedBoxes),
                    'unpacked' => $this->summarizeItems($remaining),
                    'strategy' => $strategyName,
                ];
            }

            $bestPack = $this->findBestPack($boxes, $remaining);

            if ($bestPack === null || $bestPack['result']['items_count'] === 0) {
                return [
                    'success' => false,
                    'error' => 'Nepodařilo se zabalit všechny položky',
                    'boxes' => $packedBoxes,
                    'total_boxes' => count($packedBoxes),
                    'unpacked' => $this->summarizeItems($remaining),
                    'strategy' => $strategyName,
                ];
            }

            $packedBoxes[] = $bestPack['result'];

            $packedIds = $bestPack['packed_ids'];
            $remaining = array_values(array_filter($remaining, function($item) use ($packedIds) {
                return !in_array($item['id'], $packedIds);
            }));
        }

        return [
            'success' => true,
            'boxes' => $packedBoxes,
            'total_boxes' => count($packedBoxes),
            'total_items' => count($items),
            'strategy' => $strategyName,
        ];
    }

    /**
     * Najde nejlepší balík s důrazem na efektivitu
     */
    private function findBestPack($boxes, $items) {
        $best = null;

        foreach ($boxes as $box) {
            $packed = $this->packIntoBoxMaxRects($box, $items);

            if ($packed['count'] === 0) {
                continue;
            }

            $metrics = $packed['metrics'];

            // Skóre kombinuje všechny efektivity + počet položek
            $score = ($metrics['overall_efficiency'] * $metrics['overall_efficiency'])
                + ($packed['count'] * 100);

            if ($best === null || $score > $best['score']) {
                $best = $this->createPackResult($box, $packed, $score);
            }
        }

        return $best;
    }

    /**
     * MaxRects algoritmus pro 3D bin packing
     */
    private function packIntoBoxMaxRects($box, $items) {
        $boxL = $box['inner'][0];
        $boxW = $box['inner'][1];
        $boxH = $box['inner'][2];

        $placements = [];
        $packed = [];

        $freeRects = [
            ['x' => 0, 'y' => 0, 'z' => 0, 'l' => $boxL, 'w' => $boxW, 'h' => $boxH]
        ];

        foreach ($items as $item) {
            $bestPlacement = null;
            $bestScore = PHP_INT_MAX;
            $bestRectIndex = null;

            foreach ($freeRects as $rectIndex => $rect) {
                if (!$this->canPlaceInRect($rect, $item, $placements)) {
                    continue;
                }

                $rotations = $this->getRotations($item['dims']);

                foreach ($rotations as $rotation) {
                    $l = $rotation[0];
                    $w = $rotation[1];
                    $h = $rotation[2];

                    if ($l > $rect['l'] || $w > $rect['w'] || $h > $rect['h']) {
                        continue;
                    }

                    // Simulovat umístění a vypočítat efektivitu tvaru
                    $testPlacements = $placements;
                    $testPlacements[] = [
                        'x' => $rect['x'],
                        'y' => $rect['y'],
                        'z' => $rect['z'],
                        'l' => $l,
                        'w' => $w,
                        'h' => $h,
                    ];

                    $testBbox = $this->calculateBoundingBox($testPlacements);
                    $shapeEff = $this->calculateShapeEfficiency(
                        $testBbox['l'], $testBbox['w'], $testBbox['h']
                    );

                    // Preferovat vyšší efektivitu tvaru a nižší pozice
                    $score = (100 - $shapeEff) * 100 + ($rect['z'] * 10);

                    if ($score < $bestScore) {
                        $bestScore = $score;
                        $bestPlacement = [
                            'x' => $rect['x'],
                            'y' => $rect['y'],
                            'z' => $rect['z'],
                            'l' => $l,
                            'w' => $w,
                            'h' => $h,
                            'name' => $item['name'],
                        ];
                        $bestRectIndex = $rectIndex;
                    }
                }
            }

            if ($bestPlacement !== null) {
                $placements[] = $bestPlacement;
                $packed[] = $item;

                $freeRects = $this->splitFreeRects($freeRects, $bestPlacement, $boxH);
            }
        }

        return $this->calculatePackingResult($placements, $packed);
    }

    /**
     * Vypočítá efektivitu tvaru (100% = kostka)
     */
    private function calculateShapeEfficiency($l, $w, $h) {
        if ($l <= 0 || $w <= 0 || $h <= 0) {
            return 0;
        }

        $volume = $l * $w * $h;
        $actualSurface = 2 * ($l * $w + $l * $h + $w * $h);

        // Ideální kostka pro stejný objem
        $idealEdge = pow($volume, 1/3);
        $idealSurface = 6 * $idealEdge * $idealEdge;

        // Efektivita = povrch kostky / skutečný povrch × 100
        return round(($idealSurface / $actualSurface) * 100, 1);
    }

    /**
     * Kontrola pravidel stackingu
     */
    private function canPlaceInRect($rect, $item, $placements) {
        if ($rect['z'] == 0) {
            return true;
        }

        if ($this->stackingMode === 'all') {
            return true;
        }

        if ($this->stackingMode === 'none') {
            return false;
        }

        foreach ($placements as $p) {
            $topZ = $p['z'] + $p['h'];

            if (abs($topZ - $rect['z']) < 0.001) {
                if ($this->rectsOverlap($rect, $p)) {
                    return $p['name'] === $item['name'];
                }
            }
        }

        return true;
    }

    /**
     * Kontrola překryvu v XY
     */
    private function rectsOverlap($r1, $r2) {
        return $r1['x'] < ($r2['x'] + $r2['l']) &&
            ($r1['x'] + $r1['l']) > $r2['x'] &&
            $r1['y'] < ($r2['y'] + $r2['w']) &&
            ($r1['y'] + $r1['w']) > $r2['y'];
    }

    /**
     * MaxRects split
     */
    private function splitFreeRects($freeRects, $placement, $maxH) {
        $newRects = [];

        foreach ($freeRects as $rect) {
            if (!$this->rectsOverlap3D($rect, $placement)) {
                $newRects[] = $rect;
                continue;
            }

            // Vlevo
            if ($placement['x'] > $rect['x']) {
                $newRects[] = [
                    'x' => $rect['x'],
                    'y' => $rect['y'],
                    'z' => $rect['z'],
                    'l' => $placement['x'] - $rect['x'],
                    'w' => $rect['w'],
                    'h' => $rect['h'],
                ];
            }

            // Vpravo
            $rightX = $placement['x'] + $placement['l'];
            $rectEndX = $rect['x'] + $rect['l'];
            if ($rightX < $rectEndX) {
                $newRects[] = [
                    'x' => $rightX,
                    'y' => $rect['y'],
                    'z' => $rect['z'],
                    'l' => $rectEndX - $rightX,
                    'w' => $rect['w'],
                    'h' => $rect['h'],
                ];
            }

            // Vpředu
            if ($placement['y'] > $rect['y']) {
                $newRects[] = [
                    'x' => $rect['x'],
                    'y' => $rect['y'],
                    'z' => $rect['z'],
                    'l' => $rect['l'],
                    'w' => $placement['y'] - $rect['y'],
                    'h' => $rect['h'],
                ];
            }

            // Vzadu
            $backY = $placement['y'] + $placement['w'];
            $rectEndY = $rect['y'] + $rect['w'];
            if ($backY < $rectEndY) {
                $newRects[] = [
                    'x' => $rect['x'],
                    'y' => $backY,
                    'z' => $rect['z'],
                    'l' => $rect['l'],
                    'w' => $rectEndY - $backY,
                    'h' => $rect['h'],
                ];
            }

            // Nahoře
            if ($this->stackingMode !== 'none') {
                $topZ = $placement['z'] + $placement['h'];
                $rectEndZ = $rect['z'] + $rect['h'];
                if ($topZ < $rectEndZ && $topZ < $maxH) {
                    $newRects[] = [
                        'x' => $placement['x'],
                        'y' => $placement['y'],
                        'z' => $topZ,
                        'l' => $placement['l'],
                        'w' => $placement['w'],
                        'h' => $rectEndZ - $topZ,
                    ];
                }
            }
        }

        $newRects = array_filter($newRects, function($r) {
            return $r['l'] > 0 && $r['w'] > 0 && $r['h'] > 0;
        });

        return array_values($this->removeContainedRects($newRects));
    }

    /**
     * Kontrola 3D překryvu
     */
    private function rectsOverlap3D($r1, $r2) {
        return $r1['x'] < ($r2['x'] + $r2['l']) && ($r1['x'] + $r1['l']) > $r2['x'] &&
            $r1['y'] < ($r2['y'] + $r2['w']) && ($r1['y'] + $r1['w']) > $r2['y'] &&
            $r1['z'] < ($r2['z'] + $r2['h']) && ($r1['z'] + $r1['h']) > $r2['z'];
    }

    /**
     * Odstraní obsažené obdélníky
     */
    private function removeContainedRects($rects) {
        $result = [];

        foreach ($rects as $i => $r1) {
            $contained = false;

            foreach ($rects as $j => $r2) {
                if ($i === $j) continue;

                if ($this->rectContains($r2, $r1)) {
                    $contained = true;
                    break;
                }
            }

            if (!$contained) {
                $result[] = $r1;
            }
        }

        return $result;
    }

    /**
     * Kontrola, zda r1 obsahuje r2
     */
    private function rectContains($r1, $r2) {
        return $r1['x'] <= $r2['x'] &&
            $r1['y'] <= $r2['y'] &&
            $r1['z'] <= $r2['z'] &&
            ($r1['x'] + $r1['l']) >= ($r2['x'] + $r2['l']) &&
            ($r1['y'] + $r1['w']) >= ($r2['y'] + $r2['w']) &&
            ($r1['z'] + $r1['h']) >= ($r2['z'] + $r2['h']);
    }

    /**
     * Vypočítá výsledek balení
     */
    private function calculatePackingResult($placements, $packed) {
        $boundingBox = $this->calculateBoundingBox($placements);
        $l = $boundingBox['l'];
        $w = $boundingBox['w'];
        $h = $boundingBox['h'];

        $bboxVolume = $l * $w * $h;
        $bboxSurface = $bboxVolume > 0 ? 2 * ($l * $w + $l * $h + $w * $h) : 0;

        $itemsVolume = 0;
        foreach ($packed as $item) {
            $itemsVolume += $item['volume'];
        }

        $packingEfficiency = $bboxVolume > 0
            ? round(($itemsVolume / $bboxVolume) * 100, 1)
            : 0;

        $shapeEfficiency = $this->calculateShapeEfficiency($l, $w, $h);

        // Overall = kombinace obou (geometrický průměr)
        $overallEfficiency = ($packingEfficiency > 0 && $shapeEfficiency > 0)
            ? round(sqrt($packingEfficiency * $shapeEfficiency), 1)
            : 0;

        return [
            'items' => $packed,
            'count' => count($packed),
            'bounding_box' => $boundingBox,
            'placements' => $placements,
            'metrics' => [
                'items_volume' => $itemsVolume,
                'bounding_box_volume' => $bboxVolume,
                'bounding_box_surface' => $bboxSurface,
                'packing_efficiency' => $packingEfficiency,
                'shape_efficiency' => $shapeEfficiency,
                'overall_efficiency' => $overallEfficiency,
                'wasted_space' => $bboxVolume - $itemsVolume,
            ],
        ];
    }

    /**
     * Vytvoří výsledek balíku
     */
    private function createPackResult($box, $packed, $score) {
        $bbox = $packed['bounding_box'];
        $padding = $box['padding'];

        $outerBbox = [
            $bbox['l'] + (2 * $padding),
            $bbox['w'] + (2 * $padding),
            $bbox['h'] + (2 * $padding),
        ];
        rsort($outerBbox);

        return [
            'score' => $score,
            'packed_ids' => array_column($packed['items'], 'id'),
            'result' => [
                'box_name' => $box['name'],
                'box_max_outer' => [
                    'length' => $box['outer'][0],
                    'width' => $box['outer'][1],
                    'height' => $box['outer'][2],
                ],
                'box_max_inner' => [
                    'length' => $box['inner'][0],
                    'width' => $box['inner'][1],
                    'height' => $box['inner'][2],
                ],
                'packed_outer' => [
                    'length' => $outerBbox[0],
                    'width' => $outerBbox[1],
                    'height' => $outerBbox[2],
                ],
                'packed_inner' => [
                    'length' => $bbox['l'],
                    'width' => $bbox['w'],
                    'height' => $bbox['h'],
                ],
                'items_count' => $packed['count'],
                'items' => $this->summarizeItems($packed['items']),
                'metrics' => $packed['metrics'],
            ],
        ];
    }

    /**
     * Vypočítá celkové skóre pro porovnání strategií
     */
    private function calculateOverallScore($result) {
        if (!$result['success']) {
            return -PHP_INT_MAX;
        }

        $totalOverall = 0;
        $boxCount = count($result['boxes']);

        foreach ($result['boxes'] as $box) {
            $totalOverall += $box['metrics']['overall_efficiency'];
        }

        $avgOverall = $boxCount > 0 ? $totalOverall / $boxCount : 0;

        // Méně balíků a vyšší efektivita = lepší
        return ($avgOverall * 100) - ($boxCount * 500);
    }

    /**
     * Vypočítá souhrnné metriky
     */
    private function calculateSummary($boxes) {
        $totalItemsVolume = 0;
        $totalBboxVolume = 0;
        $totalBboxSurface = 0;
        $totalWastedSpace = 0;
        $packingEfficiencies = [];
        $shapeEfficiencies = [];
        $overallEfficiencies = [];

        foreach ($boxes as $box) {
            $m = $box['metrics'];
            $totalItemsVolume += $m['items_volume'];
            $totalBboxVolume += $m['bounding_box_volume'];
            $totalBboxSurface += $m['bounding_box_surface'];
            $totalWastedSpace += $m['wasted_space'];
            $packingEfficiencies[] = $m['packing_efficiency'];
            $shapeEfficiencies[] = $m['shape_efficiency'];
            $overallEfficiencies[] = $m['overall_efficiency'];
        }

        $count = count($boxes);

        return [
            'total_items_volume' => $totalItemsVolume,
            'total_bounding_box_volume' => $totalBboxVolume,
            'total_bounding_box_surface' => $totalBboxSurface,
            'total_wasted_space' => $totalWastedSpace,
            'average_packing_efficiency' => $count > 0
                ? round(array_sum($packingEfficiencies) / $count, 1)
                : 0,
            'average_shape_efficiency' => $count > 0
                ? round(array_sum($shapeEfficiencies) / $count, 1)
                : 0,
            'average_overall_efficiency' => $count > 0
                ? round(array_sum($overallEfficiencies) / $count, 1)
                : 0,
        ];
    }

    private function expandItems() {
        $expanded = [];
        $id = 0;

        foreach ($this->items as $item) {
            for ($i = 0; $i < $item['qty']; $i++) {
                $expanded[] = [
                    'id' => $id++,
                    'name' => $item['name'],
                    'dims' => $item['dims'],
                    'volume' => $item['volume'],
                ];
            }
        }

        return $expanded;
    }

    private function calculateBoundingBox($placements, $newPlacement = null) {
        if (empty($placements) && $newPlacement === null) {
            return ['l' => 0, 'w' => 0, 'h' => 0];
        }

        $maxL = $maxW = $maxH = 0;

        foreach ($placements as $p) {
            $maxL = max($maxL, $p['x'] + $p['l']);
            $maxW = max($maxW, $p['y'] + $p['w']);
            $maxH = max($maxH, $p['z'] + $p['h']);
        }

        if ($newPlacement !== null) {
            $maxL = max($maxL, $newPlacement['x'] + $newPlacement['l']);
            $maxW = max($maxW, $newPlacement['y'] + $newPlacement['w']);
            $maxH = max($maxH, $newPlacement['z'] + $newPlacement['h']);
        }

        return ['l' => $maxL, 'w' => $maxW, 'h' => $maxH];
    }

    private function itemFitsInBox($itemDims, $boxDims) {
        foreach ($this->getRotations($itemDims) as $rotation) {
            if ($rotation[0] <= $boxDims[0] &&
                $rotation[1] <= $boxDims[1] &&
                $rotation[2] <= $boxDims[2]) {
                return true;
            }
        }
        return false;
    }

    private function getRotations($dims) {
        $rotations = [
            [$dims[0], $dims[1], $dims[2]],
            [$dims[0], $dims[2], $dims[1]],
            [$dims[1], $dims[0], $dims[2]],
            [$dims[1], $dims[2], $dims[0]],
            [$dims[2], $dims[0], $dims[1]],
            [$dims[2], $dims[1], $dims[0]],
        ];

        $unique = [];
        foreach ($rotations as $r) {
            $unique[implode('x', $r)] = $r;
        }

        return array_values($unique);
    }

    private function summarizeItems($items) {
        $summary = [];
        foreach ($items as $item) {
            $name = $item['name'];
            if (!isset($summary[$name])) {
                $summary[$name] = [
                    'name' => $name,
                    'dimensions' => $this->formatDimensions($item['dims']),
                    'qty' => 0,
                ];
            }
            $summary[$name]['qty']++;
        }
        return array_values($summary);
    }

    private function formatDimensions($dims) {
        return sprintf('%d x %d x %d mm', $dims[0], $dims[1], $dims[2]);
    }

    private function errorResult($message, $unpacked = [], $packedBoxes = []) {
        return [
            'success' => false,
            'error' => $message,
            'boxes' => $packedBoxes,
            'total_boxes' => count($packedBoxes),
            'unpacked' => $this->summarizeItems($unpacked),
        ];
    }
}