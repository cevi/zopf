<?php

namespace App\Imports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrdersImport implements SkipsEmptyRows, ToModel, WithHeadingRow
{
    use Importable;

    /**
     * @return Model|null
     */
    public function model(array $row)
    {
        return new Order([
            //
            'name' => $row['name'],
            'firstname' => $row['vorname'],
            'street' => $row['strasse'],
            'plz' => $row['plz'],
            'city' => $row['ortschaft'],
            'quantity' => $row['anzahl'],
            'route' => $row['route'],
            'pick_up' => $row['abholung'],
            'comment' => $row['bemerkung'],
        ]);
    }

    public function isEmptyWhen(array $row): bool
    {
        return ! isset($row['abholung']) && ! isset($row['vorname']);
    }
}
