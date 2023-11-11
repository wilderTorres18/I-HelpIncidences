<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter
{
    protected array $safeParms = [];

    protected array $columnMap = [];

    protected array $operatorMap = [];

    public function transform(Request $request): array
    {
        $eloquery = [];

        foreach ($this->safeParms as $parm => $operators) {
            $query = $request->query($parm);
            if (!isset($query)) {
                continue;
            }

            $column = $this->columnMap[$parm] ?? $parm;
            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $eloquery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }
        return $eloquery;
    }
}
