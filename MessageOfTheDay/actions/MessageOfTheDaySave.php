<?php

namespace Modules\MessageOfTheDay\Actions;

use CController,
    CControllerResponseRedirect,
    CUrl,
    CWebUser;

class MessageOfTheDaySave extends CController {

    public function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        $fields = [
            'enabled'     => 'in 0,1',
            'message'     => 'string',
            'type'        => 'in info,warning,danger,success',
            'dismissible' => 'in 0,1',
            'show_to'     => 'in all,admins'
        ];

        $ret = $this->validateInput($fields);

        if (!$ret) {
            $this->setResponse(new CControllerResponseRedirect(
                (new CUrl('zabbix.php'))
                    ->setArgument('action', 'messageoftheday.edit')
                    ->setArgument('saved', '0')
                    ->getUrl()
            ));
        }

        return $ret;
    }

    protected function checkPermissions(): bool {
        return (CWebUser::getType() == USER_TYPE_SUPER_ADMIN);
    }

    protected function doAction(): void {
        $config = [
            'enabled'     => (bool) $this->getInput('enabled', 0),
            'message'     => $this->getInput('message', ''),
            'type'        => $this->getInput('type', 'info'),
            'dismissible' => (bool) $this->getInput('dismissible', 1),
            'show_to'     => $this->getInput('show_to', 'all')
        ];

        $config_file = __DIR__ . '/../config.json';
        $success = file_put_contents(
            $config_file,
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $this->setResponse(new CControllerResponseRedirect(
            (new CUrl('zabbix.php'))
                ->setArgument('action', 'messageoftheday.edit')
                ->setArgument('saved', $success !== false ? '1' : '0')
                ->getUrl()
        ));
    }
}
