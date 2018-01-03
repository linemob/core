<?php

namespace LineMob\Core\Mocky\Auth;

use LineMob\Core\Input;
use LineMob\Core\Mocky\Auth\Command\BaseCommand;
use LineMob\Core\Mocky\Doctrine\Model\User;
use LineMob\Core\Workflow\AbstractWorkflow;
use Symfony\Component\Workflow\Workflow;

class AuthenticationFlow extends AbstractWorkflow
{
    protected $mappingMethods = [
        'doApplyStart',
        'doApplyEnterUsernameAndPassword',
        'doApplyEnterUsername',
        'doApplyEnterPassword'
    ];

    /**
     * {@inheritdoc}
     */
    protected function getConfig()
    {
        return [
            'name' => 'Authentication',
            'marking_store' => [
                'type' => 'multiple_state',
                'arguments' => ['state'],
            ],
            'supports' => [
                User::class,
            ],
            'places' => [
                'started',
                'wait_for_username',
                'wait_for_password',
                'wait_for_username_n_password',
                'finished',
            ],
            'transitions' => [
                'start' => [
                    'from' => 'started',
                    'to' => ['wait_for_username_n_password', 'wait_for_username'],
                ],
                'enter_username' => [
                    'from' => 'wait_for_username',
                    'to' => 'wait_for_password',
                ],
                'enter_password' => [
                    'from' => 'wait_for_password',
                    'to' => 'finished',
                ],
                'enter_username_n_password' => [
                    'from' => 'wait_for_username_n_password',
                    'to' => 'finished',
                ],
            ],
        ];
    }

    /**
     * @param Input $input
     *
     * @return array
     */
    private function captureUserAndPassword(Input $input)
    {
        $text = trim(preg_replace('|\W+|', ' ', $input->text));

        return explode(' ', $text);
    }

    /**
     * @param BaseCommand $command
     * @param Workflow $workflow
     *
     * @return bool
     */
    public function doApplyStart(BaseCommand $command, Workflow $workflow)
    {
        $subject = $command->storage;
        if ($workflow->can($subject, 'start')) {
            $workflow->apply($subject, 'start');

            $command->message->text = 'Please Enter username & password.';

            return true;
        }

        return false;
    }

    /**
     * @param BaseCommand $command
     * @param Workflow $workflow
     *
     * @return bool
     */
    public function doApplyEnterUsernameAndPassword(BaseCommand $command, Workflow $workflow)
    {
        $subject = $command->storage;

        @list($username, $password) = $this->captureUserAndPassword($command->input);

        $command->message->text = 'Try again ...';

        if (!$username || !$password || !$workflow->can($subject, 'enter_username_n_password')) {
            return false;
        }

        if ($username === 'demo' && $password === 'demo') {
            $workflow->apply($subject, 'enter_username_n_password');

            $command->storage->setLineLastLogin(new \DateTimeImmutable());
            $command->active = false;
            $command->message->text = 'Success!';

            return true;
        }

        return false;
    }

    /**
     * @param BaseCommand $command
     * @param Workflow $workflow
     *
     * @return bool
     */
    public function doApplyEnterUsername(BaseCommand $command, Workflow $workflow)
    {
        $subject = $command->storage;
        $username = $this->captureUserAndPassword($command->input)[0];

        if ($username && $workflow->can($subject, 'enter_username')) {
            if ($username === 'demo') {
                $workflow->apply($subject, 'enter_username');

                $command->message->text = 'Please Enter password!';
            } else {
                $command->message->text = 'Not found username, Try again ...';
            }

            return true;
        }

        return false;
    }

    /**
     * @param BaseCommand $command
     * @param Workflow $workflow
     *
     * @return bool
     */
    public function doApplyEnterPassword(BaseCommand $command, Workflow $workflow)
    {
        $subject = $command->storage;
        $password = $this->captureUserAndPassword($command->input)[0];

        if ($password && $workflow->can($subject, 'enter_password')) {
            if ($password === 'demo') {
                $workflow->apply($subject, 'enter_password');

                $command->storage->setLineLastLogin(new \DateTimeImmutable());
                $command->storage->setLineActiveCmd(null);
                $command->storage->setLineCommandData([]);
                $command->active = false;
                $command->message->text = 'Success!';
            } else {
                $command->message->text = 'Password not match, Try again ...';
            }

            return true;
        }

        return false;
    }
}
