<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Command\CreateOrderCommand as AppCreateOrderCommand;
use App\Application\Command\CreateOrderHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-order', description: 'Creates an order from the CLI')]
final class CreateOrderConsoleCommand extends Command
{
    public function __construct(
        private readonly CreateOrderHandler $createOrderHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('customerId', InputArgument::REQUIRED, 'Customer ID')
            ->addOption('item', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Item in format sku:quantity:price_cents; repeatable', [])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $customerIdArg = $input->getArgument('customerId');
        $customerId = is_scalar($customerIdArg) ? (string) $customerIdArg : '';
        if ('' === $customerId) {
            $output->writeln('<error>CustomerId is required</error>');

            return Command::INVALID;
        }
        $itemsRaw = $input->getOption('item');
        if (! is_array($itemsRaw)) {
            $itemsRaw = [];
        }

        if ([] === $itemsRaw) {
            $output->writeln('<error>No items provided. Use --item sku:qty:price_cents</error>');

            return Command::INVALID;
        }

        $items = [];
        foreach ($itemsRaw as $itemRaw) {
            $rawStr = is_scalar($itemRaw) ? (string) $itemRaw : '';
            $parts = explode(':', $rawStr);
            if (3 !== count($parts)) {
                $output->writeln("<error>Invalid item format: {$rawStr}</error>");

                return Command::INVALID;
            }
            [$sku, $qty, $price] = $parts;
            $items[] = [
                'sku' => $sku,
                'quantity' => (int) $qty,
                'price_cents' => (int) $price,
            ];
        }

        $createOrderCommand = new AppCreateOrderCommand($customerId, $items);

        try {
            $result = $this->createOrderHandler->handle($createOrderCommand);
        } catch (\Throwable $e) {
            $output->writeln('<error>Failed to create order: '.$e->getMessage().'</error>');

            return Command::FAILURE;
        }

        $output->writeln('<info>Order created: '.$result->orderId.'</info>');

        return Command::SUCCESS;
    }
}
