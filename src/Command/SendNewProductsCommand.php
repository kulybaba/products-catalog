<?php

namespace App\Command;

use App\Repository\ProductRepository;
use App\Service\EmailService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendNewProductsCommand extends Command
{
    private $productRepository;

    private $emailService;

    public function __construct(ProductRepository $productRepository, EmailService $emailService)
    {
        $this->productRepository = $productRepository;
        $this->emailService = $emailService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('send:new-products')
            ->setDescription('Sends new products per day to the administrator');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $start = date('Y-m-d H:i:s', strtotime('-1 day'));
        $end = date('Y-m-d H:i:s');

        $products = $this->productRepository->findStartEndDates($start, $end);

        $this->emailService->sendProductsEmail($products);

        $io->success('New products sent!');
    }
}
