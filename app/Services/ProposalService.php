<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;

class ProposalService
{
    /**
     * Convert a proposal to an order.
     *
     * @param Proposal $proposal
     * @return Order
     * @throws \Exception
     */
    public function convertToOrder(Proposal $proposal): Order
    {
        if ($proposal->lines->isEmpty()) {
            throw new \Exception('A proposta não tem linhas para converter.');
        }

        DB::beginTransaction();
        try {
            // Close the proposal if not already closed
            if (!$proposal->isClosed()) {
                $proposal->close();
            }

            // Create the order
            $order = Order::create([
                'order_date' => now(),
                'entity_id' => $proposal->entity_id,
                'proposal_id' => $proposal->id,
                'notes' => $proposal->notes,
            ]);

            // Copy all lines from proposal to order
            foreach ($proposal->lines as $proposalLine) {
                $order->lines()->create([
                    'article_id' => $proposalLine->article_id,
                    'article_reference' => $proposalLine->article_reference,
                    'article_name' => $proposalLine->article_name,
                    'description' => $proposalLine->description,
                    'quantity' => $proposalLine->quantity,
                    'unit_price' => $proposalLine->unit_price,
                    'discount' => $proposalLine->discount,
                    'discount_percentage' => $proposalLine->discount_percentage,
                    'vat_rate_id' => $proposalLine->vat_rate_id,
                    'vat_rate' => $proposalLine->vat_rate,
                    'supplier_id' => $proposalLine->supplier_id,
                    'cost_price' => $proposalLine->cost_price,
                    'sort_order' => $proposalLine->sort_order,
                ]);
            }

            // Load lines and recalculate order totals
            $order->load('lines');
            $order->calculateTotals();

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Erro ao converter proposta para encomenda: ' . $e->getMessage());
        }
    }

    /**
     * Create supplier orders from order lines grouped by supplier.
     *
     * @param Order $order
     * @return array Array of created supplier orders
     * @throws \Exception
     */
    public function createSupplierOrders(Order $order): array
    {
        if (!$order->isClosed()) {
            throw new \Exception('A encomenda precisa estar fechada para criar encomendas de fornecedor.');
        }

        // Group lines by supplier
        $linesBySupplier = $order->lines()
            ->whereNotNull('supplier_id')
            ->get()
            ->groupBy('supplier_id');

        if ($linesBySupplier->isEmpty()) {
            throw new \Exception('Nenhuma linha com fornecedor definido.');
        }

        $supplierOrders = [];

        DB::beginTransaction();
        try {
            foreach ($linesBySupplier as $supplierId => $lines) {
                // Create supplier order
                $supplierOrder = Order::create([
                    'order_date' => now(),
                    'entity_id' => $supplierId,
                    'notes' => "Encomenda criada automaticamente a partir da encomenda #{$order->number}",
                ]);

                // Create lines for supplier order using cost_price
                foreach ($lines as $index => $line) {
                    $supplierOrder->lines()->create([
                        'article_id' => $line->article_id,
                        'article_reference' => $line->article_reference,
                        'article_name' => $line->article_name,
                        'description' => $line->description,
                        'quantity' => $line->quantity,
                        'unit_price' => $line->cost_price ?? $line->unit_price,
                        'discount' => 0,
                        'discount_percentage' => 0,
                        'vat_rate_id' => $line->vat_rate_id,
                        'vat_rate' => $line->vat_rate,
                        'sort_order' => $index,
                    ]);
                }

                // Load lines and recalculate totals
                $supplierOrder->load('lines');
                $supplierOrder->calculateTotals();
                $supplierOrders[] = $supplierOrder;
            }

            DB::commit();

            return $supplierOrders;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Erro ao criar encomendas de fornecedor: ' . $e->getMessage());
        }
    }
}
