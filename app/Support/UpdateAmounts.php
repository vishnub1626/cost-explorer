<?php

namespace App\Support;

class UpdateAmounts
{
    public function execute($clients)
    {
        foreach ($clients as $client) {
            $this->calculate($client);
        }

        return $clients;
    }

    public function calculate($data)
    {
        if ($data->children->isEmpty()) {
            return $data->amount;
        }

        $amount = 0;
        foreach ($data->children as $child) {
            $amount += $this->calculate($child);
        }

        $data->amount = $amount;

        return $data->amount;
    }
}
