<?php

declare(strict_types=1);

final class HiddenItemGame
{
    private const WALL = '#';
    private const START = 'X';
    private const MARK = '$';

    private const NORTH = [-1, 0];
    private const EAST = [0, 1];
    private const SOUTH = [1, 0];

    private array $grid = [
        "########",
        "#......#",
        "#.###..#",
        "#...#.##",
        "#X#....#",
        "########",
    ];

    private array $start;

    public function __construct()
    {
        $this->start = $this->findStart();
    }

    public function solve(int $a, int $b, int $c): void
    {
        if ($a < 0 || $b < 0 || $c < 0) {
            echo "Input tidak valid: langkah tidak boleh negatif.\n";
            return;
        }

        $target = $this->trace([$a, $b, $c]);
        $locations = $target ? [$target] : [];

        $this->printLocations($locations);
        $this->printGrid($locations);
    }

    private function trace(array $steps): ?array
    {
        $path = [
            [self::NORTH, $steps[0]],
            [self::EAST, $steps[1]],
            [self::SOUTH, $steps[2]],
        ];

        $pos = $this->start;

        foreach ($path as [$dir, $count]) {
            $pos = $this->walk($pos, $dir, $count);
            if ($pos === null) {
                return null;
            }
        }

        return $pos;
    }

    private function walk(array $from, array $dir, int $steps): ?array
    {
        [$r, $c] = [$from['r'], $from['c']];
        [$dr, $dc] = $dir;

        for ($i = 0; $i < $steps; $i++) {
            $r += $dr;
            $c += $dc;

            if (!$this->isWalkable($r, $c)) {
                return null;
            }
        }

        return ['r' => $r, 'c' => $c];
    }

    private function isWalkable(int $r, int $c): bool
    {
        return isset($this->grid[$r][$c]) && $this->grid[$r][$c] !== self::WALL;
    }

    private function findStart(): array
    {
        foreach ($this->grid as $r => $row) {
            $c = strpos($row, self::START);
            if ($c !== false) {
                return ['r' => $r, 'c' => $c];
            }
        }

        throw new RuntimeException("Start position 'X' tidak ditemukan.");
    }

    private function printLocations(array $locations): void
    {
        echo "\nProbable Coordinate Points (Row, Column):\n";

        if (!$locations) {
            echo "No valid path found (Hit an obstacle or out of bounds).\n";
            return;
        }

        foreach ($locations as $loc) {
            echo "- Row: {$loc['r']}, Column: {$loc['c']}\n";
        }
    }

    private function printGrid(array $locations): void
    {
        echo "\nGrid Output with Marker ($):\n";

        $marks = [];
        foreach ($locations as $loc) {
            $marks[$loc['r'] . ':' . $loc['c']] = true;
        }

        foreach ($this->grid as $r => $row) {
            $line = '';
            $len = strlen($row);

            for ($c = 0; $c < $len; $c++) {
                $line .= isset($marks[$r . ':' . $c]) ? self::MARK : $row[$c];
            }

            echo $line . "\n";
        }
    }
}

if (PHP_SAPI === 'cli') {
    echo "--- HIDDEN ITEM GAME ---\n";

    $a = (int) readline("Enter steps Up/North (A): ");
    $b = (int) readline("Enter steps Right/East (B): ");
    $c = (int) readline("Enter steps Down/South (C): ");

    (new HiddenItemGame())->solve($a, $b, $c);
}
