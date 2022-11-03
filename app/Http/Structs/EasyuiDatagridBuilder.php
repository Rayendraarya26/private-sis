<?php

namespace App\Http\Structs;

use Illuminate\Support\Collection;

class EasyuiDatagridBuilder
{
    private Collection $data;
    private int $page = 1;
    private int $row = 10;

    public function __construct(array $data)
    {
        $this->data  = collect($data);
        $this->total = count($data);
    }

    public function take(int $page, int $row): static
    {
        $this->page = $page;
        $this->row  = $row;
        return $this;
    }

    public function search($key, $value): static
    {
        $this->data = $this->data->filter(function ($item) use ($key, $value) {
            return (false !== stripos($item[$key], $value));
        });
        return $this;
    }

    /*
     * @params sorter = [['keyName1', 'asc'],['keyName2', 'asc']]
     * */
    public function sort(array $sorter): static
    {
        if (count($sorter) > 0) {
            $this->data = $this->data->sortBy($sorter);
        }
        return $this;
    }

    public function toArray(): array
    {
        $total  = $this->data->count();
        $chunks = $this->data->chunk($this->row);

        if (isset($chunks[$this->page - 1])) {
            $rows = array_values($chunks[$this->page - 1]->toArray());
        } else {
            $rows = [];
        }


        return ['rows' => $rows, 'total' => $total];
    }
}