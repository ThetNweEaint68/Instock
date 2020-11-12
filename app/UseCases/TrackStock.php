<?php

namespace App\UseCases;

use App\Models\User;
use App\Models\Stock;
use App\Models\History;
use App\Clients\StockStatus;
use Illuminate\Queue\SerializesModels;
use App\Notifications\ImportantStockUpdate;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrackStock implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    protected Stock $stocks;

    protected StockStatus $status;

    public function __construct(Stock $stock)
    {
        $this->stocks = $stock;
    }

    public function handle()
    {
        $this->checkAvailability();

        $this->notifyUser();
        $this->refreshStock();
        $this->recordToHistory();
    }

    protected function checkAvailability()
    {
        $this->status = $this->stocks
            ->retailer
            ->client()
            ->checkAvailability($this->stocks);
    }

    protected function notifyUser()
    {
        if ($this->isNowInStock()) {
            User::first()->notify(
                new ImportantStockUpdate($this->stock)
            );
        }
    }

    protected function refreshStock()
    {
        $this->stocks->update([
            'in_stock' => $this->status->available,
            'price' => $this->status->price
        ]);
    }

    protected function recordToHistory()
    {
        History::create([
            'price' => $this->stocks->price,
            'in_stock' => $this->stocks->in_stock,
            'product_id' => $this->stocks->product_id,
            'stock_id' => $this->stocks->id
        ]);
    }

    protected function isNowInStock(): bool
    {
        return ! $this->stocks->in_stock && $this->status->available;
    }
}