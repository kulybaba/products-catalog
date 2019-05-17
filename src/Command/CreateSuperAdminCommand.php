<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateSuperAdminCommand extends Command
{
    /**
     * @var UserService $userService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('create:user')
            ->setDescription('Create super admin')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $result = $this->userService->createSuperAdmin($email, $password);

        if (count($result['errors'])) {
            foreach ($result['errors'] as $error) {
                $io->error('Error: ' . json_encode($error, true));
            }
        } else {
            $io->success('User created: ' . $result['user']->getEmail() . ' ' . $result['user']->getPlainPassword());
        }
    }
}
