<?php

namespace App\Model;


use Nette;

class Calculator
{
    use Nette\SmartObject;

    /** @var int[] marže */
    const MARGINS = [
        1 => 10,
        2 => 20,
        3 => 30,
        4 => 40,
        5 => 50,
        6 => 60,
        7 => 70,
        8 => 80,
        9 => 90,
        10 => 100,
    ];

    /** @var string[] dodavatelé */
    const SUPPLIERS = [
        1 => 'Dodavatel 1',
        2 => 'Dodavatel 2',
        3 => 'Dodavatel 3',
        4 => 'Dodavatel 4',
        5 => 'Dodavatel 5',
        6 => 'Dodavatel 6',
        7 => 'Dodavatel 7',
        8 => 'Dodavatel 8',
        9 => 'Dodavatel 9',
        10 => 'Dodavatel 10',
    ];

    /**
     * Zpracovat data z formu a vypočítat cenu s marží
     *
     * @param Nette\Utils\ArrayHash $values
     * @return array
     */
    public function processData(Nette\Utils\ArrayHash $values): array
    {
        $supplier = $values->supplier;
        $offer = $values->offer;

        // Rozdělení řetězce podle odřádkování
        $lines = explode("\n", $offer);

        $results = [];
        foreach ($lines as $line) {
            if (!empty($line))
            {
                // Množství a cena
                $sourceData = $this->getPriceAndPiecesFromString($line);

                $results[] = [
                    'withoutMargin' => $sourceData,
                    'withMargin' => $this->getPriceWithMargin($sourceData, $supplier)
                ];
            }
        }

        return $results;
    }

    /**
     * Získat cenu a množství se stringu
     *
     * @param string $string
     * @return array
     */
    private function getPriceAndPiecesFromString(string $string): array
    {
        // Nahradit všechny nežádoucí znaky
        $cleanedString = preg_replace('/[^\d:;,]/', '', $string);

        // Rozdělit pomocí oddělovačů
        $parts = preg_split('/[:;,]+/', $cleanedString, -1, PREG_SPLIT_NO_EMPTY);

        $quantity = str_replace(' ', '', $parts[0]);
        $price = str_replace(' ', '', $parts[1]);

        // Získat jen číslo
        $quantity = filter_var($quantity, FILTER_SANITIZE_NUMBER_INT);
        $price = filter_var($price, FILTER_SANITIZE_NUMBER_INT);

        return [
            'quantity' => $quantity,
            'price' => $price
        ];
    }

    /**
     * Vypočítat cenu s marží
     *
     * @param array $arr
     * @param int $supplier
     * @return array
     */
    private function getPriceWithMargin(array $arr, int $supplier): array
    {
        $quantity = $arr['quantity'];
        $price = $arr['price'];
        $margin = self::MARGINS[$supplier] / 100;

        // Výpočet ceny s marží
        $priceWithMargin = $price + ($price * $margin);

        return [
            'quantity' => $quantity,
            'price' => $price,
            'priceWithMargin' => $priceWithMargin,
            'margin' => self::MARGINS[$supplier]
        ];
    }
}
